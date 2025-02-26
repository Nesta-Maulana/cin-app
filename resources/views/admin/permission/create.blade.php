@extends('layouts.admin.app')
@section('title', 'Permission / 权限')

@section('content')
<div class="row">
    <div class="col-md-9 mx-md-auto">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Create @yield('title')</h5>
                    <small class="text-muted">Create New Permission / 创建新权限</small>
                </div>
                <a href="{{ route('permission.index') }}" class="btn p-0" title="Back / 返回">
                    <i class="ti ti-x ti-sm text-muted"></i>
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('permission.store') }}" method="POST">
                    @csrf

                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There are some problems with your input. / 您的输入存在一些问题。<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Permission Name / 权限名称</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Permission Name / 权限名称" autofocus required />
                            <small class="text-muted">Example: user.create, user.edit, etc. / 例如：user.create, user.edit, 等</small>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="guard_name" class="form-label">Guard Name / 守卫名称</label>
                            <select class="form-select" id="guard_name" name="guard_name" required>
                                <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>web</option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>api</option>
                                <option value="sanctum" {{ old('guard_name') == 'sanctum' ? 'selected' : '' }}>sanctum</option>
                            </select>
                            <small class="text-muted">Authentication guard for this permission / 此权限的认证守卫</small>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="group" class="form-label">Group / 分组</label>
                            <input class="form-control" type="text" id="group" name="group" value="{{ old('group') }}"
                                placeholder="Permission Group / 权限分组" />
                            <small class="text-muted">Group for organizing permissions (optional) / 用于组织权限的分组（可选）</small>
                        </div>
                    </div>

                    <div class="my-2">
                        <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                        <a href="{{ route('permission.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
