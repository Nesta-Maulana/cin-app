<div>
    <div class="card">
        <!-- Card Header -->
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search... / 搜索...">
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
                    @can('create-delivery-order')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('delivery-order.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                    <span>Create Delivery Order / 创建送货单</span>
                                </span>
                            </a>
                        </div>
                    @endcan
                @else
                    <div class="my-1 me-md-2">
                        <span class="px-1">
                            {{ count($selected) }} data selected / 已选择的数据
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
                        @can('update-delivery-order')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>Process Number / 处理编号</th>
                        <th>Process Date / 处理日期</th>
                        <th>Customer Order / 客户订单</th>
                        <th>Status / 状态</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $item)
                        @php
                            $approvalRequest = $item->approvalRequest('create')->first();
                        @endphp
                        <tr wire:key="row{{ $item->id }}">
                            @can('update-delivery-order')
                                <td>
                                    <input style="width: 17px; height: 17px;" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan
                            <td>{{ $item->process_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->process_date)->format('Y-m-d') }}</td>
                            <td>{{ $item->customerOrder->order_number ?? '-' }}</td>
                            <td>
                                {{ $item->process_status }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @can('read-delivery-order')
                                        <a href="{{ route('delivery-order.show', $item->id) }}" class="action-btn"
                                            title="View / 查看">
                                            <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('update-delivery-order')
                                        @if ($item->process_status == 'Waiting Approval Manager')
                                            <a href="{{ route('delivery-order.edit', $item->id) }}" class="action-btn"
                                                title="Edit / 编辑">
                                                <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    @can('approve-delivery-order')
                                        @if (!is_null($approvalRequest))
                                            @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\User')
                                                @if (Auth::user()->id == $approvalRequest->currentLevel->approver->approver_reference_id)
                                                    <a href="javascript:;" class="action-btn" title="Approval Process"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                        <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\Role')
                                                @if (Auth::user()->hasRole($approvalRequest->currentLevel->approver->name))
                                                    @if (is_null($approvalRequest->currentLevel->department_id))
                                                        <a href="javascript:;" class="action-btn" title="Approval Process"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                            <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                        </a>
                                                    @else
                                                        @if (count(array_intersect(
                                                                    $item->createdBy->departments->pluck('id')->toArray(),
                                                                    auth()->user()->departments->pluck('id')->toArray())) > 0)
                                                            <a href="javascript:;" class="action-btn"
                                                                title="Approval Process" data-bs-toggle="modal"
                                                                data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                                <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                            @include('admin.modal.approval')
                                        @endif
                                    @endcan
                                    {{-- @can('delete-delivery-order')
                                        @if ($item->process_status == 'pending')
                                            <a href="javascript:;" class="action-btn" title="Delete / 删除"
                                                data-bs-toggle="modal" data-bs-target="#modalDelete{{ $item->id }}">
                                                <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                            </a>
                                            @include('admin.modal.delete', [
                                                'id' => $item->id,
                                                'updateRoute' => route('delivery-order.destroy', $item->id),
                                            ])
                                        @endif
                                    @endcan --}}
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
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data / 显示
                    {{ $table->firstItem() }} 到 {{ $table->lastItem() }} 的 {{ $table->total() }} 数据
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
