<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search... / 搜索..." />
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
                @can('create-supplier')
                    <div class="flex-wrap my-1">
                        <a href="{{ route('supplier.create') }}" class="btn btn-secondary text-white add-new btn-primary">
                            <span>
                                <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                <span>
                                    Add Supplier / 添加供应商
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
                        <th>Supplier Name / 供应商名称</th>
                        <th>Tax Number / 税号</th>
                        <th>Phone / 电话</th>
                        <th>Email / 电子邮件</th>
                        <th>Address / 地址</th>
                        <th>Business Type / 商业类型</th>
                        <th>Status / 状态</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        <tr wire:key="row{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->tax_number ?? '-' }}</td>
                            <td>{{ $item->phone ?? '-' }}</td>
                            <td>{{ $item->email ?? '-' }}</td>
                            <td>{{ $item->address ?? '-' }}</td>
                            <td>{{ $item->business_type ?? '-' }}</td>
                            <td>{!! $item->status ?? '-' !!}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Show Button -->
                                    <a href="{{ route('supplier.show', $item->id) }}" class="action-btn"
                                        title="Show / 查看">
                                        <i class="fa fa-eye fa-sm me-2"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    @can('update-supplier')
                                        <a href="{{ route('supplier.edit', $item->id) }}" class="action-btn"
                                            title="Edit / 编辑">
                                            <i class="fa fa-edit fa-sm me-2"></i>
                                        </a>
                                    @endcan

                                    <!-- Delete Button -->
                                    @can('delete-supplier')
                                        <a href="javascript:;" class="action-btn" title="Delete / 删除" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="fa fa-trash fa-sm me-2"></i>
                                        </a>
                                        @include('admin.modal.delete', [
                                            'updateRoute' => route('supplier.update', $item->id),
                                        ])
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <div class="text-center col-md-7 mx-auto px-3 pt-3">
                            <div class="alert alert-secondary">
                                No data found / 未找到数据
                            </div>
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data /
                    显示 {{ $table->firstItem() }} 到 {{ $table->lastItem() }}，共 {{ $table->total() }} 条数据
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
        })
    </script>
</div>
