@extends('layouts.admin.app')
@section('title', 'Department / 部门')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title') / 编辑@yield('title')</h5>
                        <small class="text-muted">Update @yield('title') / 更新@yield('title')</small>
                    </div>
                    <a href="{{ route('department.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="fa fa-x fa-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('department.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀！</strong> There are some problems with your input. /
                                你的输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Department Name -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Name / 名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', $data->name) }}" placeholder="Enter Name / 输入名称" autofocus
                                    required />
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="slug" class="form-label">Slug / 网址别名</label>
                                <input class="form-control" type="text" id="slug" name="slug"
                                    value="{{ old('slug', $data->slug) }}" placeholder="Enter Slug / 输入网址别名" maxlength="3"
                                    required />
                            </div>
                        </div>

                        <!-- Is Active -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1"
                                        {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>
                                        Active / 启用</option>
                                    <option value="0"
                                        {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>
                                        Inactive / 不启用</option>
                                </select>
                            </div>
                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update / 更新</button>
                            <a href="{{ route('department.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
