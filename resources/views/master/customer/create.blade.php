@extends('layouts.admin.app')
@section('title', 'Customer / 客户')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Add a new customer to the system / 将新客户添加到系统中</small>
                    </div>
                    <a href="{{ route('customer.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀！</strong> There are some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Customer Fields -->
                        <div class="row">
                            <!-- Customer Code (Required) -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_code" class="form-label">Customer Code / 客户代码 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="customer_code" name="customer_code"
                                    value="{{ old('customer_code') }}" placeholder="Enter customer code / 输入客户代码"
                                    required />
                            </div>

                            <!-- Customer Name (Required) -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_name" class="form-label">Customer Name / 客户名称 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="customer_name" name="customer_name"
                                    value="{{ old('customer_name') }}" placeholder="Enter customer name / 输入客户名称"
                                    required />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Customer Address -->
                            <div class="mb-3 col-md-12">
                                <label for="customer_address" class="form-label">Customer Address / 客户地址</label>
                                <textarea class="form-control" id="customer_address" name="customer_address" rows="3"
                                    placeholder="Enter customer address / 输入客户地址">{{ old('customer_address') }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Customer Tax Number -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_tax_number" class="form-label">Tax Number / 税号</label>
                                <input class="form-control" type="text" id="customer_tax_number"
                                    name="customer_tax_number" value="{{ old('customer_tax_number') }}"
                                    placeholder="Enter tax number / 输入税号 (e.g. 1234567890)"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            </div>

                            <!-- Contact Person -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_contact" class="form-label">Contact Person / 联系人</label>
                                <input class="form-control" type="text" id="customer_contact" name="customer_contact"
                                    value="{{ old('customer_contact') }}" placeholder="Enter contact person / 输入联系人" />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Phone Number -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_phone_number" class="form-label">Phone Number / 电话号码</label>
                                <input class="form-control phone-number" type="tel" id="customer_phone_number"
                                    name="customer_phone_number" value="{{ old('customer_phone_number') }}"
                                    placeholder="Enter phone number with country code / 输入电话号码带有国家代码 (e.g. +6281234567890)" />
                            </div>

                            <!-- Email -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_email" class="form-label">Email Address / 邮箱地址</label>
                                <input class="form-control" type="email" id="customer_email" name="customer_email"
                                    value="{{ old('customer_email') }}" placeholder="Enter email address / 输入邮箱地址" />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Status -->
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active / 活跃
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive / 不活跃
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">File Upload / 文件上传</label>
                                <p class="text-muted small">
                                    Upload mandatory files (Tax Number File and Company Profile), and add additional files
                                    if needed. /
                                    上传必需的文件（税号文件和公司简介），如有需要可以添加其他文件。
                                </p>
                                <table class="table table-bordered" id="file-upload-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30%;">File Name / 文件名称</th>
                                            <th style="width: 40%;">File / 文件</th>
                                            <th style="width: 20%;">Description / 描述</th>
                                            <th style="width: 10%;">Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Mandatory Files / 必需文件 -->
                                        <tr>
                                            <td>
                                                Tax Number File / 税号文件 <span class="text-danger">*</span>
                                                <input type="hidden" name="file_names[tax_number]"
                                                    value="Tax Document">
                                            </td>
                                            <td>
                                                <input type="file" name="files[tax_number]" class="form-control"
                                                    required accept=".pdf,.jpg,.jpeg,.png,.zip" />
                                            </td>
                                            <td>
                                                <input type="text" name="descriptions[tax_number]"
                                                    class="form-control" placeholder="Description (Optional) / 描述（可选）" />
                                            </td>
                                            <td class="text-center">-</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Company Profile / 公司简介 <span class="text-danger">*</span>
                                                <input type="hidden" name="file_names[company_profile]"
                                                    value="Company Profile">
                                            </td>
                                            <td>
                                                <input type="file" name="files[company_profile]" class="form-control"
                                                    required accept=".pdf,.jpg,.jpeg,.png,.zip" />
                                            </td>
                                            <td>
                                                <input type="text" name="descriptions[company_profile]"
                                                    class="form-control" placeholder="Description (Optional) / 描述（可选）" />
                                            </td>
                                            <td class="text-center">-</td>
                                        </tr>
                                        <!-- Additional Files / 附加文件 -->
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-file-row">
                                    <i class="fa fa-plus-circle"></i> Add File / 添加文件
                                </button>
                            </div>
                        </div>


                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                <button type="button" class="btn btn-danger btn-sm remove-file-row">
                    <i class="fa fa-trash-alt"></i>
                </button>
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
@endsection
