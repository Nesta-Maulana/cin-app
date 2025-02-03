@extends('layouts.admin.app')
@section('title', 'Create Approval / 创建审批')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title') / 创建@yield('title')</h5>
                        <small class="text-muted">Add a new approval process / 添加新审批流程</small>
                    </div>
                    <a href="{{ route('approval.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('approval.store') }}" method="POST" id="approvalForm">
                        @csrf

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

                        <!-- Approval Header -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Approval Name / 审批名称 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Enter approval name / 输入审批名称" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="event" class="form-label">Event / 事件 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="event" name="event"
                                    value="{{ old('event') }}" placeholder="Enter event name / 输入事件名称" required />
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="class_name" class="form-label">Model Class / 模型类 <span
                                        class="text-danger">*</span></label>
                                <select id="class_name" name="class_name" class="form-select" required>
                                    <option value="" disabled selected>Select a model / 选择模型</option>
                                    @foreach (getModels() as $model)
                                        <option value="{{ $model }}"
                                            {{ old('class_name') == $model ? 'selected' : '' }}>
                                            {{ $model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Approval Levels -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="levels" class="form-label">Approval Levels / 审批级别</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="approval-levels-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-nowrap">Order / 顺序</th>
                                                <th class="text-nowrap">Approver Type / 审批人类型</th>
                                                <th class="text-nowrap">Reference ID / 参考ID</th>
                                                <th style="min-width: 400px;" class="text-center">On Approve / 批准时更新</th>
                                                <th style="min-width: 400px;" class="text-center">On Reject / 拒绝时更新</th>
                                                <th class="text-nowrap">Department Specific / 具体部门</th>
                                                <th class="text-nowrap">Required / 必需</th>
                                                <th class="text-nowrap">Action / 操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic rows will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-level-row">
                                    <i class="fa fa-plus-circle"></i> Add Level / 添加级别
                                </button>
                            </div>
                        </div>

                        <div class="my-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('approval.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
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
            let columns = [];

            // Fetch columns based on selected model
            document.getElementById('class_name').addEventListener('change', function() {
                const selectedModel = this.value;
                fetchColumns(selectedModel);
            });

            function fetchColumns(model) {
                $.ajax({
                    url: "{{ route('get-columns-by-model') }}",
                    type: "GET",
                    data: {
                        model
                    },
                    success: function(data) {
                        if (data.success) {
                            columns = data.columns;
                        } else {
                            alert('Failed to load columns! / 无法加载列！');
                        }
                    },
                    error: function() {
                        alert('An error occurred! / 发生错误！');
                    }
                });
            }

            addLevelButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                const currentOrder = tableBody.querySelectorAll('tr').length + 1;

                newRow.innerHTML = `
            <td>
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
                <button type="button" class="btn btn-info btn-sm add-column" data-target="on-approve">
                    + Add Column
                </button>
                <div class="on-approve-columns"></div>
            </td>
            <td>
                <button type="button" class="btn btn-warning btn-sm add-column" data-target="on-reject">
                    + Add Column
                </button>
                <div class="on-reject-columns"></div>
            </td>
            <td>
                <select name="department_id[]" class="form-select">
                    <option value="-" selected>No Specific Department / 不指定部门</option>
                    <option value="0">Spesific Requester Department / 指定申请人部门</option>
                    @foreach ($departments as $department_id => $department)
                        <option value="{{ $department_id }}">{{ $department }}</option>
                    @endforeach
                </select>
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

            tableBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-level-row')) {
                    const row = event.target.closest('tr');
                    row.remove();

                    // Recalculate order numbers
                    [...tableBody.querySelectorAll('tr')].forEach((row, index) => {
                        row.querySelector('input[name="orders[]"]').value = index + 1;
                    });
                }
                if (event.target.classList.contains('add-column')) {
                    const target = event.target.dataset.target;
                    const row = event.target.closest('tr'); // Ambil row yang benar
                    const container = row.querySelector(`.${target}-columns`); // Hanya edit row ini

                    const columnRow = document.createElement('div');
                    columnRow.classList.add('d-flex', 'mt-2');

                    columnRow.innerHTML = `
                <select class="form-select me-2" name="${target}_columns[]">
                    ${columns.map(col => `<option value="${col}">${col}</option>`).join('')}
                </select>
                <input type="text" class="form-control me-2" name="${target}_values[]" placeholder="Value">
                <button type="button" class="btn btn-danger btn-sm remove-column">X</button>
            `;

                    container.appendChild(columnRow); // Tambahkan tanpa mereset data sebelumnya
                }

                if (event.target.classList.contains('remove-column')) {
                    event.target.closest('.d-flex').remove();
                }
            });

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
    </script>
@endpush
