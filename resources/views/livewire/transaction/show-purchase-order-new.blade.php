<div>
    <!-- Pre-Purchase Order Section -->
    <div class="card mb-4">
        <div class="card-header border-bottom d-md-flex justify-content-between align-items-center">
            <h5 class="mb-0">Approved Pre-Purchase Orders</h5>
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control" placeholder="Search...">
                </label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">Pre-PO Number</th>
                        <th style="width: 20%">Customer</th>
                        <th style="width: 15%">Project</th>
                        <th style="width: 10%">Selected Supplier</th>
                        <th style="width: 15%">Status</th>
                        <th style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prePOTable as $index => $prePO)
                        <tr>
                            <td>
                                <i wire:click="togglePrePODetails({{ $prePO->id }})"
                                    class="fa fa-chevron-{{ in_array($prePO->id, $openedPrePODetails) ? 'down' : 'right' }} cursor-pointer"></i>
                            </td>
                            <td>{{ $prePO->pre_po_number }}</td>
                            <td>{{ $prePO->customerOrder->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $prePO->customerOrder->project_name ?? 'N/A' }}</td>
                            <td>
                                @if ($prePO->selectedSuppliers->isNotEmpty())
                                    @foreach ($prePO->selectedSuppliers as $supplier)
                                        <span class="badge bg-info me-1">{{ $supplier->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Not Selected</span>
                                @endif
                            </td>

                            <td>
                                <span
                                    class="badge bg-{{ $prePO->process_status === 'approved' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($prePO->process_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('pre-purchase-order.show', $prePO->id) }}" class="action-btn"
                                        title="View Details">
                                        <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                    </a>
                                    @can('create-purchase-order-new')
                                        @if ($prePO->selectedSuppliers->isNotEmpty())
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                    id="createPODropdown{{ $prePO->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    Create PO
                                                </button>
                                                <ul class="dropdown-menu"
                                                    aria-labelledby="createPODropdown{{ $prePO->id }}">
                                                    @foreach ($prePO->selectedSuppliers as $supplier)
                                                        @if ($prePO->canCreatePOForSupplier($supplier->id))
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('purchase-order-new.create', ['pre_purchase_order_id' => $prePO->id, 'supplier_id' => $supplier->id]) }}">
                                                                    For {{ $supplier->name }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @if (in_array($prePO->id, $openedPrePODetails))
                            <tr>
                                <td colspan="7" class="p-0">
                                    <div class="p-3 bg-light">
                                        <h6>Item Details</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                        <th>UOM</th>
                                                        <th>Selected Price</th>
                                                        <th>Selected Supplier</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($prePO->details as $detail)
                                                        <tr>
                                                            <td>
                                                                @if ($detail->itemRequestDetail)
                                                                    {{ $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}
                                                                @elseif($detail->manualItemRequestDetail)
                                                                    {{ $detail->manualItemRequestDetail->item_name }}
                                                                @else
                                                                    {{ $detail->item_name }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $detail->quantity }}</td>
                                                            <td>
                                                                @if ($detail->uom)
                                                                    {{ $detail->uom->unitOfMeasurement->name }}
                                                                @else
                                                                    {{ $detail->unit }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php
                                                                    // Check if there's a specific selection for this item
                                                                    $selection = $prePO
                                                                        ->itemSelections()
                                                                        ->where('pre_purchase_order_id', $prePO->id)
                                                                        ->get();

                                                                    if ($selection) {
                                                                        $specificSelection = $selection
                                                                            ->where(
                                                                                'item_key',
                                                                                $detail->itemRequestDetail
                                                                                    ->itemPriceHistory->itemUom->item
                                                                                    ->name .
                                                                                    '|' .
                                                                                    $detail->itemRequestDetail
                                                                                        ->itemPriceHistory->itemUom
                                                                                        ->unitOfMeasurement->name,
                                                                            )
                                                                            ->first();
                                                                        $supplierName = $specificSelection
                                                                            ? $specificSelection->quotation->supplier->name : 'Not Selected';
                                                                        $detailPrice = $specificSelection
                                                                            ? $specificSelection->quotation->quotationDetails()->where('pre_purchase_order_detail_id', $detail->id)->first()
                                                                            : null;
                                                                    } else {
                                                                        $detailPrice = null;
                                                                        $supplierName = 'Not Selected';
                                                                    }
                                                                @endphp
                                                                {{ $detailPrice ? number_format($detailPrice->offered_price_per_unit, 2) : 'N/A' }}
                                                            </td>
                                                            <td>
                                                                {{ $supplierName }}
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No approved pre-purchase orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- For the Pre-Purchase Orders section -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted">
                <small>
                    @if (is_object($prePOTable) && method_exists($prePOTable, 'total') && $prePOTable->total() > 0)
                        Showing {{ $prePOTable->firstItem() }} to {{ $prePOTable->lastItem() }} of
                        {{ $prePOTable->total() }} pre-purchase orders
                    @else
                        No pre-purchase orders found
                    @endif
                </small>
            </div>
            @if (is_object($prePOTable) && method_exists($prePOTable, 'links'))
                {{ $prePOTable->links() }}
            @endif
        </div>
    </div>

    <!-- Purchase Order Section -->
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <h5 class="mb-0">Purchase Orders</h5>
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                <div class="my-1 text-center text-md-start me-2">
                    <label>
                        <input wire:model.debounce.500ms="search" type="search" class="form-control"
                            placeholder="Search..">
                    </label>
                </div>
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
                    @can('create-purchase-order-new')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('purchase-order-new.create') }}"
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
                @else
                    <div class="my-1 me-md-2">
                        <span class="px-1">
                            {{ count($selected) }} data selected
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
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">PO Number</th>
                        <th style="width: 15%">Supplier</th>
                        <th style="width: 15%">Customer</th>
                        <th style="width: 15%">Project</th>
                        <th style="width: 10%">Total</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poTable as $key => $po)
                        <tr wire:key="row{{ $po->id }}">
                            <td>
                                <i wire:click="togglePODetails({{ $po->id }})"
                                    class="fa fa-chevron-{{ in_array($po->id, $openedPODetails) ? 'down' : 'right' }} cursor-pointer"></i>
                            </td>
                            <td>{{ $po->po_number }}</td>
                            <td>{{ $po->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $po->customerOrder->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $po->customerOrder->project_name ?? 'N/A' }}</td>
                            <td>{{ number_format($po->total_amount, 2) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $po->process_status === 'approved' ? 'success' : ($po->process_status === 'draft' ? 'secondary' : 'primary') }}">
                                    {{ ucfirst($po->process_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('purchase-order-new.show', $po->id) }}" class="action-btn"
                                        title="View Details">
                                        <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                    </a>

                                    @can('update-purchase-order-new')
                                        <a href="{{ route('purchase-order-new.edit', $po->id) }}" class="action-btn"
                                            title="Edit">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-purchase-order-new')
                                        <a href="javascript:;" class="action-btn" title="Delete" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $po->id }}">
                                            <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                        </a>
                                        @include('admin.modal.delete', [
                                            'route' => route('purchase-order-new.destroy', $po->id),
                                        ])
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @if (in_array($po->id, $openedPODetails))
                            <tr>
                                <td colspan="@can('update-purchase-order-new') 9 @else 8 @endcan" class="p-0">
                                    <div class="p-3 bg-light">
                                        <h6>Purchase Order Details</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                        <th>UOM</th>
                                                        <th>Original Price</th>
                                                        <th>Price</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($po->details as $detail)
                                                        <tr>
                                                            <td>{{ $detail->item_name }}</td>
                                                            <td>{{ $detail->quantity }}</td>
                                                            <td>
                                                                @if ($detail->itemUom)
                                                                    {{ $detail->itemUom->unitOfMeasurement->name }}
                                                                @else
                                                                    {{ $detail->unit }}
                                                                @endif
                                                            </td>
                                                            <td>{{ number_format($detail->original_price, 2) }}</td>
                                                            <td>{{ number_format($detail->price, 2) }}</td>
                                                            <td>{{ number_format($detail->subtotal, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" class="text-end">Subtotal:</th>
                                                        <th>{{ number_format($po->subtotal_price, 2) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="text-end">Tax
                                                            ({{ $po->tax_type === 'percentage' ? $po->tax_value . '%' : 'Fixed' }}):
                                                        </th>
                                                        <th>{{ number_format($po->tax_amount, 2) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="text-end">Shipping:</th>
                                                        <th>{{ number_format($po->shipping_cost, 2) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="text-end">Other Costs:</th>
                                                        <th>{{ number_format($po->other_cost, 2) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="text-end">Total:</th>
                                                        <th>{{ number_format($po->total_amount, 2) }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        @if ($po->prePurchaseOrder)
                                            <h6 class="mt-3">Selected Quotation Details</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Pre-PO Number</th>
                                                            <th>Supplier</th>
                                                            <th>Currency</th>
                                                            <th>Quotation Grand Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $selectedQuotation = $po->prePurchaseOrder->quotations
                                                                ->where('is_selected', true)
                                                                ->first();
                                                        @endphp
                                                        @if ($selectedQuotation)
                                                            <tr>
                                                                <td>{{ $po->prePurchaseOrder->pre_po_number }}</td>
                                                                <td>{{ $selectedQuotation->supplier->name }}</td>
                                                                <td>{{ $selectedQuotation->currency }}</td>
                                                                <td>{{ number_format($selectedQuotation->grand_total, 2) }}
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">No selected
                                                                    quotation found</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No Purchase Orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- For the Purchase Orders section -->
        <div class="card-footer d-md-flex justify-content-md-between align-items-center">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    @if (is_object($poTable) && method_exists($poTable, 'total') && $poTable->total() > 0)
                        Showing {{ $poTable->firstItem() }} to {{ $poTable->lastItem() }} of {{ $poTable->total() }}
                        purchase orders
                    @else
                        No purchase orders found
                    @endif
                </small>
            </div>
            @if (is_object($poTable) && method_exists($poTable, 'links'))
                {{ $poTable->links() }}
            @endif
        </div>

    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
        })
    </script>
</div>
