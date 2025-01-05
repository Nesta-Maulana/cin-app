@extends('layouts.admin.app')
@section('title', 'ItemUom')

@push('style')
<link rel="stylesheet" href="{{ asset('theme/custom.css') }}" />
@endpush

@push('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts />
@endpush

@section('content')
@livewire('master.show-item-uom', ['title' => $__env->yieldContent('title')])
@endsection
