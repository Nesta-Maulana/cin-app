<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control" placeholder="Search..">
                </label>
            </div>
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                <div class="my-1 me-md-2">
                    <label>
                        <select wire:model="paginate" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                </div>
                @can('create-pre-purchase-order')
                    <div class="flex-wrap my-1">
                        <a href="{{ route('pre-purchase-order.create') }}"
                            class="btn btn-secondary text-white add-new btn-primary">
                            <span>
                                <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                <span>
                                    {{ $title }}
                                </span>
                            </span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        @can('update-pre-purchase-order')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>No. Pre PO</th>
                        <th>Customer Order</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        <tr wire:key="row{{ $item->id }}">
                            @can('update-pre-purchase-order')
                                <td>
                                    <input style="width: 17px; height: 17px;" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan
                            <td>{{ $item->pre_po_number }}</td>
                            <td>{{ $item->customerOrder->order_number ?? '-' }}</td>
                            <td>
                                @if ($item->process_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($item->process_status == 'under_review')
                                    <span class="badge bg-info">Under Review</span>
                                @elseif($item->process_status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($item->process_status == 'finalized')
                                    <span class="badge bg-primary">Finalized</span>
                                @elseif($item->process_status == 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                @else
                                    <span class="badge bg-secondary">{{ $item->process_status }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($item->grand_total, 2) }}</td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>{{ date('Y/m/d', strtotime($item->created_at)) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if (isset($expandedRows[$item->id]))
                                        <a href="#" wire:click.prevent="toggleExpand({{ $item->id }})"
                                            class="action-btn fw-bold">
                                            <i class="fa fa-chevron-up fa-sm me-2 fs-5"></i>
                                        </a>
                                    @else
                                        <a href="#" wire:click.prevent="toggleExpand({{ $item->id }})"
                                            class="action-btn fw-bold">
                                            <i class="fa fa-chevron-down fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('pre-purchase-order.show', $item->id) }}" class="action-btn"
                                        title="view">
                                        <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                    </a>

                                    @can('update-pre-purchase-order')
                                        @if ($item->process_status == 'draft')
                                            <a href="{{ route('pre-purchase-order.edit', $item->id) }}" class="action-btn"
                                                title="Add Quotation">
                                                <i class="fa fa-plus-circle fa-sm me-2 fs-5"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('delete-pre-purchase-order')
                                        <a href="javascript:;" class="action-btn" title="delete"
                                            wire:click.prevent="confirmDelete({{ $item->id }})">
                                            <i class="fa fa-trash fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan


                                    @php
                                        $approvalRequest = $item->approvalRequest('create')->first();
                                    @endphp
                                    @can('approve-pre-purchase-order')
                                        @if (!is_null($approvalRequest))
                                            @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\User')
                                                @if (Auth::user()->id == $approvalRequest->currentLevel->approver->approver_reference_id)
                                                    <a href="javascript:;" class="action-btn" title="Approval Process"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                        <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\Role')
                                                @if (Auth::user()->hasRole($approvalRequest->currentLevel->approver->name))
                                                    @if (is_null($approvalRequest->currentLevel->department_id))
                                                        <a href="javascript:;" class="action-btn" title="Approval Process"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                            <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                        </a>
                                                    @else
                                                        @if (count(array_intersect(
                                                                    $item->createdBy->departments->pluck('id')->toArray(),
                                                                    auth()->user()->departments->pluck('id')->toArray())) > 0)
                                                            <a href="javascript:;" class="action-btn"
                                                                title="Approval Process" data-bs-toggle="modal"
                                                                data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                                <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                            @include('admin.modal.approval')
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded Row with Pre-Purchase Order Details -->
                        @if (isset($expandedRows[$item->id]))
                            <tr wire:key="detail{{ $item->id }}" class="bg-light">
                                <td colspan="8" class="p-0">
                                    <div class="p-1">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Items</th>
                                                        <th>Type</th>
                                                        <th>Item Request Number</th>
                                                        <th>Qty</th>
                                                        <th>Unit</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($item->details as $detail)
                                                        <tr>
                                                            <td>
                                                                @if ($detail->itemRequestDetail)
                                                                    {{ $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}
                                                                @elseif($detail->manualItemRequestDetail)
                                                                    {{ $detail->manualItemRequestDetail->item_name }}
                                                                @else
                                                                    {{ $detail->item_name ?? 'N/A' }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $detail->itemRequestDetail ? 'primary' : 'info' }}">
                                                                    {{ $detail->itemRequestDetail ? 'System' : 'Manual' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($detail->itemRequestDetail)
                                                                    {{ $detail->itemRequestDetail->itemRequest->request_number ?? '-' }}
                                                                @elseif($detail->manualItemRequestDetail)
                                                                    {{ $detail->manualItemRequestDetail->manualItemRequest->request_number ?? '-' }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>{{ $detail->quantity }}</td>
                                                            <td>
                                                                @if ($detail->uom)
                                                                    {{ $detail->uom->unitOfMeasurement->name }}
                                                                @elseif($detail->itemRequestDetail)
                                                                    {{ $detail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->name }}
                                                                @elseif($detail->manualItemRequestDetail)
                                                                    {{ $detail->manualItemRequestDetail->unit }}
                                                                @else
                                                                    {{ $detail->unit ?? '-' }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $detail->remarks }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">No item details found
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        @if (count($item->quotations) > 0)
                                            <div class="mt-3">
                                                <h6 class="mb-2">Supplier Quotations</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Supplier</th>
                                                                <th>Currency</th>
                                                                <th>Items Total</th>
                                                                <th>Tax Amount</th>
                                                                <th>Other Costs</th>
                                                                <th>Grand Total</th>
                                                                <th>Selected</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($item->quotations as $quotation)
                                                                <tr>
                                                                    <td>{{ $quotation->supplier->name }}</td>
                                                                    <td>{{ strtoupper($quotation->currency ?? 'IDR') }}
                                                                    </td>
                                                                    <td>{{ number_format(
                                                                        $quotation->quotationDetails->sum(function ($detail) {
                                                                            return $detail->offered_price_per_unit * $detail->quantity;
                                                                        }),
                                                                        2,
                                                                    ) }}
                                                                    </td>
                                                                    <td>{{ number_format($quotation->tax_amount ?? 0, 2) }}
                                                                    </td>
                                                                    <td>{{ number_format(
                                                                        ($quotation->beforeTaxCosts->sum('amount') ?? 0) + ($quotation->afterTaxCosts->sum('amount') ?? 0),
                                                                        2,
                                                                    ) }}
                                                                    </td>
                                                                    <td>{{ number_format($quotation->total_amount ?? 0, 2) }}
                                                                    </td>
                                                                    <td>
                                                                        @if ($quotation->is_selected)
                                                                            <span
                                                                                class="badge bg-success">Selected</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">No</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <a href="{{ route('pre-purchase-order.edit', $item->id) }}#supplier-card-{{ $loop->index }}"
                                                                                class="action-btn" title="edit">
                                                                                <i
                                                                                    class="fa fa-edit fa-sm me-2 fs-5"></i>
                                                                            </a>
                                                                            @if (!$quotation->is_selected && $item->process_status == 'under_review')
                                                                                <a href="javascript:;"
                                                                                    class="action-btn"
                                                                                    wire:click.prevent="selectSupplier({{ $quotation->id }})"
                                                                                    title="Select Supplier">
                                                                                    <i
                                                                                        class="fa fa-check-circle fa-sm me-2 fs-5"></i>
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="alert alert-secondary">
                                    Data tidak ditemukan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() ?? 0 }} to {{ $table->lastItem() ?? 0 }} of
                    {{ $table->total() ?? 0 }} data
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <!-- Modal for updating selected status -->
    <div wire:ignore.self class="modal fade" id="modalSelectedStatus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select wire:model="newStatus" class="form-select">
                            <option value="">Choose Status</option>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="finalized">Finalized</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" wire:click="updateSelectedStatus" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for selecting supplier confirmation -->
    <div wire:ignore.self class="modal fade" id="modalSelectSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to select this supplier? This action will mark the Pre-Purchase Order as
                        approved.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" wire:click="confirmSelectSupplier"
                        class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
            $('#modalSelectedStatus').modal('hide');
            $('#modalSelectSupplier').modal('hide');
        });

        window.addEventListener('open-supplier-modal', event => {
            $('#modalSelectSupplier').modal('show');
        });
    </script>
</div>
