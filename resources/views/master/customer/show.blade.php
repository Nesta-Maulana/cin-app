@extends('layouts.admin.app')
@section('title', 'Customer Details / 客户详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Customer Details / 客户详情</h5>
                        <small class="text-muted">View detailed information about the customer / 查看客户的详细信息</small>
                    </div>
                    <a href="{{ route('customer.index') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <div class="card-body">
                    <!-- Customer Information -->
                    <h6 class="text-primary">Customer Information / 客户信息</h6>
                    <table class="table  mb-4">
                        <tbody>
                            <tr>
                                <th style="width: 20%;">Customer Code / 客户代码</th>
                                <td>{{ $customer->customer_code }}</td>
                            </tr>
                            <tr>
                                <th>Customer Name / 客户名称</th>
                                <td>{{ $customer->customer_name }}</td>
                            </tr>
                            <tr>
                                <th>Address / 地址</th>
                                <td>{{ $customer->customer_address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tax Number / 税号</th>
                                <td>{{ $customer->customer_tax_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Contact Person / 联系人</th>
                                <td>{{ $customer->customer_contact ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Phone Number / 电话号码</th>
                                <td>{{ $customer->customer_phone_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email Address / 电子邮件地址</th>
                                <td>{{ $customer->customer_email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status / 状态</th>
                                <td>
                                    <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $customer->is_active ? 'Active / 活跃' : 'Inactive / 不活跃' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Customer Files -->
                    <h6 class="text-primary">Customer Files / 客户文件</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>File Name / 文件名</th>
                                    <th>Description / 描述</th>
                                    <th>File Type / 文件类型</th>
                                    <th>File Size / 文件大小</th>
                                    <th>Uploaded At / 上传时间</th>
                                    <th>Action / 操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customer->files as $index => $file)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $file->file_name }}</td>
                                        <td>{{ $file->description ?? '-' }}</td>
                                        <td>{{ strtoupper($file->file_type) }}</td>
                                        <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                                        <td> {{ $file->uploaded_at ? $file->uploaded_at->format('Y-m-d H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                                class="btn btn-info btn-sm">
                                                <i class="ti ti-download"></i> Download / 下载
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No files available / 无可用文件</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
