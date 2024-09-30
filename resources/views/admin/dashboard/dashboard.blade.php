@extends('layouts.admin.app')
@section('title', 'Dashboard')

@push('style')
@endpush

@push('vendor-script')
<script src="{{ asset('theme/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-xl-4 mb-4 col-lg-5">
        <div class="card bg-primary text-white">
            <div class="d-flex align-items-end row">
                <div class="col-7">
                    <div class="card-body text-nowrap">
                        <h5 class="card-title text-white mb-0">{{ holderName() }}</h5>
                        <p class="mb-5">{{ appName() }}</p>
                        <a href="#" class="btn btn-light">Data</a>
                    </div>
                </div>
                <div class="col-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('theme/assets/img/illustrations/card-advance-sale.png') }}" height="140"
                            alt="view sales">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
