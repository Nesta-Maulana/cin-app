@extends('layouts.admin.app')
@section('title', 'ItemRequest')

@push('style')
<link rel="stylesheet" href="{{ asset('theme/custom.css') }}" />
@endpush

@push('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts />
@endpush

@section('content')
@livewire('transaction.show-item-request', ['title' => $__env->yieldContent('title')])
@endsection
