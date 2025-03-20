@extends('layouts.admin.app')
@section('title', 'Edit Pre-Purchase Order / 编辑预采购单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Pre-Purchase Order / 编辑预采购单</h5>
                        <small class="text-muted">Update the pre-purchase order information / 更新预采购单信息</small>
                    </div>
                    <a href="{{ route('pre-purchase-order.show', $data->id) }}" class="btn btn-secondary btn-sm"
                        title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('pre-purchase-order.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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

                        <!-- Pre-Purchase Order Header Section -->
                        <h6 class="text-primary mb-3">Pre-Purchase Order Header / 预采购单头部</h6>
                        <div class="mb-3">
                            <label for="pre_po_number" class="form-label">Pre-Purchase Order Number / 预采购单编号</label>
                            <input type="text" id="pre_po_number" name="pre_po_number" class="form-control"
                                value="{{ $data->pre_po_number }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="customer_order_id" class="form-label">Customer Order Number / 客户订单编号</label>
                            <select class="form-select" id="customer_order_id" name="customer_order_id"
                                {{ $data->process_status !== 'pending' ? 'disabled' : '' }}>
                                <option value="" disabled>Select a customer order / 选择客户订单</option>
                                @foreach ($customerOrders as $customerOrder)
                                    <option value="{{ $customerOrder->id }}"
                                        {{ $data->customer_order_id == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($data->process_status !== 'pending')
                                <input type="hidden" name="customer_order_id" value="{{ $data->customer_order_id }}">
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks / 请输入备注">{{ old('remarks', $data->remarks) }}</textarea>
                        </div>

                        <!-- Pre-Purchase Order Details Section -->
                        <h6 class="text-primary mb-3">Pre-Purchase Order Details / 预采购单详细信息</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailsTable">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>#</th>
                                        <th>Item Request Number / 请求编号</th>
                                        <th>Item Name / 物品</th>
                                        <th>Order Quantity / 订购数量</th>
                                        <th>UOM / 单位</th>
                                        <th>Remarks / 备注</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->details as $index => $detail)
                                        <tr id="item-{{ $index }}"
                                            data-item-id="{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item_id : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemRequest->request_number : '-' }}
                                                <input type="hidden" name="details[{{ $index }}][id]"
                                                    value="{{ $detail->id }}">
                                                <input type="hidden"
                                                    name="details[{{ $index }}][item_request_detail_id]"
                                                    value="{{ $detail->item_request_detail_id }}">
                                            </td>
                                            <td>
                                                <input type="hidden" name="details[{{ $index }}][item_id]"
                                                    value="{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item_id : '' }}"
                                                    class="item-id">
                                                <span
                                                    class="item-name">{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name : '-' }}</span>
                                            </td>
                                            <td>
                                                <input type="hidden" name="details[{{ $index }}][quantity]"
                                                    value="{{ $detail->quantity }}" class="order-quantity">
                                                {{ $detail->quantity }}
                                            </td>
                                            <td>
                                                <input type="hidden" name="details[{{ $index }}][uom_id]"
                                                    value="{{ $detail->uom_id }}" class="uom-id">
                                                <span
                                                    class="uom-name">{{ $detail->uom ? $detail->uom->unitOfMeasurement->name : '-' }}</span>
                                            </td>
                                            <td>
                                                <input type="hidden" name="details[{{ $index }}][remarks]"
                                                    value="{{ $detail->remarks }}">
                                                {{ $detail->remarks }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Supplier Offers Section -->
                        <h6 class="text-primary mt-4 mb-2">Supplier Offers / 供应商报价</h6>
                        <button type="button" class="btn btn-secondary mb-3" id="addSupplierOffer">
                            <i class="fa fa-plus"></i> Add Supplier Offer / 添加供应商报价
                        </button>

                        <div id="supplierOffersContainer">
                            @foreach ($data->quotations as $quotationIndex => $quotation)
                                <div id="supplier-card-{{ $quotationIndex }}"
                                    class="card mb-4 border-1 {{ $quotation->is_selected ? 'border-success' : '' }}">
                                    <div
                                        class="card-header {{ $quotation->is_selected ? 'bg-success text-white' : 'bg-light' }} d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Supplier Offer #{{ $quotationIndex + 1 }} -
                                            {{ $quotation->supplier->name }}</h6>
                                        <div>
                                            @if ($quotation->is_selected)
                                                <span class="badge bg-white text-success me-2">Selected / 已选择</span>
                                            @endif
                                            <button type="button" class="btn btn-danger btn-sm remove-supplier"
                                                data-id="{{ $quotationIndex }}">
                                                <i class="fa fa-trash"></i> Remove / 删除
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3 mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Supplier / 供应商</label>
                                                <input type="hidden" name="quotations[{{ $quotationIndex }}][id]"
                                                    value="{{ $quotation->id }}">
                                                <select name="quotations[{{ $quotationIndex }}][supplier_id]"
                                                    class="form-select supplier-select" required
                                                    {{ $quotation->is_selected ? 'disabled' : '' }}>
                                                    <option value="" disabled>Please choose supplier / 请选择供应商</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ $quotation->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($quotation->is_selected)
                                                    <input type="hidden"
                                                        name="quotations[{{ $quotationIndex }}][supplier_id]"
                                                        value="{{ $quotation->supplier_id }}">
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Currency / 货币</label>
                                                <select name="quotations[{{ $quotationIndex }}][currency]"
                                                    class="form-select currency-select" required
                                                    {{ $quotation->is_selected ? 'disabled' : '' }}>
                                                    @php
                                                        $currencies = getCurrency();
                                                    @endphp
                                                    @foreach ($currencies as $code => $name)
                                                        <option value="{{ $code }}"
                                                            {{ ($quotation->currency ?? 'idr') == $code ? 'selected' : '' }}>
                                                            {{ strtoupper($code) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered"
                                                id="offer-items-table-{{ $quotationIndex }}">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Item / 物品</th>
                                                        <th>Quantity / 数量</th>
                                                        <th>UOM / 单位</th>
                                                        <th>Unit Price / 单价</th>
                                                        <th>Subtotal Price / 小计</th>
                                                        <th>Shipping Cost / 运输费</th>
                                                        <th>Grand Total / 总计</th>
                                                        <th>Remarks / 备注</th>
                                                        <th>New Unit Price / 新单价</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($quotation->quotationDetails as $detailIndex => $detail)
                                                        <tr>
                                                            <td>
                                                                <input type="hidden"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][id]"
                                                                    value="{{ $detail->id }}">
                                                                <input type="hidden"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][pre_purchase_order_detail_id]"
                                                                    value="{{ $detail->pre_purchase_order_detail_id }}">
                                                                <input type="hidden"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][item_id]"
                                                                    value="{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item_id }}">
                                                                {{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][quantity]"
                                                                    value="{{ $detail->quantity }}"
                                                                    class="item-quantity">
                                                                {{ $detail->quantity }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][uom_id]"
                                                                    value="{{ $detail->prePurchaseOrderDetail->uom_id }}">
                                                                {{ $detail->prePurchaseOrderDetail->uom->unitOfMeasurement->name }}
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][price]"
                                                                    class="form-control item-price" step="0.01"
                                                                    min="0"
                                                                    value="{{ $detail->offered_price_per_unit }}"
                                                                    {{ $quotation->is_selected ? 'readonly' : '' }}
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][subtotal_price]"
                                                                    class="form-control item-subtotal" step="0.01"
                                                                    min="0"
                                                                    value="{{ $detail->offered_price_per_unit * $detail->quantity }}"
                                                                    readonly required>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][shipping_cost]"
                                                                    class="form-control item-shipping" step="0.01"
                                                                    min="0"
                                                                    value="{{ $detail->shipping_cost ?? 0 }}"
                                                                    {{ $quotation->is_selected ? 'readonly' : '' }}
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][grand_total]"
                                                                    class="form-control item-grand-total" step="0.01"
                                                                    min="0"
                                                                    value="{{ $detail->offered_price_per_unit * $detail->quantity + ($detail->shipping_cost ?? 0) }}"
                                                                    readonly required>
                                                            </td>
                                                            <td>
                                                                <textarea name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][remarks]" class="form-control"
                                                                    rows="2" placeholder="Remarks (optional) / 备注 (可选)" {{ $quotation->is_selected ? 'readonly' : '' }}>{{ $detail->remarks }}</textarea>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][new_unit_price]"
                                                                    class="form-control item-new-price" step="0.01"
                                                                    min="0"
                                                                    value="{{ ($detail->offered_price_per_unit * $detail->quantity + ($detail->shipping_cost ?? 0)) / $detail->quantity }}"
                                                                    readonly required>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Additional Costs Before Tax Section -->
                                        <div class="card mt-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Additional Costs Before Tax / 税前附加费用</h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="before-tax-costs-{{ $quotationIndex }}">
                                                    @foreach ($quotation->beforeTaxCosts ?? [] as $costIndex => $cost)
                                                        <div class="row mb-2 before-tax-cost-row">
                                                            <div class="col-md-5">
                                                                <input type="text"
                                                                    name="quotations[{{ $quotationIndex }}][before_tax_costs][{{ $costIndex }}][description]"
                                                                    class="form-control" placeholder="Description / 描述"
                                                                    value="{{ $cost->description }}"
                                                                    {{ $quotation->is_selected ? 'readonly' : '' }}
                                                                    required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][before_tax_costs][{{ $costIndex }}][amount]"
                                                                    class="form-control before-tax-cost-amount"
                                                                    step="0.01" min="0"
                                                                    value="{{ $cost->amount }}"
                                                                    {{ $quotation->is_selected ? 'readonly' : '' }}
                                                                    required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select
                                                                    name="quotations[{{ $quotationIndex }}][before_tax_costs][{{ $costIndex }}][type]"
                                                                    class="form-select"
                                                                    {{ $quotation->is_selected ? 'disabled' : '' }}>
                                                                    <option value="shipping"
                                                                        {{ $cost->type == 'shipping' ? 'selected' : '' }}>
                                                                        Shipping / 运费</option>
                                                                    <option value="handling"
                                                                        {{ $cost->type == 'handling' ? 'selected' : '' }}>
                                                                        Handling / 装卸费</option>
                                                                    <option value="insurance"
                                                                        {{ $cost->type == 'insurance' ? 'selected' : '' }}>
                                                                        Insurance / 保险</option>
                                                                    <option value="other"
                                                                        {{ $cost->type == 'other' ? 'selected' : '' }}>
                                                                        Other / 其他</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-1">
                                                                @if (!$quotation->is_selected)
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-cost">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if (!$quotation->is_selected)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary mt-2 add-before-tax-cost"
                                                        data-supplier="{{ $quotationIndex }}">
                                                        <i class="fa fa-plus"></i> Add Additional Cost / 添加附加费用
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Tax Section -->
                                        <div class="card mt-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Tax / 税</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Subtotal Before Tax / 税前小计</label>
                                                        <input type="number"
                                                            name="quotations[{{ $quotationIndex }}][subtotal_before_tax]"
                                                            class="form-control subtotal-before-tax"
                                                            value="{{ $quotation->subtotal_before_tax ?? 0 }}" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Tax Type / 税务类型</label>
                                                        <select name="quotations[{{ $quotationIndex }}][tax_type]"
                                                            class="form-select tax-type"
                                                            {{ $quotation->is_selected ? 'disabled' : '' }}>
                                                            <option value="percentage"
                                                                {{ ($quotation->tax_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>
                                                                Percentage / 百分比</option>
                                                            <option value="fixed"
                                                                {{ ($quotation->tax_type ?? 'percentage') == 'fixed' ? 'selected' : '' }}>
                                                                Fixed Amount / 固定金额</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Tax Value / 税值</label>
                                                        <div class="input-group">
                                                            <input type="number"
                                                                name="quotations[{{ $quotationIndex }}][tax_value]"
                                                                class="form-control tax-value" step="0.01"
                                                                min="0" value="{{ $quotation->tax_value ?? 0 }}"
                                                                {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                            <span
                                                                class="input-group-text tax-symbol">{{ ($quotation->tax_type ?? 'percentage') == 'percentage' ? '%' : getCurrencySymbol($quotation->currency ?? 'idr') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Tax Amount / 税额</label>
                                                        <input type="number"
                                                            name="quotations[{{ $quotationIndex }}][tax_amount]"
                                                            class="form-control tax-amount"
                                                            value="{{ $quotation->tax_amount ?? 0 }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Costs After Tax Section -->
                                        <div class="card mt-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Additional Costs After Tax / 税后附加费用</h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="after-tax-costs-{{ $quotationIndex }}">
                                                    @foreach ($quotation->afterTaxCosts ?? [] as $costIndex => $cost)
                                                        <div class="row mb-2 after-tax-cost-row">
                                                            <div class="col-md-5">
                                                                <input type="text"
                                                                    name="quotations[{{ $quotationIndex }}][after_tax_costs][{{ $costIndex }}][description]"
                                                                    class="form-control" placeholder="Description / 描述"
                                                                    value="{{ $cost->description }}"
                                                                    {{ $quotation->is_selected ? 'readonly' : '' }}
                                                                    required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="number"
                                                                    name="quotations[{{ $quotationIndex }}][after_tax_costs][{{ $costIndex }}][amount]"
                                                                    class="form-control after-tax-cost-amount"
                                                                    step="0.01" min="0"
                                                                    value="{{ $cost->amount }}"
                                                                    {{ $quotation->is_selected ? 'readonly' : '' }}
                                                                    required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select
                                                                    name="quotations[{{ $quotationIndex }}][after_tax_costs][{{ $costIndex }}][type]"
                                                                    class="form-select"
                                                                    {{ $quotation->is_selected ? 'disabled' : '' }}>
                                                                    <option value="fee"
                                                                        {{ $cost->type == 'fee' ? 'selected' : '' }}>Fee /
                                                                        费用</option>
                                                                    <option value="discount"
                                                                        {{ $cost->type == 'discount' ? 'selected' : '' }}>
                                                                        Discount / 折扣</option>
                                                                    <option value="other"
                                                                        {{ $cost->type == 'other' ? 'selected' : '' }}>
                                                                        Other / 其他</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-1">
                                                                @if (!$quotation->is_selected)
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-cost">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if (!$quotation->is_selected)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary mt-2 add-after-tax-cost"
                                                        data-supplier="{{ $quotationIndex }}">
                                                        <i class="fa fa-plus"></i> Add Additional Cost / 添加附加费用
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Grand Total Section -->
                                        <div class="card mt-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Grand Total / 总计</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Total Amount / 总金额</label>
                                                        <input type="number"
                                                            name="quotations[{{ $quotationIndex }}][total_amount]"
                                                            class="form-control total-amount"
                                                            value="{{ $quotation->total_amount ?? 0 }}" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Remarks / 备注</label>
                                                        <input type="text"
                                                            name="quotations[{{ $quotationIndex }}][remarks]"
                                                            class="form-control" value="{{ $quotation->remarks }}"
                                                            {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (!$quotation->is_selected && $data->process_status == 'under_review')
                                            <div class="mt-3 text-end">
                                                <button type="button" class="btn btn-success select-supplier"
                                                    data-index="{{ $quotationIndex }}">
                                                    Select This Supplier / 选择该供应商
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <div class="my-4 d-flex justify-content-between">
                            <div>
                                @if ($data->process_status == 'pending')
                                    <button type="submit" name="submit_type" value="draft"
                                        class="btn btn-secondary px-5 me-2">
                                        Save as Draft / 保存为草稿
                                    </button>
                                @endif
                            </div>
                            <div>
                                @if ($data->process_status == 'pending')
                                    <button type="submit" name="submit_type" value="submit"
                                        class="btn btn-primary px-5 me-2">
                                        Submit for Review / 提交审核
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary px-5 me-2">
                                        Update / 更新
                                    </button>
                                @endif
                                <a href="{{ route('pre-purchase-order.show', $data->id) }}"
                                    class="btn btn-label-secondary">
                                    Cancel / 取消
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Select Supplier Confirmation Modal -->
    <div class="modal fade" id="selectSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Supplier Selection / 确认供应商选择</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to select this supplier? This action will mark the selected supplier as the
                        chosen one and finalize the pre-purchase order.</p>
                    <p>您确定要选择此供应商吗？此操作将标记所选供应商为已选择的供应商并完成预采购单。</p>
                    <input type="hidden" id="selectedQuotationIndex">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel / 取消</button>
                    <button type="button" id="confirmSelectSupplier" class="btn btn-success">Confirm / 确认</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let detailIndex = {{ count($data->details) }};
            let supplierIndex = {{ count($data->quotations) }};
            let selectedSuppliers = []; // Store selected suppliers
            let orderItems = []; // Store items with quantity > 0
            let deletedQuotations = [];

            // Initialize selectedSuppliers array with existing quotations
            @foreach ($data->quotations as $index => $quotation)
                selectedSuppliers.push({
                    index: {{ $index }},
                    supplierId: {{ $quotation->supplier_id }}
                });
            @endforeach

            // Function to collect all items from the pre-purchase order
            function updateOrderItems() {
                let tempItems = [];

                console.log("Starting updateOrderItems. Found " + $('#detailsTable tbody tr').length +
                    " rows");

                $('#detailsTable tbody tr').each(function() {
                    const row = $(this);
                    const detailId = row.find('input[name^="details"][name$="[id]"]').val();

                    // More direct selectors to ensure we get the values
                    const itemId = row.find('input[name^="details"][name$="[item_id]"]').val();
                    const uomId = row.find('input[name^="details"][name$="[uom_id]"]').val();
                    const quantity = parseFloat(row.find(
                        'input[name^="details"][name$="[quantity]"]').val()) || 0;
                    const remarks = row.find('input[name^="details"][name$="[remarks]"]')
                        .val() || '';

                    // Get item name and UOM name
                    const itemName = row.find('.item-name').text().trim() || 'Item';
                    const uomName = row.find('.uom-name').text().trim() || 'Unit';

                    console.log("Processing row: detailId=" + detailId + ", itemId=" + itemId +
                        ", uomId=" + uomId);

                    // We only need a valid detail ID to include the item
                    if (detailId) {
                        tempItems.push({
                            prePurchaseOrderDetailId: detailId,
                            itemId: itemId || row.data(
                                'item-id'), // Fallback to data attribute
                            itemName: itemName,
                            uomId: uomId,
                            uomName: uomName,
                            quantity: quantity,
                            remarks: remarks
                        });
                        console.log("Added item: " + itemName + " with detailId: " + detailId);
                    }
                });

                // Group items by itemName and uomName
                const groupedItems = {};

                tempItems.forEach(item => {
                    const key = `${item.itemName}_${item.uomName}`;

                    if (!groupedItems[key]) {
                        groupedItems[key] = {
                            itemName: item.itemName,
                            itemId: item.itemId,
                            uomName: item.uomName,
                            uomId: item.uomId,
                            quantity: 0,
                            detailIds: []
                        };
                    }

                    groupedItems[key].quantity += item.quantity;
                    groupedItems[key].detailIds.push(item.prePurchaseOrderDetailId);
                });

                // Convert grouped items back to array
                orderItems = Object.values(groupedItems);

                console.log("Finished updateOrderItems. Found " + orderItems.length + " grouped items");
            }

            // Function to add supplier offer
            $('#addSupplierOffer').click(function() {
                // Force update of order items
                updateOrderItems();

                console.log("Add supplier button clicked. Items count: " + orderItems.length);

                // Check if there are items and alert only if truly empty
                if (orderItems.length === 0) {
                    alert('There are no items in this pre-purchase order / 预采购单中没有物品');
                    return;
                }

                $.ajax({
                    url: '{{ route('get-suppliers') }}',
                    type: 'GET',
                    success: function(response) {
                        addSupplierOfferRow(response.data);
                    },
                    error: function() {
                        alert('Failed to fetch supplier data / 获取供应商数据失败');
                    }
                });
            });

            function addSupplierOfferRow(suppliers) {
                let supplierOptions = suppliers.map(supplier =>
                    `<option value="${supplier.id}">${supplier.name}</option>`
                ).join('');

                // Get currencies for dropdown
                let currencyOptions = '';
                @php
                    $currencies = getCurrency();
                @endphp
                @foreach ($currencies as $code => $name)
                    currencyOptions +=
                        `<option value="{{ $code }}" {{ $code == 'idr' ? 'selected' : '' }}>{{ strtoupper($code) }}</option>`;
                @endforeach

                // Create card for each supplier offer
                let supplierCard = `
    <div id="supplier-card-${supplierIndex}" class="card mb-4 border-1">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Supplier Offer #${supplierIndex + 1}</h6>
            <button type="button" class="btn btn-danger btn-sm remove-supplier" data-id="${supplierIndex}">
                <i class="fa fa-trash"></i> Remove / 删除
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Supplier / 供应商</label>
                    <select name="quotations[${supplierIndex}][supplier_id]" class="form-select supplier-select" required>
                        <option value="" disabled selected>Please choose supplier / 请选择供应商</option>
                        ${supplierOptions}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Currency / 货币</label>
                    <select name="quotations[${supplierIndex}][currency]" class="form-select currency-select" required>
                        ${currencyOptions}
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="offer-items-table-${supplierIndex}">
                    <thead class="table-light">
                        <tr>
                            <th>Item / 物品</th>
                            <th>Quantity / 数量</th>
                            <th>UOM / 单位</th>
                            <th>Unit Price / 单价 </th>
                            <th>Subtotal Price / 小计</th>
                            <th>Shipping Cost / 运输费</th>
                            <th>Grand Total / 总计</th>
                            <th>Remarks / 备注</th>
                            <th>New Unit Price / 新单价</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Offer items will be added here -->
                    </tbody>
                </table>
            </div>

            <!-- Additional Costs Before Tax Section -->
            <div class="card mt-3">
                <div class="card-header bg-light mb-2">
                    <h6 class="mb-0">Additional Costs Before Tax / 税前附加费用</h6>
                </div>
                <div class="card-body">
                    <div id="before-tax-costs-${supplierIndex}">
                        <!-- Additional costs before tax will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-before-tax-cost" data-supplier="${supplierIndex}">
                        <i class="fa fa-plus"></i> Add Additional Cost / 添加附加费用
                    </button>
                </div>
            </div>

            <!-- Tax Section -->
            <div class="card mt-3">
                <div class="card-header bg-light mb-2">
                    <h6 class="mb-0">Tax / 税</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Subtotal Before Tax / 税前小计</label>
                            <input type="number" name="quotations[${supplierIndex}][subtotal_before_tax]" class="form-control subtotal-before-tax" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax Type / 税务类型</label>
                            <select name="quotations[${supplierIndex}][tax_type]" class="form-select tax-type">
                                <option value="percentage">Percentage / 百分比</option>
                                <option value="fixed">Fixed Amount / 固定金额</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax Value / 税值</label>
                            <div class="input-group">
                                <input type="number" name="quotations[${supplierIndex}][tax_value]" class="form-control tax-value" step="0.01" min="0" value="0">
                                <span class="input-group-text tax-symbol">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax Amount / 税额</label>
                            <input type="number" name="quotations[${supplierIndex}][tax_amount]" class="form-control tax-amount" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Costs After Tax Section -->
            <div class="card mt-3">
                <div class="card-header bg-light mb-2">
                    <h6 class="mb-0">Additional Costs After Tax / 税后附加费用</h6>
                </div>
                <div class="card-body">
                    <div id="after-tax-costs-${supplierIndex}">
                        <!-- Additional costs after tax will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-after-tax-cost" data-supplier="${supplierIndex}">
                        <i class="fa fa-plus"></i> Add Additional Cost / 添加附加费用
                    </button>
                </div>
            </div>

            <!-- Grand Total Section -->
            <div class="card mt-3">
                <div class="card-header bg-light mb-2">
                    <h6 class="mb-0">Grand Total / 总计</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Total Amount / 总金额</label>
                            <input type="number" name="quotations[${supplierIndex}][total_amount]" class="form-control total-amount" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks / 备注</label>
                            <input type="text" name="quotations[${supplierIndex}][remarks]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;

                // Append the card to the container
                $('#supplierOffersContainer').append(supplierCard);

                // Update selected suppliers array
                selectedSuppliers.push({
                    index: supplierIndex,
                    supplierId: null
                });

                // Fill the items table
                updateOfferItemsTable(supplierIndex);

                // Setup supplier select change event
                $(`#supplier-card-${supplierIndex} .supplier-select`).change(function() {
                    const supplierId = $(this).val();
                    // Update the selectedSuppliers array
                    for (let i = 0; i < selectedSuppliers.length; i++) {
                        if (selectedSuppliers[i].index === supplierIndex) {
                            selectedSuppliers[i].supplierId = supplierId;
                            break;
                        }
                    }
                });

                // Setup tax type change event
                $(document).on('change', '.tax-type', function() {
                    // Find the parent supplier card
                    const supplierCard = $(this).closest('[id^="supplier-card-"]');
                    const taxType = $(this).val();

                    // Only proceed if we found a valid supplier card
                    if (supplierCard.length) {
                        const taxSymbol = supplierCard.find('.tax-symbol');

                        if (taxType === 'percentage') {
                            taxSymbol.text('%');
                        } else {
                            // Get currency from the same card
                            const currencyVal = supplierCard.find('.currency-select').val() || 'idr';
                            taxSymbol.text(getCurrencySymbol(currencyVal));
                        }

                        // Safely extract the supplier index from the ID
                        const cardId = supplierCard.attr('id') || '';
                        const match = cardId.match(/supplier-card-(\d+)/);

                        if (match && match[1]) {
                            const supplierIdx = match[1];
                            calculateTotalAmount(supplierIdx);
                        }
                    }
                });



                // Setup tax value change event
                $(document).on('input', '.tax-value', function() {
                    // Find the parent supplier card
                    const supplierCard = $(this).closest('[id^="supplier-card-"]');

                    // Only proceed if we found a valid supplier card
                    if (supplierCard.length) {
                        // Safely extract the supplier index from the ID
                        const cardId = supplierCard.attr('id') || '';
                        const match = cardId.match(/supplier-card-(\d+)/);

                        if (match && match[1]) {
                            const supplierIdx = match[1];
                            calculateTotalAmount(supplierIdx);
                        }
                    }
                });

                // Setup currency change event
                $(document).on('change', '.currency-select', function() {
                    // Find the parent supplier card
                    const supplierCard = $(this).closest('[id^="supplier-card-"]');

                    // Only proceed if we found a valid supplier card
                    if (supplierCard.length) {
                        const taxType = supplierCard.find('.tax-type').val();

                        if (taxType === 'fixed') {
                            const currencyVal = $(this).val() || 'idr';
                            supplierCard.find('.tax-symbol').text(getCurrencySymbol(currencyVal));
                        }

                        // Safely extract the supplier index from the ID
                        const cardId = supplierCard.attr('id') || '';
                        const match = cardId.match(/supplier-card-(\d+)/);

                        if (match && match[1]) {
                            const supplierIdx = match[1];
                            calculateTotalAmount(supplierIdx);
                        }
                    }
                });

                // Increment supplier index for next offer
                supplierIndex++;
            }

            // Add additional cost before tax
            $(document).on('click', '.add-before-tax-cost', function() {
                const supplierIdx = $(this).data('supplier');
                const container = $(`#before-tax-costs-${supplierIdx}`);
                const costIndex = container.children().length;

                const costRow = `
    <div class="row mb-2 before-tax-cost-row">
        <div class="col-md-5">
            <input type="text" name="quotations[${supplierIdx}][before_tax_costs][${costIndex}][description]"
                   class="form-control" placeholder="Description / 描述" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="quotations[${supplierIdx}][before_tax_costs][${costIndex}][amount]"
                   class="form-control before-tax-cost-amount" step="0.01" min="0" value="0" required>
        </div>
        <div class="col-md-3">
            <select name="quotations[${supplierIdx}][before_tax_costs][${costIndex}][type]" class="form-select">
                <option value="shipping">Shipping / 运费</option>
                <option value="handling">Handling / 装卸费</option>
                <option value="insurance">Insurance / 保险</option>
                <option value="other">Other / 其他</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-cost">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
    `;

                container.append(costRow);

                // Trigger calculation when a new cost is added
                container.find('.before-tax-cost-amount').last().on('input', function() {
                    calculateTotalAmount(supplierIdx);
                });

                calculateTotalAmount(supplierIdx);
            });

            // Add additional cost after tax
            $(document).on('click', '.add-after-tax-cost', function() {
                const supplierIdx = $(this).data('supplier');
                const container = $(`#after-tax-costs-${supplierIdx}`);
                const costIndex = container.children().length;

                const costRow = `
    <div class="row mb-2 after-tax-cost-row">
        <div class="col-md-5">
            <input type="text" name="quotations[${supplierIdx}][after_tax_costs][${costIndex}][description]"
                   class="form-control" placeholder="Description / 描述" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="quotations[${supplierIdx}][after_tax_costs][${costIndex}][amount]"
                   class="form-control after-tax-cost-amount" step="0.01" min="0" value="0" required>
        </div>
        <div class="col-md-3">
            <select name="quotations[${supplierIdx}][after_tax_costs][${costIndex}][type]" class="form-select">
                <option value="fee">Fee / 费用</option>
                <option value="discount">Discount / 折扣</option>
                <option value="other">Other / 其他</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-cost">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
    `;

                container.append(costRow);

                // Trigger calculation when a new cost is added
                container.find('.after-tax-cost-amount').last().on('input', function() {
                    calculateTotalAmount(supplierIdx);
                });

                calculateTotalAmount(supplierIdx);
            });

            // Remove additional cost
            $(document).on('click', '.remove-cost', function() {
                const row = $(this).closest('.row');
                const supplierCard = $(this).closest('.card');
                const supplierIdx = supplierCard.find(
                    '.add-before-tax-cost, .add-after-tax-cost').data('supplier');

                row.remove();
                calculateTotalAmount(supplierIdx);
            });

            // Helper function to get currency symbol
            function getCurrencySymbol(currencyCode) {
                // Check if currencyCode is undefined or null
                if (!currencyCode) return '';

                switch (currencyCode.toLowerCase()) {
                    case 'idr':
                        return 'Rp';
                    case 'usd':
                        return '$';
                    case 'eur':
                        return '€';
                    case 'gbp':
                        return '£';
                    case 'jpy':
                        return '¥';
                    case 'cny':
                        return '¥';
                    default:
                        return currencyCode.toUpperCase();
                }
            }



            // Calculate total amount for a supplier offer
            function calculateTotalAmount(supplierIdx) {
                // Get items subtotal
                let itemsSubtotal = 0;
                $(`#offer-items-table-${supplierIdx} tbody tr`).each(function() {
                    const grandTotal = parseFloat($(this).find('.item-grand-total').val()) || 0;
                    itemsSubtotal += grandTotal;
                });

                // Get additional costs before tax
                let beforeTaxCosts = 0;
                $(`#before-tax-costs-${supplierIdx} .before-tax-cost-amount`).each(function() {
                    const amount = parseFloat($(this).val()) || 0;
                    beforeTaxCosts += amount;
                });

                // Calculate subtotal before tax
                const subtotalBeforeTax = itemsSubtotal + beforeTaxCosts;
                $(`#supplier-card-${supplierIdx} .subtotal-before-tax`).val(subtotalBeforeTax.toFixed(
                    2));

                // Calculate tax amount
                const taxType = $(`#supplier-card-${supplierIdx} .tax-type`).val();
                const taxValue = parseFloat($(`#supplier-card-${supplierIdx} .tax-value`).val()) || 0;
                let taxAmount = 0;

                if (taxType === 'percentage') {
                    taxAmount = subtotalBeforeTax * (taxValue / 100);
                } else {
                    taxAmount = taxValue;
                }

                $(`#supplier-card-${supplierIdx} .tax-amount`).val(taxAmount.toFixed(2));

                // Get additional costs after tax
                let afterTaxCosts = 0;
                $(`#after-tax-costs-${supplierIdx} .after-tax-cost-amount`).each(function() {
                    const amount = parseFloat($(this).val()) || 0;
                    afterTaxCosts += amount;
                });

                // Calculate total amount
                const totalAmount = subtotalBeforeTax + taxAmount + afterTaxCosts;
                $(`#supplier-card-${supplierIdx} .total-amount`).val(totalAmount.toFixed(2));
            }

            function updateOfferItemsTable(supplierIdx) {
                let offerItemsTable = $(`#offer-items-table-${supplierIdx} tbody`);
                offerItemsTable.empty();

                console.log(
                    `Updating offer items table #${supplierIdx} with ${orderItems.length} items`);

                // Add rows for each order item
                orderItems.forEach((item, idx) => {
                    // For grouped items, we need to join all detailIds
                    const detailIdsInput = item.detailIds ?
                        item.detailIds.map(id =>
                            `<input type="hidden" name="quotations[${supplierIdx}][details][${idx}][detail_ids][]" value="${id}">`
                        ).join('') :
                        `<input type="hidden" name="quotations[${supplierIdx}][details][${idx}][pre_purchase_order_detail_id]" value="${item.prePurchaseOrderDetailId}">`;


                    let offerRow = `
        <tr>
            <td>
                ${detailIdsInput}
                <input type="hidden" name="quotations[${supplierIdx}][details][${idx}][item_id]" value="${item.itemId}">
                ${item.itemName}
            </td>
            <td>
                <input type="hidden" name="quotations[${supplierIdx}][details][${idx}][quantity]" value="${item.quantity}" class="item-quantity">
                ${item.quantity}
            </td>
            <td>
                <input type="hidden" name="quotations[${supplierIdx}][details][${idx}][uom_id]" value="${item.uomId}">
                ${item.uomName}
            </td>
            <td>
                <input type="number" name="quotations[${supplierIdx}][details][${idx}][price]" class="form-control item-price" step="0.01" min="0" required>
            </td>
            <td>
                <input type="number" name="quotations[${supplierIdx}][details][${idx}][subtotal_price]" class="form-control item-subtotal" step="0.01" min="0" readonly required>
            </td>
            <td>
                <input type="number" name="quotations[${supplierIdx}][details][${idx}][shipping_cost]" class="form-control item-shipping" step="0.01" min="0" value="0" required>
            </td>
            <td>
                <input type="number" name="quotations[${supplierIdx}][details][${idx}][grand_total]" class="form-control item-grand-total" step="0.01" min="0" readonly required>
            </td>
            <td>
                <textarea name="quotations[${supplierIdx}][details][${idx}][remarks]" class="form-control" rows="2" placeholder="Remarks (optional) / 备注 (可选) "></textarea>
            </td>
            <td>
                <input type="number" name="quotations[${supplierIdx}][details][${idx}][new_unit_price]" class="form-control item-new-price" step="0.01" min="0" readonly required>
            </td>
        </tr>
        `;
                    offerItemsTable.append(offerRow);
                });

                // Add event listeners to calculate values automatically
                setupCalculationEvents(supplierIdx);
            }

            // Set up event listeners for automatic calculations
            function setupCalculationEvents(supplierIdx) {
                // Get the current table
                const table = $(`#offer-items-table-${supplierIdx}`);

                // When unit price changes, update subtotal, grand total, and new unit price
                table.find('.item-price').on('input', function() {
                    const row = $(this).closest('tr');
                    calculateRowValues(row);
                    calculateTotalAmount(supplierIdx);
                });

                // When shipping cost changes, update grand total and new unit price
                table.find('.item-shipping').on('input', function() {
                    const row = $(this).closest('tr');
                    calculateRowValues(row);
                    calculateTotalAmount(supplierIdx);
                });
            }

            // Calculate values for a specific row
            function calculateRowValues(row) {
                // Get input values
                const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
                const unitPrice = parseFloat(row.find('.item-price').val()) || 0;
                const shippingCost = parseFloat(row.find('.item-shipping').val()) || 0;

                // Calculate subtotal (Quantity * Unit Price)
                const subtotal = quantity * unitPrice;
                row.find('.item-subtotal').val(subtotal.toFixed(2));

                // Calculate grand total (Subtotal + Shipping Cost)
                const grandTotal = subtotal + shippingCost;
                row.find('.item-grand-total').val(grandTotal.toFixed(2));

                // Calculate new unit price (Grand Total / Quantity)
                const newUnitPrice = quantity > 0 ? grandTotal / quantity : 0;
                row.find('.item-new-price').val(newUnitPrice.toFixed(2));
            }

            // Function to update all quotation tables
            function updateAllQuotationTables() {
                selectedSuppliers.forEach(supplier => {
                    // Skip if quotation card doesn't exist (was deleted)
                    if ($(`#supplier-card-${supplier.index}`).length) {
                        updateOfferItemsTable(supplier.index);
                    }
                });
            }

            // Remove supplier offer
            $(document).on('click', '.remove-supplier', function() {
                let supplierIdx = $(this).data('id');
                let quotationId = $(
                        `#supplier-card-${supplierIdx} input[name^="quotations"][name$="[id]"]`)
                    .val();

                if (quotationId) {
                    deletedQuotations.push(quotationId);
                    // Add a hidden input to track deleted quotations
                    $('form').append(
                        `<input type="hidden" name="deleted_quotations[]" value="${quotationId}">`
                    );
                }

                $(`#supplier-card-${supplierIdx}`).remove();

                // Remove from selected suppliers array
                selectedSuppliers = selectedSuppliers.filter(s => s.index != supplierIdx);
            });

            // Select supplier button click
            $(document).on('click', '.select-supplier', function() {
                const quotationIdx = $(this).data('index');
                $('#selectedQuotationIndex').val(quotationIdx);
                $('#selectSupplierModal').modal('show');
            });

            // Confirm select supplier
            $('#confirmSelectSupplier').click(function() {
                const quotationIdx = $('#selectedQuotationIndex').val();

                // Add a hidden input to indicate which supplier should be selected
                $('form').append(
                    `<input type="hidden" name="selected_quotation" value="${quotationIdx}">`
                );

                // Submit the form
                $('form').submit();
            });

            // Check for duplicate suppliers
            function isDuplicateSupplier(supplierId) {
                if (!supplierId) return false;

                return selectedSuppliers.some(s => {
                    return s.supplierId == supplierId;
                });
            }

            // Handle supplier selection change
            $(document).on('change', '.supplier-select', function() {
                const supplierId = $(this).val();
                const currentCard = $(this).closest('.card');
                const currentIndex = currentCard.attr('id').replace('supplier-card-', '');

                // Check if this supplier is already selected in another card
                let duplicate = false;

                selectedSuppliers.forEach(s => {
                    if (s.index != currentIndex && s.supplierId == supplierId) {
                        duplicate = true;
                    }
                });

                if (duplicate) {
                    alert('This supplier already has an offer / 该供应商已有报价');
                    $(this).val(''); // Reset selection

                    // Update selectedSuppliers array
                    for (let i = 0; i < selectedSuppliers.length; i++) {
                        if (selectedSuppliers[i].index == currentIndex) {
                            selectedSuppliers[i].supplierId = null;
                            break;
                        }
                    }
                } else {
                    // Update selectedSuppliers array
                    for (let i = 0; i < selectedSuppliers.length; i++) {
                        if (selectedSuppliers[i].index == currentIndex) {
                            selectedSuppliers[i].supplierId = supplierId;
                            break;
                        }
                    }
                }
            });

            // Setup events for existing quotations
            @foreach ($data->quotations as $quotationIndex => $quotation)
                setupCalculationEvents({{ $quotationIndex }});
                calculateTotalAmount({{ $quotationIndex }});

                // Add handlers for existing additional costs
                $(`#before-tax-costs-{{ $quotationIndex }} .before-tax-cost-amount`).on('input',
                    function() {
                        calculateTotalAmount({{ $quotationIndex }});
                    });

                $(`#after-tax-costs-{{ $quotationIndex }} .after-tax-cost-amount`).on('input',
                    function() {
                        calculateTotalAmount({{ $quotationIndex }});
                    });
            @endforeach

            // Update all totals initially
            $('.item-price').each(function() {
                $(this).trigger('input');
            });

            // Form validation before submit
            $('form').on('submit', function(e) {
                let hasItems = false;
                let hasSuppliers = false;
                let submitType = $(document.activeElement).attr('name') === 'submit_type' ?
                    $(document.activeElement).val() : '';

                // Check if at least one item exists
                updateOrderItems();
                hasItems = orderItems.length > 0;

                // Check if at least one supplier offer exists
                hasSuppliers = selectedSuppliers.length > 0;

                if (!hasItems) {
                    e.preventDefault();
                    alert('There are no items in this pre-purchase order / 预采购单中没有物品');
                    return false;
                }

                // For submit (not draft), we need quotations
                if (submitType === 'submit' && !hasSuppliers) {
                    e.preventDefault();
                    alert('Please add at least one supplier offer / 请至少添加一个供应商报价');
                    return false;
                }

                // Check if all suppliers are selected
                let missingSupplier = false;
                selectedSuppliers.forEach(supplier => {
                    if (!supplier.supplierId && $(`#supplier-card-${supplier.index}`)
                        .length) {
                        missingSupplier = true;
                    }
                });

                if (missingSupplier && submitType === 'submit') {
                    e.preventDefault();
                    alert('Please select a supplier for all offers / 请为所有报价选择供应商');
                    return false;
                }

                return true;
            });

            // Initialize by loading order items
            updateOrderItems();
        });
    </script>
@endpush
