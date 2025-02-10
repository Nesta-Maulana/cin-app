<div>
    <div class="card">
        <!-- Card Header: Search, Pagination, Create Button, and Selected Actions -->
        <!-- 卡片头部：搜索、分页、添加按钮和选择操作 -->
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search.. / 搜索..">
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
                    @can('create-stock-entry')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('stock-entry.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                    <span>{{ $title }} / 添加库存记录</span>
                                </span>
                            </a>
                        </div>
                    @endcan
                @else
                    <div class="my-1 me-md-2">
                        <span class="px-1">
                            {{ count($selected) }} data selected / 已选择 {{ count($selected) }} 条数据
                        </span>
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

        <!-- Stock Entries Table -->
        <!-- 库存记录表格 -->
        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        @can('update-stock-entry')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>ID</th>
                        <th>Warehouse / 仓库</th>
                        <th>Section / 区域</th>
                        <th>Item / 物品</th>
                        <th>Supplier / 供应商</th>
                        <th>Destination / 目的地</th>
                        <th>Type / 类型</th>
                        <th>Stock Source / 库存来源</th>
                        <th>Quantity / 数量</th>
                        <th>UOM / 单位</th>
                        <th>Reference Number / 参考编号</th>
                        <th>Date / 日期</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        <tr wire:key="row{{ $item->id }}">
                            @can('update-stock-entry')
                                <td>
                                    <input style="width: 17px; height: 17px;" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->warehouse->name ?? '-' }}</td>
                            <td>{{ $item->section->name ?? '-' }}</td>
                            <td>{{ $item->item->name ?? '-' }}</td>
                            <td>{{ $item->supplier->name ?? '-' }}</td>
                            <td>{{ $item->warehouseDestination->name ?? '-' }}</td>
                            <td>{{ ucfirst($item->type) }}</td>
                            <td>{{ ucfirst($item->stock_source) }}</td>
                            <td>
                                {{ number_format($item->quantity, 2, '.', ',') }}
                            </td>
                            <td>{{ $item->itemUom->unitOfMeasurement->name ?? '-' }}</td>
                            <td>{{ $item->reference_number ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @can('update-stock-entry')
                                        <a href="{{ route('stock-entry.edit', $item->id) }}" class="action-btn"
                                            title="Edit / 编辑">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-stock-entry')
                                        <a href="javascript:;" class="action-btn" title="Delete / 删除" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                        </a>
                                        @include('admin.modal.delete', [
                                            'updateRoute' => route('stock-entry.update', $item->id),
                                        ])
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14">
                                <div class="alert alert-secondary text-center">
                                    Data not found / 未找到数据
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination and Data Information -->
        <!-- 分页和数据说明 -->
        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data / 显示
                    {{ $table->firstItem() }} 至 {{ $table->lastItem() }} 条，共 {{ $table->total() }} 条数据
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
        });
    </script>
</div>
