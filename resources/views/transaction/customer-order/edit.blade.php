@extends('layouts.admin.app')
@section('title', 'Edit Customer Order / 编辑客户订单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title')</h5>
                        <small class="text-muted">Update customer order details / 更新客户订单详细信息</small>
                    </div>
                    <a href="{{ route('customer-order.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('customer-order.update', $order->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There are some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Customer Order Fields -->
                        <div class="row">
                            <!-- Order Number -->
                            <div class="mb-3 col-md-6">
                                <label for="order_number" class="form-label">Order Number / 订单编号</label>
                                <input class="form-control" type="text" id="order_number" name="order_number"
                                    value="{{ $order->order_number }}" readonly />
                            </div>

                            <!-- Customer -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_id" class="form-label">Customer / 客户 <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    @foreach ($customers as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $order->customer_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Project Name -->
                            <div class="mb-3 col-md-6">
                                <label for="project_name" class="form-label">Project Name / 项目名称</label>
                                <input class="form-control" type="text" id="project_name" name="project_name"
                                    value="{{ $order->project_name }}" />
                            </div>

                            <!-- Job Category -->
                            <div class="mb-3 col-md-6">
                                <label for="job_category_id" class="form-label">Job Category / 作业类别</label>
                                <select class="form-select" id="job_category_id" name="job_category_id">
                                    @foreach ($jobCategories as $id => $category)
                                        <option value="{{ $id }}"
                                            {{ $order->job_category_id == $id ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Order Date -->
                            <div class="mb-3 col-md-6">
                                <label for="order_date" class="form-label">Order Date / 订单日期 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="order_date" name="order_date"
                                    value="{{ $order->order_date->format('Y-m-d') }}" required />
                            </div>

                            <!-- DPP -->
                            <div class="mb-3 col-md-6">
                                <label for="dpp" class="form-label">DPP / 基本税额 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="number" step="0.01" id="dpp" name="dpp"
                                    value="{{ $order->dpp }}" required />
                            </div>
                        </div>

                        <div class="row">
                            <!-- PPN -->
                            <div class="mb-3 col-md-6">
                                <label for="ppn" class="form-label">PPN (%) / 增值税 (%)</label>
                                <input class="form-control" type="number" step="0.01" id="ppn" name="ppn"
                                    value="{{ number_format((($order->total_amount - $order->dpp) / $order->dpp) * 100, 2) }}"
                                    required />
                            </div>

                            <!-- Total -->
                            <div class="mb-3 col-md-6">
                                <label for="total" class="form-label">Total / 总金额</label>
                                <input class="form-control" type="number" step="0.01" id="total" name="total"
                                    value="{{ $order->total_amount }}" readonly />
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="files" class="form-label">Files / 文件</label>
                                <table class="table table-bordered" id="file-table">
                                    <thead>
                                        <tr>
                                            <th>File Name / 文件名称</th>
                                            <th>Upload File / 上传文件</th>
                                            <th>Description / 描述</th>
                                            <th>Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Existing Files -->
                                        @foreach ($order->files as $file)
                                            <tr>
                                                <td>
                                                    <input type="text" name="file_names[{{ $file->id }}]"
                                                        class="form-control" value="{{ $file->file_name }}" readonly />
                                                </td>
                                                <td>
                                                    <input type="file" name="files[{{ $file->id }}]"
                                                        class="form-control" />
                                                    @if ($file->file_path)
                                                        <a href="{{ asset('storage/' . $file->file_path) }}"
                                                            target="_blank" class="btn btn-primary btn-sm mt-2">View /
                                                            查看</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <textarea name="descriptions[{{ $file->id }}]" class="form-control" rows="1">{{ $file->description }}</textarea>
                                                </td>
                                                <td>
                                                    @if ($file->file_name != 'Customer Order File')
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-file-row"
                                                            data-id="{{ $file->id }}">Delete / 删除</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            disabled>Cannot Delete / 无法删除</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-file-row">
                                    <i class="fa fa-plus-circle"></i> Add File / 添加文件
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        @if ($order->order_status == 'Draft')
                            <div class="my-2">
                                <button type="submit" class="btn btn-primary px-5 me-2" name="order_status"
                                    value="Active">Submit / 提交</button>
                                <button type="submit" class="btn btn-secondary px-5 me-2" name="order_status"
                                    value="Draft">Save as Draft / 保存草稿</button>
                                <a href="{{ route('customer-order.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                            </div>
                        @else
                            <div class="my-2">
                                <button type="submit" class="btn btn-primary px-5 me-2" name="order_status"
                                value="Active">Update / 更新</button>
                                <a href="{{ route('customer-order.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileTable = document.getElementById('file-table').querySelector('tbody');
            const addFileButton = document.getElementById('add-file-row');

            // Add a new empty file row
            addFileButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <input type="text" name="file_names[]" class="form-control" placeholder="Enter file name / 输入文件名称" required />
                    </td>
                    <td>
                        <input type="file" name="files[]" class="form-control" required />
                    </td>
                    <td>
                        <textarea name="descriptions[]" class="form-control" rows="1" placeholder="Description (Optional) / 描述（可选）"></textarea>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-file-row">Delete / 删除</button>
                    </td>
                `;
                fileTable.appendChild(newRow);
            });

            // Remove a file row
            fileTable.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-file-row')) {
                    event.target.closest('tr').remove();
                }
            });
        });
    </script>
@endpush
