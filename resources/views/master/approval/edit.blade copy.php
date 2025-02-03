@extends('layouts.admin.app')
@section('title', 'Approval')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title')</h5>
                        <small class="text-muted">Perbarui @yield('title')</small>
                    </div>
                    <a href="{{ route('approval.index') }}" class="btn p-0" title="Kembali">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('approval.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> Terdapat beberapa masalah dengan inputan Anda.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Approval Header -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Nama Approval / 审批名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ $data->name }}" placeholder="Nama Approval / 审批名称" autofocus required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="event" class="form-label">Event / 事件 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="event" name="event"
                                    value="{{ $data->event }}" placeholder="Event / 事件" required />
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="class_name" class="form-label">Model Class / 模型类 <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="class_name" name="class_name" required>
                                    <option value="{{ $data->class_name }}">{{ $data->class_name }}</option>
                                    <!-- Other model options fetched dynamically -->
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="column_update" class="form-label">Column to Update / 更新的列</label>
                                <select class="form-select" id="column_update" name="column_update">
                                    <option value="{{ $data->column_update }}">{{ $data->column_update }}</option>
                                    <!-- Columns fetched dynamically -->
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description / 描述</label>
                                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Add description / 添加描述">{{ $data->description }}</textarea>
                            </div>
                        </div>

                        <!-- Approval Levels -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="levels" class="form-label">Approval Levels / 审批级别</label>
                                <table class="table table-bordered" id="approval-levels-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order / 顺序</th>
                                            <th>Approver Type / 审批人类型</th>
                                            <th>Reference ID / 参考ID</th>
                                            <th>On Approve / 批准时更新</th>
                                            <th>On Reject / 拒绝时更新</th>
                                            <th>Required / 必需</th>
                                            <th>Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->approvalLevels as $level)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="ids[]" class="form-control"
                                                        value="{{ $level->id }}" readonly />
                                                    <input type="number" name="orders[]" class="form-control"
                                                        value="{{ $level->hierarchy_order }}" readonly />
                                                </td>
                                                <td>
                                                    <select name="approver_types[]" class="form-select approver-type"
                                                        required>
                                                        <option value="App\Models\Role"
                                                            {{ $level->class_name_approver_type == 'App\Models\Role' ? 'selected' : '' }}>
                                                            Role</option>
                                                        <option value="App\Models\User"
                                                            {{ $level->class_name_approver_type == 'App\Models\User' ? 'selected' : '' }}>
                                                            User</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="reference_ids[]" class="form-select reference-id"
                                                        required>
                                                        <option value="{{ $level->approver_reference_id }}">
                                                            {{ $level->approver_reference_id }}</option>
                                                        <!-- Dynamic references -->
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="on_approves[]" class="form-control"
                                                        value="{{ $level->updated_value_on_approve }}" />
                                                </td>
                                                <td>
                                                    <input type="text" name="on_rejects[]" class="form-control"
                                                        value="{{ $level->updated_value_on_reject }}" />
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="requireds[]" class="form-check-input" value="1" {{ $level->required ? 'checked' : '' }} />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-level-row">
                                                        Delete / 删除
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-level-row">
                                    <i class="fa fa-plus-circle"></i> Add Level / 添加级别
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update</button>
                            <a href="{{ route('approval.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#approval-levels-table tbody');
            const addLevelButton = document.getElementById('add-level-row');

            // Add a new approval level row
            addLevelButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                const currentOrder = tableBody.querySelectorAll('tr').length + 1; // Auto-increment order

                newRow.innerHTML = `
            <td>
                <input type="hidden" name="ids[]" class="form-control" readonly />
                <input type="number" name="orders[]" class="form-control" value="${currentOrder}" readonly />
            </td>
            <td>
                <select name="approver_types[]" class="form-select approver-type" required>
                    <option value="" disabled selected>Select Type / 选择类型</option>
                    <option value="App\\Models\\Role">Role</option>
                    <option value="App\\Models\\User">User</option>
                </select>
            </td>
            <td>
                <select name="reference_ids[]" class="form-select reference-id" required>
                    <option value="" disabled selected>Select Reference / 选择参考</option>
                </select>
            </td>
            <td>
                <input type="text" name="on_approves[]" class="form-control" placeholder="Enter update on approve / 输入批准时更新" required />
            </td>
            <td>
                <input type="text" name="on_rejects[]" class="form-control" placeholder="Enter update on reject / 输入拒绝时更新" required />
            </td>
            <td class="text-center">
                <input type="checkbox" name="requireds[]" class="form-check-input" value="1" checked />
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-level-row">Delete / 删除</button>
            </td>
        `;
                tableBody.appendChild(newRow);
            });

            // Remove a row
            tableBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-level-row')) {
                    const row = event.target.closest('tr');
                    row.remove();

                    // Recalculate order numbers
                    [...tableBody.querySelectorAll('tr')].forEach((row, index) => {
                        row.querySelector('input[name="orders[]"]').value = index + 1;
                    });
                }
            });

            // Load Reference IDs dynamically
            tableBody.addEventListener('change', function(event) {
                if (event.target.classList.contains('approver-type')) {
                    const approverType = event.target.value;
                    const row = event.target.closest('tr');
                    const referenceDropdown = row.querySelector('.reference-id');

                    referenceDropdown.innerHTML =
                        '<option value="" disabled selected>Loading... / 加载中...</option>';

                    // Fetch data from backend
                    fetch(`/get-references?type=${approverType}`)
                        .then(response => response.json())
                        .then(data => {
                            referenceDropdown.innerHTML =
                                '<option value="" disabled selected>Select Reference / 选择参考</option>';
                            data.references.forEach(ref => {
                                referenceDropdown.innerHTML +=
                                    `<option value="${ref.id}">${ref.name}</option>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching references:', error);
                            alert('Failed to load references! / 无法加载参考！');
                        });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const modelDropdown = document.getElementById('class_name');
            const columnDropdown = document.getElementById('column_update');

            modelDropdown.addEventListener('change', function() {
                const selectedModel = this.value;

                // Reset column dropdown
                columnDropdown.innerHTML =
                    `<option value="" disabled selected>Loading... / 加载中...</option>`;

                // Fetch columns based on model
                $.ajax({
                    url: "{{ route('get-columns-by-model') }}", // Route to fetch columns
                    type: "GET",
                    data: {
                        model: selectedModel
                    },
                    success: function(data) {
                        if (data.success) {
                            columnDropdown.innerHTML =
                                `<option value="" disabled selected>Select a column / 选择列</option>`;
                            data.columns.forEach(column => {
                                columnDropdown.innerHTML +=
                                    `<option value="${column}">${column}</option>`;
                            });
                        } else {
                            alert('Failed to load columns! / 无法加载列！');
                        }
                    },
                    error: function() {
                        alert('An error occurred! / 发生错误！');
                    }
                });
            });
        });
    </script>
@endpush
