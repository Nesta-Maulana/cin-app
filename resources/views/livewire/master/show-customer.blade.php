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
                @can('create-customer')
                    <div class="flex-wrap my-1">
                        <a href="{{ route('customer.create') }}" class="btn btn-secondary text-white add-new btn-primary">
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
                        <th>#</th>
                        <th>Customer Code</th>
                        <th>Customer Name</th>
                        <th>Customer Address</th>
                        <th>Customer Tax Number</th>
                        <th>Customer Contact</th>
                        <th>Customer Phone</th>
                        <th>Customer Email</th>
                        <th>Status</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse($table as $key => $item)
                        @php
                            $updateRoute = route('customer.update', $item->id);
                        @endphp

                        <tr wire:key="row{{ $item->id }}">
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->customer_code }}</td>
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->customer_address }}</td>
                            <td>{{ $item->customer_tax_number }}</td>
                            <td>{{ $item->customer_contact }}</td>
                            <td>{{ $item->customer_phone_number }}</td>
                            <td>{{ $item->customer_email }}</td>
                            <td>{!! $item->status !!}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('customer.show', $item->id) }}" class="action-btn"
                                        title="Detail">
                                        <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                    </a>
                                    @can('update-customer')
                                        <a href="{{ route('customer.edit', $item->id) }}" class="action-btn"
                                            title="edit">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-customer')
                                        <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="fa fa-trash fa-sm me-2 fs-5"></i>
                                        </a>
                                        @include('admin.modal.delete')
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <div class="text-center col-md-7 mx-auto px-3 pt-3">
                            <div class="alert alert-secondary">
                                Data Not Found / 找不到数据
                            </div>
                        </div>
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
            // $('#modalStatus').modal('hide');
        })
    </script>
</div>
