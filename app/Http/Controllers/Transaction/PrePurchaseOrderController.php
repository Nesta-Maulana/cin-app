<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\PrePurchaseOrder;
use App\Models\PrePurchaseOrderDetail;
use App\Models\PrePurchaseOrderItemSelection;
use App\Models\QuotationComparison;
use App\Models\QuotationComparisonAdditionalCost;
use App\Models\QuotationComparisonDetail;
use App\Models\Supplier;
use App\Models\ItemRequest;
use App\Models\ManualItemRequest;
use App\Repositories\Transaction\PrePurchaseOrder\PrePurchaseOrderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Validator;

class PrePurchaseOrderController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(PrePurchaseOrderRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'transaction.pre-purchase-order';
        $this->route = 'pre-purchase-order';

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
        return view("{$this->view}.create");
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
            // Load the pre-purchase order with all necessary relationships
            $data = PrePurchaseOrder::with([
                'details.itemRequestDetail.itemRequest',
                'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                'details.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
                'details.manualItemRequestDetail.manualItemRequest',
                'details.uom.unitOfMeasurement',
                'quotations.supplier',
                'quotations.quotationDetails.prePurchaseOrderDetail.itemRequestDetail.itemPriceHistory.itemUom.item',
                'quotations.quotationDetails.prePurchaseOrderDetail.manualItemRequestDetail',
                'quotations.quotationDetails.prePurchaseOrderDetail.uom.unitOfMeasurement',
                'customerOrder'
            ])->findOrFail($id);

            $customerOrders = CustomerOrder::all();
            $suppliers = Supplier::all();

            return view('transaction.pre-purchase-order.edit', compact('data', 'customerOrders', 'suppliers'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
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

    public function show($id)
    {
        // Load the pre-purchase order with all necessary relationships
        $data = PrePurchaseOrder::with([
            'details.itemRequestDetail.itemRequest',
            'details.itemRequestDetail.itemPriceHistory.itemUom.item',
            'details.manualItemRequestDetail.manualItemRequest',
            'details.uom.unitOfMeasurement',
            'customerOrder',
            'quotations.supplier',
            'quotations.quotationDetails.prePurchaseOrderDetail.itemRequestDetail.itemPriceHistory.itemUom.item',
            'quotations.quotationDetails.prePurchaseOrderDetail.manualItemRequestDetail',
            'quotations.quotationDetails.prePurchaseOrderDetail.uom.unitOfMeasurement',
            'quotations.beforeTaxCosts',
            'quotations.afterTaxCosts'
        ])->findOrFail($id);

        return view($this->view . '.show', compact('data'));
    }

    public function update(Request $request, $prePurchaseOrderId)
    {
        // Validasi request
        $validationResult = $this->validateQuotationRequest($request);
        if ($validationResult !== true) {
            return $validationResult;
        }

        try {
            DB::beginTransaction();

            // Dapatkan pre-purchase order
            $prePurchaseOrder = PrePurchaseOrder::findOrFail($prePurchaseOrderId);

            // Update pre-purchase order basic info
            if ($request->has('customer_order_id')) {
                $prePurchaseOrder->update([
                    'customer_order_id' => $request->customer_order_id,
                    'remarks' => $request->remarks
                ]);
            }

            // Hapus quotation yang di-request untuk dihapus
            if ($request->has('deleted_quotations')) {
                QuotationComparison::whereIn('id', $request->deleted_quotations)->delete();
            }

            // Loop untuk setiap quotation
            if ($request->has('quotations')) {
                foreach ($request->quotations as $quotationIndex => $quotationData) {
                    $this->processQuotation($prePurchaseOrder, $quotationData);
                }
            }

            // Jika ada supplier yang dipilih
            if ($request->has('selected_quotation')) {
                $selectedIndex = $request->selected_quotation;
                $selectedQuotationId = $request->quotations[$selectedIndex]['id'] ?? null;

                if ($selectedQuotationId) {
                    // Reset semua is_selected menjadi false
                    QuotationComparison::where('pre_purchase_order_id', $prePurchaseOrderId)
                        ->update(['is_selected' => false]);

                    // Set selected quotation menjadi true
                    QuotationComparison::where('id', $selectedQuotationId)
                        ->update(['is_selected' => true]);

                    // Update status pre-purchase order jika diperlukan
                    $prePurchaseOrder->update(['process_status' => 'approved']);
                }
            }

            // Update process status if submit_type is present
            if ($request->has('submit_type')) {
                if ($request->submit_type === 'submit') {
                    $prePurchaseOrder->update(['process_status' => 'under_review']);
                } else if ($request->submit_type === 'draft') {
                    $prePurchaseOrder->update(['process_status' => 'pending']);
                }
            }

            DB::commit();

            return redirect()
                ->route('pre-purchase-order.show', $prePurchaseOrderId)
                ->with('success', 'Quotations updated successfully');

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Pre Purchase Order Update Error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update quotations: ' . $e->getMessage());
        }
    }

    /**
     * Validate quotation request
     *
     * @param Request $request
     * @return true|\Illuminate\Http\RedirectResponse
     */
    private function validateQuotationRequest(Request $request)
    {
        // Validasi dasar
        $validator = Validator::make($request->all(), [
            'quotations' => 'sometimes|array',
            'quotations.*.supplier_id' => 'required_with:quotations|exists:suppliers,id',
            'quotations.*.currency' => 'required_with:quotations|string|max:10',
            'quotations.*.details' => 'required_with:quotations|array',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validasi detail untuk setiap quotation
        if ($request->has('quotations')) {
            foreach ($request->quotations as $quotationIndex => $quotation) {
                if (isset($quotation['details'])) {
                    foreach ($quotation['details'] as $detailIndex => $detail) {
                        $hasPrePODetailId = isset($detail['pre_purchase_order_detail_id']) && !empty($detail['pre_purchase_order_detail_id']);
                        $hasDetailIds = isset($detail['detail_ids']) && is_array($detail['detail_ids']) && !empty($detail['detail_ids']);

                        if (!$hasPrePODetailId && !$hasDetailIds) {
                            return redirect()
                                ->back()
                                ->withErrors([
                                    "quotations.{$quotationIndex}.details.{$detailIndex}" => 'Each detail must have either pre_purchase_order_detail_id or detail_ids.'
                                ])
                                ->withInput();
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Process a quotation data
     */
    private function processQuotation($prePurchaseOrder, $quotationData)
    {
        // Cek apakah ini update atau create
        $quotationId = $quotationData['id'] ?? null;

        // Data untuk quotation
        $quotationModelData = [
            'pre_purchase_order_id' => $prePurchaseOrder->id,
            'supplier_id' => $quotationData['supplier_id'],
            'currency' => $quotationData['currency'],
            'subtotal_before_tax' => $quotationData['subtotal_before_tax'] ?? 0,
            'tax_type' => $quotationData['tax_type'] ?? 'percentage',
            'tax_value' => $quotationData['tax_value'] ?? 0,
            'tax_amount' => $quotationData['tax_amount'] ?? 0,
            'total_amount' => $quotationData['total_amount'] ?? 0,
            'remarks' => $quotationData['remarks'] ?? null,
        ];

        // Update atau create quotation
        if ($quotationId) {
            $quotation = QuotationComparison::find($quotationId);
            if ($quotation) {
                $quotation->update($quotationModelData);
            } else {
                $quotation = QuotationComparison::create($quotationModelData);
                $quotationId = $quotation->id;
            }
        } else {
            $quotation = QuotationComparison::create($quotationModelData);
            $quotationId = $quotation->id;
        }

        // Hapus detail dan additional costs yang ada jika ini update
        if (isset($quotationData['id'])) {
            QuotationComparisonDetail::where('quotation_comparison_id', $quotationId)->delete();
            QuotationComparisonAdditionalCost::where('quotation_comparison_id', $quotationId)->delete();
        }

        // Simpan detail item
        if (isset($quotationData['details'])) {
            $this->saveQuotationDetails($quotationId, $quotationData['details']);
        }

        // Simpan biaya tambahan sebelum pajak
        if (isset($quotationData['before_tax_costs'])) {
            $this->saveAdditionalCosts($quotationId, $quotationData['before_tax_costs'], 'before_tax');
        }

        // Simpan biaya tambahan setelah pajak
        if (isset($quotationData['after_tax_costs'])) {
            $this->saveAdditionalCosts($quotationId, $quotationData['after_tax_costs'], 'after_tax');
        }

        return $quotation;
    }

    /**
     * Save quotation details
     */
    /**
     * Save quotation details
     */
    private function saveQuotationDetails($quotationId, $details)
    {
        foreach ($details as $detail) {
            // Untuk detail dengan multiple IDs (item yang digabung)
            if (isset($detail['detail_ids']) && is_array($detail['detail_ids'])) {
                // Ambil total quantity dari UI
                $totalQuantity = $detail['quantity'];

                // Dapatkan informasi untuk setiap detail ID
                foreach ($detail['detail_ids'] as $detailId) {
                    // Dapatkan detail asli untuk mendapatkan quantity original
                    $originalDetail = PrePurchaseOrderDetail::find($detailId);

                    if ($originalDetail) {
                        $originalQuantity = $originalDetail->quantity;

                        // Hitung proporsi untuk item ini dari total
                        $proportion = ($totalQuantity > 0) ? ($originalQuantity / $totalQuantity) : 0;

                        // Tentukan item_uom_id berdasarkan jenis item request
                        $itemUomId = null;
                        if ($originalDetail->itemRequestDetail) {
                            // System item request
                            $itemUomId = $originalDetail->itemRequestDetail->itemPriceHistory->itemUom->id;
                        }

                        // Buat record untuk setiap detail ID
                        QuotationComparisonDetail::create([
                            'quotation_comparison_id' => $quotationId,
                            'pre_purchase_order_detail_id' => $detailId,
                            'item_uom_id' => $itemUomId,
                            'quantity' => $originalQuantity, // Gunakan quantity asli
                            'offered_price_per_unit' => $detail['price'] ?? 0, // Harga per unit sama
                            'subtotal_price' => ($detail['price'] ?? 0) * $originalQuantity, // Subtotal berdasarkan quantity asli
                            'shipping_cost' => isset($detail['shipping_cost']) ? $detail['shipping_cost'] * $proportion : 0, // Distribusi proporsional
                            'grand_total' => (($detail['price'] ?? 0) * $originalQuantity) +
                                (isset($detail['shipping_cost']) ? $detail['shipping_cost'] * $proportion : 0),
                            'new_unit_price' => $detail['new_unit_price'] ?? 0,
                            'remarks' => $detail['remarks'] ?? null
                        ]);
                    }
                }
            } else {
                // Untuk detail normal (tanpa penggabungan)
                $detailData = [
                    'quotation_comparison_id' => $quotationId,
                    'pre_purchase_order_detail_id' => $detail['pre_purchase_order_detail_id'],
                    'quantity' => $detail['quantity'],
                    'offered_price_per_unit' => $detail['price'] ?? 0,
                    'subtotal_price' => $detail['subtotal_price'] ?? 0,
                    'shipping_cost' => $detail['shipping_cost'] ?? 0,
                    'grand_total' => $detail['grand_total'] ?? 0,
                    'new_unit_price' => $detail['new_unit_price'] ?? 0,
                    'remarks' => $detail['remarks'] ?? null
                ];

                // Get prepo detail to check what type it is
                $prePurchaseOrderDetail = PrePurchaseOrderDetail::find($detail['pre_purchase_order_detail_id']);

                // Add item_uom_id if available from different sources
                if (isset($detail['uom_id']) && !empty($detail['uom_id'])) {
                    $detailData['item_uom_id'] = $detail['uom_id'];
                } elseif (isset($detail['item_uom_id']) && !empty($detail['item_uom_id'])) {
                    $detailData['item_uom_id'] = $detail['item_uom_id'];
                } elseif ($prePurchaseOrderDetail && $prePurchaseOrderDetail->itemRequestDetail) {
                    // For system item requests
                    $detailData['item_uom_id'] = $prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->id;
                } elseif ($prePurchaseOrderDetail && $prePurchaseOrderDetail->uom_id) {
                    // Use item's UOM if available
                    $detailData['item_uom_id'] = $prePurchaseOrderDetail->uom_id;
                }
                // For manual item requests without UOM, item_uom_id will be null (requires the migration above)

                QuotationComparisonDetail::create($detailData);
            }
        }
    }

    /**
     * Save additional costs
     */
    private function saveAdditionalCosts($quotationId, $costs, $category)
    {
        foreach ($costs as $cost) {
            QuotationComparisonAdditionalCost::create([
                'quotation_comparison_id' => $quotationId,
                'description' => $cost['description'] ?? '',
                'amount' => $cost['amount'] ?? 0,
                'type' => $cost['type'] ?? 'other',
                'category' => $category
            ]);
        }
    }

    /**
     * Get suppliers as JSON for AJAX requests
     */
    public function getSuppliers()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return response()->json(['data' => $suppliers]);
    }

    public function selectItems(Request $request, $id)
    {
        // dd($request->all());
        // Validate the incoming request
        // 验证传入的请求
        $validatedData = $request->validate([
            'selected_supplier' => 'required|array',
            'selected_supplier.*' => 'required|exists:quotation_comparisons,id',
        ], [
            'selected_supplier.required' => 'Please select suppliers for items / 请为物品选择供应商',
            'selected_supplier.*.required' => 'Each item must have a selected supplier / 每个物品必须有一个所选供应商',
            'selected_supplier.*.exists' => 'Selected supplier quotation does not exist / 所选供应商报价不存在',
        ]);

        try {
            DB::beginTransaction();

            // Get the pre-purchase order
            // 获取预采购单
            $prePurchaseOrder = PrePurchaseOrder::findOrFail($id);

            // Check if pre-purchase order is in a valid state for editing
            // 检查预采购单是否处于可编辑的状态
            if ($prePurchaseOrder->process_status === 'approved') {
                return redirect()->back()->with('error', 'This pre-purchase order is already approved and cannot be modified / 此预采购单已被批准，无法修改');
            }

            // Update or create item selections
            // 更新或创建物品选择
            foreach ($validatedData['selected_supplier'] as $itemKey => $quotationId) {
                PrePurchaseOrderItemSelection::updateOrCreate(
                    [
                        'pre_purchase_order_id' => $id,
                        'item_key' => $itemKey,
                    ],
                    [
                        'quotation_id' => $quotationId,
                    ]
                );
            }

            // Update the pre-purchase order status to "Waiting Approval Manager" if it was "pending"
            // 如果预采购单状态为"pending"，则将其更新为"Waiting Approval Manager"
            if ($prePurchaseOrder->process_status == 'pending' || $prePurchaseOrder->process_status == 'draft') {
                $prePurchaseOrder->update([
                    'process_status' => 'Waiting Approval Manager'
                ]);

                // Check for approval if necessary
                // 如有必要，检查审批
                $checkApproval = $this->repository->checkApproval('create', $prePurchaseOrder->id);
            }

            DB::commit();

            return redirect()->route('pre-purchase-order.show', $id)
                ->with('success', 'Supplier selections have been saved successfully. The pre-purchase order has been submitted for approval. / 供应商选择已成功保存。预采购单已提交审批。');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Pre-Purchase Order Item Selection Error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save supplier selections: ' . $e->getMessage() . ' / 保存供应商选择失败：' . $e->getMessage());
        }
    }
    public function submit(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Get the pre-purchase order
            // 获取预采购单
            $prePurchaseOrder = PrePurchaseOrder::findOrFail($id);

            // Check if order is in pending status
            // 检查订单是否处于待处理状态
            if ($prePurchaseOrder->process_status !== 'pending' && $prePurchaseOrder->process_status !== 'Draft') {
                return redirect()->back()
                    ->with('error', 'This pre-purchase order is not in draft status and cannot be submitted / 此预采购单不是草稿状态，无法提交');
            }

            // Update status to "under_review"
            // 将状态更新为"under_review"
            $prePurchaseOrder->update([
                'process_status' => 'Waiting Approval Manager'
            ]);

            // Check for approval
            // 检查审批
            $checkApproval = $this->repository->checkApproval('create', $prePurchaseOrder->id);

            DB::commit();

            return redirect()->route('pre-purchase-order.show', $id)
                ->with('success', 'Pre-purchase order has been submitted for review / 预采购单已提交审核');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Pre-Purchase Order Submit Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to submit pre-purchase order: ' . $e->getMessage() . ' / 提交预采购单失败：' . $e->getMessage());
        }
    }
    public function selectSupplier(Request $request, $id, $quotationId)
    {
        try {
            DB::beginTransaction();

            // Get the pre-purchase order
            // 获取预采购单
            $prePurchaseOrder = PrePurchaseOrder::findOrFail($id);

            // Check if the order is in a state that allows supplier selection
            // 检查订单是否处于允许选择供应商的状态
            if ($prePurchaseOrder->process_status !== 'under_review' && $prePurchaseOrder->process_status !== 'Waiting Approval Manager') {
                return redirect()->back()
                    ->with('error', 'Supplier selection is only allowed when the pre-purchase order is under review / 只有当预采购单处于审核中状态时，才允许选择供应商');
            }

            // Get the quotation
            // 获取报价
            $quotation = QuotationComparison::where('pre_purchase_order_id', $id)
                ->findOrFail($quotationId);

            // Reset all quotations to not selected
            // 重置所有报价为未选择
            QuotationComparison::where('pre_purchase_order_id', $id)
                ->update(['is_selected' => false]);

            // Mark this quotation as selected
            // 将此报价标记为已选择
            $quotation->update(['is_selected' => true]);

            // Update pre-purchase order status to "approved"
            // 将预采购单状态更新为"已批准"
            $prePurchaseOrder->update([
                'process_status' => 'approved',
                'finalized_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('pre-purchase-order.show', $id)
                ->with('success', 'Supplier "' . $quotation->supplier->name . '" has been selected and the pre-purchase order has been approved / 供应商 "' . $quotation->supplier->name . '" 已被选择，预采购单已获批准');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Pre-Purchase Order Supplier Selection Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to select supplier: ' . $e->getMessage() . ' / 选择供应商失败：' . $e->getMessage());
        }
    }

}
