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
                    @can('create-bot')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('bot.create') }}" class="btn btn-secondary text-white add-new btn-primary">
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
                        @can('update-bot')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>Session Name</th>
                        <th>Tenant Name</th>
                        <th class="text-center">Status</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($table->count() == 0)
                        <div class="text-center col-md-7 mx-auto px-3 pt-3">
                            <div class="alert alert-secondary">
                                Data tidak ditemukan
                            </div>
                        </div>
                    @else
                        @foreach ($table as $key => $item)
                            @php
                                $updateRoute = route('bot.update', $item->id);
                            @endphp

                            <tr wire:key="row{{ $item->id }}">
                                <td>
                                    {{ $key + 1 }}
                                </td>
                                <td>
                                    {{ $item->session_name }}
                                </td>
                                <td>
                                    {{ $item->tenant->tenant_name }}
                                </td>
                                <td class="text-center">
                                    {!! $item->statusBot['message'] !!}
                                </td>

                                <td>
                                    <div class="align-items-center">
                                        {{-- plug-connected-x --}}
                                        @switch($item->statusBot['status'])
                                            @case('Disconnected')
                                                @switch($item->statusBot['message'])
                                                    @case('Session Closed')
                                                        <a href="{{ route('bot.start-session', $item->id) }}" class="action-btn"
                                                            title="Start Session">
                                                            <i class="fa fa-play fa-sm me-2 fs-5"></i>
                                                        </a>
                                                    @break
                                                @endswitch
                                            @break

                                            @case('Connected')
                                                <a href="{{ route('bot.close-session', $item->id) }}" class="action-btn"
                                                    title="Close Session">
                                                    <i class="fa fa-pause fa-sm me-2 fs-5"></i>
                                                </a>
                                                <a href="{{ route('bot.logout-session', $item->id) }}" class="action-btn"
                                                    title="Logout Session">
                                                    <i class="fa fa-link-slash fa-sm me-2 fs-5"></i>
                                                </a>
                                            @break
                                        @endswitch
                                        @can('update-bot')
                                            <a href="{{ route('bot.edit', $item->id) }}" class="action-btn"
                                                title="edit">
                                                <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                            </a>
                                        @endcan

                                        @can('delete-bot')
                                            <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                                data-bs-target="#modalDelete{{ $item->id }}">
                                                <i class="fa fa-trash fa-sm me-2 fs-5"></i>
                                            </a>
                                            @include('admin.modal.delete')
                                        @endcan

                                        {{--  <a wire:click="modelId({{ $item->id }})" href="javascript:;"
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
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    @if ($table->count() > 0)
                        Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
                    @else
                        No data available
                    @endif
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
        setInterval(function() {
            @this.render();
        }, 5000);
    </script>
</div>
