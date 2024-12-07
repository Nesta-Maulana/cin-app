@extends('layouts.admin.app')
@section('title', 'BOM')

@push('style')
    <link rel="stylesheet" href="{{ asset('theme/custom.css') }}" />
@endpush

@push('script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
@endpush

@section('content')
    @livewire('transaction.show-b-o-m', ['title' => $__env->yieldContent('title'), 'routeAlias' => $routeAlias])
@endsection
