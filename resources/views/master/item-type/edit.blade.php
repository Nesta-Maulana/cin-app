@extends('layouts.admin.app')
@section('title', 'Item Type')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Item Type / 编辑项目类型</h5>
                        <small class="text-muted">Update the details of the item type / 更新项目类型的详细信息</small>
                    </div>
                    <a href="{{ route('item-type.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('item-type.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Error Handling -->
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops! There are some problems with your input. / 哎呀！您的输入有一些问题。</strong>
                                <br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Name -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="name" class="form-label">Name / 名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', $data->name) }}" placeholder="Enter item type name / 输入项目类型名称"
                                    autofocus required />
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description / 描述</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter item type description / 输入项目类型描述">{{ old('description', $data->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1"
                                        {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>Active / 激活
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>Inactive / 未激活
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update / 更新</button>
                            <a href="{{ route('item-type.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
