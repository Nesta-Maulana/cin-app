@extends('layouts.admin.app')
@section('title', 'Customer Order Detail / 客户订单详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white mb-3">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Customer Order Detail / 客户订单详情</h5>
                        <small class="text-light">Order details for reference / 订单详细信息</small>
                    </div>
                    <a href="{{ route('customer-order.index') }}" class="btn btn-light btn-sm" title="Back / 返回">
                        <i class="ti ti-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Order Details -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <!-- Order Information -->
                                <tr>
                                    <th>Order Number / 订单编号</th>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name / 客户名称</th>
                                    <td>{{ $order->customer->customer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Project Name / 项目名称</th>
                                    <td>{{ $order->project_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Job Category / 作业类别</th>
                                    <td>{{ $order->jobCategory->category_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date / 订单日期</th>
                                    <td>{{ $order->order_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Status / 状态</th>
                                    <td>
                                        <span
                                            class="badge {{ $order->order_status == 'Active' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>DPP / 基本税额</th>
                                    <td>Rp {{ number_format($order->dpp, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>PPN (Calculated) / 增值税（计算得出）</th>
                                    <td>
                                        {{ number_format((($order->total_amount - $order->dpp) / $order->dpp) * 100, 2) }}%
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Amount / 总金额</th>
                                    <td class="fw-bold text-primary">Rp {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Person in Charge / 负责人</th>
                                    <td>{{ $order->personInCharge->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Description / 描述</th>
                                    <td>{{ $order->description ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- File Information -->
                    <div class="table-responsive mt-4">
                        <h5>Attached Files / 附件</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>File Name / 文件名称</th>
                                    <th>Description / 描述</th>
                                    <th>Uploaded At / 上传日期</th>
                                    <th>Actions / 操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->files as $file)
                                    <tr>
                                        <td>{{ $file->file_name }}</td>
                                        <td>{{ $file->description ?? '-' }}</td>
                                        <td>{{ $file->uploaded_at ? $file->uploaded_at->format('Y-m-d H:i:s') : '-' }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="ti ti-download"></i> Download / 下载
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No files attached / 无附件</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-3">
                        <a href="{{ route('customer-order.index') }}" class="btn btn-primary px-5">
                            <i class="ti ti-arrow-left"></i> Back / 返回
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
