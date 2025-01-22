@extends('layouts.admin.app')
@section('title', 'Item Category')

@section('content')
    <div class="row">
        <div class="col-md-9 mx-md-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Item Category</h5>
                        <small class="text-muted">Update the item category details</small>
                    </div>
                    <a href="{{ route('item-category.index') }}" class="btn p-0" title="Back">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('item-category.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Error Handling -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There are some problems with your input.<br><br>
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
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name', $data->name) }}" placeholder="Enter item category name" autofocus
                                    required />
                            </div>
                        </div>

                        <!-- Item Type -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="item_type_id" class="form-label">Item Type</label>
                                <select class="form-select" id="item_type_id" name="item_type_id" required>
                                    <option value="" disabled>Select item type</option>
                                    @foreach ($itemTypes as $idItemType => $itemType)
                                        <option value="{{ $idItemType }}"
                                            {{ old('item_type_id', $data->item_type_id) == $idItemType ? 'selected' : '' }}>
                                            {{ $itemType }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Parent Category -->
                            <div class="mb-3 col-md-6">
                                <label for="parent_id" class="form-label">Parent Category (Optional)</label>
                                <select class="form-select" id="parent_id" name="parent_id" @if ($data->parent_id === null) disabled @endif>
                                    <option value=""
                                        {{ old('parent_id', $data->parent_id) === null ? 'selected' : '' }}>No Parent
                                    </option>
                                    @foreach ($parentCategories as $idParentCategory => $parentCategory)
                                        <option value="{{ $idParentCategory }}"
                                            {{ old('parent_id', $data->parent_id) == $idParentCategory ? 'selected' : '' }}>
                                            {{ $parentCategory }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter category description">{{ old('description', $data->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1"
                                        {{ old('is_active', $data->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0"
                                        {{ old('is_active', $data->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Update</button>
                            <a href="{{ route('item-category.index') }}" class="btn btn-label-secondary">Cancel</a>
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
