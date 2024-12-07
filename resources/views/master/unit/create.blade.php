@extends('layouts.admin.app')
@section('title', 'Unit')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Buat @yield('title')</small>
                    </div>
                    <a href="{{ route('unit.index') }}" class="btn p-0" title="Kembali">
                        <i class="fa fa-x fa-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('unit.store') }}" method="POST">
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

                        <div class="row">
                            <!-- Nama / Name -->
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name / 名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Name" autofocus required maxlength="100" />
                            </div>

                            <!-- Nama (Mandarin) / Name (Mandarin) -->
                            <div class="mb-3 col-md-6">
                                <label for="name_mandarin" class="form-label">Name (Mandarin) / 中文名称</label>
                                <input class="form-control" type="text" id="name_mandarin" name="name_mandarin"
                                    value="{{ old('name_mandarin') }}" placeholder="Name in Mandarin" required
                                    maxlength="100" />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Singkatan / Abbreviation -->
                            <div class="mb-3 col-md-6">
                                <label for="abbreviation" class="form-label">Abbreviation / 简称</label>
                                <input class="form-control" type="text" id="abbreviation" name="abbreviation"
                                    value="{{ old('abbreviation') }}" placeholder="Abbreviation, e.g., 'kg', 'm'" required
                                    maxlength="10" />
                            </div>

                            <!-- Singkatan (Mandarin) / Abbreviation (Mandarin) -->
                            <div class="mb-3 col-md-6">
                                <label for="abbreviation_mandarin" class="form-label">Abbreviation (Mandarin) / 简称
                                    (中文)</label>
                                <input class="form-control" type="text" id="abbreviation_mandarin"
                                    name="abbreviation_mandarin" value="{{ old('abbreviation_mandarin') }}"
                                    placeholder="Abbreviation in Mandarin" maxlength="10" />
                            </div>
                        </div>

                        <!-- Deskripsi / Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description / 描述</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description of the unit"
                                required>{{ old('description') }}</textarea>
                        </div>


                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                            <a href="{{ route('unit.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
