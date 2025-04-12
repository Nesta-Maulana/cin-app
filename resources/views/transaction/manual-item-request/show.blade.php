@extends('layouts.admin.app')
@section('title', 'Manual Item Request Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Manual Item Request Details</h5>
                        <small class="text-muted">Detail Permintaan Item Manual</small>
                    </div>
                    <div>
                        <a href="{{ route('manual-item-request.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Back</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold">Request Information / Informasi Permintaan</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Request Number / Nomor Permintaan</th>
                                        <td>{{ $manualItemRequest->request_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date / Tanggal</th>
                                        <td>
                                            @if (is_string($manualItemRequest->request_date))
                                                {{ $manualItemRequest->request_date }}
                                            @else
                                                {{ $manualItemRequest->request_date ? $manualItemRequest->request_date->format('d-m-Y') : '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if ($manualItemRequest->status == 'pending')
                                                <span class="badge bg-warning">Pending / Tertunda</span>
                                            @elseif($manualItemRequest->status == 'approved')
                                                <span class="badge bg-success">Approved / Disetujui</span>
                                            @elseif($manualItemRequest->status == 'rejected')
                                                <span class="badge bg-danger">Rejected / Ditolak</span>
                                            @elseif($manualItemRequest->status == 'completed')
                                                <span class="badge bg-info">Completed / Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Notes / Catatan</th>
                                        <td>{{ $manualItemRequest->notes ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold">Customer Information / Informasi Pelanggan</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Customer Order Number</th>
                                        <td>{{ $manualItemRequest->customerOrder->order_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Name</th>
                                        <td>{{ $manualItemRequest->customerOrder->customer->customer_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Project Name</th>
                                        <td>{{ $manualItemRequest->customerOrder->project_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Date</th>
                                        <td>
                                            @if (isset($manualItemRequest->customerOrder->order_date))
                                                @if (is_string($manualItemRequest->customerOrder->order_date))
                                                    {{ $manualItemRequest->customerOrder->order_date }}
                                                @else
                                                    {{ $manualItemRequest->customerOrder->order_date->format('d-m-Y') }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-semibold">Requested Items / Item yang Diminta</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th>Item Name / Nama Item</th>
                                        <th>Description / Deskripsi</th>
                                        <th>Specification / Spesifikasi</th>
                                        <th>Unit</th>
                                        <th class="text-end">Quantity / Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($manualItemRequest->details as $index => $detail)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td>{{ $detail->description ?? '-' }}</td>
                                            <td>{{ $detail->specification ?? '-' }}</td>
                                            <td>{{ $detail->unit }}</td>
                                            <td class="text-end">{{ number_format($detail->quantity, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No items found / Tidak ada item yang
                                                ditemukan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- @if ($manualItemRequest->status == 'pending' && auth()->user()->can('approval-manual-item-request'))
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Approval Actions / Tindakan Persetujuan</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('manual-item-request.approval', $manualItemRequest->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Remarks / Catatan</label>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter remarks (optional)"></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="approval_status" value="approved"
                                                class="btn btn-success">
                                                <i class="fa fa-check me-1"></i> Approve / Setujui
                                            </button>
                                            <button type="submit" name="approval_status" value="rejected"
                                                class="btn btn-danger">
                                                <i class="fa fa-times me-1"></i> Reject / Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif --}}

                    <div class="mt-4">
                        <div class="d-flex gap-2">
                            <a href="{{ route('manual-item-request.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Back / Kembali
                            </a>

                            @if ($manualItemRequest->status == 'pending' && auth()->user()->can('update-manual-item-request'))
                                <a href="{{ route('manual-item-request.edit', $manualItemRequest->id) }}"
                                    class="btn btn-primary">
                                    <i class="fa fa-edit me-1"></i> Edit
                                </a>
                            @endif

                            @if (auth()->user()->can('create-purchase-order') && in_array($manualItemRequest->status, ['approved', 'completed']))
                                <a href="{{ route('purchase-order.create', ['manual_item_request_id' => $manualItemRequest->id]) }}"
                                    class="btn btn-info text-white">
                                    <i class="fa fa-file-invoice me-1"></i> Create Purchase Order / Buat Pesanan Pembelian
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
