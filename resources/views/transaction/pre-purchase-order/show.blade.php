@extends('layouts.admin.app')
@section('title', 'Pre-Purchase Order Details / 预采购单详情')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4 shadow-sm">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Pre-Purchase Order Details / 预采购单详情</h5>
                    <small class="text-muted">View pre-purchase order information / 查看预采购单信息</small>
                </div>
                <div>
                    @if($data->process_status == 'pending' || $data->process_status == 'under_review')
                        <a href="{{ route('pre-purchase-order.edit', $data->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-edit"></i> Edit / 编辑
                        </a>
                    @endif
                    <a href="{{ route('pre-purchase-order.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-list"></i> List / 列表
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Status Panel -->
                <div class="alert alert-{{ $data->process_status == 'approved' ? 'success' : ($data->process_status == 'under_review' ? 'info' : 'warning') }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Status: <strong>{{ ucfirst($data->process_status) }}</strong></h6>
                            <small>
                                @if($data->process_status == 'pending')
                                    This pre-purchase order is pending and needs to be submitted for review.
                                @elseif($data->process_status == 'under_review')
                                    This pre-purchase order is under review. Supplier offers can be added and selected.
                                @elseif($data->process_status == 'approved')
                                    This pre-purchase order has been approved with a selected supplier.
                                @endif
                            </small>
                        </div>
                        @if($data->process_status == 'pending')
                            <form action="{{ route('pre-purchase-order.submit', $data->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Submit for Review / 提交审核</button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Pre-Purchase Order Header -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Pre-Purchase Order Header / 预采购单头部</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pre-Purchase Order Number / 预采购单编号</label>
                                    <p>{{ $data->pre_po_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Customer Order Number / 客户订单编号</label>
                                    <p>{{ $data->customerOrder->order_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created Date / 创建日期</label>
                                    <p>{{ $data->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Remarks / 备注</label>
                                    <p>{{ $data->remarks ?: 'No remarks' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pre-Purchase Order Details -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Pre-Purchase Order Details / 预采购单详细信息</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
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
                                    @foreach($data->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemRequest->request_number : '-' }}</td>
                                        <td>{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name : '-' }}</td>
                                        <td class="text-end">{{ number_format($detail->quantity, 2) }}</td>
                                        <td>{{ $detail->uom ? $detail->uom->unitOfMeasurement->name : '-' }}</td>
                                        <td>{{ $detail->remarks ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Supplier Offers -->
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Supplier Offers / 供应商报价</h6>
                        @if($data->process_status != 'approved')
                            <a href="{{ route('pre-purchase-order.edit', $data->id) }}#addSupplierOffer" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> Add Supplier Offer / 添加供应商报价
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($data->quotations->isEmpty())
                            <div class="alert alert-info">
                                No supplier offers yet. Please add supplier offers to proceed.
                            </div>
                        @else
                            <ul class="nav nav-tabs" id="supplierTabs" role="tablist">
                                @foreach($data->quotations as $quotationIndex => $quotation)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $quotationIndex == 0 ? 'active' : '' }} {{ $quotation->is_selected ? 'text-success' : '' }}"
                                                id="supplier-tab-{{ $quotationIndex }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#supplier-content-{{ $quotationIndex }}"
                                                type="button"
                                                role="tab"
                                                aria-controls="supplier-content-{{ $quotationIndex }}"
                                                aria-selected="{{ $quotationIndex == 0 ? 'true' : 'false' }}">
                                            {{ $quotation->supplier->name }}
                                            @if($quotation->is_selected)
                                                <i class="fa fa-check-circle text-success"></i>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content py-3" id="supplierTabContent">
                                @foreach($data->quotations as $quotationIndex => $quotation)
                                    <div class="tab-pane fade {{ $quotationIndex == 0 ? 'show active' : '' }}"
                                         id="supplier-content-{{ $quotationIndex }}"
                                         role="tabpanel"
                                         aria-labelledby="supplier-tab-{{ $quotationIndex }}">

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="card h-100 {{ $quotation->is_selected ? 'border-success' : 'border-1' }}">
                                                    <div class="card-header {{ $quotation->is_selected ? 'bg-success text-white' : 'bg-light' }}">
                                                        <h6 class="mb-1">Supplier Information / 供应商信息</h6>
                                                    </div>
                                                    <div class="card-body p-1">
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <td>Name</td>
                                                                <td>{{ $quotation->supplier->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Address</td>
                                                                <td>{{ $quotation->supplier->address }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Address</td>
                                                                <td>{{ $quotation->currency }}</td>
                                                            </tr>
                                                        </table>
                                                        @if($quotation->is_selected)
                                                            <div class="alert alert-success mb-0">
                                                                <i class="fa fa-check-circle"></i> Selected Supplier / 已选择的供应商
                                                            </div>
                                                        @elseif($data->process_status == 'under_review')
                                                            <form action="{{ route('pre-purchase-order.select-supplier', [$data->id, $quotation->id]) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success w-100">
                                                                    Select This Supplier / 选择该供应商
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Total Summary / 总结</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <table class="table table-borderless">
                                                            <tr>
                                                                <th>Items Subtotal / 物品小计:</th>
                                                                <td class="text-end">{{ strtoupper($quotation->currency) }} {{ number_format($quotation->quotationDetails->sum(function($detail) { return $detail->offered_price_per_unit * $detail->quantity; }), 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Additional Costs Before Tax / 税前附加费用:</th>
                                                                <td class="text-end">{{ strtoupper($quotation->currency) }} {{ number_format($quotation->beforeTaxCosts->sum('amount') ?? 0, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Tax Amount / 税额:</th>
                                                                <td class="text-end">{{ strtoupper($quotation->currency) }} {{ number_format($quotation->tax_amount ?? 0, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Additional Costs After Tax / 税后附加费用:</th>
                                                                <td class="text-end">{{ strtoupper($quotation->currency) }} {{ number_format($quotation->afterTaxCosts->sum('amount') ?? 0, 2) }}</td>
                                                            </tr>
                                                            <tr class="table-active">
                                                                <th>Total Amount / 总金额:</th>
                                                                <td class="text-end fw-bold">{{ strtoupper($quotation->currency) }} {{ number_format($quotation->total_amount, 2) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Item Details -->
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Item Details / 物品详情</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Item / 物品</th>
                                                                <th>Quantity / 数量</th>
                                                                <th>UOM / 单位</th>
                                                                <th>Unit Price / 单价</th>
                                                                <th>Subtotal Price / 小计</th>
                                                                <th>Shipping Cost / 运输费</th>
                                                                <th>Grand Total / 总计</th>
                                                                <th>New Unit Price / 新单价</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($quotation->quotationDetails as $detail)
                                                                <tr>
                                                                    <td>
                                                                        {{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}
                                                                        @if($detail->remarks)
                                                                            <div class="small text-muted">{{ $detail->remarks }}</div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end">{{ number_format($detail->quantity, 2) }}</td>
                                                                    <td>{{ $detail->prePurchaseOrderDetail->uom->unitOfMeasurement->name }}</td>
                                                                    <td class="text-end">{{ number_format($detail->offered_price_per_unit, 2) }}</td>
                                                                    <td class="text-end">{{ number_format($detail->offered_price_per_unit * $detail->quantity, 2) }}</td>
                                                                    <td class="text-end">{{ number_format($detail->shipping_cost ?? 0, 2) }}</td>
                                                                    <td class="text-end">{{ number_format($detail->offered_price_per_unit * $detail->quantity + ($detail->shipping_cost ?? 0), 2) }}</td>
                                                                    <td class="text-end">{{ number_format($detail->new_unit_price ?? (($detail->offered_price_per_unit * $detail->quantity + ($detail->shipping_cost ?? 0)) / $detail->quantity), 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Costs and Tax Information -->
                                        <div class="row">
                                            <!-- Before Tax Costs -->
                                            <div class="col-md-4">
                                                <div class="card mb-3 border-1">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Additional Costs Before Tax / 税前附加费用</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        @if(isset($quotation->beforeTaxCosts) && $quotation->beforeTaxCosts->count() > 0)
                                                            <div class="">
                                                                <table class="table table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Description / 描述</th>
                                                                            <th>Type / 类型</th>
                                                                            <th>Amount / 金额</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($quotation->beforeTaxCosts as $cost)
                                                                            <tr>
                                                                                <td>{{ $cost->description }}</td>
                                                                                <td>
                                                                                    @if($cost->type == 'shipping')
                                                                                        Shipping / 运费
                                                                                    @elseif($cost->type == 'handling')
                                                                                        Handling / 装卸费
                                                                                    @elseif($cost->type == 'insurance')
                                                                                        Insurance / 保险
                                                                                    @else
                                                                                        Other / 其他
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-end">{{ number_format($cost->amount, 2) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr class="table-light">
                                                                            <th colspan="2">Total / 总计</th>
                                                                            <th class="text-end">{{ number_format($quotation->beforeTaxCosts->sum('amount'), 2) }}</th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <p class="text-muted">No additional costs before tax</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tax Information -->
                                            <div class="col-md-4">
                                                <div class="card mb-3 border-1">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Tax Information / 税务信息</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>Subtotal Before Tax / 税前小计</th>
                                                                <td class="text-end">{{ number_format($quotation->subtotal_before_tax ?? 0, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Tax Type / 税务类型</th>
                                                                <td>
                                                                    {{ ($quotation->tax_type ?? 'percentage') == 'percentage' ? 'Percentage / 百分比' : 'Fixed Amount / 固定金额' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Tax Value / 税值</th>
                                                                <td class="text-end">
                                                                    {{ number_format($quotation->tax_value ?? 0, 2) }}
                                                                    {{ ($quotation->tax_type ?? 'percentage') == 'percentage' ? '%' : strtoupper($quotation->currency) }}
                                                                </td>
                                                            </tr>
                                                            <tr class="table-light">
                                                                <th>Tax Amount / 税额</th>
                                                                <td class="text-end">{{ number_format($quotation->tax_amount ?? 0, 2) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- After Tax Costs -->
                                            <div class="col-md-4">
                                                <div class="card mb-3 border-1">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Additional Costs After Tax / 税后附加费用</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        @if(isset($quotation->afterTaxCosts) && $quotation->afterTaxCosts->count() > 0)
                                                            <div class="">
                                                                <table class="table table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Description / 描述</th>
                                                                            <th>Type / 类型</th>
                                                                            <th>Amount / 金额</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($quotation->afterTaxCosts as $cost)
                                                                            <tr>
                                                                                <td>{{ $cost->description }}</td>
                                                                                <td>
                                                                                    @if($cost->type == 'fee')
                                                                                        Fee / 费用
                                                                                    @elseif($cost->type == 'discount')
                                                                                        Discount / 折扣
                                                                                    @else
                                                                                        Other / 其他
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-end">{{ number_format($cost->amount, 2) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr class="table-light">
                                                                            <th colspan="2">Total / 总计</th>
                                                                            <th class="text-end">{{ number_format($quotation->afterTaxCosts->sum('amount'), 2) }}</th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <p class="text-muted">No additional costs after tax</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks -->
                                        @if($quotation->remarks)
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Remarks / 备注</h6>
                                                </div>
                                                <div class="card-body">
                                                    {{ $quotation->remarks }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        @if($data->process_status == 'pending')
                            <form action="{{ route('pre-purchase-order.submit', $data->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-paper-plane"></i> Submit for Review / 提交审核
                                </button>
                            </form>
                        @endif
                    </div>
                    <div>
                        @if($data->process_status != 'approved')
                            <a href="{{ route('pre-purchase-order.edit', $data->id) }}" class="btn btn-primary">
                                <i class="fa fa-edit"></i> Edit / 编辑
                            </a>
                        @endif
                        <a href="{{ route('pre-purchase-order.index') }}" class="btn btn-secondary ms-2">
                            <i class="fa fa-list"></i> Back to List / 返回列表
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap tabs if present
            var triggerTabList = [].slice.call(document.querySelectorAll('#supplierTabs button'))
            triggerTabList.forEach(function (triggerEl) {
                new bootstrap.Tab(triggerEl)
            });
        });
    </script>
@endpush
