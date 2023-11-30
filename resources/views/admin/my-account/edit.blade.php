@extends('layouts.admin.app')
@section('title', 'My Account')

@push('script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
@endpush

@section('content')
    <div class="row mt-4">
        <div class="col-md-9 mx-md-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Account</h5>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('my-account.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('put')

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
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ $data->name }}" placeholder="Nama Lengkap" autofocus required />
                            </div>

                            <div class="mb-3 col-md-12">
                                <label for="username" class="form-label">Username</label>
                                <input class="form-control" type="text" id="username" name="username"
                                    value="{{ $data->username }}" placeholder="Username" required />
                            </div>

                            <div class="mb-3 col-md-12">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control" type="email" id="email" name="email"
                                    value="{{ $data->email }}" placeholder="Email" required />
                            </div>

                            <div class="mb-3 col-md-12">
                                <label for="password" class="form-label">Password</label>
                                <input class="form-control" type="password" id="password" name="password"
                                    placeholder="Password" />
                            </div>

                            <div class="mb-3 col-md-12">
                                <label for="password_confirmation" class="form-label">Ulangi Password</label>
                                <input class="form-control" type="password" id="password_confirmation"
                                    name="password_confirmation" placeholder="Ulangi Password" />
                            </div>

                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
