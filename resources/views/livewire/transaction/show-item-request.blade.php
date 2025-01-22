<div>
    <div class="card">
        <!-- Card Header -->
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <!-- Search Input -->
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search... / 搜索...">
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                @if (!$selected)
                    <!-- Pagination Dropdown -->
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

                    @can('create-item-request')
                        <!-- Add New Button -->
                        <div class="flex-wrap my-1">
                            <a href="{{ route('item-request.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                    <span>Create Item Request / 创建物品请求</span>
                                </span>
                            </a>
                        </div>
                    @endcan
                @else
                    <!-- Bulk Actions -->
                    <div class="my-1 me-md-2">
                        <span class="px-1">
                            {{ count($selected) }} data selected / 数据已选中
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

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        @can('update-item-request')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>Request Number / 请求编号</th>
                        <th>Request Date / 请求日期</th>
                        <th>Customer Order / 客户订单</th>
                        <th>Status / 状态</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $item)
                        <tr wire:key="row{{ $item->id }}">
                            @can('update-item-request')
                                <td>
                                    <input style="width: 17px; height: 17px;" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan
                            <td>{{ $item->request_number }}</td>
                            <td>{{ $item->request_date->format('Y-m-d') }}</td>
                            <td>{{ $item->customerOrder->order_number ?? '-' }}</td>
                            <td>{{ $item->request_status }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @can('update-item-request')
                                        @if (in_array($item->request_status, ['Draft', 'Waiting Approval Manager']))
                                            @if ($item->created_by == auth()->user()->id)
                                                <a href="{{ route('item-request.edit', $item->id) }}" class="action-btn"
                                                    title="Edit / 编辑">
                                                    <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                                </a>
                                            @endif
                                        @endif
                                    @endcan

                                    @can('read-item-request')
                                        <a href="{{ route('item-request.show', $item->id) }}" class="action-btn"
                                            title="View / 查看">
                                            <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-item-request')
                                        @if (in_array($item->request_status, ['Draft', 'Waiting Approval Manager']))
                                            @if ($item->created_by == auth()->user()->id)
                                                <a href="javascript:;" class="action-btn" title="Delete / 删除"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete{{ $item->id }}">
                                                    <i class="fa fa-trash fa-sm sm me-2 fs-5"></i>
                                                </a>
                                                @include('admin.modal.delete', [
                                                    'id' => $item->id,
                                                    'updateRoute' => route('item-request.update', $item->id),
                                                ])
                                            @endif
                                        @endif
                                    @endcan

                                    @can('approve-item-request')
                                        @if ($item->request_status == 'Waiting Approval Manager')
                                            <a href="javascript:;" class="action-btn" title="Approve / 审批"
                                                data-bs-toggle="modal" data-bs-target="#modalApprove{{ $item->id }}">
                                                <i class="fa fa-list-check fa-sm sm me-2 fs-5"></i>
                                            </a>
                                            @include('admin.modal.manual-approval', [
                                                'id' => $item->id,
                                                'updateRoute' => route('item-request.update', $item->id),
                                            ])
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-secondary">
                                    No data found / 没有找到数据
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
                    / 显示 {{ $table->firstItem() }} 到 {{ $table->lastItem() }} 的 {{ $table->total() }} 数据
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
