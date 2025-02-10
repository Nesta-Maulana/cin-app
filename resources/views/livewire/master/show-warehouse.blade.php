<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
                <div class="my-1 text-center text-md-start">
                    <label>
                        <input wire:model.debounce.500ms="search" type="search" class="form-control"
                            placeholder="Search..">
                    </label>
                </div>
                <div
                    class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
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
                        @can('create-warehouse')
                            <div class="flex-wrap my-1">
                                <a href="{{ route('warehouse.create') }}"
                                    class="btn btn-secondary text-white add-new btn-primary">
                                    <span>
                                        <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                        <span>Create Warehouse / 创建仓库</span>
                                    </span>
                                </a>
                            </div>
                        @endcan
                    @else
                        <div class="my-1 me-md-2">
                            <span class="px-1">{{ count($selected) }} data selected / 数据已选择</span>
                        </div>
                        <div class="flex-wrap my-1">
                            <a href="#" class="btn btn-secondary add-new btn-label-primary" data-bs-toggle="modal"
                                data-bs-target="#modalSelectedStatus">
                                Update Status / 更新状态
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            @can('update-warehouse')
                                <th>
                                    <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                        class="form-check-input">
                                </th>
                            @endcan
                            <th>#</th>
                            <th>Warehouse Name / 仓库名称</th>
                            <th>Location / 位置</th>
                            <th>Status / 状态</th>
                            <th>Actions / 操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table as $key => $item)
                            @php
                                $updateRoute = route('warehouse.update', $item->id);
                            @endphp

                            <tr wire:key="row{{ $item->id }}">
                                @can('update-warehouse')
                                    <td>
                                        <input style="width: 17px; height: 17px;" wire:model="selected"
                                            value="{{ $item->id }}" type="checkbox"
                                            class="dt-checkboxes form-check-input">
                                    </td>
                                @endcan
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->location ?? '-' }}</td>
                                <td>
                                    {!! $item->status !!}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a class="btn btn-sm btn-light me-2" data-bs-toggle="collapse"
                                            href="#collapseSections{{ $item->id }}" role="button"
                                            aria-expanded="false" aria-controls="collapseSections{{ $item->id }}"
                                            title="View Sections / 查看分区">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('update-warehouse')
                                            <a href="{{ route('warehouse.edit', $item->id) }}" class="action-btn"
                                                title="edit">
                                                <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                            </a>
                                        @endcan
                                        {{-- @can('delete-warehouse')
                                            <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                                data-bs-target="#modalDelete{{ $item->id }}">
                                                <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                            </a>
                                            @include('admin.modal.delete')
                                        @endcan --}}
                                    </div>
                                </td>
                            </tr>
                            <!-- Collapsible Warehouse Sections -->
                            <tr class="collapse" id="collapseSections{{ $item->id }}">
                                <td colspan="6">
                                    <table class="table table-bordered mt-2">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>#</th>
                                                <th>Section Name / 分区名称</th>
                                                <th>Status / 状态</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($item->warehouseSections as $section)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $section->name }}</td>
                                                    <td>{!! $section->status !!}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">No Sections Found /
                                                        没有找到分区
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Warehouses Found / 没有找到仓库</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
                <div class="align-self-start my-2 d-none d-md-block text-muted">
                    <small>
                        Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }}
                        data
                    </small>
                </div>
                {{ $table->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('close-modal', event => {
        $('.dropdown-toggle').dropdown('hide');
    })
</script>
