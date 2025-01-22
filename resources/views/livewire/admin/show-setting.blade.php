<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
            </div>
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                @if($table->isEmpty())
                @can('create-setting')
                <div class="flex-wrap my-1">
                    <a href="{{ route('setting.create') }}" class="btn btn-secondary text-white add-new btn-primary">
                        <span>
                            <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                            <span>
                                {{ $title }}
                            </span>
                        </span>
                    </a>
                </div>
                @endcan
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th></th>
                        <th>App</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                    @php
                    $updateRoute = route('setting.update', $item->id);
                    @endphp

                    <tr wire:key="row{{ $item->id }}">
                        <td>
                            <div class="avatar-wrapper">
                                <div class="avatar me-2">
                                    <img src="{{ imageAsset($item->logo) }}" alt="Logo" class="rounded">
                                </div>
                            </div>
                        </td>
                        <td class="col-3">
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-truncate">
                                    <span class="fw-semibold">
                                        {{ $item->app_name }}
                                    </span>
                                </a>
                                <small class="text-muted">Version : {{ $item->app_version }}</small>
                            </div>
                        </td>

                        <td class="col-4">
                            {{ $item->name }}
                        </td>

                        <td class="col-5">
                            {{ $item->address }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                @can('update-setting')
                                <a href="{{ route('setting.edit', $item->id) }}" class="action-btn" title="edit">
                                    <i class="ti ti-edit ti-sm me-2 fs-5"></i>
                                </a>
                                @endcan

                                @can('delete-setting')
                                <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                    data-bs-target="#modalDelete{{ $item->id }}">
                                    <i class="ti ti-trash ti-sm mx-2 fs-5"></i>
                                </a>
                                @include('admin.modal.delete')
                                @endcan

                                {{-- <a wire:click="modelId({{ $item->id }})" href="javascript:;"
                                    class="action-btn dropdown-toggle hide-arrow" title="more"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical ti-sm mx-1 fs-5"></i>
                                </a>

                                <div wire:ignore class="dropdown-menu dropdown-menu-end m-0" id="myDropdown">
                                    <a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalPassword">Password</a>
                                    <a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalStatus">Change Status</a>
                                </div> --}}
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