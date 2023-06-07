@extends('layouts.admin.app')
@section('title', 'Setting')

@push('script')
<script>
    $(function () {
        // upload image
        let accountUserImage = document.getElementById('uploadedAvatar');
        const fileInput = document.querySelector('.account-file-input'),
        resetFileInput = document.querySelector('.account-image-reset');

        if (accountUserImage) {
        const resetImage = accountUserImage.src;
        fileInput.onchange = () => {
            if (fileInput.files[0]) {
            accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
            }
        };
        resetFileInput.onclick = () => {
            fileInput.value = '';
            accountUserImage.src = resetImage;
        };
        }
    });
</script>
@endpush

@section('content')
<div class="row">
    <div class="col-md-9 mx-md-auto">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Edit @yield('title')</h5>
                    <small class="text-muted">Perbarui @yield('title')</small>
                </div>
                <a href="{{ route('setting.index') }}" class="btn p-0" title="Kembali">
                    <i class="ti ti-x ti-sm text-muted"></i>
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('setting.update', $data->id) }}" method="POST" enctype="multipart/form-data">
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

                    <div class="row mt-3">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="@if(!$data->logo) http://shofyan.my.id/theme/images/portfolio/psb.jpg @else {{ imageAsset($data->logo) }} @endif"
                                alt="logo" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar">
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                    tabindex="0">
                                    <span class="d-none d-sm-block">Change Logo</span>
                                    <i class="ti ti-upload d-block d-sm-none"></i>
                                    <input name="logo" type="file" id="upload" class="account-file-input"
                                        accept="image/png,image/jpeg" hidden="">
                                </label>
                                <button type="button"
                                    class="btn btn-label-secondary account-image-reset mb-3 waves-effect">
                                    <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>

                                <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800K</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="mb-3 col-md-8">
                            <label for="name" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="name" name="app_name"
                                value="{{ $data->app_name }}" placeholder="Nama App" autofocus required />
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="version" class="form-label">Versi</label>
                            <input class="form-control" type="text" id="version" name="app_version"
                                value="{{ $data->app_version }}" placeholder="v1.0" />
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Nama Insititusi</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ $data->name }}"
                                placeholder="Nama Insititusi" required />
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="address" class="form-label">Alamat Insititusi</label>
                            <textarea class="form-control" name="address" id="address" rows="4"
                                placeholder="Alamat Insititusi">{{ $data->address }}</textarea>
                        </div>
                    </div>

                    <div class="my-2">
                        <button type="submit" class="btn btn-primary px-5 me-2">Update</button>
                        <a href="{{ route('setting.index') }}" class="btn btn-label-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection