@extends('layouts.admin.app')
@section('title', 'Job Categories / 工作类别')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header border-bottom d-md-flex justify-content-between align-items-center">
                    <div class="my-1">
                        <label>
                            <input wire:model.debounce.500ms="search" type="search" class="form-control"
                                placeholder="Search... / 搜索...">
                        </label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <label>
                                <select wire:model="paginate" class="form-select">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                        @can('create-job-category')
                            <a href="{{ route('job-category.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-1"></i> Add New / 添加新类别
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Code / 类别代码</th>
                                <th>Category Name / 类别名称</th>
                                <th>Description / 描述</th>
                                <th>Actions / 操作</th>
                                <th>Status / 状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($table->currentPage() - 1) * $table->perPage();
                            @endphp
                            @forelse($table as $key => $item)
                                <tr wire:key="row{{ $item->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 text-body">{{ ++$no }}</p>
                                        </div>
                                    </td>
                                    <td>{{ $item->category_code }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->description ?? 'N/A' }}</td>
                                    <td>{!! $item->status !!}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @can('update-job-category')
                                                <a href="{{ route('job-category.edit', $item->id) }}"
                                                    class="btn btn-sm btn-info me-2" title="Edit / 编辑">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete-job-category')
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete{{ $item->id }}" title="Delete / 删除">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                @include('admin.modal.delete', [
                                                    'id' => $item->id,
                                                    'updateRoute' => route('job-category.destroy', $item->id),
                                                ])
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="alert alert-secondary mb-0">
                                            No data found / 未找到数据
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-body d-flex justify-content-between align-items-center pt-3 pb-2">
                    <div class="text-muted">
                        <small>
                            Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} entries
                            / 显示第 {{ $table->firstItem() }} 到第 {{ $table->lastItem() }} 条，共 {{ $table->total() }} 条数据
                        </small>
                    </div>
                    {{ $table->links() }}
                </div>
            </div>
        </div>

        <!-- JS Event Listener -->
        <script>
            window.addEventListener('close-modal', event => {
                $('.dropdown-toggle').dropdown('hide');
            });
        </script>
    </div>
@endsection
