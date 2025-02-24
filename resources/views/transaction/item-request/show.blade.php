@extends('layouts.admin.app')
@section('title', 'View Item Request / 查看物品请求')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white mb-3">
                    <div class="card-title mb-0">
                        <h5 class="mb-0 text-white">@yield('title')</h5>
                        <small>View item request details / 查看物品请求详情</small>
                        <span class="badge rounded-pill bg-info">{{ $itemRequest->request_status }}</span>
                    </div>
                    <a href="{{ route('item-request.index') }}" class="btn btn-light btn-sm" title="Back / 返回">
                        <i class="ti ti-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Item Request Header -->
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Request Number / 请求编号</label>
                            <input type="text" class="form-control" value="{{ $itemRequest->request_number }}" readonly>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Request Date / 请求日期</label>
                            <input type="text" class="form-control" value="{{ $itemRequest->request_date->format('Y-m-d') }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Customer Order / 客户订单</label>
                            <input type="text" class="form-control" value="{{ $itemRequest->customerOrder->order_number ?? '-' }}" readonly>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Remark / 备注</label>
                            <input type="text" class="form-control" value="{{ $itemRequest->remarks ?? '-' }}" readonly>
                        </div>
                    </div>

                    <!-- Item Request Details -->
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Item Details / 物品详情</label>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Item / 物品</th>
                                        <th>Specification / 规格</th>
                                        <th>UOM / 单位</th>
                                        <th>Quantity / 数量</th>
                                        <th>Remarks / 备注</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($itemRequest->details as $index => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->itemPriceHistory->itemUom->item->name }}</td>
                                            <td>{{ $item->itemPriceHistory->itemUom->item->spesification }}</td>
                                            <td>{{ $item->itemPriceHistory->itemUom->unitOfMeasurement->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->remarks ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No item details found / 未找到物品详情</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('item-request.index') }}" class="btn btn-secondary px-5">Back / 返回</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
