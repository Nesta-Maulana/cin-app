@extends('layouts.admin.app')
@section('title', 'Item Category / 项目类别')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Item Category / 创建项目类别</h5>
                        <small class="text-muted">Fill the form to create a new item category / 填写表单以创建新项目类别</small>
                    </div>
                    <a href="{{ route('item-category.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('item-category.store') }}" method="POST">
                        @csrf

                        <!-- Error Handling -->
                        @if ($errors->any())
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
                                    value="{{ old('name') }}" placeholder="Enter item category name / 输入项目类别名称" autofocus
                                    required />
                            </div>
                        </div>
                        <div class="row">
                            {{-- Item Type --}}
                            <div class="mb-3 col-md-6">
                                <label for="item_type_id" class="form-label">Item Type / 项目类型</label>
                                <select class="form-select" id="item_type_id" name="item_type_id" required>
                                    <option value="" disabled selected>Select item type / 选择项目类型</option>
                                    @foreach ($itemTypes as $idItemType => $itemType)
                                        <option value="{{ $idItemType }}">
                                            {{ $itemType }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Parent Category --}}
                            <div class="mb-3 col-md-6">
                                <label for="parent_id" class="form-label">Parent Category (Optional) / 父类别（可选）</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="" selected>No Parent or Select Parent / 无父类别或选择父类别</option>
                                </select>
                            </div>

                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description / 描述</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter category description / 输入类别描述">{{ old('description') }}</textarea>
                            </div>
                        </div>


                        <!-- Active Status -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active / 激活
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive / 未激活
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('item-category.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#item_type_id').on('change', function() {
                var itemTypeId = $(this).val();

                if (itemTypeId) {
                    // Lakukan request ke server
                    $.ajax({
                        url: "{{ route('get-parent-categories') }}", // Endpoint untuk get data
                        type: "GET",
                        data: {
                            item_type_id: itemTypeId,
                        },
                        success: function(data) {
                            $('#parent_id').empty();
                            $('#parent_id').append(
                                '<option value="" selected>No Parent or Select Parent / 无父类别或选择父类别</option>'
                                );
                            $.each(data, function(key, value) {
                                $('#parent_id').append('<option value="' + key + '">' +
                                    value + '</option>');
                            });
                        },
                        error: function() {
                            alert('Failed to fetch data! / 数据获取失败！');
                        }
                    });
                } else {
                    $('#parent_id').empty();
                    $('#parent_id').append(
                        '<option value="" selected>No Parent or Select Parent / 无父类别或选择父类别</option>');
                }
            });
        });
    </script>
@endpush
