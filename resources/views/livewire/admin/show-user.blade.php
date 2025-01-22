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
                        <select wire:model="paginate" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    @can('create-user')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('user.create') }}" class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="fa-solid fa-plus me-0 me-sm-1 fa-xs"></i>
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
                <thead class="table-light text-center">
                    <tr>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($table as $key => $item)
                        @php
                            $updateRoute = route('user.update', $item->id);
                        @endphp

                        <tr wire:key="row{{ $item->id }}">
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-primary">
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

                            <td>
                                {{ $item->username }}
                            </td>
                            <td>
                                @if (isset($item->roles) && count($item->roles) > 0)
                                    @php
                                        $role = $item->roles->first();
                                    @endphp
                                    <span class="badge bg-{{ roleColor($role->name) }}">
                                        <small>{{ $role->name }}</small>
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">

                                {!! isActive($item->id, $item->is_active) !!}

                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    @can('update-user')
                                        <a href="{{ route('user.edit', $item->id) }}" class="action-btn" title="edit">
                                            <i class="fa-solid fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="action-btn" title="Reset Password"
                                            wire:click="updatePassword('{{ $item->id }}')">
                                            <i class="fa-solid fa-unlock-keyhole fa-sm mx-2 fs-5"></i>
                                        </a>
                                    @endcan
                                    @can('delete-user')
                                        @include('admin.modal.delete')
                                        <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="fa-solid fa-trash fa-sm mx-2 fs-5"></i>
                                        </a>
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
    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
            $('#modalPassword').modal('hide');
        })
    </script>
</div>
