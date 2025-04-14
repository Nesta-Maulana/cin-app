<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\PrePurchaseOrder;
use App\Models\PurchaseOrderNew;
use App\Models\PurchaseOrderNewStatusHistory;
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
    public function show($id)
    {
        try {
            // Find the purchase order with all relevant relationships
            $purchaseOrder = PurchaseOrderNew::with([
                'supplier',
                'customerOrder.customer',
                'prePurchaseOrder',
                'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                'details.manualItemRequestDetail',
                'details.itemUom.unitOfMeasurement',
                'additionalCosts',
                'attachments',
                'statusHistory.changedBy',
                'createdBy',
                'updatedBy',
                'approvedBy'
            ])->findOrFail($id);

            // Group additional costs by category
            $beforeTaxCosts = $purchaseOrder->additionalCosts->where('category', 'before_tax');
            $afterTaxCosts = $purchaseOrder->additionalCosts->where('category', 'after_tax');

            // Get approval requests if any
            $approvalRequest = null;
            if (method_exists($purchaseOrder, 'approvalRequest')) {
                $approvalRequest = $purchaseOrder->approvalRequest('create');
            }

            return view('transaction.purchase-order-new.show', compact(
                'purchaseOrder',
                'beforeTaxCosts',
                'afterTaxCosts',
                'approvalRequest'
            ));
        } catch (Exception $e) {
            Log::error('Purchase Order Show Error: ' . $e->getMessage());
            alertNotif('error', 'Error loading purchase order: ' . $e->getMessage());
            return redirect()->route('purchase-order-new.index');
        }
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'po_number' => 'required|string|max:100|unique:purchase_order_news,po_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'customer_order_id' => 'required|exists:customer_orders,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'subtotal_price' => 'required|numeric|min:0',
            'tax_type' => 'required|in:percentage,fixed',
            'tax_value' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'pre_purchase_order_id' => 'nullable|exists:pre_purchase_orders,id',
            'details' => 'required|array|min:1',
            'details.*.item_name' => 'required|string',
            'details.*.quantity' => 'required|numeric|min:0.001',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.subtotal' => 'required|numeric|min:0', // Note that this field is required
            'details.*.specification' => 'nullable',
            'details.*.unit' => 'nullable',
            'details.*.original_price' => 'nullable',
            'details.*.discount_percentage' => 'nullable',
            'details.*.discount_amount' => 'nullable',
            'details.*.remarks' => 'nullable',
            'details.*.item_id' => 'nullable',
            'details.*.item_uom_id' => 'nullable',
            'details.*.item_request_detail_id' => 'nullable',
            'details.*.manual_item_request_detail_id' => 'nullable',
            'details.*.quotation_comparison_detail_id' => 'nullable',
            'before_tax_costs' => 'nullable|array',
            'after_tax_costs' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Determine the status based on action value (draft or submit)
            $status = $request->input('action') === 'draft' ? 'Draft' : 'Waiting Approval Manager';

            // Create the purchase order
            $purchaseOrder = PurchaseOrderNew::create([
                'po_number' => $validated['po_number'],
                'supplier_id' => $validated['supplier_id'],
                'customer_order_id' => $validated['customer_order_id'],
                'pre_purchase_order_id' => $validated['pre_purchase_order_id'] ?? null,
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'currency' => $validated['currency'],
                'subtotal_price' => $validated['subtotal_price'],
                'tax_type' => $validated['tax_type'],
                'tax_value' => $validated['tax_value'],
                'tax_amount' => $validated['tax_amount'],
                'total_amount' => $validated['total_amount'],
                'payment_terms' => $validated['payment_terms'] ?? null,
                'process_status' => $status,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Add purchase order status history
            PurchaseOrderNewStatusHistory::create([
                'purchase_order_id' => $purchaseOrder->id,
                'from_status' => 'New',
                'to_status' => $status,
                'remarks' => 'Purchase order created',
                'changed_by' => auth()->id(),
            ]);

            // Process purchase order details
            foreach ($validated['details'] as $detail) {
                $purchaseOrder->details()->create([
                    'item_name' => $detail['item_name'],
                    'specification' => $detail['specification'] ?? null,
                    'unit' => $detail['unit'] ?? null,
                    'quantity' => $detail['quantity'],
                    'original_price' => $detail['original_price'] ?? $detail['price'],
                    'price' => $detail['price'],
                    'discount_percentage' => $detail['discount_percentage'] ?? 0,
                    'discount_amount' => $detail['discount_amount'] ?? 0,
                    'subtotal' => $detail['subtotal'],
                    'remarks' => $detail['remarks'] ?? null,
                    'item_id' => $detail['item_id'] ?? null,
                    'item_uom_id' => $detail['item_uom_id'] ?? null,
                    'item_request_detail_id' => $detail['item_request_detail_id'] ?? null,
                    'manual_item_request_detail_id' => $detail['manual_item_request_detail_id'] ?? null,
                    'quotation_comparison_detail_id' => $detail['quotation_comparison_detail_id'] ?? null,
                ]);
            }

            // Process before tax additional costs
            if (isset($validated['before_tax_costs'])) {
                foreach ($validated['before_tax_costs'] as $cost) {
                    $purchaseOrder->additionalCosts()->create([
                        'description' => $cost['description'],
                        'amount' => $cost['amount'],
                        'type' => $cost['type'] ?? 'other',
                        'category' => 'before_tax',
                    ]);
                }
            }

            // Process after tax additional costs
            if (isset($validated['after_tax_costs'])) {
                foreach ($validated['after_tax_costs'] as $cost) {
                    $purchaseOrder->additionalCosts()->create([
                        'description' => $cost['description'],
                        'amount' => $cost['amount'],
                        'type' => $cost['type'] ?? 'other',
                        'category' => 'after_tax',
                    ]);
                }
            }

            // Process file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('purchase_orders/' . $purchaseOrder->po_number, 'public');

                    $purchaseOrder->attachments()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_at' => now(),
                        'is_active' => true,
                    ]);
                }
            }

            // If not a draft, check for approval process
            if ($status !== 'Draft') {
                // Check if an approval process is configured for purchase orders
                $checkApproval = $this->repository->checkApproval('create', $purchaseOrder->id);

                // If approval check returns success status, update the PO status
                if (isset($checkApproval['status']) && $checkApproval['status'] == 200) {
                    $purchaseOrder->update([
                        'process_status' => 'Waiting Approval'
                    ]);
                }
            }

            DB::commit();

            // Set appropriate success message based on status
            $message = $status === 'Draft'
                ? 'Purchase order has been saved as draft. / 采购单已保存为草稿。'
                : 'Purchase order has been submitted for approval. / 采购单已提交审批。';

            alertNotif('success', $message);
            return redirect()->route('purchase-order-new.show', $purchaseOrder->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Creation Error: ' . $e->getMessage());
            alertNotif('error', 'Error creating purchase order: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function exportPdf($id)
    {
        try {
            // Find the purchase order with all relevant relationships
            $purchaseOrder = PurchaseOrderNew::with([
                'supplier',
                'customerOrder.customer',
                'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                'details.manualItemRequestDetail',
                'details.itemUom.unitOfMeasurement',
                'additionalCosts',
                'attachments',
            ])->findOrFail($id);

            // Group additional costs by category
            $beforeTaxCosts = $purchaseOrder->additionalCosts->where('category', 'before_tax');
            $afterTaxCosts = $purchaseOrder->additionalCosts->where('category', 'after_tax');

            // Calculate sub-total, tax, and total
            $subtotal = $purchaseOrder->subtotal_price;
            $tax = $purchaseOrder->tax_amount;
            $totalBeforeTax = $subtotal;

            // Add before tax costs
            foreach ($beforeTaxCosts as $cost) {
                $totalBeforeTax += $cost->amount;
            }

            // Calculate total
            $total = $totalBeforeTax + $tax;

            // Add after tax costs
            foreach ($afterTaxCosts as $cost) {
                $total += $cost->amount;
            }

            // Format the PDF data
            $pdfData = [
                'po_number' => $purchaseOrder->po_number,
                'order_date' => $purchaseOrder->order_date->format('Y-m-d'),
                'supplier' => [
                    'name' => $purchaseOrder->supplier->name,
                    'address' => $purchaseOrder->supplier->address,
                ],
                'delivery_address' => $purchaseOrder->customerOrder->delivery_address ?? 'PT Cakrawala Inti Nusantara',
                'project_name' => $purchaseOrder->customerOrder->project_name,
                'expected_delivery' => $purchaseOrder->expected_delivery_date->format('Y-m-d'),
                'co_number' => $purchaseOrder->customerOrder->order_number,
                'currency' => $purchaseOrder->currency,
                'payment_terms' => $purchaseOrder->payment_terms,
                'items' => $purchaseOrder->details->map(function ($detail) use ($purchaseOrder) {
                    return [
                        'item_number' => $detail->id,
                        'description' => $detail->item_name . ($detail->specification ? ' - ' . $detail->specification : ''),
                        'quantity' => $detail->quantity,
                        'unit' => $detail->unit,
                        'price' => $detail->price,
                        'total' => $detail->subtotal,
                    ];
                }),
                'subtotal' => $subtotal,
                'discount' => $purchaseOrder->details->sum('discount_amount'),
                'tax' => [
                    'type' => $purchaseOrder->tax_type,
                    'value' => $purchaseOrder->tax_value,
                    'amount' => $purchaseOrder->tax_amount,
                ],
                'shipping' => $purchaseOrder->additionalCosts->where('type', 'shipping')->sum('amount'),
                'total' => $purchaseOrder->total_amount,
                'notes' => $purchaseOrder->remarks,
                'created_by' => $purchaseOrder->createdBy->name ?? '',
                'approved_by' => $purchaseOrder->approvedBy->name ?? '',
            ];

            // Generate PDF using a PDF library (TCPDF example shown)
            $pdf = new \TCPDF('P', 'mm', 'F4', true, 'UTF-8');

            // Set document information
            $pdf->SetCreator('PT Cakrawala Inti Nusantara');
            $pdf->SetAuthor('PT Cakrawala Inti Nusantara');
            $pdf->SetTitle('Purchase Order - ' . $purchaseOrder->po_number);
            $pdf->SetSubject('Purchase Order');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(10, 10, 10);

            // Add a page
            $pdf->AddPage();

            // Logo and company information
            $pdf->Image(public_path('images/logo.png'), 10, 10, 40);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'PT Cakrawala Inti Nusantara', 0, 1, 'R');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 5, 'Villa Mutiara Cikarang Blok RA-34', 0, 1, 'R');
            $pdf->Cell(0, 5, 'Ciantra, Kec. Cikarang Selatan, Kab. Bekasi', 0, 1, 'R');

            // Purchase Order title
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Pesanan Pembelian', 0, 1, 'C');

            // PO Number and Date
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Ln(5);
            $pdf->Cell(90, 8, 'No. Pesanan: ' . $pdfData['po_number'], 1);
            $pdf->Cell(90, 8, 'Tgl. Pesanan: ' . $pdfData['order_date'], 1, 1);

            // Supplier and Delivery Info
            $pdf->Ln(5);
            $pdf->Cell(90, 8, 'Pemasok: ' . $pdfData['supplier']['name'], 1, 0);
            $pdf->Cell(90, 8, 'Maksimal Dikirim: ' . $pdfData['expected_delivery'], 1, 1);

            $pdf->Cell(90, 8, 'Mata Uang: ' . $pdfData['currency'], 1, 0);
            $pdf->Cell(90, 8, 'Nama Proyek: ' . $pdfData['project_name'], 1, 1);

            // Supplier Address and Delivery Address
            $address = $pdfData['supplier']['address'];
            $address = str_replace("\n", ' ', $address);
            $address = wordwrap($address, 60, "\n");
            // $pdf->MultiCell(0, 8, 'Alamat Pemasok: ' . $address, 1, 'L');
            $pdf->Cell(90, 8, 'Alamat Pemasok: ' . $address,1,0,'L',0,'',1);
            $pdf->Cell(90, 8, 'Kirim Ke: ' . $pdfData['delivery_address'], 1, 1,'L',0,'',1);
            // $pdf->Cell(0, 0, 'TEST CELL STRETCH: scaling', 1, 1, 'C', 0, '', 1);

            // Additional info
            $pdf->Cell(90, 8, 'Pengiriman: ', 1, 0);
            $pdf->Cell(90, 8, 'CO Number: ' . $pdfData['co_number'], 1, 1);

            // Items Table Header
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(10, 8, 'No.', 1, 0, 'C');
            $pdf->Cell(30, 8, 'No. Barang', 1, 0, 'C');
            $pdf->Cell(55, 8, 'Deskripsi Barang', 1, 0, 'C');
            $pdf->Cell(15, 8, 'Qty', 1, 0, 'C');
            $pdf->Cell(20, 8, 'Satuan', 1, 0, 'C');
            $pdf->Cell(30, 8, 'Harga Satuan', 1, 0, 'C');
            $pdf->Cell(35, 8, 'Jumlah', 1, 1, 'C');

            // Items Table Content
            $pdf->SetFont('helvetica', '', 9);
            $i = 1;
            foreach ($pdfData['items'] as $item) {
                $pdf->Cell(10, 8, $i, 1, 0, 'C');
                $pdf->Cell(30, 8, $item['item_number'], 1);
                $pdf->Cell(55, 8, $item['description'], 1);
                $pdf->Cell(15, 8, $item['quantity'], 1, 0, 'R');
                $pdf->Cell(20, 8, $item['unit'], 1, 0, 'C');
                $pdf->Cell(30, 8, number_format($item['price'], 2), 1, 0, 'C');
                $pdf->Cell(35, 8, number_format($item['total'], 2), 1, 1, 'C');
                $i++;
            }

            // Add empty rows if needed
            $remainingRows = 10 - count($pdfData['items']);
            for ($j = 0; $j < $remainingRows; $j++) {
                $pdf->Cell(10, 8, '', 1, 0, 'C');
                $pdf->Cell(30, 8, '', 1);
                $pdf->Cell(55, 8, '', 1);
                $pdf->Cell(15, 8, '', 1);
                $pdf->Cell(20, 8, '', 1);
                $pdf->Cell(30, 8, '', 1);
                $pdf->Cell(35, 8, '', 1, 1);
            }

            // Payment Info
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(130, 8, 'Tata Cara Pembayaran: ' . $pdfData['payment_terms'], 1);

            // Subtotal and other costs
            $pdf->Cell(30, 8, 'Sub Total', 1);
            $pdf->Cell(35, 8, number_format($pdfData['subtotal'], 2), 1, 1, 'R');

            $pdf->Cell(130, 8, '', 0);
            $pdf->Cell(30, 8, 'Diskon', 1);
            $pdf->Cell(35, 8, number_format($pdfData['discount'], 2), 1, 1, 'R');

            $pdf->Cell(130, 8, '', 0);
            $pdf->Cell(30, 8, 'PPN 11%', 1);
            $pdf->Cell(35, 8, number_format($pdfData['tax']['amount'], 2), 1, 1, 'R');

            $pdf->Cell(130, 8, '', 0);
            $pdf->Cell(30, 8, 'Transportation', 1);
            $pdf->Cell(35, 8, number_format($pdfData['shipping'], 2), 1, 1, 'R');

            $pdf->Cell(130, 8, '', 0);
            $pdf->Cell(30, 8, 'Total', 1);
            $pdf->Cell(35, 8, number_format($pdfData['total'], 2), 1, 1, 'R');

            // Notes
            $pdf->Ln(5);
            $pdf->Cell(30, 8, 'Special Note:', 0);
            $pdf->Ln(8);
            $pdf->MultiCell(0, 8, $pdfData['notes'], 0);

            // Signatures
            $pdf->Ln(15);
            $pdf->Cell(63, 8, 'Dibuat Oleh', 0, 0, 'C');
            $pdf->Cell(63, 8, 'Disetujui Oleh', 0, 0, 'C');
            $pdf->Cell(63, 8, 'Diterima', 0, 1, 'C');

            $pdf->Ln(25);
            $pdf->Cell(63, 0, '', 'T', 0, 'C');
            $pdf->Cell(63, 0, '', 'T', 0, 'C');
            $pdf->Cell(63, 0, '', 'T', 1, 'C');

            // Output the PDF
            return $pdf->Output('purchase_order_' . $purchaseOrder->po_number . '.pdf', 'I');

        } catch (Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            alertNotif('error', 'Error exporting PDF: ' . $e->getMessage());
            return redirect()->back();
        }
    }


    public function submit(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the purchase order
            $purchaseOrder = PurchaseOrderNew::findOrFail($id);

            // Check if the purchase order is in Draft status
            if ($purchaseOrder->process_status !== 'Draft') {
                return redirect()->back()->with('error', 'Only purchase orders in Draft status can be submitted for approval. / 只有处于草稿状态的采购单才能提交审批。');
            }

            // Update the status to "Waiting Approval"
            $checkApproval = $this->repository->checkApproval('create', $purchaseOrder->id);

            // If approval check returns success status, update the PO status
            if (isset($checkApproval['status']) && $checkApproval['status'] == 200) {
                $purchaseOrder->update([
                    'process_status' => 'Waiting Approval Manager',
                    'updated_by' => auth()->id(),
                ]);
            }

            // Add status history record
            PurchaseOrderNewStatusHistory::create([
                'purchase_order_id' => $purchaseOrder->id,
                'from_status' => 'Draft',
                'to_status' => 'Waiting Approval Manager',
                'remarks' => 'Submitted for approval',
                'changed_by' => auth()->id(),
            ]);

            // Check for approval process
            $checkApproval = $this->repository->checkApproval('create', $purchaseOrder->id);

            DB::commit();

            return redirect()->route('purchase-order-new.show', $purchaseOrder->id)
                ->with('success', 'Purchase order has been submitted for approval. / 采购单已提交审批。');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase Order Submit Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error submitting purchase order: ' . $e->getMessage());
        }
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
