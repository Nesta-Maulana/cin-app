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
    {{-- @livewire('transaction.show-b-o-m', ['title' => $__env->yieldContent('title')]) --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">@yield('title')</h5>
                        <small class="text-muted">Daftar Permintaan BOM</small>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Request Number</th>
                                <th>Project Name</th>
                                <th>Requested By</th>
                                <th>Request Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 10; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>BOM/{{ date('Y') }}/{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>Project {{ $i }}</td>
                                    <td>User {{ $i }}</td>
                                    <td>{{ date('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{route('warehouse.requested-bom.show', $i)}}" class="btn btn-primary btn-sm">View Details</a>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
