<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\PrePurchaseOrder;
use App\Models\PurchaseOrderNew;
use App\Models\QuotationComparison;
use App\Models\QuotationComparisonDetail;
use App\Models\Supplier;
use App\Repositories\Transaction\PurchaseOrderNew\PurchaseOrderNewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseOrderNewController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(PurchaseOrderNewRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'transaction.purchase-order-new';
        $this->route = 'purchase-order-new';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }

    /* public function create()
    {
        return view("{$this->view}.create");
    } */

    public function create(Request $request)
    {
        try {
            // Get pre_purchase_order_id and supplier_id from request if available
            $prePurchaseOrderId = $request->input('pre_purchase_order_id');
            $supplierId = $request->input('supplier_id');

            $prePurchaseOrder = null;
            $selectedQuotation = null;
            $supplier = null;
            $customerOrder = null;
            $quotationDetails = collect([]);

            // Load pre-purchase order if specified
            if ($prePurchaseOrderId) {
                $prePurchaseOrder = PrePurchaseOrder::with([
                    'customerOrder',
                    'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                    'details.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
                    'details.manualItemRequestDetail',
                    'details.uom.unitOfMeasurement',
                    'quotations.supplier',
                    'itemSelections'
                ])->findOrFail($prePurchaseOrderId);

                $customerOrder = $prePurchaseOrder->customerOrder;

                // Handle supplier selection
                if ($supplierId) {
                    $supplier = Supplier::findOrFail($supplierId);

                    // Get quotation for this supplier
                    if ($prePurchaseOrder->itemSelections()->count() > 0) {
                        // If we have item selections, get items selected for this supplier
                        $selectedQuotation = QuotationComparison::where('pre_purchase_order_id', $prePurchaseOrderId)
                            ->where('supplier_id', $supplierId)
                            ->first();

                        if ($selectedQuotation) {
                            // Get details that were selected for this supplier
                            $selectedItemKeys = $prePurchaseOrder->itemSelections()
                                ->where('quotation_id', $selectedQuotation->id)
                                ->pluck('item_key');

                            // Group items by name and UOM
                            $groupedItems = [];

                            // Process each item key
                            foreach ($selectedItemKeys as $itemKey) {
                                // Split the key to get name and UOM
                                list($itemName, $uomName) = explode('|', $itemKey);

                                // Find all details for this item name and UOM
                                // Find all details for this item name and UOM
                                $details = collect();

                                // Get all quotation details for this supplier
                                $allQuotationDetails = QuotationComparisonDetail::where('quotation_comparison_id', $selectedQuotation->id)->get();

                                // Filter them manually with if statements
                                foreach ($allQuotationDetails as $detail) {
                                    $prePODetail = $detail->prePurchaseOrderDetail;

                                    if (!$prePODetail) {
                                        continue; // Skip if pre-purchase order detail is missing
                                    }

                                    // CASE 1: Check if this is a system item request
                                    if (!is_null($prePODetail->item_uom_id) && $prePODetail->itemRequestDetail) {
                                        $itemRelation = $prePODetail->itemRequestDetail->itemPriceHistory->itemUom->item ?? null;
                                        $uomRelation = $prePODetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement ?? null;

                                        if (
                                            $itemRelation && $uomRelation &&
                                            $itemRelation->name === $itemName &&
                                            $uomRelation->name === $uomName
                                        ) {
                                            $details->push($detail);
                                            continue;
                                        }
                                    }

                                    // CASE 2: Check if this is a manual item request
                                    if (is_null($prePODetail->item_uom_id) && $prePODetail->manualItemRequestDetail) {
                                        if (
                                            $prePODetail->manualItemRequestDetail->item_name === $itemName &&
                                            $prePODetail->manualItemRequestDetail->unit === $uomName
                                        ) {
                                            $details->push($detail);
                                        }
                                    }
                                }

                                // If no details found, continue to next item
                                if ($details->isEmpty()) {
                                    continue;
                                }

                                // Calculate total quantity and store reference to first detail
                                $totalQuantity = $details->sum('quantity');
                                $firstDetail = $details->first();

                                // Add to grouped items array with combined quantity
                                if (!isset($groupedItems[$itemKey])) {
                                    // Clone the first detail to avoid modifying the original
                                    $combinedDetail = clone $firstDetail;
                                    $combinedDetail->quantity = $totalQuantity;
                                    $combinedDetail->subtotal_price = $combinedDetail->offered_price_per_unit * $totalQuantity;
                                    $combinedDetail->grand_total = $combinedDetail->subtotal_price + $combinedDetail->shipping_cost;
                                    $combinedDetail->detail_ids = $details->pluck('pre_purchase_order_detail_id')->toArray();

                                    $groupedItems[$itemKey] = $combinedDetail;
                                }
                            }

                            // Convert grouped items to a collection
                            $quotationDetails = collect(array_values($groupedItems));
                        }
                    } else {
                        // Get globally selected quotation
                        $selectedQuotation = $prePurchaseOrder->selectedQuotation();

                        if ($selectedQuotation && $selectedQuotation->supplier_id == $supplierId) {
                            // Group items by name and UOM
                            $groupedItems = [];

                            foreach ($selectedQuotation->quotationDetails as $detail) {
                                $itemName = null;
                                $uomName = null;

                                if ($detail->prePurchaseOrderDetail->itemRequestDetail) {
                                    // For system item requests
                                    $itemName = $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name;
                                    $uomName = $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->name;
                                } elseif ($detail->prePurchaseOrderDetail->manualItemRequestDetail) {
                                    // For manual item requests
                                    $itemName = $detail->prePurchaseOrderDetail->manualItemRequestDetail->item_name;
                                    $uomName = $detail->prePurchaseOrderDetail->manualItemRequestDetail->unit;
                                } else {
                                    continue; // Skip if we can't determine name/UOM
                                }

                                $itemKey = "{$itemName}|{$uomName}";

                                if (!isset($groupedItems[$itemKey])) {
                                    // Clone the detail to avoid modifying the original
                                    $combinedDetail = clone $detail;
                                    $combinedDetail->quantity = $detail->quantity;
                                    $combinedDetail->detail_ids = [$detail->pre_purchase_order_detail_id];
                                    $groupedItems[$itemKey] = $combinedDetail;
                                } else {
                                    // Add to existing group
                                    $groupedItems[$itemKey]->quantity += $detail->quantity;
                                    $groupedItems[$itemKey]->subtotal_price += $detail->subtotal_price;
                                    $groupedItems[$itemKey]->grand_total += $detail->grand_total;
                                    $groupedItems[$itemKey]->detail_ids[] = $detail->pre_purchase_order_detail_id;
                                }
                            }

                            // Convert grouped items to a collection
                            $quotationDetails = collect(array_values($groupedItems));
                        }
                    }
                }
            }

            // Get all suppliers for dropdown
            $suppliers = Supplier::where('is_active', true)->get();

            // Get all customer orders for dropdown
            $customerOrders = CustomerOrder::where('is_active', true)->get();

            // Generate a unique PO number
            $poNumber = $this->generatePONumber();

            return view('transaction.purchase-order-new.create', compact(
                'prePurchaseOrder',
                'supplier',
                'supplierId',
                'customerOrder',
                'suppliers',
                'customerOrders',
                'selectedQuotation',
                'quotationDetails',
                'poNumber'
            ));

        } catch (Exception $e) {
            dd($e->getMessage());
            Log::error('Purchase Order Create Error: ' . $e->getMessage());
            alertNotif('error', 'Error loading purchase order create page: ' . $e->getMessage());

            return redirect()->route('purchase-order-new.index');
        }
    }
    private function generatePONumber()
    {
        // Get current year and month
        $year = date('y'); // Two-digit year
        $month = date('m'); // Two-digit month
        $prefix = "PO{$year}{$month}";

        // Count existing purchase orders for the current month
        $count = PurchaseOrderNew::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        // Increment and format the count as 3 digits (e.g., 001, 002)
        $increment = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        // Return formatted PO number
        return "{$prefix}{$increment}";
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            //
        ]);
        try {
            DB::transaction(function () use ($request, $data) {
                $this->repository->create($data);
            });
            alertNotif('save');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function edit($id)
    {
        try {
            $data = $this->repository->find($id);
            return view("{$this->view}.edit", compact('data'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            //
        ]);
        $data['updated_by'] = auth()->user()->id;
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $this->repository->update($id, $data);
            });
            alertNotif('update');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
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
}
