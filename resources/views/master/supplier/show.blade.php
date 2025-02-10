@extends('layouts.admin.app')
@section('title', 'Supplier Details / 供应商详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Supplier Details / 供应商详情</h5>
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Back / 返回
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Supplier Name / 供应商名称</th>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Tax Number / 税号</th>
                                <td>{{ $supplier->tax_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Address / 地址</th>
                                <td>{{ $supplier->address }}</td>
                            </tr>
                            <tr>
                                <th>Phone / 电话</th>
                                <td>{{ $supplier->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email / 电子邮件</th>
                                <td>{{ $supplier->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Website / 网站</th>
                                <td>{{ $supplier->website ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Business Type / 业务类型</th>
                                <td>{{ $supplier->business_type }}</td>
                            </tr>
                            <tr>
                                <th>Contact Person / 联系人</th>
                                <td>{{ $supplier->contact_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Contact Position / 联系人职位</th>
                                <td>{{ $supplier->contact_position ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Contact Phone / 联系人电话</th>
                                <td>{{ $supplier->contact_phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Bank / 银行</th>
                                <td>{{ $supplier->bank }}</td>
                            </tr>
                            <tr>
                                <th>Account Number / 银行账户号码</th>
                                <td>{{ $supplier->account_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Account Holder / 账户持有人</th>
                                <td>{{ $supplier->account_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Currency / 货币</th>
                                <td>{{ $supplier->currency ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Payment Terms / 付款条款</th>
                                <td>{{ $supplier->payment_terms ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status / 状态</th>
                                <td>
                                    {!! $supplier->status ?? '-' !!}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Section for Uploaded Files -->
                    @if ($supplier->files->isNotEmpty())
                        <h5 class="mt-4">Uploaded Files / 上传的文件</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>File Name / 文件名称</th>
                                    <th>Description / 描述</th>
                                    <th>Download / 下载</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplier->files as $file)
                                    <tr>
                                        <td>{{ $file->file_name }}</td>
                                        <td>{{ $file->description ?? '-' }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-download"></i> Download / 下载
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No files uploaded / 没有上传的文件</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
