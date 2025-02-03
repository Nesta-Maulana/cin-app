@extends('layouts.admin.app')
@section('title', 'User')

@push('style')
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0 text-white">Create @yield('title')</h5>
                        <small class="text-white-50">Buat @yield('title') Baru</small>
                    </div>
                    <a href="{{ route('user.index') }}" class="btn btn-outline-light" title="Kembali">
                        <i class="fa-solid fa-x text-dark-50"></i>
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

                        <div class="row mt-2">
                            <div class="mb-1 col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    placeholder="Nama Lengkap" autocomplete="off" value="{{ old('name') }}" autofocus
                                    required />
                            </div>
                            <div class="mb-1 col-md-6">
                                <label for="department_id" class="form-label">Department</label>
                                <select class="form-select select2" multiple name="department_id[]" id="department_id"
                                    wire:model="department_id" required>
                                    <option value="">- Choose Department -</option>
                                    @foreach ($departments as $department_id => $department)
                                        <option value="{{ $department_id }}" @selected(in_array($department_id, old('department_id', [])))>
                                            {{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-1 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email"
                                    id="email" name="email" autocomplete="off" placeholder="john@example.com"
                                    value="{{ old('email') }}" required />
                            </div>

                            <div class="mb-1 col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input class="form-control @error('username') is-invalid @enderror" type="text"
                                    id="username" name="username" value="{{ old('username') }}" autocomplete="off"
                                    placeholder="Username" required />
                            </div>
                            <div class="mb-1 col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select select2" name="role" id="role" wire:model="role_id">
                                    <option value="0">- Choose Role -</option>
                                    @foreach ($roles as $key => $item)
                                        <option value="{{ $item }}" @selected(old('role') == $item)>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                            @livewire('admin.set-user-permission')
                        </div>
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
