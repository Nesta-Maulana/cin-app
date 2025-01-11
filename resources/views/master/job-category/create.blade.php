@extends('layouts.admin.app')
@section('title', 'Job Category / 工作类别')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Add a new job category / 添加新的工作类别</small>
                    </div>
                    <a href="{{ route('job-category.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <!-- Body -->
                <div class="card-body">
                    <form action="{{ route('job-category.store') }}" method="POST">
                        @csrf

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There are some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Fields -->
                        <div class="row">
                            <!-- Category Code -->
                            <div class="mb-3 col-md-6">
                                <label for="category_code" class="form-label">Category Code / 类别代码 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="category_code" name="category_code"
                                    value="{{ old('category_code') }}" placeholder="Enter category code / 输入类别代码"
                                    required />
                            </div>

                            <!-- Category Name -->
                            <div class="mb-3 col-md-6">
                                <label for="category_name" class="form-label">Category Name / 类别名称 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="category_name" name="category_name"
                                    value="{{ old('category_name') }}" placeholder="Enter category name / 输入类别名称"
                                    required />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Description -->
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description (Optional) / 描述（可选）</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter description / 输入描述">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Status -->
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态 <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active / 活跃
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive / 不活跃
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('job-category.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
