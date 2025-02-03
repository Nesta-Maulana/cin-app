@extends('layouts.admin.app')
@section('title', 'Menu')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Create @yield('title')</h5>
                    <small class="text-muted">Buat @yield('title')</small>
                </div>
                <a href="{{ route('menu.index') }}" class="btn p-0" title="Kembali">
                    <i class="ti ti-x ti-sm text-muted"></i>
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('menu.store') }}" method="POST">
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
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Nama Menu" autofocus required />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="url" class="form-label">URL</label>
                            <input class="form-control" type="text" id="url" name="url" value="{{ old('url') }}"
                                placeholder="admin/contoh" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="permission" class="form-label">Permission</label>
                            <select class="form-select" name="permission_id" id="permission">
                                <option value="">Pilih</option>
                                @foreach ($permission as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="icon" class="form-label">Icon</label>
                            <input class="form-control" type="text" id="icon" name="icon" value="{{ old('icon') }}"
                                placeholder="Nama Tabler Icon" />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="main_menu" class="form-label">Main Menu</label>
                            <select class="form-select" name="main_menu" id="main_menu">
                                <option value="">Pilih</option>
                                @foreach ($menu as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="sort" class="form-label">Urutan</label>
                            <input class="form-control" type="number" id="sort" name="sort" value="{{ old('sort') }}"
                                placeholder="No. Urut" required />
                        </div>
                    </div>

                    <div class="my-2">
                        <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                        <a href="{{ route('menu.index') }}" class="btn btn-label-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
