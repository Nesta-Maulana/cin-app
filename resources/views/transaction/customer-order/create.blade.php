@extends('layouts.admin.app')
@section('title', 'Customer Order / 客户订单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create @yield('title')</h5>
                        <small class="text-muted">Add a new customer order / 添加新客户订单</small>
                    </div>
                    <a href="{{ route('customer-order.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('customer-order.store') }}" method="POST">
                        @csrf

                        <!-- Validation Errors -->
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

                        <!-- Customer Order Fields -->
                        <div class="row">
                            <!-- Order Number -->
                            <div class="mb-3 col-md-6">
                                <label for="order_number" class="form-label">Order Number / 订单编号 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="order_number" name="order_number"
                                    value="{{ old('order_number') }}" placeholder="Enter order number / 输入订单编号" required />
                            </div>

                            <!-- Customer -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_id" class="form-label">Customer / 客户 <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="" disabled selected>Select a customer / 选择客户</option>
                                    @foreach ($customers as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('customer_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Project Name -->
                            <div class="mb-3 col-md-6">
                                <label for="project_name" class="form-label">Project Name / 项目名称</label>
                                <input class="form-control" type="text" id="project_name" name="project_name"
                                    value="{{ old('project_name') }}" placeholder="Enter project name / 输入项目名称" />
                            </div>
                            <!-- Job Category -->
                            <div class="mb-3 col-md-6">
                                <label for="job_category_id" class="form-label">Job Category / 作业类别</label>
                                <select class="form-select" id="job_category_id" name="job_category_id">
                                    <option value="" disabled selected>Select a job category / 选择作业类别</option>
                                    @foreach ($jobCategories as $id => $category)
                                        <option value="{{ $id }}"
                                            {{ old('job_category_id') == $id ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                        </div>

                        <div class="row">
                            <!-- Order Date -->
                            <div class="mb-3 col-md-6">
                                <label for="order_date" class="form-label">Order Date / 订单日期 <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="order_date" name="order_date"
                                    value="{{ old('order_date') }}" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="dpp" class="form-label">DPP / 基本税额</label>
                                <input type="number" step="0.01" class="form-control" name="dpp"
                                    placeholder="e.g., 100.00 / 例如 100.00" value="{{ old('dpp', 0) }}" required />
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description / 描述</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter description / 输入描述">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2" name="order_status"
                                value="Active">Submit / 提交</button>
                            <button type="submit" class="btn btn-secondary px-5 me-2" name="order_status"
                                value="Draft">Draft / 草稿</button>
                            <a href="{{ route('customer-order.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
