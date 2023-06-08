@extends('layouts.admin.app')
@section('title', 'Administrator')

@push('style')
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('vendor-script')
<script src="{{ asset('theme/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('theme/assets/js/forms-selects.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-md-9 mx-md-auto">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Create @yield('title')</h5>
                    <small class="text-muted">Buat @yield('title') Baru</small>
                </div>
                <a href="{{ route('user.index') }}" class="btn p-0" title="Kembali">
                    <i class="ti ti-x ti-sm text-muted"></i>
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('user.store') }}" method="POST">
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
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Nama Lengkap" autofocus required />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
                                name="email" value="{{ old('email') }}" placeholder="john@example.com" required />
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control @error('username') is-invalid @enderror" type="text"
                                id="username" name="username" value="{{ old('username') }}" placeholder="Username"
                                required />
                        </div>

                        <div class="mb-3 col-md-6 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="Password" aria-describedby="password" required
                                    autocomplete="current-password" />

                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" name="role" id="role">
                                @foreach ($role as $key => $item)
                                <option value="{{ $item }}" @selected(old('role')==$item)>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="my-2">
                        <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                        <button type="reset" class="btn btn-label-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection