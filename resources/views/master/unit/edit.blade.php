@extends('layouts.admin.app')
@section('title', 'Unit')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title')</h5>
                        <small class="text-muted">Perbarui @yield('title')</small>
                    </div>
                    <a href="{{ route('unit.index') }}" class="btn p-0" title="Kembali">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('unit.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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

                        <div class="row">
                            <!-- Nama / Name -->
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name / 名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', $data->name) }}" placeholder="Name" autofocus required />
                            </div>

                            <!-- Nama (Mandarin) / Name (Mandarin) -->
                            <div class="mb-3 col-md-6">
                                <label for="name_mandarin" class="form-label">Name (Mandarin) / 中文名称</label>
                                <input class="form-control" type="text" id="name_mandarin" name="name_mandarin"
                                    value="{{ old('name_mandarin', $data->name_mandarin) }}" placeholder="Name in Mandarin"
                                    required />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Singkatan / Abbreviation -->
                            <div class="mb-3 col-md-6">
                                <label for="abbreviation" class="form-label">Abbreviation / 简称</label>
                                <input class="form-control" type="text" id="abbreviation" name="abbreviation"
                                    value="{{ old('abbreviation', $data->abbreviation) }}"
                                    placeholder="Abbreviation, e.g., 'kg', 'm'" required />
                            </div>

                            <!-- Singkatan (Mandarin) / Abbreviation (Mandarin) -->
                            <div class="mb-3 col-md-6">
                                <label for="abbreviation_mandarin" class="form-label">Abbreviation (Mandarin) / 简称
                                    (中文)</label>
                                <input class="form-control" type="text" id="abbreviation_mandarin"
                                    name="abbreviation_mandarin"
                                    value="{{ old('abbreviation_mandarin', $data->abbreviation_mandarin) }}"
                                    placeholder="Abbreviation in Mandarin" />
                            </div>
                        </div>

                        <!-- Deskripsi / Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description / 描述</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description of the unit"
                                required>{{ old('description', $data->description) }}</textarea>
                        </div>


                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update</button>
                            <a href="{{ route('unit.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
