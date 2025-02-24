@extends('layouts.admin.app')
@section('title', 'Edit Item Request / 编辑物品请求')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white mb-3">
                    <div class="card-title mb-0">
                        <h5 class="mb-0 text-white">@yield('title')</h5>
                        <small>Edit item request / 编辑物品请求</small>
                    </div>
                    <a href="{{ route('item-request.index') }}" class="btn btn-light btn-sm" title="Back / 返回">
                        <i class="ti ti-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('item-request.update', $itemRequest->id) }}" method="POST" id="itemRequestForm">
                        @csrf
                        @method('PUT')

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

                        <!-- Item Request Header -->
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="request_number" class="form-label">Request Number / 请求编号</label>
                                <input type="text" id="request_number" name="request_number" class="form-control"
                                    value="{{ $itemRequest->request_number }}" readonly />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="request_date" class="form-label">Request Date / 请求日期 <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="request_date" name="request_date" class="form-control"
                                    value="{{ old('request_date', $itemRequest->request_date->format('Y-m-d')) }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <!-- Customer Order -->
                            <div class="mb-3 col-md-6">
                                <label for="customer_order_id" class="form-label">Customer Order / 客户订单 <span
                                        class="text-danger">*</span></label>
                                <select id="customer_order_id" name="customer_order_id" class="form-select" required>
                                    <option value="" disabled>Select a customer order / 选择客户订单</option>
                                    @foreach ($customerOrders as $id => $order)
                                        <option value="{{ $id }}"
                                            {{ old('customer_order_id', $itemRequest->customer_order_id) == $id ? 'selected' : '' }}>
                                            {{ $order }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="remark" class="form-label">Remark / 备注</label>
                                <textarea id="remark" name="remark" class="form-control" rows="1"
                                    placeholder="Add remark for this request / 为此请求添加备注">{{ old('remark', $itemRequest->remarks) }}</textarea>
                            </div>
                        </div>

                        <!-- Item Request Details -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="items" class="form-label">Item Details / 物品详情</label>
                                <table class="table table-bordered" id="item-request-details-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item / 物品</th>
                                            <th>Specification / 规格</th>
                                            <th>UOM / 单位</th>
                                            <th>Quantity / 数量</th>
                                            <th>Remarks / 备注</th>
                                            <th>Action / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemRequest->details as $detail)
                                            <tr>
                                                <td>
                                                    <select name="items[]" class="form-select item" required>
                                                        <option value="" disabled>Select / 选择</option>
                                                        @foreach ($items as $id => $name)
                                                            <option value="{{ $id }}"
                                                                {{ $detail->item_id == $id ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="spesifications[]" class="form-control spesification" rows="1" readonly>{{ $detail->itemPriceHistory->itemUom->item->specification }}</textarea>
                                                </td>
                                                <td>
                                                    <select name="uoms[]" class="form-select uom" required>
                                                        <option value="" disabled>Select / 选择</option>
                                                        @foreach ($detail->itemPriceHistory->itemUom->item->itemUoms as $uom)
                                                            <option value="{{ $uom->id }}"
                                                                {{ $detail->itemPriceHistory->itemUom->id == $uom->id ? 'selected' : '' }}>
                                                                {{ $uom->unitOfMeasurement->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="quantities[]" class="form-control"
                                                        value="{{ $detail->quantity }}" required />
                                                </td>
                                                <td>
                                                    <input type="text" name="remarks[]" class="form-control"
                                                        value="{{ $detail->remarks }}"
                                                        placeholder="Remarks (optional) / 备注（可选）" />
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-item-row">Delete
                                                        / 删除</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-item-row">
                                    <i class="fa fa-plus-circle"></i> Add Item / 添加物品
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 me-2" name="request_status"
                                value="Need Approval Manager">Submit / 提交</button>
                            <button type="submit" class="btn btn-secondary px-5 me-2" name="request_status"
                                value="Draft">Save as Draft / 保存草稿</button>
                            <a href="{{ route('item-request.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailsTable = document.querySelector('#item-request-details-table tbody');
            const addItemButton = document.getElementById('add-item-row');

            // Add new row
            addItemButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="items[]" class="form-select item" required>
                            <option value="" disabled selected>Select / 选择</option>
                            @foreach ($items as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <textarea name="spesifications[]" class="form-control spesification" rows="1" readonly></textarea>
                    </td>
                    <td>
                        <select name="uoms[]" class="form-select uom" required>
                            <option value="" disabled selected>Select / 选择</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="quantities[]" class="form-control" placeholder="Quantity / 数量" required />
                    </td>
                    <td>
                        <input type="text" name="remarks[]" class="form-control" placeholder="Remarks (optional) / 备注（可选）" />
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item-row">Delete / 删除</button>
                    </td>
                `;
                detailsTable.appendChild(newRow);
            });

            // Remove row
            detailsTable.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-item-row')) {
                    const tbody = detailsTable.getElementsByTagName('tr');
                    if (tbody.length > 1) {
                        event.target.closest('tr').remove();
                    } else {
                        alert('Cannot delete the last row! / 不能删除最后一行！');
                    }
                }
            });

            // Update UOM and specification when item is selected
            detailsTable.addEventListener('change', function(event) {
                if (event.target.classList.contains('item')) {
                    const itemId = event.target.value;
                    const row = event.target.closest('tr');
                    const uomDropdown = row.querySelector('.uom');
                    const spesificationInput = row.querySelector('.spesification');

                    // Clear UOM dropdown before getting new data
                    uomDropdown.innerHTML =
                        `<option value="" disabled selected>Loading... / 加载中...</option>`;

                    // Call API to get UOM and specification
                    $.ajax({
                        url: "{{ route('get-uom-by-item') }}",
                        type: "GET",
                        data: {
                            item_id: itemId,
                        },
                        success: function(data) {
                            if (data.success) {
                                // Update specification
                                spesificationInput.value = data.uoms[0].item?.specification ||
                                    '';

                                // Update UOM dropdown
                                uomDropdown.innerHTML =
                                    `<option value="" disabled selected>Select / 选择</option>`;
                                data.uoms.forEach(uom => {
                                    uomDropdown.innerHTML +=
                                        `<option value="${uom.id}">${uom.unit_of_measurement.name}</option>`;
                                });
                            } else {
                                alert('No UOM found for the selected item! / 未找到对应单位！');
                            }
                        },
                        error: function() {
                            alert('Failed to fetch UOM! / 无法获取单位！');
                        }
                    });

        }
            });
        });
    </script>
@endpush
