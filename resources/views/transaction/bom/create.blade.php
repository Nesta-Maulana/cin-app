@extends('layouts.admin.app')
@section('title', 'Request BOM')

@section('content')
    <div class="row">
        <div class="col-md-12 mx-md-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Request BOM (创建请求 BOM)</h5>
                        <small class="text-muted">Create Bill of Materials Request (创建物料清单请求)</small>
                    </div>
                    <a href="{{ route('bom.index') }}" class="btn p-0" title="Back (返回)">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('bom.store') }}" method="POST">
                        @csrf

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There are some problems with your input. (哎呀！您的输入有些问题。)<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Main Request Data -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="request_number" class="form-label">Request Number (请求编号)</label>
                                <input class="form-control" type="text" id="request_number" name="request_number"
                                    placeholder="Request Number (请求编号)" value="{{ $code }}" readonly required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="requested_by" class="form-label">Requested By (请求者)</label>
                                <input class="form-control" type="text" id="requested_by" name="requested_by"
                                    value="{{ old('requested_by', auth()->user()->name) }}" placeholder="Requested By (请求者)"
                                    readonly required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="request_date" class="form-label">Request Date (请求日期)</label>
                                <input class="form-control" type="date" id="request_date" name="request_date"
                                    value="{{ old('request_date', date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="project_name" class="form-label">Project Name (项目名称)</label>
                                <input class="form-control" type="text" id="project_name" name="project_name"
                                    value="{{ old('project_name') }}" placeholder="Project Name (项目名称)" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description (描述)</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description (描述)">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Material Details -->
                        <hr>
                        <h6>Material Details (材料详情)</h6>
                        <div id="material-details">
                            <div class="row align-items-end material-row">
                                <div class="mb-3 col-md-4">
                                    <label for="material_id[]" class="form-label">Material (材料)</label>
                                    <select class="form-select select2" name="material_id[]">
                                        <option value="" disabled selected>Choose Material (选择材料)</option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->id }}"
                                                data-unit="{{ $material->unit->name }}({{ $material->unit->name_mandarin }})"
                                                data-remarks="{{ $material->description }}">
                                                {{ $material->name . ' (' . $material->name_mandarin . ') | ' . $material->unit->name . ' | ' . $material->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-2">
                                    <label for="quantity[]" class="form-label">Quantity (数量)</label>
                                    <input class="form-control" type="number" name="quantity[]" min="1"
                                        placeholder="Quantity (数量)" required />
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="unit_id[]" class="form-label">Unit (单元)</label>
                                    <input class="form-control unit-input" type="text" name="unit_id[]"
                                        placeholder="Unit (单元)" readonly required />
                                </div>
                                <div class="mb-3 col-md-2">
                                    <label for="remarks[]" class="form-label">Remarks (备注)</label>
                                    <textarea name="remarks[]" class="form-control remarks-input" cols="30" rows="2" readonly></textarea>
                                </div>
                                <div class="mb-3 col-md-1">
                                    <button type="button" class="btn btn-danger remove-material"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" id="add-material" class="btn btn-sm btn-outline-primary">Add Material
                                (添加材料)</button>
                        </div>

                        <!-- Submit Form -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2" name="action" value="submit">Submit (提交)</button>
                            <button type="submit" class="btn btn-secondary px-5 me-2" name="action" value="draft">Save as Draft (保存草稿)</button>
                            <a href="{{ route('bom.index') }}" class="btn btn-label-secondary">Cancel (取消)</a>
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
            initializeSelect2();

            const materialDetails = document.getElementById('material-details');
            const addMaterialButton = document.getElementById('add-material');
            const firstMaterialRow = document.querySelector('.material-row');

            addMaterialButton.addEventListener('click', function() {
                const newRow = firstMaterialRow.cloneNode(true);
                $(newRow).find('.select2-container').remove(); // Remove cloned select2 container

                // Reset inputs
                newRow.querySelectorAll('input, textarea, select').forEach(input => {
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = '';
                    }
                });

                // Append and reinitialize select2
                materialDetails.appendChild(newRow);
                $(newRow).find('select').select2({
                    placeholder: "Choose Material (选择材料)",
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    updateMaterialDetails.call(this);
                });

                // Add delete functionality
                newRow.querySelector('.remove-material').addEventListener('click', function() {
                    this.closest('.material-row').remove();
                    updateDeleteButtonsVisibility();
                });

                updateDeleteButtonsVisibility();
            });

            function initializeSelect2() {
                $('.select2').each(function() {
                    $(this).select2({
                        placeholder: "Choose Material (选择材料)",
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        updateMaterialDetails.call(this);
                    });
                });
            }

            function updateMaterialDetails() {
                const unitInput = this.closest('.material-row').querySelector('.unit-input');
                const remarksInput = this.closest('.material-row').querySelector('.remarks-input');
                const selectedOption = this.options[this.selectedIndex];

                if (selectedOption) {
                    unitInput.value = selectedOption.getAttribute('data-unit');
                    remarksInput.value = selectedOption.getAttribute('data-remarks');
                }
            }

            function updateDeleteButtonsVisibility() {
                const rows = document.querySelectorAll('.material-row');
                const deleteButtons = document.querySelectorAll('.remove-material');
                if (rows.length === 1) {
                    deleteButtons.forEach(button => button.style.display = 'none');
                } else {
                    deleteButtons.forEach(button => button.style.display = 'inline-block');
                }
            }

            updateDeleteButtonsVisibility();
        });
    </script>
@endpush
