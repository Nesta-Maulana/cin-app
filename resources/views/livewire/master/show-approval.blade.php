<div>
    <div class="card">
        <!-- Header Section -->
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
                    @can('create-approval')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('approval.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                    <span>
                                        Create Approval / 创建审批
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endcan
                @else
                    <div class="my-1 me-md-2">
                        <span class="px-1">
                            {{ count($selected) }} data selected / 数据已选择
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

        <!-- Main Table -->
        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        @can('update-approval')
                            <th>
                                <input style="width: 17px; height: 17px;" wire:model="selectAll" type="checkbox"
                                    class="form-check-input">
                            </th>
                        @endcan
                        <th>#</th>
                        <th>Approval Name / 审批名称</th>
                        <th>Description / 描述</th>
                        <th>Event / 事件</th>
                        <th>Status / 状态</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $item)
                        <tr>
                            @can('update-approval')
                                <td>
                                    <input style="width: 17px; height: 17px;" wire:model="selected"
                                        value="{{ $item->id }}" type="checkbox" class="dt-checkboxes form-check-input">
                                </td>
                            @endcan
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>{{ ucfirst($item->event) }}</td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $item->is_active ? 'Active / 活跃' : 'Inactive / 不活跃' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a class="btn btn-sm btn-light me-2" data-bs-toggle="collapse"
                                        href="#collapseLevels{{ $item->id }}" role="button" aria-expanded="false"
                                        aria-controls="collapseLevels{{ $item->id }}" title="View Levels / 查看级别">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @can('update-approval')
                                        <a href="{{ route('approval.edit', $item->id) }}" class="action-btn"
                                            title="Edit / 编辑">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan
                                    @can('delete-approval')
                                        <a href="javascript:;" class="action-btn" title="Delete / 删除" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                        </a>
                                        @include('admin.modal.delete', [
                                            'updateRoute' => route('approval.update', $item->id),
                                        ])
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        <!-- Collapsible Approval Levels -->
                        <tr class="collapse" id="collapseLevels{{ $item->id }}">
                            <td colspan="7">
                                <table class="table table-bordered mt-2">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>#</th>
                                            <th>Hierarchy Order / 层级顺序</th>
                                            <th>Approver Type / 审批人类型</th>
                                            <th>Reference ID / 参考ID</th>
                                            <th>On Approve / 批准时更新</th>
                                            <th>On Reject / 拒绝时更新</th>
                                            <th>Required / 必需</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($item->approvalLevels as $level)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $level->hierarchy_order }}</td>
                                                <td>{{ $level->class_name_approver_type }}</td>
                                                <td>{{ $level->approver->name }}</td>
                                                <td>{{ $level->updated_value_on_approve }}</td>
                                                <td>{{ $level->updated_value_on_reject }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $level->required ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $level->required ? 'Yes / 是' : 'No / 否' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No Levels Found / 没有找到级别</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No Approvals Found / 没有找到审批</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of
                    {{ $table->total() }}
                    data
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>
</div>
