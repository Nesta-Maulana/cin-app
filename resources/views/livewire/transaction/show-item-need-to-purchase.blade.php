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
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>Customer Order</th>
                        <th>Customer Name</th>
                        <th>Project Name</th>
                        <th>Total Items</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedOrders = $table->groupBy('customer_order_id');
                    @endphp

                    @forelse($groupedOrders as $customerOrderId => $group)
                        @php
                            $customerOrder = $group->first()->customerOrder;
                            $latestRequestDate = $group->max('request_date');
                            $totalItems = $group->sum(function ($item) {
                                return $item->itemNeedToPurchaseDetail->count();
                            });
                        @endphp

                        <tr>
                            <td>{{ $customerOrder->order_number ?? '-' }}</td>
                            <td>{{ $customerOrder->customer->customer_name ?? '-' }}</td>
                            <td>{{ $customerOrder->project_name ?? '-' }}</td>
                            <td>{{ $totalItems }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-primary me-2" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDetails{{ $customerOrderId }}" aria-expanded="false"
                                        aria-controls="collapseDetails{{ $customerOrderId }}" title="View Details / 查看详情">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    <a href="{{ route('item-need-to-purchase.show', $customerOrderId) }}"
                                        class="btn btn-sm btn-info text-white" title="View Detail / 查看详情">
                                        <i class="fa fa-info-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Collapsible Row for Item Requests Details -->
                        <tr class="collapse" id="collapseDetails{{ $customerOrderId }}">
                            <td colspan="5">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request Number</th>
                                                <th>Warehouse</th>
                                                <th>Item Name</th>
                                                <th>Quantity Needed</th>
                                                <th>Process Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($group as $itemNeedToPurchase)
                                                @foreach ($itemNeedToPurchase->itemNeedToPurchaseDetail as $detail)
                                                    <tr>
                                                        <td>{{ $detail->itemRequestDetail->itemRequest->request_number ?? '-' }}
                                                        </td>
                                                        <td>{{ $detail->warehouse->name ?? '-' }}</td>
                                                        <td>{{ $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? '-' }}
                                                        </td>
                                                        <td>
                                                            {{ getQuantity($detail->itemRequestDetail) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary">
                                                                {{ ucfirst($detail->itemNeedToPurchaseHeader->process_status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="alert alert-secondary">
                                    No data found.
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
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
        })
    </script>
</div>
