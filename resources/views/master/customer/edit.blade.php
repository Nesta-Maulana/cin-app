@extends('layouts.admin.app')
@section('title', 'Edit Customer / 编辑客户')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title')</h5>
                        <small class="text-muted">Update customer details / 更新客户详细信息</small>
                    </div>
                    <a href="{{ route('customer.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('customer.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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

                        <!-- Customer Fields -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="customer_code" class="form-label">Customer Code / 客户代码</label>
                                <input class="form-control" type="text" id="customer_code" name="customer_code"
                                    value="{{ old('customer_code', $data->customer_code) }}"
                                    placeholder="Enter customer code / 输入客户代码" required />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="customer_name" class="form-label">Customer Name / 客户名称</label>
                                <input class="form-control" type="text" id="customer_name" name="customer_name"
                                    value="{{ old('customer_name', $data->customer_name) }}"
                                    placeholder="Enter customer name / 输入客户名称" required />
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="customer_address" class="form-label">Customer Address / 客户地址</label>
                                <textarea class="form-control" id="customer_address" name="customer_address" rows="3"
                                    placeholder="Enter customer address / 输入客户地址">{{ old('customer_address', $data->customer_address) }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="customer_tax_number" class="form-label">Tax Number / 税号</label>
                                <input class="form-control" type="text" id="customer_tax_number"
                                    name="customer_tax_number"
                                    value="{{ old('customer_tax_number', $data->customer_tax_number) }}"
                                    placeholder="Enter tax number / 输入税号" />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="customer_contact" class="form-label">Contact Person / 联系人</label>
                                <input class="form-control" type="text" id="customer_contact" name="customer_contact"
                                    value="{{ old('customer_contact', $data->customer_contact) }}"
                                    placeholder="Enter contact person / 输入联系人" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="customer_phone_number" class="form-label">Phone Number / 电话号码</label>
                                <input class="form-control" type="text" id="customer_phone_number"
                                    name="customer_phone_number"
                                    value="{{ old('customer_phone_number', $data->customer_phone_number) }}"
                                    placeholder="Enter phone number / 输入电话号码" />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="customer_email" class="form-label">Email Address / 邮箱地址</label>
                                <input class="form-control" type="email" id="customer_email" name="customer_email"
                                    value="{{ old('customer_email', $data->customer_email) }}"
                                    placeholder="Enter email address / 输入邮箱地址" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1"
                                        {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>Active / 活跃
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>Inactive / 不活跃
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Files / 文件</label>
                                <table class="table table-bordered" id="file-upload-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>File Name / 文件名称</th>
                                            <th>File / 文件</th>
                                            <th>Description / 描述</th>
                                            <th>Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="file-container">
                                        <!-- Tax Number File -->
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="file_names[tax_number]"
                                                    value="Tax Number" readonly />
                                            </td>
                                            <td>
                                                <input type="file" class="form-control" name="files[tax_number]" />
                                                @if ($data->files->firstWhere('file_name', 'Tax Document'))
                                                    <input type="hidden" class="form-control" name="ids[tax_number]"
                                                        value="{{ $data->files->firstWhere('file_name', 'Tax Document')->id }}"
                                                        readonly />
                                                    <a href="{{ asset('storage/' . $data->files->firstWhere('file_name', 'Tax Document')->file_path) }}"
                                                        target="_blank">View File / 查看文件</a>
                                                @endif
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="descriptions[tax_number]" rows="1">{{ $data->files->firstWhere('file_name', 'Tax Document')->description ?? null }}</textarea>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" disabled>Cannot
                                                    Delete / 无法删除</button>
                                            </td>
                                        </tr>

                                        <!-- Company Profile File -->
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                    name="file_names[company_profile]" value="Company Profile" readonly />
                                            </td>
                                            <td>
                                                <input type="file" class="form-control"
                                                    name="files[company_profile]" />
                                                @if ($data->files->firstWhere('file_name', 'Company Profile'))
                                                    <input type="hidden" class="form-control"
                                                        name="ids[company_profile]"
                                                        value="{{ $data->files->firstWhere('file_name', 'Company Profile')->id }}"
                                                        readonly />
                                                    <a href="{{ asset('storage/' . $data->files->firstWhere('file_name', 'Company Profile')->file_path) }}"
                                                        target="_blank">View File / 查看文件</a>
                                                @endif
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="descriptions[company_profile]" rows="1">{{ $data->files->firstWhere('file_name', 'Company Profile')->description ?? null }}</textarea>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" disabled>Cannot
                                                    Delete / 无法删除</button>
                                            </td>
                                        </tr>

                                        <!-- Dynamic Files -->
                                        @foreach ($data->files->whereNotIn('file_name', ['Tax Document', 'Company Profile']) as $file)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="file_names[{{ $file->file_name }}]"
                                                        value="{{ $file->file_name }}" />
                                                </td>
                                                <td>
                                                    <input type="file" class="form-control"
                                                        name="files[{{ $file->file_name }}]" />
                                                    <input type="hidden" class="form-control"
                                                        name="ids[{{ $file->file_name }}]" value="{{ $file->id }}"
                                                        readonly />
                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        target="_blank">View File / 查看文件</a>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="descriptions[{{ $file->file_name }}]" rows="1">{{ $file->description }}</textarea>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-file-row">Delete / 删除</button>
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
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update / 更新</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Add event listeners for dynamic file upload functionality / 添加动态文件上传功能的事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            const fileTable = document.querySelector(
                '#file-upload-table tbody'); // Table body for file rows / 文件行的表格主体
            const addFileButton = document.querySelector(
                '#add-file-row'); // Button to add new file rows / 添加新文件行的按钮
            let fileIndex = 1; // Index for additional files / 附加文件的索引

            // Add a new file row / 添加新的文件行
            addFileButton.addEventListener('click', function() {
                const newRow = document.createElement('tr'); // Create a new table row / 创建一个新的表格行

                // Define the structure of the new row / 定义新行的结构
                newRow.innerHTML = `
                    <td>
                        <input type="text" name="file_names[${fileIndex}]" class="form-control"
                            placeholder="Enter file name / 输入文件名称" required />
                        <input type="hidden" name="ids[${fileIndex}]" class="form-control"/>
                    </td>
                    <td>
                        <input type="file" name="files[${fileIndex}]" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,.zip" required />
                    </td>
                    <td>
                        <input type="text" name="descriptions[${fileIndex}]" class="form-control"
                            placeholder="Description (Optional) / 描述（可选）" />
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-file-row">Delete / 删除</button>
                    </td>
                `;

                fileTable.appendChild(newRow); // Append the new row to the table / 将新行附加到表格中
                fileIndex++; // Increment the file index / 增加文件索引
            });

            // Remove a file row / 删除文件行
            fileTable.addEventListener('click', function(event) {
                if (event.target.closest('.remove-file-row')) {
                    const row = event.target.closest('tr'); // Identify the row to remove / 确定要删除的行
                    row.remove(); // Remove the row / 删除行
                }
            });
        });
    </script>
@endpush
