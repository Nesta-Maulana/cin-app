@extends('layouts.admin.app')
@section('title', 'Item Request Need Process For Customer Order ' . $customerOrder->order_number)

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white mb-3">
                    <div class="card-title mb-0">
                        <h5 class="mb-0 text-white">
                            Item Request Need Process For Customer Order {{ $customerOrder->order_number }}
                            / 客户订单 {{ $customerOrder->order_number }} 的物品请求
                        </h5>
                        <small class="text-light">Order details for reference / 订单详细信息</small>
                    </div>
                    <a href="{{ route('customer-order.index') }}" class="btn btn-light btn-sm" title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Customer Order Details Section -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Order Number / 订单编号</th>
                                    <td>{{ $customerOrder->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name / 客户名称</th>
                                    <td>{{ $customerOrder->customer->customer_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Project Name / 项目名称</th>
                                    <td>{{ $customerOrder->project_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Job Category / 作业类别</th>
                                    <td>{{ $customerOrder->jobCategory->category_name ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Combined Item Request Details Section -->
                    <h6 class="text-primary mb-3">All Item Request Details / 所有物品请求详情</h6>
                    @php
                        // Merge all item request details from the customer order into one collection.
                        $allDetails = collect();
                        foreach ($customerOrder->itemRequests as $itemRequest) {
                            foreach ($itemRequest->details as $detail) {
                                // Add a custom property for the request number.
                                $detail->request_number = $itemRequest->request_number;
                                $allDetails->push($detail);
                            }
                        }
                        // Initialize counters for Delivery Order (DO) and Purchase Order (PO)
                        $countReadyDO = 0;
                        $countNeedPO = 0;
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Request Number / 请求编号</th>
                                    <th>Item / 物品</th>
                                    <th>Specification / 规格</th>
                                    <th>UOM / 单位</th>
                                    <th>Quantity / 数量</th>
                                    <th>Remarks / 备注</th>
                                    <th class="bg-secondary text-white">Stock on Warehouse</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allDetails as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->request_number }}</td>
                                        <td>{{ $detail->itemPriceHistory->itemUom->item->name ?? '-' }}</td>
                                        <td>{{ $detail->itemPriceHistory->itemUom->item->spesification ?? '-' }}</td>
                                        <td>{{ $detail->itemPriceHistory->itemUom->unitOfMeasurement->name ?? '-' }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ $detail->remarks ?? '-' }}</td>
                                        @php
                                            // Calculate the available stock from the warehouse (assumed in the primary unit)
                                            $stock = $detail->itemPriceHistory->itemUom->item->warehouseStocks->sum(
                                                'current_stock',
                                            );

                                            // Compare stock with the requested quantity. If the UOM for request is the same as the primary UOM,
                                            // use the request quantity; otherwise, convert the quantity based on the conversion factor.
                                            if (
                                                $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->id ==
                                                $detail->itemPriceHistory->itemUom->unitOfMeasurement->id
                                            ) {
                                                if ($stock >= $detail->quantity) {
                                                    $bgColor = 'bg-success';
                                                    $countReadyDO++;
                                                } else {
                                                    $bgColor = 'bg-danger';
                                                    $countNeedPO++;
                                                }
                                            } else {
                                                $requestQuantity =
                                                    $detail->quantity *
                                                    $detail->itemPriceHistory->itemUom->item->unitOfMeasurement
                                                        ->conversion;
                                                if ($stock >= $requestQuantity) {
                                                    $bgColor = 'bg-success';
                                                    $countReadyDO++;
                                                } else {
                                                    $bgColor = 'bg-danger';
                                                    $countNeedPO++;
                                                }
                                            }
                                        @endphp
                                        <td class="{{ $bgColor }}">
                                            <p class="text-white">
                                                @foreach ($detail->itemPriceHistory->itemUom->item->itemUoms as $uom)
                                                    {{ number_format($stock / $uom->conversion, 2, ',', '.') }}
                                                    {{ $uom->unitOfMeasurement->name }}
                                                    @if (!$loop->last)
                                                        |
                                                    @endif
                                                @endforeach
                                            </p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            No item request details found / 未找到物品请求详情
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Buttons Section for Delivery Order and Purchase Order Creation -->
                    <div class="d-flex justify-content-end mt-3">
                        @if ($countReadyDO > 0)
                            <a href="{{ route('delivery-order.create', ['order_id' => $customerOrder->id]) }}" class="btn btn-success me-2">
                                Create Delivery Order / 创建送货单 ({{ $countReadyDO }})
                            </a>
                        @endif

                        @if ($countNeedPO > 0)
                            <a href="{{-- {{ route('purchase-order.create', ['order_id' => $customerOrder->id]) }} --}}" class="btn btn-warning">
                                Create Purchase Order / 创建采购单 ({{ $countNeedPO }})
                            </a>
                        @endif
                    </div>
                </div>
                <!-- End of Card Body -->
            </div>
        </div>
    </div>
@endsection
