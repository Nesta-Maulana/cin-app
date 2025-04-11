@extends('layouts.admin.app')
@section('title', 'Create New Purchase Order / 创建新采购单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create New Purchase Order / 创建新采购单</h5>
                        <small class="text-muted">
                            Create a new purchase order based on quotation / 基于报价创建新采购单
                            @if ($prePurchaseOrder)
                                <br>
                                <a href="{{ route('pre-purchase-order.show', $prePurchaseOrder->id) }}" class="href">
                                    <div class="badge bg-info">
                                        From Pre-Purchase Order: {{ $prePurchaseOrder->pre_po_number }}
                                    </div>
                                </a>
                            @endif
                        </small>
                    </div>
                    <a href="{{ route('purchase-order-new.index') }}" class="btn btn-secondary btn-sm" title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form class="form" action="{{ route('purchase-order-new.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 出错了!</strong> Please check your input / 请检查您的输入.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Hidden fields for pre-purchase order and supplier if provided -->
                        @if ($prePurchaseOrder)
                            <input type="hidden" name="pre_purchase_order_id" value="{{ $prePurchaseOrder->id }}">
                        @endif

                        <!-- Purchase Order Header Section -->
                        <h6 class="text-primary mb-3">Purchase Order Header / 采购单头部</h6>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="po_number">PO Number / 采购单编号 <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="po_number"
                                        class="form-control @error('po_number') is-invalid @enderror" name="po_number"
                                        value="{{ old('po_number', $poNumber ?? '') }}" readonly>
                                    @error('po_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="order_date">Order Date / 订单日期 <span
                                            class="text-danger">*</span></label>
                                    <input type="date" id="order_date"
                                        class="form-control @error('order_date') is-invalid @enderror" name="order_date"
                                        value="{{ old('order_date', date('Y-m-d')) }}">
                                    @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="supplier_id">Supplier / 供应商 <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id"
                                        name="supplier_id" {{ $supplier ? 'readonly' : '' }}>
                                        <option value="">Select Supplier / 选择供应商</option>
                                        @foreach ($suppliers as $supplierOption)
                                            <option value="{{ $supplierOption->id }}"
                                                {{ old('supplier_id', $supplier->id ?? ($supplierId ?? '')) == $supplierOption->id ? 'selected' : '' }}>
                                                {{ $supplierOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="customer_order_id">Customer Order / 客户订单 <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_order_id') is-invalid @enderror"
                                        id="customer_order_id" name="customer_order_id"
                                        {{ $customerOrder ? 'readonly' : '' }}>
                                        <option value="">Select Customer Order / 选择客户订单</option>
                                        @foreach ($customerOrders as $co)
                                            <option value="{{ $co->id }}"
                                                {{ old('customer_order_id', $customerOrder->id ?? '') == $co->id ? 'selected' : '' }}>
                                                {{ $co->order_number }} - {{ $co->project_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="expected_delivery_date">Expected Delivery Date / 预期交货日期
                                        <span class="text-danger">*</span></label>
                                    <input type="date" id="expected_delivery_date"
                                        class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                        name="expected_delivery_date"
                                        value="{{ old('expected_delivery_date', date('Y-m-d', strtotime('+7 days'))) }}">
                                    @error('expected_delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="currency">Currency / 货币 <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency"
                                        name="currency">
                                        <option value="IDR"
                                            {{ old('currency', $selectedQuotation->currency ?? 'IDR') == 'IDR' ? 'selected' : '' }}>
                                            IDR - Indonesian Rupiah
                                        </option>
                                        <option value="USD"
                                            {{ old('currency', $selectedQuotation->currency ?? '') == 'USD' ? 'selected' : '' }}>
                                            USD - US Dollar
                                        </option>
                                        <option value="EUR"
                                            {{ old('currency', $selectedQuotation->currency ?? '') == 'EUR' ? 'selected' : '' }}>
                                            EUR - Euro
                                        </option>
                                        <option value="CNY"
                                            {{ old('currency', $selectedQuotation->currency ?? '') == 'CNY' ? 'selected' : '' }}>
                                            CNY - Chinese Yuan
                                        </option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="payment_terms">Payment Terms / 付款条件</label>
                                    <select class="form-select @error('payment_terms') is-invalid @enderror"
                                        id="payment_terms" name="payment_terms">
                                        <option value="">Select Payment Terms / 选择付款条件</option>
                                        <option value="Net 30" {{ old('payment_terms') == 'Net 30' ? 'selected' : '' }}>
                                            Net 30</option>
                                        <option value="Net 45" {{ old('payment_terms') == 'Net 45' ? 'selected' : '' }}>
                                            Net 45</option>
                                        <option value="Net 60" {{ old('payment_terms') == 'Net 60' ? 'selected' : '' }}>
                                            Net 60</option>
                                        <option value="Immediate"
                                            {{ old('payment_terms') == 'Immediate' ? 'selected' : '' }}>Immediate</option>
                                    </select>
                                    @error('payment_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Purchase Order Items Section -->
                        <h6 class="text-primary mt-4 mb-3">Purchase Order Items / 采购单物品</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="po-items-table">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Item / 物品</th>
                                        <th width="15%">Specifications / 规格</th>
                                        <th width="10%">Unit / 单位</th>
                                        <th width="10%">Quantity / 数量</th>
                                        <th width="10%">Price / 价格</th>
                                        <th width="10%">Discount / 折扣</th>
                                        <th width="10%">Subtotal / 小计</th>
                                        <th width="10%">Action / 操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($quotationDetails) && $quotationDetails->count() > 0)
                                        @foreach ($quotationDetails as $index => $detail)
                                            <tr class="item-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="hidden"
                                                        name="details[{{ $index }}][quotation_comparison_detail_id]"
                                                        value="{{ $detail->id }}">
                                                    <input type="hidden"
                                                        name="details[{{ $index }}][pre_purchase_order_detail_id]"
                                                        value="{{ $detail->pre_purchase_order_detail_id }}">
                                                    @if (!is_null($detail->item_uom_id))
                                                        <input type="hidden"
                                                            name="details[{{ $index }}][item_request_detail_id]"
                                                            value="{{ $detail->prePurchaseOrderDetail->itemRequestDetail->id }}">
                                                        <input type="hidden"
                                                            name="details[{{ $index }}][item_id]"
                                                            value="{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->id }}">
                                                        <input type="text" class="form-control"
                                                            name="details[{{ $index }}][item_name]"
                                                            value="{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}"
                                                            readonly>
                                                    @else
                                                        <input type="hidden"
                                                            name="details[{{ $index }}][manual_item_request_detail_id]"
                                                            value="{{ $detail->prePurchaseOrderDetail->manualItemRequestDetail->id }}">
                                                        <input type="text" class="form-control"
                                                            name="details[{{ $index }}][item_name]"
                                                            value="{{ $detail->prePurchaseOrderDetail->manualItemRequestDetail->item_name }}"
                                                            readonly>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!is_null($detail->item_uom_id))
                                                        <textarea class="form-control" name="details[{{ $index }}][specification]" rows="2" readonly>{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->spesification }}</textarea>
                                                    @else
                                                        <textarea class="form-control" name="details[{{ $index }}][specification]" rows="2" readonly>{{ $detail->prePurchaseOrderDetail->manualItemRequestDetail->specification }}</textarea>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!is_null($detail->item_uom_id))
                                                        <input type="hidden"
                                                            name="details[{{ $index }}][item_uom_id]"
                                                            value="{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->id }}">
                                                        <input type="text" class="form-control"
                                                            name="details[{{ $index }}][unit]"
                                                            value="{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->name }}"
                                                            readonly>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="details[{{ $index }}][unit]"
                                                            value="{{ $detail->prePurchaseOrderDetail->manualItemRequestDetail->unit }}"
                                                            readonly>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-quantity"
                                                        name="details[{{ $index }}][quantity]"
                                                        value="{{ $detail->quantity }}" min="0.001" step="0.001"
                                                        required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-price"
                                                        name="details[{{ $index }}][price]"
                                                        value="{{ $detail->offered_price_per_unit }}" min="0"
                                                        step="0.01" required>
                                                    <input type="hidden"
                                                        name="details[{{ $index }}][original_price]"
                                                        value="{{ $detail->offered_price_per_unit }}">
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control item-discount"
                                                            name="details[{{ $index }}][discount_percentage]"
                                                            value="0" min="0" max="100" step="0.01">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <input type="hidden" class="item-discount-amount"
                                                        name="details[{{ $index }}][discount_amount]"
                                                        value="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-subtotal"
                                                        name="details[{{ $index }}][subtotal]"
                                                        value="{{ $detail->subtotal_price }}" readonly>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-item">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="no-items-row">
                                            <td colspan="9" class="text-center">No items added yet. Add items below. /
                                                尚未添加物品。在下方添加物品。</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">
                                            <button type="button" class="btn btn-sm btn-primary" id="add-item-btn">
                                                <i class="fa fa-plus"></i> Add Item / 添加物品
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <!-- Before Tax Costs Card -->
                                <div class="card mb-3 border-1">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Before Tax Additional Costs / 税前附加费用</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <div id="before-tax-costs">
                                            @if (isset($selectedQuotation) && $selectedQuotation->beforeTaxCosts->count() > 0)
                                                @foreach ($selectedQuotation->beforeTaxCosts as $index => $cost)
                                                    <div class="row mb-2 cost-row">
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control"
                                                                name="before_tax_costs[{{ $index }}][description]"
                                                                value="{{ $cost->description }}"
                                                                placeholder="Description / 描述" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="number" class="form-control before-tax-cost"
                                                                name="before_tax_costs[{{ $index }}][amount]"
                                                                value="{{ $cost->amount }}" min="0"
                                                                step="0.01" placeholder="Amount / 金额" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select class="form-select"
                                                                name="before_tax_costs[{{ $index }}][type]">
                                                                <option value="shipping"
                                                                    {{ $cost->type == 'shipping' ? 'selected' : '' }}>
                                                                    Shipping / 运费</option>
                                                                <option value="handling"
                                                                    {{ $cost->type == 'handling' ? 'selected' : '' }}>
                                                                    Handling / 装卸费</option>
                                                                <option value="other"
                                                                    {{ $cost->type == 'other' ? 'selected' : '' }}>Other /
                                                                    其他</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-cost">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                            id="add-before-tax-cost">
                                            <i class="fa fa-plus"></i> Add Cost / 添加费用
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tax Section -->
                        <div class="card mb-3 border-1">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Tax / 税</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="mb-0">
                                            <label class="form-label" for="tax_type">Tax Type / 税务类型</label>
                                            <select class="form-select" id="tax_type" name="tax_type">
                                                <option value="percentage"
                                                    {{ old('tax_type', 'percentage') == 'percentage' ? 'selected' : '' }}>
                                                    Percentage / 百分比
                                                </option>
                                                <option value="fixed" {{ old('tax_type') == 'fixed' ? 'selected' : '' }}>
                                                    Fixed Amount / 固定金额
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-0">
                                            <label class="form-label" for="tax_value">Tax Value / 税值</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="tax_value"
                                                    name="tax_value" value="{{ old('tax_value', 11) }}" min="0"
                                                    step="0.01">
                                                <span class="input-group-text tax-symbol">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-0">
                                            <label class="form-label" for="tax_amount">Tax Amount / 税额</label>
                                            <input type="number" class="form-control" id="tax_amount" name="tax_amount"
                                                value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <!-- After Tax Costs Card -->
                                <div class="card mb-3 border-1">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">After Tax Additional Costs / 税后附加费用</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <div id="after-tax-costs">
                                            @if (isset($selectedQuotation) && $selectedQuotation->afterTaxCosts->count() > 0)
                                                @foreach ($selectedQuotation->afterTaxCosts as $index => $cost)
                                                    <div class="row mb-2 cost-row">
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control"
                                                                name="after_tax_costs[{{ $index }}][description]"
                                                                value="{{ $cost->description }}"
                                                                placeholder="Description / 描述" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="number" class="form-control after-tax-cost"
                                                                name="after_tax_costs[{{ $index }}][amount]"
                                                                value="{{ $cost->amount }}" min="0"
                                                                step="0.01" placeholder="Amount / 金额" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select class="form-select"
                                                                name="after_tax_costs[{{ $index }}][type]">
                                                                <option value="shipping"
                                                                    {{ $cost->type == 'shipping' ? 'selected' : '' }}>
                                                                    Shipping / 运费</option>
                                                                <option value="handling"
                                                                    {{ $cost->type == 'handling' ? 'selected' : '' }}>
                                                                    Handling / 装卸费</option>
                                                                <option value="other"
                                                                    {{ $cost->type == 'other' ? 'selected' : '' }}>Other /
                                                                    其他</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-cost">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                            id="add-after-tax-cost">
                                            <i class="fa fa-plus"></i> Add Cost / 添加费用
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">

                                <!-- Remarks & Attachments -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Additional Information / 附加信息</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-0">
                                                    <label class="form-label" for="remarks">Remarks / 备注</label>
                                                    <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                                        placeholder="Enter any remarks or notes / 输入任何备注或说明">{{ old('remarks', $selectedQuotation->remarks ?? '') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-0">
                                                    <label class="form-label" for="attachments">Attachments / 附件</label>
                                                    <input type="file" class="form-control" id="attachments"
                                                        name="attachments[]" multiple>
                                                    <small class="text-muted">You can upload multiple files (PDF, JPEG,
                                                        PNG, ZIP)
                                                        up to 5MB each. /
                                                        您可以上传多个文件（PDF、JPEG、PNG、ZIP）每个最大5MB。</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <!-- Summary Section -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Summary / 总结</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Subtotal / 小计:</th>
                                                        <td class="text-end">
                                                            <span id="subtotal-display">0.00</span>
                                                            <input type="hidden" name="subtotal_price"
                                                                id="subtotal-input" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Before Tax Costs / 税前费用:</th>
                                                        <td class="text-end">
                                                            <span id="before-tax-costs-display">0.00</span>
                                                            <input type="hidden" name="before_tax_costs_total"
                                                                id="before-tax-costs-input" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tax / 税 <span id="tax-rate-display">(11%)</span>:</th>
                                                        <td class="text-end">
                                                            <span id="tax-amount-display">0.00</span>
                                                            <input type="hidden" name="tax_amount" id="tax-amount-input"
                                                                value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>After Tax Costs / 税后费用:</th>
                                                        <td class="text-end">
                                                            <span id="after-tax-costs-display">0.00</span>
                                                            <input type="hidden" name="after_tax_costs_total"
                                                                id="after-tax-costs-input" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total / 总计:</th>
                                                        <td class="text-end">
                                                            <strong><span id="total-amount-display">0.00</span></strong>
                                                            <input type="hidden" name="total_amount"
                                                                id="total-amount-input" value="0">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="my-4 d-flex justify-content-between">
                            <div>
                                <a href="{{ route('purchase-order-new.index') }}" class="btn btn-outline-secondary me-1">
                                    Cancel / 取消
                                </a>
                            </div>
                            <div>
                                <button type="submit" name="action" value="draft"
                                    class="btn btn-secondary px-5 me-2">
                                    Save as Draft / 保存为草稿
                                </button>
                                <button type="submit" name="action" value="submit" class="btn btn-primary px-5">
                                    Submit for Approval / 提交审批
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Format currency
            function formatCurrency(amount) {
                return parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Item template for adding new items
            const itemTemplate = `
        <tr class="item-row">
            <td class="item-number"></td>
            <td>
                <input type="text" class="form-control" name="details[{index}][item_name]" required>
            </td>
            <td>
                <textarea class="form-control" name="details[{index}][specification]" rows="2"></textarea>
            </td>
            <td>
                <input type="text" class="form-control" name="details[{index}][unit]" required>
            </td>
            <td>
                <input type="number" class="form-control item-quantity" name="details[{index}][quantity]" value="1" min="0.001" step="0.001" required>
            </td>
            <td>
                <input type="number" class="form-control item-price" name="details[{index}][price]" value="0" min="0" step="0.01" required>
                <input type="hidden" name="details[{index}][original_price]" value="0">
            </td>
            <td>
                <div class="input-group">
                    <input type="number" class="form-control item-discount" name="details[{index}][discount_percentage]" value="0" min="0" max="100" step="0.01">
                    <span class="input-group-text">%</span>
                </div>
                <input type="hidden" class="item-discount-amount" name="details[{index}][discount_amount]" value="0">
            </td>
            <td>
                <input type="number" class="form-control item-subtotal" name="details[{index}][subtotal]" value="0" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
     p-2    </tr>
    `;

            // Before tax cost template
            const beforeTaxCostTemplate = `
        <div class="row mb-2 cost-row">
            <div class="col-md-5">
                <input type="text" class="form-control" name="before_tax_costs[{index}][description]" placeholder="Description / 描述" required>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control before-tax-cost" name="before_tax_costs[{index}][amount]" value="0" min="0" step="0.01" placeholder="Amount / 金额" required>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="before_tax_costs[{index}][type]">
                    <option value="shipping">Shipping / 运费</option>
                    <option value="handling">Handling / 装卸费</option>
                    <option value="other" selected>Other / 其他</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-cost">
                    <i class="fa fa-times"></i>
                </button>
            </div>
     p-2    </div>
    `;

            // After tax cost template
            const afterTaxCostTemplate = `
        <div class="row mb-2 cost-row">
            <div class="col-md-5">
                <input type="text" class="form-control" name="after_tax_costs[{index}][description]" placeholder="Description / 描述" required>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control after-tax-cost" name="after_tax_costs[{index}][amount]" value="0" min="0" step="0.01" placeholder="Amount / 金额" required>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="after_tax_costs[{index}][type]">
                    <option value="shipping">Shipping / 运费</option>
                    <option value="handling">Handling / 装卸费</option>
                    <option value="other" selected>Other / 其他</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-cost">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    `;

            // Add Item
            $('#add-item-btn').on('click', function() {
                $('#no-items-row').remove();

                const itemCount = $('.item-row').length;
                const newRow = $(itemTemplate.replace(/{index}/g, itemCount));

                newRow.find('.item-number').text(itemCount + 1);
                $('#po-items-table tbody').append(newRow);

                // Re-initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Update calculations
                calculateTotals();
            });

            // Remove Item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();

                // Renumber items
                $('.item-row').each(function(index) {
                    $(this).find('.item-number').text(index + 1);
                });

                // If no items are left, show the empty message
                if ($('.item-row').length === 0) {
                    $('#po-items-table tbody').append(
                        '<tr id="no-items-row"><td colspan="9" class="text-center">No items added yet. Add items below. / 尚未添加物品。在下方添加物品。</td></tr>'
                    );
                }

                // Update calculations
                calculateTotals();
            });

            // Add Before Tax Cost
            $('#add-before-tax-cost').on('click', function() {
                const costCount = $('#before-tax-costs .cost-row').length;
                const newCost = $(beforeTaxCostTemplate.replace(/{index}/g, costCount));

                $('#before-tax-costs').append(newCost);

                // Re-initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Update calculations
                calculateTotals();
            });

            // Add After Tax Cost
            $('#add-after-tax-cost').on('click', function() {
                const costCount = $('#after-tax-costs .cost-row').length;
                const newCost = $(afterTaxCostTemplate.replace(/{index}/g, costCount));

                $('#after-tax-costs').append(newCost);

                // Re-initialize feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                // Update calculations
                calculateTotals();
            });

            // Remove Cost
            $(document).on('click', '.remove-cost', function() {
                $(this).closest('.cost-row').remove();

                // Update calculations
                calculateTotals();
            });

            // Calculate item subtotal when price, quantity, or discount changes
            $(document).on('input', '.item-price, .item-quantity, .item-discount', function() {
                const row = $(this).closest('tr');
                calculateRowTotal(row);
                calculateTotals();
            });

            // Recalculate totals when additional costs change
            $(document).on('input', '.before-tax-cost, .after-tax-cost', function() {
                calculateTotals();
            });

            // Update tax calculation when tax type or value changes
            $('#tax_type, #tax_value').on('change input', function() {
                // Update tax symbol display
                if ($('#tax_type').val() === 'percentage') {
                    $('.tax-symbol').text('%');
                    $('#tax-rate-display').text('(' + $('#tax_value').val() + '%)');
                } else {
                    $('.tax-symbol').text($('#currency').val());
                    $('#tax-rate-display').text('(Fixed)');
                }

                calculateTotals();
            });

            // Change currency symbol when currency changes
            $('#currency').on('change', function() {
                if ($('#tax_type').val() === 'fixed') {
                    $('.tax-symbol').text($(this).val());
                }
                calculateTotals();
            });

            // Calculate row total
            function calculateRowTotal(row) {
                const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
                const price = parseFloat(row.find('.item-price').val()) || 0;
                const discountPercentage = parseFloat(row.find('.item-discount').val()) || 0;

                // Save original price
                row.find('input[name$="[original_price]"]').val(price);

                // Calculate discount amount
                const discountAmount = (price * quantity * discountPercentage) / 100;
                row.find('.item-discount-amount').val(discountAmount.toFixed(2));

                // Calculate subtotal
                const subtotal = (price * quantity) - discountAmount;
                row.find('.item-subtotal').val(subtotal.toFixed(2));
            }

            // Calculate all totals
            function calculateTotals() {
                // Calculate subtotal of all items
                let subtotal = 0;
                $('.item-subtotal').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                // Calculate before tax costs
                let beforeTaxCostsTotal = 0;
                $('.before-tax-cost').each(function() {
                    beforeTaxCostsTotal += parseFloat($(this).val()) || 0;
                });

                // Calculate tax
                const taxableAmount = subtotal + beforeTaxCostsTotal;
                let taxAmount = 0;

                if ($('#tax_type').val() === 'percentage') {
                    const taxRate = parseFloat($('#tax_value').val()) || 0;
                    taxAmount = (taxableAmount * taxRate) / 100;
                } else {
                    taxAmount = parseFloat($('#tax_value').val()) || 0;
                }

                // Set tax amount in the tax section
                $('#tax_amount').val(taxAmount.toFixed(2));

                // Calculate after tax costs
                let afterTaxCostsTotal = 0;
                $('.after-tax-cost').each(function() {
                    afterTaxCostsTotal += parseFloat($(this).val()) || 0;
                });

                // Calculate total amount
                const totalAmount = taxableAmount + taxAmount + afterTaxCostsTotal;

                // Update summary section with formatted numbers for display
                $('#subtotal-display').text(formatCurrency(subtotal));
                $('#subtotal-input').val(subtotal.toFixed(2));

                $('#before-tax-costs-display').text(formatCurrency(beforeTaxCostsTotal));
                $('#before-tax-costs-input').val(beforeTaxCostsTotal.toFixed(2));

                $('#tax-amount-display').text(formatCurrency(taxAmount));
                $('#tax-amount-input').val(taxAmount.toFixed(2));

                $('#after-tax-costs-display').text(formatCurrency(afterTaxCostsTotal));
                $('#after-tax-costs-input').val(afterTaxCostsTotal.toFixed(2));

                $('#total-amount-display').text(formatCurrency(totalAmount));
                $('#total-amount-input').val(totalAmount.toFixed(2));
            }

            // Initialize existing item rows
            function initializeExistingItems() {
                // Calculate row totals for existing items
                $('.item-row').each(function() {
                    calculateRowTotal($(this));
                });

                // Calculate overall totals
                calculateTotals();
            }

            // Initialize supplier change handler for pre-purchase orders
            function initializeSupplierChange() {
                $('#supplier_id').on('change', function() {
                    const supplierId = $(this).val();
                    const prePurchaseOrderId = $('input[name="pre_purchase_order_id"]').val();

                    if (prePurchaseOrderId && supplierId) {
                        // Redirect to the same page but with the new supplier selected
                        window.location.href =
                            `${window.location.pathname}?pre_purchase_order_id=${prePurchaseOrderId}&supplier_id=${supplierId}`;
                    }
                });
            }

            // Form validation before submission
            $('form').on('submit', function(e) {
                // Check if at least one item exists
                if ($('.item-row').length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the purchase order / 请至少向采购单添加一个物品');
                    return false;
                }

                // Check if at least one item has quantity > 0
                let hasItems = false;
                $('.item-quantity').each(function() {
                    if (parseFloat($(this).val()) > 0) {
                        hasItems = true;
                        return false; // Break the loop
                    }
                });

                if (!hasItems) {
                    e.preventDefault();
                    alert('Please add at least one item with quantity greater than 0 / 请至少添加一个数量大于0的物品');
                    return false;
                }

                // Check if supplier is selected
                if (!$('#supplier_id').val()) {
                    e.preventDefault();
                    alert('Please select a supplier / 请选择供应商');
                    return false;
                }

                // Check if customer order is selected
                if (!$('#customer_order_id').val()) {
                    e.preventDefault();
                    alert('Please select a customer order / 请选择客户订单');
                    return false;
                }

                return true;
            });

            // Confirm before leaving the page with unsaved changes
            let formChanged = false;
            $('form :input').on('change', function() {
                formChanged = true;
            });

            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue =
                        'You have unsaved changes. Are you sure you want to leave? / 您有未保存的更改。确定要离开吗？';
                }
            });

            // Mark form as unchanged when submitting
            $('form').on('submit', function() {
                formChanged = false;
            });

            // Initialize everything
            initializeExistingItems();
            initializeSupplierChange();
        });
    </script>
@endpush
