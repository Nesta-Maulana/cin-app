@extends('layouts.admin.app')
@section('title', 'Job Category / 职位类别')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit @yield('title')</h5>
                        <small class="text-muted">Update job category details / 更新职位类别详细信息</small>
                    </div>
                    <a href="{{ route('job-category.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('job-category.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀！</strong> There are some problems with your input. / 您的输入有一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Input: Category Code -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="category_code" class="form-label">Category Code / 类别代码</label>
                                <input class="form-control" type="text" id="category_code" name="category_code"
                                    value="{{ old('category_code', $data->category_code) }}"
                                    placeholder="Enter category code / 输入类别代码" required />
                                <small class="form-text text-muted">Category code must be unique and cannot be empty. /
                                    类别代码必须唯一且不能为空。</small>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="category_name" class="form-label">Category Name / 类别名称</label>
                                <input class="form-control" type="text" id="category_name" name="category_name"
                                    value="{{ old('category_name', $data->category_name) }}"
                                    placeholder="Enter category name / 输入类别名称" required />
                                <small class="form-text text-muted">Category name is required. / 类别名称为必填项。</small>
                            </div>
                        </div>

                        <!-- Input: Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description / 描述</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter category description (optional) / 输入类别描述（可选）">{{ old('description', $data->description) }}</textarea>
                                <small class="form-text text-muted">Description is optional. / 描述是可选项。</small>
                            </div>
                        </div>

                        <!-- Input: Status -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1"
                                        {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>
                                        Active / 活跃
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>
                                        Inactive / 不活跃
                                    </option>
                                </select>
                                <small class="form-text text-muted">Status is required. / 状态为必填项。</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update / 更新</button>
                            <a href="{{ route('job-category.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
