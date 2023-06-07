@extends('layouts.admin.app')
@section('title', 'Role')

@push('style')
<link rel="stylesheet" href="{{ asset('theme/assets/css/custom.css') }}" />
@endpush

@push('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts />
@endpush

@section('content')
@livewire('admin.show-role', ['title' => $__env->yieldContent('title')])
@endsection