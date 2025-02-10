@extends('layouts.admin.app')
@section('title', 'Supplier / 供应商')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title') / 创建供应商</h5>
                        <small class="text-muted">Fill in the supplier details / 填写供应商信息</small>
                    </div>
                    <a href="{{ route('supplier.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="fa fa-x fa-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('supplier.store') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀!</strong> There were some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Supplier Name -->
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">
                                    Supplier Name / 供应商名称
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Enter supplier name / 输入供应商名称" required />
                            </div>

                            <!-- Tax Number -->
                            <div class="mb-3 col-md-6">
                                <label for="tax_number" class="form-label">Tax Number / 税号</label>
                                <input class="form-control" type="text" id="tax_number" name="tax_number"
                                    value="{{ old('tax_number') }}" placeholder="Enter tax number / 输入税号" />
                            </div>

                            <!-- Address -->
                            <div class="mb-3 col-md-12">
                                <label for="address" class="form-label">
                                    Address / 地址
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="address" name="address" placeholder="Enter supplier address / 输入供应商地址" required>{{ old('address') }}</textarea>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label">
                                    Phone / 电话
                                </label>
                                <input class="form-control" type="text" id="phone" name="phone"
                                    value="{{ old('phone') }}" placeholder="Enter phone number / 输入电话号码" />
                            </div>

                            <!-- Email -->
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email / 电子邮件</label>
                                <input class="form-control" type="email" id="email" name="email"
                                    value="{{ old('email') }}" placeholder="Enter email / 输入电子邮件" />
                            </div>

                            <!-- Website -->
                            <div class="mb-3 col-md-6">
                                <label for="website" class="form-label">Website / 网站</label>
                                <input class="form-control" type="text" id="website" name="website"
                                    value="{{ old('website') }}" placeholder="Enter website URL / 输入网站URL" />
                            </div>

                            <!-- Business Type -->
                            <div class="mb-3 col-md-6">
                                <label for="business_type" class="form-label">
                                    Business Type / 业务类型
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="text" id="business_type" name="business_type"
                                    value="{{ old('business_type') }}" placeholder="Enter business type / 输入业务类型" />
                            </div>

                            <!-- Contact Name -->
                            <div class="mb-3 col-md-6">
                                <label for="contact_name" class="form-label">
                                    Contact Person / 联系人
                                </label>
                                <input class="form-control" type="text" id="contact_name" name="contact_name"
                                    value="{{ old('contact_name') }}" placeholder="Enter contact person / 输入联系人" />
                            </div>

                            <!-- Contact Position -->
                            <div class="mb-3 col-md-6">
                                <label for="contact_position" class="form-label">Position / 职位</label>
                                <input class="form-control" type="text" id="contact_position" name="contact_position"
                                    value="{{ old('contact_position') }}" placeholder="Enter position / 输入职位" />
                            </div>

                            <!-- Contact Phone -->
                            <div class="mb-3 col-md-6">
                                <label for="contact_phone" class="form-label">Contact Phone / 联系人电话</label>
                                <input class="form-control" type="text" id="contact_phone" name="contact_phone"
                                    value="{{ old('contact_phone') }}" placeholder="Enter contact phone / 输入联系人电话" />
                            </div>

                            <!-- Bank -->
                            <div class="mb-3 col-md-6">
                                <label for="bank" class="form-label">Bank / 银行</label>
                                <select class="form-select" id="bank" name="bank">
                                    <option value="" disabled selected>Choose Bank / 选择银行</option>
                                    <option value="BCA">BCA</option>
                                    <option value="BNI">BNI</option>
                                    <option value="BRI">BRI</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="CIMB Niaga">CIMB Niaga</option>
                                    <option value="Danamon">Danamon</option>
                                    <option value="Permata">Permata</option>
                                    <option value="OCBC NISP">OCBC NISP</option>
                                    <option value="UOB">UOB</option>
                                    <option value="Maybank">Maybank</option>
                                    <option value="HSBC">HSBC</option>
                                    <option value="Standard Chartered">Standard Chartered</option>
                                    <option value="Citibank">Citibank</option>
                                    <option value="Panin">Panin</option>
                                    <option value="Bank Jateng">Bank Jateng</option>
                                    <option value="Bank Jabar">Bank Jabar</option>
                                    <option value="Bank Banten">Bank Banten</option>
                                    <option value="Bank DKI">Bank DKI</option>
                                </select>
                            </div>
                            <!-- Account Number -->
                            <div class="mb-3 col-md-6">
                                <label for="account_number" class="form-label">Account Number / 银行账户号码</label>
                                <input class="form-control" type="text" id="account_number" name="account_number"
                                    value="{{ old('account_number') }}" placeholder="Enter account number / 输入银行账户号码" />
                            </div>

                            <!-- Account Name -->
                            <div class="mb-3 col-md-6">
                                <label for="account_name" class="form-label">Account Holder / 账户持有人</label>
                                <input class="form-control" type="text" id="account_name" name="account_name"
                                    value="{{ old('account_name') }}"
                                    placeholder="Enter account holder name / 输入账户持有人" />
                            </div>

                            <!-- Currency -->
                            <div class="mb-3 col-md-6">
                                <label for="currency" class="form-label">Currency / 货币</label>
                                <select class="form-select select2" id="currency" name="currency">
                                    <option value="" disabled selected>Select Currency / 选择货币</option>
                                    @foreach (getCurrency() as $key => $currency)
                                        <option value="{{ $key }}">
                                            {{ "$key - $currency" }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Terms -->
                            <div class="mb-3 col-md-6">
                                <label for="payment_terms" class="form-label">Payment Terms / 付款条款</label>
                                <input class="form-control" type="text" id="payment_terms" name="payment_terms"
                                    value="{{ old('payment_terms') }}" placeholder="Enter payment terms / 输入付款条款" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">File Upload / 文件上传</label>
                                {{-- <p class="text-muted small">
                                    Upload mandatory files (Tax Number File and Company Profile), and add additional files
                                    if needed. /
                                    上传必需的文件（税号文件和公司简介），如有需要可以添加其他文件。
                                </p> --}}
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
                            <a href="{{ route('supplier.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
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
