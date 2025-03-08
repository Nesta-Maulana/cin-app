@extends('layouts.admin.app')
@section('title', 'View Purchase Order / 查看采购单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Purchase Order Details / 采购单详情</h5>
                        <small class="text-muted">PO Number: {{ $purchaseOrder->po_number }}</small>
                    </div>
                    <div>
                        @can('update-purchase-order')
                            @if ($purchaseOrder->process_status == 'Draft')
                                <a href="{{ route('purchase-order.edit', $purchaseOrder->id) }}"
                                    class="btn btn-primary btn-sm me-2">
                                    <i class="fa fa-edit"></i> Edit / 编辑
                                </a>
                            @endif
                        @endcan
                        <a href="{{ route('purchase-order.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back / 返回
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Status Badge -->
                    {{--  <div class="d-flex justify-content-between mb-4">
                        <div>
                            @can('update-purchase-order')
                                @if ($purchaseOrder->process_status == 'Waiting Approval Manager')
                                    <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="fa fa-check"></i> Approve / 批准
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="fa fa-times"></i> Reject / 拒绝
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div> --}}

                    <!-- Purchase Order Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4 border-1">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Purchase Order Information / 采购单信息</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered mt-2">
                                        <tbody>
                                            <tr>
                                                <td style="width: 40%" class="fw-semibold">PO Number / 采购单编号</td>
                                                <td>{{ $purchaseOrder->po_number }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Customer Order / 客户订单</td>
                                                <td>
                                                    @if ($purchaseOrder->customerOrder)
                                                        <a
                                                            href="{{ route('customer-order.show', $purchaseOrder->customerOrder->id) }}">
                                                            {{ $purchaseOrder->customerOrder->order_number }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Request Date / 请求日期</td>
                                                <td>{{ $purchaseOrder->request_date ? date('d M Y', strtotime($purchaseOrder->request_date)) : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Expected Delivery / 预计交货日期</td>
                                                <td>{{ $purchaseOrder->expected_delivery_date ? date('d M Y', strtotime($purchaseOrder->expected_delivery_date)) : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Remarks / 备注</td>
                                                <td>{{ $purchaseOrder->remarks ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Status
                                                </td>
                                                <td>
                                                    <div>
                                                        @php
                                                            $statusClass = match ($purchaseOrder->process_status) {
                                                                'Draft' => 'bg-secondary',
                                                                'Waiting Approval Manager' => 'bg-warning text-dark',
                                                                'Approved' => 'bg-success',
                                                                'Rejected' => 'bg-danger',
                                                                'Completed' => 'bg-info',
                                                                'Cancelled' => 'bg-dark',
                                                                default => 'bg-primary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $statusClass }}">
                                                            {{ $purchaseOrder->process_status }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4 border-1">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Additional Information / 附加信息</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered mt-2">
                                        <tbody>
                                            <tr>
                                                <td style="width: 40%" class="fw-semibold">Created By / 创建人</td>
                                                <td>{{ $purchaseOrder->createdBy->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Created Date / 创建日期</td>
                                                <td>{{ $purchaseOrder->created_at ? date('d M Y H:i', strtotime($purchaseOrder->created_at)) : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Last Updated / 最后更新</td>
                                                <td>{{ $purchaseOrder->updated_at ? date('d M Y H:i', strtotime($purchaseOrder->updated_at)) : '-' }}
                                                </td>
                                            </tr>
                                            @if ($purchaseOrder->process_status != 'Draft')
                                                <tr>
                                                    <td class="fw-semibold">Subtotal / 小计</td>
                                                    <td>{{ number_format($purchaseOrder->subtotal_price, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Shipping Cost / 运输费</td>
                                                    <td>{{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Other Cost / 其他费用</td>
                                                    <td>{{ number_format($purchaseOrder->other_cost, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Total Amount / 总金额</td>
                                                    <td class="fw-bold">{{ number_format($purchaseOrder->total_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order Items -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Purchase Order Items / 采购单物品</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mt-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th style="width: 15%">Request Number / 请求编号</th>
                                            <th style="width: 30%">Item / 物品</th>
                                            <th style="width: 15%">Quantity / 数量</th>
                                            <th style="width: 15%">UOM / 单位</th>
                                            <th style="width: 20%">Remarks / 备注</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchaseOrder->details as $index => $detail)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $detail->itemRequestDetail->itemRequest->request_number ?? '-' }}
                                                </td>
                                                <td>{{ $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? '-' }}
                                                </td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $detail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->name ?? '-' }}
                                                </td>
                                                <td>{{ $detail->remarks ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No items found / 未找到物品</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Offers -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Supplier Offers / 供应商报价</h6>
                        </div>
                        <div class="card-body">
                            <div class="accordion mt-2" id="supplierOffersAccordion">
                                @forelse($purchaseOrder->offers as $index => $offer)
                                    <div class="accordion-item mb-3 border">
                                        <h2 class="accordion-header" id="heading{{ $offer->id }}">
                                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $offer->id }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $offer->id }}">
                                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                    <span>
                                                        <strong>{{ $offer->supplier->name ?? 'Unknown Supplier' }}</strong>
                                                        @if ($offer->is_selected)
                                                            <span class="badge bg-success ms-2">Selected / 已选择</span>
                                                        @endif
                                                    </span>
                                                    <span>
                                                        <strong>Total: {{ strtoupper($offer->currency) }}
                                                            {{ number_format($offer->grand_total, 2) }}</strong>
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $offer->id }}"
                                            class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                            aria-labelledby="heading{{ $offer->id }}"
                                            data-bs-parent="#supplierOffersAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Supplier / 供应商</label>
                                                            <p>{{ $offer->supplier->name ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Currency / 货币</label>
                                                            <p>{{ strtoupper($offer->currency) }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Remarks / 备注</label>
                                                            <p>{{ $offer->remarks ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <table class="table table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 5%">#</th>
                                                                <th>Item / 物品</th>
                                                                <th>Quantity / 数量</th>
                                                                <th>UOM / 单位</th>
                                                                <th>Unit Price / 单价 </th>
                                                                <th>Subtotal Price / 小计</th>
                                                                <th>Shipping Cost / 运输费</th>
                                                                <th>Grand Total / 总计</th>
                                                                <th>Remaks / 备注</th>
                                                                <th>New Shipping Cost / 新运输费</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($offer->offerDetails->groupBy(function($detail) {
                                                                                                        return $detail->purchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name . '-' . $detail->purchaseOrderDetail->uom->unitOfMeasurement->name;
                                                                                                    }) as $key => $groupedDetails)
                                                                @php
                                                                    $firstDetail = $groupedDetails->first();
                                                                    $totalQuantity = $groupedDetails->sum('quantity');
                                                                    $offeredPrice =
                                                                        $groupedDetails->offered_price_per_unit;
                                                                    $totalPrice = round(
                                                                        $groupedDetails->sum('offered_price_per_unit') *
                                                                            $groupedDetails->sum('quantity'),
                                                                    );
                                                                    $shippingCost = round(
                                                                        $groupedDetails->sum('shipping_cost'),
                                                                    );
                                                                    $grandTotal = round($totalPrice + $shippingCost);
                                                                    $remarks = $groupedDetails->first()->remarks;
                                                                    $newShippingCost = round(
                                                                        $grandTotal / $totalQuantity,
                                                                    );

                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $firstDetail->purchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? '-' }}
                                                                    </td>
                                                                    <td>{{ $totalQuantity }}</td>
                                                                    <td>
                                                                        {{ $firstDetail->purchaseOrderDetail->uom->unitOfMeasurement->name ?? '-' }}
                                                                    </td>
                                                                    <td>{{ strtoupper($offer->currency) }}
                                                                        {{ number_format($offeredPrice, 2) }}</td>
                                                                    <td>{{ strtoupper($offer->currency) }}
                                                                        {{ number_format($totalPrice, 2) }}</td>
                                                                    <td>{{ strtoupper($offer->currency) }}
                                                                        {{ number_format($shippingCost, 2) }}</td>
                                                                    <td>{{ strtoupper($offer->currency) }}
                                                                        {{ number_format($grandTotal, 2) }}</td>
                                                                    <td>{{ $remarks ?? '-' }}</td>
                                                                    <td>{{ strtoupper($offer->currency) }}
                                                                        {{ number_format($newShippingCost, 2) }}</td>
                                                                    {{-- <td>{{ $totalQuantity }}</td>
                                                                    <td>{{ $firstDetail->purchaseOrderDetail->uom->unitOfMeasurement->name ?? '-' }}
                                                                    </td>
                                                                    <td>{{ strtoupper($offer->currency) }}
                                                                        {{ number_format($totalPrice, 2) }}
                                                                    </td> --}}
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5" class="text-center">No items found
                                                                        / 未找到物品</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-light">
                                                                <td colspan="4" class="text-end fw-semibold">Subtotal /
                                                                    The subtotal is calculated from the individual items. /
                                                                    小计：</td>
                                                                <td>{{ strtoupper($offer->currency) }}
                                                                    {{ number_format(round($offer->offerDetails->sum('total_price')), 2) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" class="text-end fw-semibold">Shipping
                                                                    Cost / 运输费：</td>
                                                                <td>{{ strtoupper($offer->currency) }}
                                                                    {{ number_format($offer->shipping_cost, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" class="text-end fw-semibold">Other Cost
                                                                    / 其他费用：</td>
                                                                <td>{{ strtoupper($offer->currency) }}
                                                                    {{ number_format($offer->other_cost, 2) }}</td>
                                                            </tr>
                                                            <tr class="table-light">
                                                                <td colspan="4" class="text-end fw-bold">Grand Total /
                                                                    总计：</td>
                                                                <td class="fw-bold">{{ strtoupper($offer->currency) }}
                                                                    {{ number_format($offer->grand_total, 2) }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info">
                                        No supplier offers available / 没有可用的供应商报价
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log -->
                    {{-- <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Activity Log / 活动日志</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach ($purchaseOrder->activities->sortByDesc('created_at') as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-point-sm timeline-point-success">
                                            <i class="fa fa-circle"></i>
                                        </div>
                                        <div class="timeline-event">
                                            <div class="timeline-heading">
                                                <h6 class="mb-0">{{ $activity->description }}</h6>
                                            </div>
                                            <div class="timeline-body">
                                                <div>by {{ $activity->causer->name ?? 'System' }}</div>
                                                <div class="text-muted">{{ $activity->created_at->format('d M Y H:i') }}
                                                </div>
                                            </div>
                                            @if (!empty($activity->changes))
                                                <div class="timeline-footer">
                                                    <a href="#" data-bs-toggle="collapse"
                                                        data-bs-target="#activity-{{ $activity->id }}"
                                                        class="text-primary">
                                                        Show details / 显示详情
                                                    </a>
                                                    <div id="activity-{{ $activity->id }}" class="collapse mt-2">
                                                        <pre class="bg-light p-2 rounded"><code>{{ json_encode($activity->changes, JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    {{-- <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('purchase-order.approve', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Purchase Order / 批准采购单</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve this purchase order? / 您确定要批准此采购单吗？</p>
                        <div class="mb-3">
                            <label for="approve_remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="approve_remarks" name="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel / 取消</button>
                        <button type="submit" class="btn btn-success">Approve / 批准</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <!-- Reject Modal -->
    {{-- <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('purchase-order.reject', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Purchase Order / 拒绝采购单</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reject this purchase order? / 您确定要拒绝此采购单吗？</p>
                        <div class="mb-3">
                            <label for="reject_remarks" class="form-label">Rejection Reason / 拒绝原因 <span
                                    class="text-danger">*</span></label>
                            <textarea id="reject_remarks" name="remarks" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel / 取消</button>
                        <button type="submit" class="btn btn-danger">Reject / 拒绝</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <!-- Change Supplier Modal -->
    {{-- <div class="modal fade" id="changeSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('purchase-order.change-supplier', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Change Selected Supplier / 更换选定供应商</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Select Supplier / 选择供应商 <span
                                    class="text-danger">*</span></label>
                            <select id="supplier_id" name="supplier_id" class="form-select" required>
                                @foreach ($purchaseOrder->offers as $offer)
                                    <option value="{{ $offer->id }}" {{ $offer->is_selected ? 'selected' : '' }}>
                                        {{ $offer->supplier->name }} - {{ strtoupper($offer->currency) }}
                                        {{ number_format($offer->grand_total, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="change_remarks" class="form-label">Reason for Change / 更换原因 <span
                                    class="text-danger">*</span></label>
                            <textarea id="change_remarks" name="remarks" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel / 取消</button>
                        <button type="submit" class="btn btn-primary">Change Supplier / 更换供应商</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

@endsection

@push('style')
    <style>
        /* Timeline styling */
        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 11px;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-point-sm {
            position: absolute;
            left: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            z-index: 1;
        }

        .timeline-point-success i {
            color: #28a745;
        }

        .timeline-event {
            position: relative;
            padding-bottom: 15px;
        }

        .timeline-heading h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-body {
            margin-bottom: 10px;
        }
    </style>
@endpush
