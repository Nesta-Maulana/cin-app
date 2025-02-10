@extends('layouts.admin.app')
@section('title', 'Create Warehouse / 创建仓库')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title') / 创建@yield('title')</h5>
                        <small class="text-muted">Add a new warehouse / 添加新仓库</small>
                    </div>
                    <a href="{{ route('warehouse.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('warehouse.store') }}" method="POST">
                        @csrf

                        <!-- Error Handling -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀！</strong> There are some problems with your input. /
                                你的输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Warehouse Information -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Warehouse Name / 仓库名称 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Enter warehouse name / 输入仓库名称" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active / 启用
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive / 不启用
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="location" class="form-label">Location / 位置</label>
                                <textarea class="form-control" id="location" name="location" rows="3"
                                    placeholder="Enter warehouse location / 输入仓库位置">{{ old('location') }}</textarea>
                            </div>
                        </div>

                        <!-- Warehouse Sections -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Warehouse Sections / 仓库分区</label>
                                <table class="table table-bordered" id="sections-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Section Name / 分区名称</th>
                                            <th>Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Jika ada error sebelumnya, isi ulang data -->
                                        @if (old('sections'))
                                            @foreach (old('sections') as $index => $section)
                                                <tr>
                                                    <td>
                                                        <input type="text" name="sections[{{ $index }}][name]"
                                                            class="form-control" value="{{ $section['name'] }}" required />
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-section-row">Delete /
                                                            删除</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-section-row">
                                    <i class="fa fa-plus-circle"></i> Add Section / 添加分区
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">Cancel / 取消</a>
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
            const tableBody = document.querySelector('#sections-table tbody');
            const addSectionButton = document.getElementById('add-section-row');

            let sectionIndex = tableBody.children.length; // Menghitung jumlah row yang sudah ada

            // Add new section row
            addSectionButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <input type="text" name="sections[${sectionIndex}][name]" class="form-control"
                            placeholder="Enter section name / 输入分区名称" required />
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-section-row">Delete / 删除</button>
                    </td>
                `;
                tableBody.appendChild(newRow);
                sectionIndex++;
            });

            // Remove section row
            tableBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-section-row')) {
                    event.target.closest('tr').remove();
                    updateSectionIndices();
                }
            });

            // Perbarui indeks setelah penghapusan agar tetap terurut
            function updateSectionIndices() {
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    row.querySelector('input[name^="sections"]').setAttribute('name',
                        `sections[${index}][name]`);
                });
                sectionIndex = rows.length;
            }
        });
    </script>
@endpush
