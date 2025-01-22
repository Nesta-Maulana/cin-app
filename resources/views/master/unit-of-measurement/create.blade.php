@extends('layouts.admin.app')
@section('title', 'Unit Of Measurement / 单位管理')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Unit Of Measurement / 创建单位</h5>
                        <small class="text-muted">Fill the form to create a new unit of measurement / 填写表单以创建新单位</small>
                    </div>
                    <a href="{{ route('unit-of-measurement.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('unit-of-measurement.store') }}" method="POST">
                        @csrf

                        <!-- Error Handling -->
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There are some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Name -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name / 名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Enter unit name (e.g., Kilogram) / 输入单位名称（例如：千克）" autofocus required />
                            </div>

                            <!-- Code -->
                            <div class="mb-3 col-md-6">
                                <label for="code" class="form-label">Code / 代码</label>
                                <input class="form-control" type="text" id="code" name="code"
                                    value="{{ old('code') }}" placeholder="Enter unit code (e.g., kg) / 输入单位代码（例如：kg）"
                                    required />
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="type" class="form-label">Type / 类型</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="" disabled selected>Select type / 选择类型</option>
                                    <option value="weight" {{ old('type') == 'weight' ? 'selected' : '' }}>Weight / 重量
                                    </option>
                                    <option value="volume" {{ old('type') == 'volume' ? 'selected' : '' }}>Volume / 容量
                                    </option>
                                    <option value="quantity" {{ old('type') == 'quantity' ? 'selected' : '' }}>Quantity / 数量
                                    </option>
                                    <option value="length" {{ old('type') == 'length' ? 'selected' : '' }}>Length / 长度
                                    </option>
                                    <option value="area" {{ old('type') == 'area' ? 'selected' : '' }}>Area / 面积</option>
                                    <option value="time" {{ old('type') == 'time' ? 'selected' : '' }}>Time / 时间</option>
                                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service / 服务
                                    </option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active / 活跃
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive / 不活跃
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description / 描述</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter a description (optional) / 输入描述（可选）">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('unit-of-measurement.index') }}" class="btn btn-label-secondary">Cancel /
                                取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
