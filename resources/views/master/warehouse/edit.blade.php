@extends('layouts.admin.app')
@section('title', 'Edit Warehouse / 编辑仓库')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title') / 编辑@yield('title')</h5>
                        <small class="text-muted">Perbarui @yield('title') / 更新@yield('title')</small>
                    </div>
                    <a href="{{ route('warehouse.index') }}" class="btn p-0" title="Kembali / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('warehouse.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Error Handling -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀！</strong> Terdapat beberapa masalah dengan inputan Anda. /
                                你的输入有一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Warehouse Name & Status -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Warehouse Name / 仓库名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', $data->name) }}" placeholder="Enter warehouse name / 输入仓库名称"
                                    required />
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1"
                                        {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>
                                        Active / 启用
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>
                                        Inactive / 不启用
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Warehouse Location -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="location" class="form-label">Location / 位置</label>
                                <textarea class="form-control" id="location" name="location" placeholder="Enter warehouse location / 输入仓库位置"
                                    rows="3">{{ old('location', $data->location) }}</textarea>
                            </div>
                        </div>

                        <!-- Warehouse Sections -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Warehouse Sections / 仓库分区</label>
                                <table class="table table-bordered" id="sections-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Section Name / 分区名称</th>
                                            <th>Status / 状态</th>
                                            <th>Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sections-table-body">
                                        @foreach ($data->warehouseSections as $index => $section)
                                            <tr>
                                                <td class="section-index">{{ $loop->iteration }}</td>
                                                <td>
                                                    <input type="hidden" name="sections[{{ $index }}][id]"
                                                        value="{{ $section->id }}">
                                                    <input type="text" name="sections[{{ $index }}][name]"
                                                        class="form-control"
                                                        value="{{ old("sections.$index.name", $section->name) }}" required>
                                                </td>
                                                <td>
                                                    <select name="sections[{{ $index }}][is_active]"
                                                        class="form-select">
                                                        <option value="1" {{ $section->is_active ? 'selected' : '' }}>
                                                            Active / 启用</option>
                                                        <option value="0"
                                                            {{ !$section->is_active ? 'selected' : '' }}>Inactive / 停用
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-section">Delete / 删除</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-section">
                                    <i class="fa fa-plus-circle"></i> Add Section / 添加分区
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update / 更新</button>
                            <a href="{{ route('warehouse.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
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
            const sectionsTableBody = document.querySelector('#sections-table-body');
            const addSectionButton = document.getElementById('add-section');

            // Fungsi menambah section baru
            addSectionButton.addEventListener('click', function() {
                const sectionCount = sectionsTableBody.querySelectorAll('tr').length;
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
            <td class="section-index">${sectionCount + 1}</td>
            <td>
                <input type="text" name="sections[${sectionCount}][name]" class="form-control"
                       placeholder="Enter section name / 输入分区名称" required>
            </td>
            <td>
                <select name="sections[${sectionCount}][is_active]" class="form-select">
                    <option value="1">Active / 启用</option>
                    <option value="0">Inactive / 停用</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-section">Delete / 删除</button>
            </td>
        `;
                sectionsTableBody.appendChild(newRow);
                updateSectionNumbers();
            });

            // Hapus Section
            sectionsTableBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-section')) {
                    event.target.closest('tr').remove();
                    updateSectionNumbers();
                }
            });

            // Perbarui Nomor Urut Section
            function updateSectionNumbers() {
                const rows = sectionsTableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    row.querySelector('.section-index').textContent = index + 1;
                });
            }
        });
    </script>
@endpush
