@extends('layouts.admin.app')
@section('title', 'Role')

@section('content')
    <div class="row">
        <div class="col-md-9 mx-md-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Buat @yield('title')</small>
                    </div>
                    <a href="{{ route('role.index') }}" class="btn p-0" title="Kembali">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('role.store') }}" method="POST">
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
                            <div class="col-md-12">
                                <label for="name" class="form-label">Nama</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Nama" autofocus required />
                            </div>
                            @livewire('admin.set-role-permission', [])
                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit</button>
                            <a href="{{ route('role.index') }}" class="btn btn-label-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
