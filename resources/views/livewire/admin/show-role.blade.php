<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control" placeholder="Search..">
                </label>
            </div>
            <div></div>
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
                @can('create-role')
                <div class="flex-wrap my-1">
                    <a href="{{ route('role.create') }}" class="btn btn-secondary text-white add-new btn-primary">
                        <span>
                            <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
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
                        <th>Nama Role</th>
                        <th class="text-center">Permission</th>
                        <th class="text-center">Jml User</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                    @php
                    $updateRoute = route('role.update', $item->id);
                    $permission = App\Models\Permission::all();
                    @endphp

                    <tr wire:key="row{{ $item->id }}">
                        <td class="col-4 ps-4">
                            {{ $item->name }}
                        </td>
                        <td class="col-4 text-center">
                            @if($item->permissions()->count() > 0)
                            <small class="text-muted">
                                {{ $item->permissions()->count() }} dari {{ $permission->count() }}
                            </small>
                            @endif
                        </td>

                        <td class="col-4 text-center">
                            {{ $item->users()->count() }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                @can('update-role')
                                <a href="{{ route('role.edit', $item->id) }}" class="action-btn" title="edit">
                                    <i class="ti ti-edit ti-sm me-2 fs-5"></i>
                                </a>
                                @endcan

                                @can('delete-role')
                                <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                    data-bs-target="#modalDelete{{ $item->id }}">
                                    <i class="ti ti-trash ti-sm mx-2 fs-5"></i>
                                </a>
                                @include('admin.modal.delete')
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <div class="text-center col-md-7 mx-auto px-3 pt-3">
                        <div class="alert alert-secondary">
                            Data tidak ditemukan
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