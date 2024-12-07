<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control" placeholder="Search..">
                </label>
            </div>
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
                    @can('create-b-o-m')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('bom.create') }}" class="btn btn-secondary text-white add-new btn-primary">
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
                        <th>No</th>
                        <th>Request Number</th>
                        <th>Request Type</th>
                        <th>Project Name</th>
                        <th>Request Date</th>
                        <th>Requested By</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        @php
                            $no = ($table->currentPage() - 1) * $table->perPage() + $key + 1;
                            $updateRoute = route('bom.update', $item->id);

                        @endphp
                        <tr wire:key="row{{ $item->id }}" class="toggle-row"
                            data-target="details-row{{ $item->id }}">
                            <td>{{ $no }}</td>
                            <td>{{ $item->request_number }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->project_name }}</td>
                            <td>{{ $item->request_date }}</td>
                            <td>{{ $item->requested_by }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->statusName }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-eye fa-sm me-2 fs-5 cursor-pointer" title="View Details"></i>
                                    @can('update-bom')
                                        @if ($routeAlias == 'warehouse')
                                            <a href="{{ route('warehouse.requested-bom.edit', $item->id) }}" class="action-btn" title="Edit">
                                                <i class="fa fa-truck fa-sm me-2 fs-5"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('bom.edit', $item->id) }}" class="action-btn" title="Edit">
                                                <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    @can('delete-bom')
                                        @if ($routeAlias !== 'warehouse')
                                            <a href="#" class="action-btn" title="Delete" data-bs-toggle="modal"
                                                data-bs-target="#modalDelete{{ $item->id }}">
                                                <i class="fa fa-trash fa-sm fs-5"></i>
                                            </a>
                                            @include('admin.modal.delete')
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        <!-- BOM Details, hidden by default -->
                        <tr wire:key="details-row{{ $item->id }}" id="details-row{{ $item->id }}"
                            class="collapse">
                            <td colspan="9">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->bomDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->material->name }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $detail->material->unit->name }}</td>
                                                <td>{{ $detail->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="alert alert-secondary">
                                    Data tidak ditemukan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.fa-eye').forEach(eyeIcon => {
                    eyeIcon.addEventListener('click', function() {
                        // Get the parent row of the clicked icon
                        let parentRow = this.closest('tr');

                        // Get the target row ID from the parent row's data attribute
                        let targetId = parentRow.dataset.target;

                        // Find the target row by ID
                        let targetRow = document.getElementById(targetId);

                        // Toggle the collapse class on the target row
                        if (targetRow) {
                            targetRow.classList.toggle('collapse');
                        } else {
                            console.error('Target row not found for ID:', targetId);
                        }
                    });
                });
            });
        </script>


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
            // $('#modalStatus').modal('hide');
        })
    </script>
</div>
