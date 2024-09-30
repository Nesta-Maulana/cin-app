@extends('layouts.admin.app')
@section('title', 'ChatList')

@push('style')
    <link rel="stylesheet" href="{{ asset('theme/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/app-assets/css/pages/app-chat.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/app-assets/css/pages/app-chat-list.min.css') }}" />
@endpush

@push('script')
    <script src="{{ asset('theme/app-assets/js/scripts/pages/app-chat.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
@endpush

@section('content')
    <div class="chat-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0">
            @livewire('transaction.show-chat-list', ['title' => $__env->yieldContent('title')])
            @livewire('transaction.show-chat-room')
        </div>
    </div>
@endsection
