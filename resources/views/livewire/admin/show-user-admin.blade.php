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
                    @can('create-user-admin')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('user-admin.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
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
                        @can('update-user-admin')
                            <th>
                                <input style="width: 17px; height: 17px" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        @php
                            $updateRoute = route('user-admin.update', $item->id);
                        @endphp

                        <tr wire:key="row{{ $item->id }}">
                            @can('update-user-admin')
                                <td>
                                    <input style="width: 17px; height: 17px" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan

                            <td class="col-4">
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ userInitial($item->id) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#" class="text-body text-truncate">
                                            <span class="fw-semibold">
                                                {{ $item->name }}
                                            </span>
                                        </a>
                                        <small class="text-muted">{{ $item->email }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="col-3">
                                {{ $item->username }}
                            </td>

                            <td class="col-3">
                                @if ($item->getRoleNames()->first())
                                    <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
                                        <span
                                            class="badge badge-dot bg-{{ roleColor($item->getRoleNames()->first()) }} me-1"></span>
                                        <small class="text-secondary">{{ $item->getRoleNames()->first() }}</small>
                                    </div>
                                @endif
                            </td>

                            <td class="col-2 text-center">
                                {!! isActive($item->status) !!}
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    @can('update-user-admin')
                                        <a href="{{ route('user-admin.edit', $item->id) }}" class="action-btn"
                                            title="edit">
                                            <i class="ti ti-edit ti-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('update-user-admin')
                                        <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="ti ti-trash ti-sm mx-2 fs-5"></i>
                                        </a>
                                        @include('admin.modal.delete')
                                    @endcan

                                    @can('update-user-admin')
                                        <a wire:click="modelId({{ $item->id }})" href="javascript:;"
                                            class="action-btn dropdown-toggle hide-arrow" title="more"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical ti-sm mx-1 fs-5"></i>
                                        </a>

                                        <div wire:ignore class="dropdown-menu dropdown-menu-end m-0" id="myDropdown">
                                            <a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#modalPassword">Password</a>
                                            <a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#modalStatus">Change Status</a>
                                        </div>
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

    @include('admin.modal.password')
    @include('admin.modal.status')

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
            $('#modalPassword').modal('hide');
            $('#modalStatus').modal('hide');
            $('#modalSelectedStatus').modal('hide');
        })
    </script>
</div>
