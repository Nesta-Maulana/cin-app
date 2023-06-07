@extends('layouts.admin.app')
@section('title', 'Dashboard')

@push('style')
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/swiper/swiper.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet"
    href="{{ asset('theme/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet"
    href="{{ asset('theme/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/pages/cards-advance.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@push('vendor-script')
<script src="{{ asset('theme/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-xl-4 mb-4 col-lg-5">
        <div class="card bg-primary text-white">
            <div class="d-flex align-items-end row">
                <div class="col-6">
                    <div class="card-body text-nowrap">
                        <h5 class="card-title text-white mb-0">{{ institutionName() }}</h5>
                        <p class="mb-5">{{ appName() }}</p>
                        <a href="{{ route('student.index') }}" class="btn btn-light">Data Santri</a>
                    </div>
                </div>
                <div class="col-6 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-2">
                        <img src="{{ asset('theme/assets/img/santri.png') }}" height="145" alt="view sales" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 mb-4 col-lg-7 col-12">
        @livewire('admin.dashboard.stat-academic')
    </div>

    <div class="col-12 col-xl-8 mb-4">
        @livewire('admin.dashboard.graph-student')
    </div>

    <!-- Sales last 6 months -->
    <div class="col-md-6 col-xl-4 mb-4">
        @livewire('admin.dashboard.stat-gender')
    </div>
</div>
@endsection