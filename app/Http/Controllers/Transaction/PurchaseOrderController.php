<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\SupplierOffersExport;
use App\Http\Controllers\Controller;
use App\Models\ItemNeedToPurchaseDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderSupplierOffer;
use App\Models\PurchaseOrderSupplierOfferDetail;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\Transaction\PurchaseOrder\PurchaseOrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderController extends Controller
{
    public $view, $route;
    protected $repository, $customerOrderRepository;
    public function __construct(
        PurchaseOrderRepositoryInterface $repository,
        CustomerOrderRepositoryInterface $customerOrderRepository
    ) {
        $this->repository = $repository;
        $this->customerOrderRepository = $customerOrderRepository;
        $this->view = 'transaction.purchase-order';
        $this->route = 'purchase-order';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }

    public function create()
    {
        $orderNumber = $this->generatePONumber();
        $customerOrders = $this->customerOrderRepository->all();
        $orderId = request()->query('customer_order_id');
        return view("{$this->view}.create", compact('orderNumber', 'customerOrders', 'orderId'));
    }
    private function generatePONumber()
    {
        // Ambil tanggal, bulan, dan tahun (2 digit)
        $datePart = Carbon::now()->format('dmy'); // Contoh: "100224" (10 Feb 2024)

        // Hitung jumlah PO yang sudah ada dalam tahun ini
        $year = Carbon::now()->year; // Contoh: 2024
        $lastPO = PurchaseOrder::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir, jika ada, increment +1, jika tidak mulai dari 001
        $incrementalNumber = $lastPO ? intval(substr($lastPO->po_number, -3)) + 1 : 1;

        // Formatkan nomor incremental menjadi 3 digit (001, 002, 003, ...)
        $formattedIncrement = str_pad($incrementalNumber, 3, '0', STR_PAD_LEFT);

        // Hasil format akhir: PO{DD}{MM}{YY}-{XXX}
        return "PO{$datePart}-{$formattedIncrement}";
    }
    public function show($id)
    {
        try {
            // Find the purchase order with its relationships
            $purchaseOrder = PurchaseOrder::with([
                'details.itemRequestDetail.itemRequest',
                'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                'details.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
                'details.uom.unitOfMeasurement',
                'offers.supplier',
                'offers.offerDetails.purchaseOrderDetail',
                'offers.offerDetails.uom.unitOfMeasurement',
                'customerOrder.customer',
                'createdBy',
                'activities.causer'
            ])->findOrFail($id);

            // Sort the activities by created_at descending
            $purchaseOrder->activities = $purchaseOrder->activities->sortByDesc('created_at');

            return view("{$this->view}.show", compact('purchaseOrder'));
        } catch (Exception $e) {
            Log::error('Purchase Order Show Error: ' . $e->getMessage());
            dd($e->getMessage());
            alertNotif('error', 'Error loading purchase order: ' . $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'po_number' => 'required|string|max:100|unique:purchase_orders,po_number',
            'customer_order_id' => 'required|exists:customer_orders,id',
            'expected_delivery_date' => 'required|date',
            'remarks' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.item_request_detail_id' => 'required|exists:item_request_details,id',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.order_quantity' => 'required|numeric|min:0.001',
            'details.*.uom_id' => 'required|exists:item_uoms,id',
            'offers' => 'sometimes|array',
            'offers.*.supplier_id' => 'required_with:offers|exists:suppliers,id',
            'offers.*.shipping_cost' => 'required_with:offers|numeric|min:0',
            'offers.*.other_cost' => 'required_with:offers|numeric|min:0',
            'offers.*.remarks' => 'required_with:offers|nullable|string',
            'offers.*.items' => 'required_with:offers|array',
            'offers.*.items.*.item_id' => 'required_with:offers.*.items|exists:items,id',
            'offers.*.items.*.price' => 'required_with:offers.*.items|numeric|min:0',
            'offers.*.items.*.quantity' => 'required_with:offers.*.items|numeric|min:0.001',
            'offers.*.items.*.uom_id' => 'required_with:offers.*.items|exists:item_uoms,id',
            'offers.*.currency' => 'required_with:offers|string|size:3',
        ], [
            'po_number.required' => 'Purchase order number is required. / 采购单编号是必填项。',
            'customer_order_id.required' => 'Customer order is required. / 客户订单是必填项。',
            'expected_delivery_date.required' => 'Expected delivery date is required. / 预计交货日期是必填项。',
            'details.required' => 'At least one item detail is required. / 至少需要一个物品明细。',
            'details.*.order_quantity.min' => 'Order quantity must be greater than 0. / 订购数量必须大于0。',
            'offers.*.supplier_id.required_with' => 'Supplier is required for each offer. / 每个报价都需要指定供应商。',
            'offers.*.items.*.price.required_with' => 'Price is required for each item offer. / 每个物品报价都需要指定价格。',
        ]);

        DB::beginTransaction();

        try {
            // Determine the status based on submit type
            $status = $request->input('submit_type') === 'draft' ? 'Draft' : 'Waiting Approval Manager';

            // Create the purchase order
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $validated['po_number'],
                'customer_order_id' => $validated['customer_order_id'],
                'request_date' => now(),
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'process_status' => $status,
                'remarks' => $validated['remarks'],
                'subtotal_price' => 0, // Will be updated if offers are selected
                'shipping_cost' => 0,
                'other_cost' => 0,
                'created_by' => auth()->id(),
            ]);

            // Process purchase order details
            $purchaseOrderDetailsMap = []; // Format: [itemId-uomId => [poDetailId => quantity, ...]]
            $itemNeedToPurchaseMap = []; // Track itemNeedToPurchase objects by header ID

            foreach ($validated['details'] as $detail) {
                // Only add details with quantity > 0
                if ((float) $detail['order_quantity'] > 0) {
                    $poDetail = PurchaseOrderDetail::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'item_request_detail_id' => $detail['item_request_detail_id'],
                        'quantity' => $detail['order_quantity'],
                        'item_uom_id' => $detail['uom_id'],
                        'remarks' => $detail['remarks'] ?? null,
                    ]);

                    // Store mapping using item_id and uom_id as key
                    $key = $detail['item_id'] . '-' . $detail['uom_id'];
                    if (!isset($purchaseOrderDetailsMap[$key])) {
                        $purchaseOrderDetailsMap[$key] = [];
                    }
                    $purchaseOrderDetailsMap[$key][$poDetail->id] = $detail['order_quantity'];

                    // Update the item need to purchase status if not a draft
                    if ($status !== 'Draft') {
                        $itemNeedToPurchaseDetail = ItemNeedToPurchaseDetail::where('item_request_detail_id', $detail['item_request_detail_id'])->first();

                        if ($itemNeedToPurchaseDetail) {
                            // Get the parent item_need_to_purchase
                            $itemNeedToPurchase = $itemNeedToPurchaseDetail->itemNeedToPurchaseHeader;

                            if ($itemNeedToPurchase) {
                                // Store in our map for later processing
                                $itemNeedToPurchaseMap[$itemNeedToPurchase->id] = $itemNeedToPurchase;
                            }
                        }
                    }
                }
            }

            // Process item need to purchase statuses
            if ($status !== 'Draft' && !empty($itemNeedToPurchaseMap)) {
                foreach ($itemNeedToPurchaseMap as $itemNeedToPurchase) {
                    // Calculate if any related details still need purchasing
                    $needMorePurchases = false;

                    foreach ($itemNeedToPurchase->itemNeedToPurchaseDetail as $relatedDetail) {
                        $requestQuantity = $relatedDetail->itemRequestDetail->quantity;
                        $receivedQuantity = $relatedDetail->itemRequestDetail->deliveryOrderDetails()
                            ->whereHas('header', function ($query) {
                                $query->where('process_status', '<>', 'Draft');
                            })
                            ->sum('quantity');

                        $pendingQuantity = $requestQuantity - $receivedQuantity;
                        $purchaseQuantity = $relatedDetail->itemRequestDetail->purchaseOrderDetails->sum('quantity');

                        // Apply UOM conversion if needed
                        $requestedUOM = $relatedDetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->id;
                        $baseUOM = $relatedDetail->itemRequestDetail->itemPriceHistory->itemUom->item->unitOfMeasurement->id;

                        if ($requestedUOM != $baseUOM) {
                            $pendingQuantity *= $relatedDetail->itemRequestDetail->itemPriceHistory->itemUom->conversion;
                        }

                        $needToBuyQuantity = $pendingQuantity - $purchaseQuantity;

                        if ($needToBuyQuantity > 0) {
                            $needMorePurchases = true;
                            break;
                        }
                    }

                    // Update the item_need_to_purchase status based on remaining needs
                    $newStatus = $needMorePurchases
                        ? 'Partial Purchasing Order'
                        : 'On Process Purchasing Order';

                    $itemNeedToPurchase->update([
                        'process_status' => $newStatus
                    ]);
                }
                $checkApproval = $this->repository->checkApproval('create', $purchaseOrder->id);
                if ($checkApproval['status'] == 200) {
                    $purchaseOrder->update([
                        'process_status' => 'Waiting Approval Manager'
                    ]);
                }
            }

            // Process supplier offers (only if not a draft or offers are provided)
            if (isset($validated['offers'])) {
                $subtotalPrice = 0;

                foreach ($validated['offers'] as $offerData) {
                    $offer = PurchaseOrderSupplierOffer::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'currency' => $offerData['currency'],
                        'supplier_id' => $offerData['supplier_id'],
                        'shipping_cost' => $offerData['shipping_cost'] ?? 0,
                        'other_cost' => $offerData['other_cost'] ?? 0,
                        'grand_total' => 0, // Will be calculated after adding details
                        'is_selected' => false, // Not selected by default
                        'remarks' => $offerData['remarks'] ?? null,
                    ]);

                    $offerSubtotal = 0;

                    // Add offer details for each item
                    foreach ($offerData['items'] as $item) {
                        $key = $item['item_id'] . '-' . $item['uom_id'];
                        $offerItemQuantity = $item['quantity'];
                        $offerdItemPrice = $item['price'];
                        // Check if this item is in our purchase order details
                        if (isset($purchaseOrderDetailsMap[$key]) && !empty($purchaseOrderDetailsMap[$key])) {
                            $totalPOQuantity = array_sum($purchaseOrderDetailsMap[$key]);
                            $remainingQuantity = $offerItemQuantity;
                            $detailIds = array_keys($purchaseOrderDetailsMap[$key]);

                            // For each PO detail that matches this item+UOM
                            foreach ($detailIds as $index => $poDetailId) {
                                $poDetailQuantity = $purchaseOrderDetailsMap[$key][$poDetailId];

                                // Calculate this detail's proportion of the offer quantity
                                $proportion = $poDetailQuantity / $totalPOQuantity;

                                // For the last detail, use remaining quantity to avoid rounding errors
                                $offerDetailQuantity = ($index == count($detailIds) - 1)
                                    ? $remainingQuantity
                                    : round($offerItemQuantity * $proportion, 3);

                                $remainingQuantity -= $offerDetailQuantity;
                                // Create offer detail linking to this PO detail
                                PurchaseOrderSupplierOfferDetail::create([
                                    'offer_id' => $offer->id,
                                    'purchase_order_detail_id' => $poDetailId,
                                    'quantity' => $offerDetailQuantity,
                                    'offered_price_per_unit' => $offerdItemPrice / $item['quantity'],
                                    // total_price is calculated automatically (GENERATED ALWAYS AS)
                                ]);
                            }

                            $offerSubtotal += $item['price'];
                        }
                    }

                    // Update the grand total
                    $grandTotal = $offerSubtotal + $offerData['shipping_cost'] + $offerData['other_cost'];
                    $offer->update(['grand_total' => $grandTotal]);

                    // If this is the first offer, use it as the selected one
                    if (!isset($firstOffer)) {
                        $firstOffer = $offer;
                        $offer->update(['is_selected' => true]);

                        // Update the purchase order with this supplier's prices
                        $subtotalPrice = $offerSubtotal;
                        $purchaseOrder->update([
                            'subtotal_price' => $offerSubtotal,
                            'shipping_cost' => $offerData['shipping_cost'],
                            'other_cost' => $offerData['other_cost'],
                            // total_price is calculated automatically (GENERATED ALWAYS AS)
                        ]);
                    }
                }
            }

            DB::commit();

            $message = $status === 'Draft'
                ? 'Purchase order saved as draft successfully! / 采购单已成功保存为草稿！'
                : 'Purchase order created successfully! / 采购单已成功创建！';

            alertNotif('success', $message);
            return redirect()->route('purchase-order.index');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Creation Error: ' . $e->getMessage());
            dd($e->getMessage());
            alertNotif('error', 'Error creating purchase order: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        try {
            // Find the purchase order with all necessary relationships
            $data = PurchaseOrder::with([
                'details.itemRequestDetail.itemRequest',
                'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                'details.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
                'details.uom.unitOfMeasurement',
                'offers.supplier',
                'offers.offerDetails.purchaseOrderDetail',
                'customerOrder.customer',
            ])->findOrFail($id);

            // Check if purchase order is in Draft status
            if ($data->process_status !== 'Draft') {
                // If not in Draft status, you might want to restrict editing
                // Or you can continue but with limited editing capabilities
                alertNotif('warning', 'You can only modify limited information as the purchase order is not in Draft status. / 由于采购单不是草稿状态，您只能修改有限的信息。');
            }

            // Get all customer orders for the dropdown
            $customerOrders = $this->customerOrderRepository->all();

            // Get all suppliers for the supplier offer dropdowns
            $suppliers = app(\App\Repositories\Master\Supplier\SupplierRepositoryInterface::class)->all();

            // Get currencies
            $currencies = getCurrency();

            // Group offer details by item for better display
            $groupedOfferDetails = [];

            foreach ($data->offers as $offer) {
                $groupedOfferDetails[$offer->id] = [];

                foreach ($offer->offerDetails as $detail) {
                    $poDetail = $detail->purchaseOrderDetail;
                    if (!$poDetail)
                        continue;

                    $itemId = $poDetail->itemRequestDetail->itemPriceHistory->itemUom->item_id ?? null;
                    $itemName = $poDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? 'Unknown Item';
                    $uomId = $poDetail->item_uom_id ?? null;
                    $uomName = $poDetail->uom->unitOfMeasurement->name ?? 'Unknown UOM';
                    $price = $detail->offered_price_per_unit;

                    $key = $itemId . '-' . $uomId . '-' . $price;

                    if (!isset($groupedOfferDetails[$offer->id][$key])) {
                        $groupedOfferDetails[$offer->id][$key] = [
                            'itemId' => $itemId,
                            'itemName' => $itemName,
                            'uomId' => $uomId,
                            'uomName' => $uomName,
                            'quantity' => 0,
                            'price' => $price
                        ];
                    }

                    $groupedOfferDetails[$offer->id][$key]['quantity'] += $detail->quantity;
                }
            }

            return view("{$this->view}.edit", compact(
                'data',
                'customerOrders',
                'suppliers',
                'currencies',
                'groupedOfferDetails'
            ));

        } catch (Exception $e) {
            Log::error('Purchase Order Edit Error: ' . $e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }


    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Validate the request data
        $validated = $request->validate([
            'po_number' => 'required|string|max:100',
            'customer_order_id' => 'required|exists:customer_orders,id',
            'expected_delivery_date' => 'required|date',
            'remarks' => 'nullable|string',
            'details' => 'required_if:submit_type,submit|array|min:1',
            'details.*.id' => 'sometimes|exists:purchase_order_details,id',
            'details.*.item_request_detail_id' => 'required|exists:item_request_details,id',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.order_quantity' => 'required|numeric|min:0.001',
            'details.*.uom_id' => 'required|exists:item_uoms,id',
            'details.*.remarks' => 'nullable|string',
            'offers' => 'required_if:submit_type,submit|array',
            'offers.*.id' => 'sometimes|exists:purchase_order_supplier_offers,id',
            'offers.*.supplier_id' => 'required_with:offers|exists:suppliers,id',
            'offers.*.shipping_cost' => 'required_with:offers|numeric|min:0',
            'offers.*.other_cost' => 'required_with:offers|numeric|min:0',
            'offers.*.remarks' => 'nullable|string',
            'offers.*.items' => 'required_with:offers|array',
            'offers.*.items.*.item_id' => 'required_with:offers.*.items|exists:items,id',
            'offers.*.items.*.price' => 'required_with:offers.*.items|numeric|min:0',
            'offers.*.items.*.quantity' => 'required_with:offers.*.items|numeric|min:0.001',
            'offers.*.items.*.uom_id' => 'required_with:offers.*.items|exists:item_uoms,id',
            'offers.*.currency' => 'required_with:offers|string|size:3',
            'deleted_details' => 'sometimes|array',
            'deleted_offers' => 'sometimes|array',
            'submit_type' => 'required|in:draft,submit',
        ], [
            'po_number.required' => 'Purchase order number is required. / 采购单编号是必填项。',
            'customer_order_id.required' => 'Customer order is required. / 客户订单是必填项。',
            'expected_delivery_date.required' => 'Expected delivery date is required. / 预计交货日期是必填项。',
            'details.required_if' => 'At least one item detail is required when submitting. / 提交时至少需要一个物品明细。',
            'details.*.order_quantity.min' => 'Order quantity must be greater than 0. / 订购数量必须大于0。',
            'offers.required_if' => 'At least one supplier offer is required when submitting. / 提交时至少需要一个供应商报价。',
        ]);

        DB::beginTransaction();

        try {
            // Update the purchase order header
            $purchaseOrder->update([
                'customer_order_id' => $validated['customer_order_id'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'remarks' => $validated['remarks'],
            ]);

            // Determine the status based on submit type
            $status = $request->input('submit_type') === 'draft' ? 'Draft' : 'Waiting Approval Manager';
            if ($status !== 'Draft' && $purchaseOrder->process_status === 'Draft') {
                // Change status only if not draft and previous status was draft
                $purchaseOrder->process_status = $status;
                $purchaseOrder->save();
            }

            // Update purchase order details
            $purchaseOrderDetailsMap = [];
            $itemNeedToPurchaseMap = [];

            // Remove deleted details
            if (isset($validated['deleted_details'])) {
                PurchaseOrderDetail::destroy($validated['deleted_details']);
            }

            // Update or create details
            foreach ($validated['details'] as $detail) {
                if ((float) $detail['order_quantity'] > 0) {
                    $poDetail = PurchaseOrderDetail::updateOrCreate(
                        ['id' => $detail['id'] ?? null],
                        [
                            'purchase_order_id' => $purchaseOrder->id,
                            'item_request_detail_id' => $detail['item_request_detail_id'],
                            'quantity' => $detail['order_quantity'],
                            'item_uom_id' => $detail['uom_id'],
                            'remarks' => $detail['remarks'] ?? null,
                        ]
                    );

                    // Store mapping using purchase order detail ID as key
                    $purchaseOrderDetailsMap[$poDetail->id] = [
                        'item_id' => $detail['item_id'],
                        'uom_id' => $detail['uom_id'],
                        'quantity' => $detail['order_quantity'],
                    ];

                    // Update the item need to purchase status if not a draft
                    if ($status !== 'Draft') {
                        $itemNeedToPurchaseDetail = ItemNeedToPurchaseDetail::where('item_request_detail_id', $detail['item_request_detail_id'])->first();

                        if ($itemNeedToPurchaseDetail) {
                            $itemNeedToPurchase = $itemNeedToPurchaseDetail->itemNeedToPurchaseHeader;

                            if ($itemNeedToPurchase) {
                                $itemNeedToPurchaseMap[$itemNeedToPurchase->id] = $itemNeedToPurchase;
                            }
                        }
                    }
                }
            }
            // Update item need to purchase statuses
            if ($status !== 'Draft' && !empty($itemNeedToPurchaseMap)) {
                foreach ($itemNeedToPurchaseMap as $itemNeedToPurchase) {
                    // Calculate if any related details still need purchasing
                    $needMorePurchases = false;
                    foreach ($itemNeedToPurchase->itemNeedToPurchaseDetail as $relatedDetail) {
                        $requestQuantity = $relatedDetail->itemRequestDetail->quantity;
                        $receivedQuantity = $relatedDetail->itemRequestDetail->deliveryOrderDetails()
                            ->whereHas('header', function ($query) {
                                $query->where('process_status', '<>', 'Draft');
                            })
                            ->sum('quantity');

                        $pendingQuantity = $requestQuantity - $receivedQuantity;
                        $purchaseQuantity = $relatedDetail->itemRequestDetail->purchaseOrderDetails->sum('quantity');

                        // Apply UOM conversion if needed
                        $requestedUOM = $relatedDetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->id;
                        $baseUOM = $relatedDetail->itemRequestDetail->itemPriceHistory->itemUom->item->unitOfMeasurement->id;

                        if ($requestedUOM != $baseUOM) {
                            $pendingQuantity *= $relatedDetail->itemRequestDetail->itemPriceHistory->itemUom->conversion;
                        }

                        $needToBuyQuantity = $pendingQuantity - $purchaseQuantity;

                        if ($needToBuyQuantity > 0) {
                            $needMorePurchases = true;
                            break;
                        }
                    }

                    // Update the item_need_to_purchase status based on remaining needs
                    $newStatus = $needMorePurchases
                        ? 'Partial Purchasing Order'
                        : 'On Process Purchasing Order';

                    $itemNeedToPurchase->update([
                        'process_status' => $newStatus
                    ]);
                }
                $checkApproval = $this->repository->checkApproval('create', $purchaseOrder->id);
                if ($checkApproval['status'] == 200) {
                    $purchaseOrder->update([
                        'process_status' => 'Waiting Approval Manager'
                    ]);
                }
            }

            // Remove deleted offers
            if (isset($validated['deleted_offers'])) {
                PurchaseOrderSupplierOffer::destroy($validated['deleted_offers']);
            }

            // Process supplier offers (only if not a draft or offers are provided)
            if (isset($validated['offers'])) {
                $subtotalPrice = 0;

                foreach ($validated['offers'] as $offerData) {
                    $offer = PurchaseOrderSupplierOffer::updateOrCreate(
                        ['id' => $offerData['id'] ?? null],
                        [
                            'purchase_order_id' => $purchaseOrder->id,
                            'currency' => $offerData['currency'],
                            'supplier_id' => $offerData['supplier_id'],
                            'shipping_cost' => $offerData['shipping_cost'] ?? 0,
                            'other_cost' => $offerData['other_cost'] ?? 0,
                            'grand_total' => 0, // Will be calculated after adding details
                            'is_selected' => false, // Not selected by default
                            'remarks' => $offerData['remarks'] ?? null,
                        ]
                    );

                    $offerSubtotal = 0;

                    // Remove old details that weren't included in the update
                    $offer->offerDetails()->whereNotIn('purchase_order_detail_id', array_keys($purchaseOrderDetailsMap))->delete();

                    // Add or update offer details for each item
                    foreach ($offerData['items'] as $item) {
                        $offerItemQuantity = $item['quantity'];
                        $offerdItemPrice = $item['price'];

                        // Get the matching PO details for this item+UOM
                        $matchingDetails = collect($purchaseOrderDetailsMap)->filter(function ($detail) use ($item) {
                            return $detail['item_id'] == $item['item_id'] && $detail['uom_id'] == $item['uom_id'];
                        });

                        // Allocate the offer quantity among the matching PO details
                        $remainingQuantity = $offerItemQuantity;
                        foreach ($matchingDetails as $poDetailId => $poDetail) {
                            $poDetailQuantity = $poDetail['quantity'];
                            $totalPOQuantity = $matchingDetails->sum('quantity');

                            // Calculate this detail's proportion of the offer quantity
                            $proportion = $poDetailQuantity / $totalPOQuantity;
                            $offerDetailQuantity = round($offerItemQuantity * $proportion, 3);

                            // For the last detail, use remaining quantity to avoid rounding errors
                            if ($matchingDetails->keys()->last() == $poDetailId) {
                                $offerDetailQuantity = $remainingQuantity;
                            }

                            $remainingQuantity -= $offerDetailQuantity;

                            // Create or update offer detail linking to this PO detail
                            PurchaseOrderSupplierOfferDetail::updateOrCreate(
                                ['offer_id' => $offer->id, 'purchase_order_detail_id' => $poDetailId],
                                [
                                    'quantity' => $offerDetailQuantity,
                                    'offered_price_per_unit' => $offerdItemPrice / $item['quantity'],
                                    // total_price is calculated automatically (GENERATED ALWAYS AS)
                                ]
                            );
                        }

                        $offerSubtotal += $item['price'];
                    }

                    // Update the grand total
                    $grandTotal = $offerSubtotal + $offerData['shipping_cost'] + $offerData['other_cost'];
                    $offer->update(['grand_total' => $grandTotal]);

                    // If this is the first offer, use it as the selected one
                    if (!isset($firstOffer)) {
                        $firstOffer = $offer;
                        $offer->update(['is_selected' => true]);

                        // Update the purchase order with this supplier's prices
                        $subtotalPrice = $offerSubtotal;
                        $purchaseOrder->update([
                            'subtotal_price' => $offerSubtotal,
                            'shipping_cost' => $offerData['shipping_cost'],
                            'other_cost' => $offerData['other_cost'],
                            // total_price is calculated automatically (GENERATED ALWAYS AS)
                        ]);
                    }
                }
            }

            DB::commit();

            $message = $purchaseOrder->process_status === 'Draft'
                ? 'Purchase order updated successfully! / 采购单已成功更新！'
                : 'Purchase order submitted successfully! / 采购单已成功提交！';

            alertNotif('success', $message);
            return redirect()->route('purchase-order.show', $purchaseOrder->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Update Error: ' . $e->getMessage());
            dd($e->getMessage());
            alertNotif('error', 'Error updating purchase order: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $repository->delete();
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
    public function downloadSupplierOffers(PurchaseOrder $purchaseOrder)
    {
        $offers = $purchaseOrder->offers()->with('supplier', 'offerDetails.purchaseOrderDetail.itemRequestDetail.itemPriceHistory.itemUom.item')->get();

        $excelData = [
            ['NO', 'ITEM / 物品', 'UNIT / 单位']
        ];

        $suppliers = $offers->pluck('supplier')->unique();
        foreach ($suppliers as $supplier) {
            $excelData[0][] = $supplier->name . ' / ' . $supplier->name_cn;
        }

        $itemMap = [];

        foreach ($offers as $offer) {
            foreach ($offer->offerDetails as $detail) {
                $item = $detail->purchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item;
                $unit = $detail->purchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->name;
                $key = $item->id . '-' . $unit;

                if (!isset($itemMap[$key])) {
                    $itemMap[$key] = [
                        'item_name' => $item->name,
                        'unit' => $unit,
                        'prices' => [],
                    ];
                }

                $itemMap[$key]['prices'][$offer->supplier->name] = 'Rp ' . number_format($detail->total_price, 0, ',', '.');
            }
        }
        $no = 0;
        foreach ($itemMap as $index => $item) {
            $row = [$no + 1, $item['item_name'], $item['unit']];
            foreach ($suppliers as $supplier) {
                $row[] = $item['prices'][$supplier->name] ?? '-';
            }

            $excelData[] = $row;
        }

        $excelData[] = ['', 'TOTAL / 总计', '', '=SUM(D2:D' . (count($itemMap) + 1) . ')'];
        foreach (range(4, count($suppliers) + 3) as $column) {
            $columnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
            $excelData[count($excelData) - 1][] = '=SUM(' . $columnName . '2:' . $columnName . (count($itemMap) + 1) . ')';
        }

        $excelData[] = ['', 'VAT 11% / 11% 增值税', '', '=D' . (count($itemMap) + 2) . '*0.11'];
        foreach (range(4, count($suppliers) + 3) as $column) {
            $columnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
            $excelData[count($excelData) - 1][] = '=' . $columnName . (count($itemMap) + 2) . '*0.11';
        }

        $excelData[] = ['', 'SHIPPING / 运费', '', '', '', ''];

        $excelData[] = ['', 'GRAND TOTAL / 总金额', '', '=D' . (count($itemMap) + 2) . '+D' . (count($itemMap) + 3) . '+D' . (count($itemMap) + 4)];
        foreach (range(4, count($suppliers) + 3) as $column) {
            $columnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
            $excelData[count($excelData) - 1][] = '=' . $columnName . (count($itemMap) + 2) . '+' . $columnName . (count($itemMap) + 3) . '+' . $columnName . (count($itemMap) + 4);
        }

        $filename = 'supplier_offers_comparison_' . $purchaseOrder->po_number . '.xlsx';

        return Excel::download(new SupplierOffersExport($excelData, $suppliers), $filename);
    }
    public function updateSupplierOfferSelection(Request $request)
    {
        // Validasi input
        $request->validate([
            'offer_id' => 'required|exists:purchase_order_supplier_offers,id',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
        ]);

        try {
            // Mulai transaction untuk memastikan konsistensi data
            DB::beginTransaction();

            // Ambil data yang diperlukan
            $offerId = $request->offer_id;
            $purchaseOrderId = $request->purchase_order_id;

            // Reset semua supplier offers untuk PO ini (is_selected = false)
            $purchaseOrderSupplierOffer = PurchaseOrderSupplierOffer::where('purchase_order_id', $purchaseOrderId)->first();
            $purchaseOrderSupplierOffer->is_selected = false;
            $purchaseOrderSupplierOffer->save();

            // Update supplier offer yang dipilih menjadi is_selected = true
            $purchaseOrderSupplierOffer = PurchaseOrderSupplierOffer::where('id', $offerId)->first();
            $purchaseOrderSupplierOffer->is_selected = true;
            $purchaseOrderSupplierOffer->save();

            // Commit transaction jika semua operasi berhasil
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier offer selection updated successfully',
            ]);
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier offer selection',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
