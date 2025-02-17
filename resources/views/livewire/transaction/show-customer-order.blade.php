<div>
    <div class="card">
        <!-- Header Section -->
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

                @can('create-customer-order')
                    <!-- Add New Button -->
                    <div class="flex-wrap my-1">
                        <a href="{{ route('customer-order.create') }}"
                            class="btn btn-secondary text-white add-new btn-primary">
                            <span>
                                <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                <span>Create Order / 创建订单</span>
                            </span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>Order Number / 订单编号</th>
                        <th>Customer Name / 客户名称</th>
                        <th>Project Name / 项目名称</th>
                        <th>Order Date / 订单日期</th>
                        <th>Status / 状态</th>
                        <th>Total Amount / 总金额</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        @php
                            $updateRoute = route('customer-order.update', $item->id);
                            $approvalRequest = $item->approvalRequest('delete')->first();
                        @endphp

                        <tr wire:key="row{{ $item->id }}">
                            <td>{{ $item->order_number }}</td>
                            <td>{{ $item->customer->customer_name }}</td>
                            <td>{{ $item->project_name ?? '-' }}</td>
                            <td>{{ $item->order_date->format('Y-m-d') }}</td>
                            <td>{!! ucfirst($item->status) !!}</td>
                            <td>{{ number_format($item->total_amount, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @can('view-customer-order')
                                        <!-- View Detail Button -->
                                        <a href="{{ route('customer-order.show', $item->id) }}" class="action-btn"
                                            title="View / 查看">
                                            <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('update-customer-order')
                                        <a href="{{ route('customer-order.edit', $item->id) }}" class="action-btn"
                                            title="Edit / 编辑">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-customer-order')
                                        @if (!is_null($approvalRequest))
                                            <a href="javascript:;" class="action-btn disabled"
                                                title="Delete Waiting Approval from {{ $approvalRequest->currentLevel->approver->name }}">
                                                <i class="fa fa-lock fa-sm me-2 fs-5 text-secondary"></i>
                                            </a>
                                        @else
                                            @if ($item->is_active)
                                                <a href="javascript:;" class="action-btn" title="Delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete{{ $item->id }}">
                                                    <i class="fa fa-trash fa-sm me-2 fs-5"></i>
                                                </a>
                                                @include('admin.modal.delete')
                                            @endif
                                        @endif
                                    @endcan
                                    @can('approve-delete-customer-order')
                                        @if (!is_null($approvalRequest))
                                            @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\User')
                                                @if (Auth::user()->id == $approvalRequest->currentLevel->approver_reference_id)
                                                    <a href="javascript:;" class="action-btn" title="Approval Process"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                        <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\Role')
                                                {{-- @if (Auth::user()->hasRole($approvalRequest->currentLevel->approver->name))
                                                    <a href="javascript:;" class="action-btn" title="Approval Process"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalApprove{{ $approvalRequest->id }}">
                                                        <i class="fa fa-list-check fa-sm me-2 fs-5"></i>
                                                    </a>
                                                @endif --}}
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
                                </div>
                            </td>

                        </tr>
                    @empty
                        <div class="text-center col-md-7 mx-auto px-3 pt-3">
                            <div class="alert alert-secondary">
                                Data not found / 数据未找到
                            </div>
                        </div>
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
