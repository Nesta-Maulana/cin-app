@extends('layouts.admin.app')
@section('title', 'Delivery Order Details / 送货单详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <div class="card-title mb-0">
                        <h5 class="mb-0 text-white">Delivery Order Details / 送货单详情</h5>
                        <small class="text-light">Detail information of the delivery order / 送货单的详细信息</small>
                    </div>
                    <a href="{{ route('delivery-order.index') }}" class="btn btn-light btn-sm" title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Delivery Order Header Section -->
                    <h6 class="text-primary mb-3">Delivery Order Header / 送货单头部</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Process Number / 处理编号</th>
                                    <td>{{ $deliveryOrder->process_number }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Order Number / 客户订单编号</th>
                                    <td>{{ $deliveryOrder->customerOrder->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Process Date / 处理日期</th>
                                    <td>{{ \Carbon\Carbon::parse($deliveryOrder->process_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Status / 状态</th>
                                    <td>
                                        <span
                                            class="badge {{ $deliveryOrder->process_status == 'approved' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($deliveryOrder->process_status) }} /
                                            {{ $deliveryOrder->process_status == 'approved' ? '已批准' : '待处理' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Remarks / 备注</th>
                                    <td>{{ $deliveryOrder->remarks ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Delivery Order Details Section -->
                    <h6 class="text-primary mb-3">Delivery Order Items / 送货单物品</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light text-nowrap">
                                <tr>
                                    <th>#</th>
                                    <th>Item Request Number / 请求编号</th>
                                    <th>Item Name / 物品</th>
                                    <th>Request Quantity / 请求数量</th>
                                    <th>UOM / 单位</th>
                                    <th>Stock Warehouse / 仓库库存</th>
                                    <th>Warehouse / 仓库</th>
                                    <th>Section / 区域</th>
                                    <th>Delivered Quantity / 送货数量</th>
                                    <th>Delivered UOM / 送货单位</th>
                                    <th>Remarks / 备注</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryOrder->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->itemRequestDetail->itemRequest->request_number ?? '-' }}</td>
                                        <td>{{ $detail->item->name ?? '-' }}</td>
                                        <td>{{ number_format($detail->itemRequestDetail->quantity, 3) }}</td>
                                        <td>{{ $detail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->name ?? '-' }}</td>
                                        <td>{{ number_format($detail->item->warehouseStocks->sum('current_stock'), 2) }}
                                        </td>
                                        <td>{{ $detail->warehouse->name ?? '-' }}</td>
                                        <td>{{ $detail->section->name ?? '-' }}</td>
                                        <td>{{ number_format($detail->quantity, 3) }}</td>
                                        <td>{{ $detail->itemUomFullfill->unitOfMeasurement->name ?? '-' }}</td>
                                        <td>{{ $detail->remarks ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('delivery-order.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back / 返回
                        </a>
                    </div>
                </div>
                <!-- End of Card Body -->
            </div>
        </div>
    </div>
@endsection
