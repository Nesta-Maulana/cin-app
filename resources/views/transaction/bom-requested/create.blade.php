@extends('layouts.admin.app')
@section('title', 'Request BOM')

@section('content')
    <div class="row">
        <div class="col-md-12 mx-md-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Buat Permintaan @yield('title')</small>
                    </div>
                    <a href="{{ route('bom.index') }}" class="btn p-0" title="Kembali">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('bom.store') }}" method="POST">
                        @csrf

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> Terdapat beberapa masalah dengan inputan Anda.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Data Utama Request -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="request_number" class="form-label">Request Number</label>
                                <input class="form-control" type="text" id="request_number" name="request_number"
                                    value="{{ old('request_number') }}" placeholder="Nomor Permintaan" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="project_name" class="form-label">Project Name</label>
                                <input class="form-control" type="text" id="project_name" name="project_name"
                                    value="{{ old('project_name') }}" placeholder="Nama Proyek" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="request_date" class="form-label">Request Date</label>
                                <input class="form-control" type="date" id="request_date" name="request_date"
                                    value="{{ old('request_date') }}" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="requested_by" class="form-label">Requested By</label>
                                <input class="form-control" type="text" id="requested_by" name="requested_by"
                                    value="{{ old('requested_by') }}" placeholder="Nama Pemohon" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi Permintaan">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Detail Material -->
                        <hr>
                        <h6>Material Details</h6>
                        <div id="material-details">
                            <div class="row align-items-end material-row">
                                <div class="mb-3 col-md-4">
                                    <label for="material_id[]" class="form-label">Material</label>
                                    <select class="form-select" id="material_id[]" name="material_id[]">
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-2">
                                    <label for="quantity[]" class="form-label">Quantity</label>
                                    <input class="form-control" type="number" id="quantity[]" name="quantity[]"
                                        min="1" placeholder="Qty" required />
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="unit_price[]" class="form-label">Unit Price</label>
                                    <input class="form-control" type="number" step="0.01" id="unit_price[]"
                                        name="unit_price[]" placeholder="Harga per unit" required />
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="remarks[]" class="form-label">Remarks</label>
                                    <input class="form-control" type="text" id="remarks[]" name="remarks[]"
                                        placeholder="Catatan (opsional)" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" id="add-material" class="btn btn-sm btn-outline-primary">Add
                                Material</button>
                        </div>

                        <!-- Submit Form -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                            <a href="{{ route('bom.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        document.getElementById('add-material').addEventListener('click', function() {
            const materialDetails = document.getElementById('material-details');
            const materialRow = document.querySelector('.material-row');
            const newRow = materialRow.cloneNode(true);

            // Reset input values
            newRow.querySelectorAll('input, select').forEach(function(input) {
                input.value = '';
            });

            materialDetails.appendChild(newRow);
        });
    </script>
@endpush
