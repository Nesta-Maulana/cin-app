<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search / 搜索..">
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
                            {{ count($selected) }} data selected / 选中的数据
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

        <div class="table-responsive">
            <table class="table border-top">
                <thead class="table-light text-center">
                    <tr>
                        <th>User / 用户</th>
                        <th>Username / 用户名</th>
                        <th>Department / 部门</th>
                        <th>Role / 角色</th>
                        <th>Status / 状态</th>
                        <th>Action / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        @php
                            $updateRoute = route('user.update', $item->id);
                        @endphp
                        <tr wire:key="row{{ $item->id }}">
                            <td class="text-left">
                                {{ $item->name }} <br>
                                <small class="text-muted">{{ $item->email }}</small>
                            </td>

                            <td class="text-center">
                                {{ $item->username }}
                            </td>

                            <!-- Department Column -->
                            <td class="text-center">
                                @if ($item->departments->count() > 0)
                                    @foreach ($item->departments as $department)
                                        <span class="badge bg-primary">
                                            <small>{{ $department->name }}</small>
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No Department / 无部门</span>
                                @endif
                            </td>

                            <!-- Role Column -->
                            <td class="text-center">
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

                            <td class="text-center">
                                @can('update-user')
                                    <a href="{{ route('user.edit', $item->id) }}" class="action-btn" title="Edit / 编辑">
                                        <i class="fa-solid fa-edit fa-md me-1"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="action-btn" title="Reset Password / 重置密码"
                                        wire:click="updatePassword('{{ $item->id }}')">
                                        <i class="fa-solid fa-unlock-keyhole fa-md me-1"></i>
                                    </a>
                                @endcan
                                @can('delete-user')
                                    @include('admin.modal.delete')
                                    <a href="javascript:;" class="action-btn" title="Delete / 删除" data-bs-toggle="modal"
                                        data-bs-target="#modalDelete{{ $item->id }}">
                                        <i class="fa-solid fa-trash fa-md me-1"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <div class="text-center col-md-7 mx-auto px-3 pt-3">
                            <div class="alert alert-secondary">
                                No data found / 数据未找到
                            </div>
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} records /
                    显示 {{ $table->firstItem() }} 到 {{ $table->lastItem() }} 共 {{ $table->total() }} 条数据
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
