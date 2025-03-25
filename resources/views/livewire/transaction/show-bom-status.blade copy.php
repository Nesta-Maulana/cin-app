<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <!-- Filter Section -->
            <div class="my-2 d-flex flex-wrap align-items-center">
                <div class="me-3 mb-2">
                    <label class="form-label">Customer Order</label>
                    <select wire:model="selectedCustomerOrder" class="form-select">
                        <option value="">All Customer Orders</option>
                        @foreach ($customerOrders as $key => $order)
                            <option value="{{ $order->id }}" {{ $key == 0 ? 'selected' : '' }}>
                                {{ $order->order_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="me-3 mb-2">
                    <label class="form-label">Status</label>
                    <select wire:model="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="waiting">待採購 (Waiting)</option>
                        <option value="processing">購買中 (Processing)</option>
                        <option value="done">完成 (Done)</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Search</label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search..">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                @if (!$selected)
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
                    @can('create-item-request')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('item-request.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                    <span>
                                        Add Item Request
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endcan
                @else
                    <div class="my-1 me-md-2">
                        <span class="px-1">
                            {{ count($selected) }} data terpilih
                        </span>
                    </div>
                    <div class="flex-wrap my-1">
                        <a href="#" class="btn btn-secondary add-new btn-label-primary" data-bs-toggle="modal"
                            data-bs-target="#modalSelectedStatus">
                            Update Status
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        @can('update-item-request')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>No. BOM</th>
                        <th>Status</th>
                        <th>PIC</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        <tr wire:key="row{{ $item->id }}">
                            @can('update-item-request')
                                <td>
                                    <input style="width: 17px; height: 17px;" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan
                            <td>
                                {{ $item->request_number }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->request_status }}</span>
                            </td>
                            <td>{{ $item->createdBy->name }}</td>
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
                                            <i class="fa fa-chevron-down  fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endif

                                    @can('update-item-request')
                                        <a href="{{ route('item-request.edit', $item->id) }}" class="action-btn"
                                            title="edit">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-item-request')
                                        <a href="javascript:;" class="action-btn" title="delete"
                                            wire:click.prevent="confirmDelete({{ $item->id }})">
                                            <i class="fa fa-trash fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded Row with Item Details -->
                        @if (isset($expandedRows[$item->id]))
                            <tr wire:key="detail{{ $item->id }}" class="bg-light">
                                <td colspan="6" class="p-0">
                                    <div class="p-1">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Items</th>
                                                        <th>Qty</th>
                                                        <th>Unit</th>
                                                        <th>Create Comparison</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($item->details as $detail)
                                                        <tr>
                                                            <td>{{ $detail->itemPriceHistory->itemUom->item->name }}
                                                            </td>
                                                            <td>{{ $detail->deliveryOrderDetails->sum('quantity') }}/{{ $detail->quantity }}
                                                            </td>
                                                            <td>{{ $detail->itemPriceHistory->itemUom->unitOfMeasurement->name }}
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control"
                                                                    wire:model.defer="comparisonPrices.{{ $detail->id }}"
                                                                    wire:change="validateQuantity({{ $detail->id }}, {{ $detail->quantity }})"
                                                                    placeholder="Enter Quantity for Comparison">
                                                                @if (isset($quantityErrors[$detail->id]))
                                                                    <span
                                                                        class="text-danger small">{{ $quantityErrors[$detail->id] }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $detail->remarks }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">No item details
                                                                found
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-secondary">
                                    Data tidak ditemukan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Create Comparison Table Button (Global) -->
        @if (count($validComparisonItems) > 0)
            <div class="card-footer text-end">
                <button type="button" class="btn btn-primary" wire:click="createComparisonTable">
                    Create Comparison Table ({{ count($validComparisonItems) }} items)
                </button>
            </div>
        @endif

        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    @if ($table->total() > 0)
                        Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
                    @else
                        No data available
                    @endif
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
                            <option value="Waiting On Process Warehouse">待採購 (Waiting)</option>
                            <option value="Partial Delivery by Warehouse">購買中 (Processing)</option>
                            <option value="Done">完成 (Done)</option>
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

    <!-- Modal for Create Comparison Table confirmation -->
    <div wire:ignore.self class="modal fade" id="modalCreateComparison" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Comparison Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (count($mergedComparisonItems) > 0)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">No. CT</label>
                                    <input type="text" class="form-control" readonly
                                        value="{{ $comparisonNumber ?? 'CT01' }}">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Items</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mergedComparisonItems as $detail)
                                        <tr>
                                            <td>{{ $detail['item_name'] }}</td>
                                            <td>{{ $detail['unit'] }}</td>
                                            <td>{{ $detail['quantity'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No items selected</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">PIC</label>
                                    <input type="text" class="form-control" readonly
                                        value="{{ auth()->user()->name ?? 'Citra' }}">
                                </div>
                            </div>
                        </div>
                    @else
                        <p>No items selected for comparison.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" wire:click="confirmCreateComparisonTable"
                        class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            @this.set('selectedCustomerOrder', @js($customerOrders->first()->id))
        });

        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
            $('#modalSelectedStatus').modal('hide');
            $('#modalCreateComparison').modal('hide');
        });

        window.addEventListener('open-comparison-modal', event => {
            $('#modalCreateComparison').modal('show');
        });
    </script>
</div>
