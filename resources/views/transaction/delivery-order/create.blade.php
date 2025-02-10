@extends('layouts.admin.app')
@section('title', 'Delivery Order')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Delivery Order / 创建送货单</h5>
                        <small class="text-muted">Fill in the form below / 请填写以下表单</small>
                    </div>
                    <a href="{{ route('delivery-order.index') }}" class="btn btn-secondary btn-sm" title="Back / 返回">
                        <i class="fa fa-x fa-sm text-muted"></i>
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('delivery-order.store') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 出错了!</strong> Please check your input / 请检查您的输入.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif



                        <!-- Delivery Order Header Section -->
                        <h6 class="text-primary mb-3">Delivery Order Header / 送货单头部</h6>
                        <div class="mb-3">
                            <label for="process_number" class="form-label">Process Number / 处理编号</label>
                            <input type="text" id="process_number" name="process_number" class="form-control"
                                value="{{ $orderNumber }}" placeholder="e.g., DO-0001" readonly required>
                        </div>
                        @php
                            // Retrieve order_id from query string, if present.
                            $orderId = request()->query('order_id');
                        @endphp
                        <div class="mb-3">
                            <label for="customer_order_id" class="form-label">Customer Order ID / 客户订单编号</label>
                            <select class="form-select" id="customer_order_id" name="customer_order_id" required>
                                <option value="" disabled selected>Select a customer order / 选择客户订单</option>
                                @foreach ($customerOrders as $key => $customerOrder)
                                    <option value="{{ $customerOrder->id }}"
                                        {{ $orderId == $customerOrder->id || old('customer_order_id') == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number . ' - ' . $customerOrder->customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="process_date" class="form-label">Process Date / 处理日期</label>
                            <input type="date" id="process_date" name="process_date" class="form-control"
                                value="{{ old('process_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks / 请输入备注">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Delivery Order Details Section -->
                        <h6 class="text-primary mb-3">Delivery Order Details / 送货单详细信息</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailsTable">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>#</th>
                                        <th>Item Request Number / 请求编号</th>
                                        <th>Item Name/ 物品</th>
                                        <th>Request Quantity / 数量</th>
                                        <th>UOM / 单位</th>
                                        <th>Stock On Warehouse / 仓库</th>
                                        <th>Warehouse / 仓库</th>
                                        <th>Section / 区域</th>
                                        <th>Fullfill Quantity / 数量</th>
                                        <th>Fullfill UOM / 单位</th>
                                        <th>Remarks / 备注</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="loadingRow">
                                        <td colspan="8" class="text-center text-muted">Select Customer Order first... /
                                            请先选择客户订单...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="addRow">Add Detail Row / 添加详细行</button>
                        </div> --}}

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('delivery-order.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
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
            let selectedOrderId = $('#customer_order_id').val();

            // Pastikan trigger change hanya dilakukan jika ada order_id yang valid
            if (selectedOrderId && selectedOrderId !== "") {
                setTimeout(() => {
                    $('#customer_order_id').trigger('change');
                }, 1000);
            }

            $('#customer_order_id').change(function() {
                let orderId = $(this).val();
                let detailsTable = $('#detailsTable tbody');

                if (!orderId) return;

                detailsTable.html(
                    '<tr><td colspan="8" class="text-center text-muted">Loading... / 加载中...</td></tr>');

                $.ajax({
                    url: '{{ route('get-item-requests-by-customer-order') }}',
                    type: 'GET',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        detailsTable.empty();
                        if (response.data.length === 0) {
                            detailsTable.html(
                                '<tr><td colspan="8" class="text-center text-muted">No item requests found / 未找到物品请求</td></tr>'
                            );
                            return;
                        }
                        response.data.forEach((item, index) => {
                            detailsTable.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_request_detail_id]" class="form-control" value="${item.id}" readonly>
                                        <input type="text" name="details[${index}][item_request_number]" class="form-control" value="${item.request_number}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_id]" class="form-control" value="${item.item_id}" readonly>
                                        <input type="text" name="details[${index}][item_name]" class="form-control" value="${item.item_name}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="details[${index}][quantity]" class="form-control quantity" value="${item.quantity}" step="0.001" min="0" required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_uom_id]" class="form-control" value="${item.item_uom_id}" readonly>
                                        <input type="text" name="details[${index}][item_uom]" class="form-control" value="${item.item_uom}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="details[${index}][stock]" class="form-control" value="${item.stock}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][warehouse_id]" class="form-control" value="${item.warehouse_id}" readonly>
                                        <input type="text" name="details[${index}][warehouse_name]" class="form-control" value="${item.warehouse_name}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][section_id]" class="form-control" value="${item.section_id}" readonly>
                                        <input type="text" name="details[${index}][section_name]" class="form-control" value="${item.section_name}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="details[${index}][fullfill_quantity]" class="form-control fullfill-quantity" value="0"  step="0.001" min="0" required>
                                        <small class="text-danger validation-error d-none">Fullfill quantity cannot exceed request quantity / 数量不能超过请求数量</small>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_uom_fullfill_id]" class="form-control" value="${item.item_uom_id}" readonly>
                                        <input type="text" name="details[${index}][item_uom_fullfill]" class="form-control" value="${item.item_uom}" readonly>
                                    </td>
                                    <td><input type="text" name="details[${index}][remarks]" class="form-control" placeholder="Remarks / 备注"></td>
                                </tr>
                            `);
                        });
                    },
                    error: function() {
                        detailsTable.html(
                            '<tr><td colspan="8" class="text-center text-danger">Failed to fetch data / 获取数据失败</td></tr>'
                        );
                    }
                });
                $(document).on('keyup', '.fullfill-quantity', function() {
                    console.log(this);
                    validateFullfillQuantity($(this));
                });
            });
        });

        function validateFullfillQuantity(inputElement) {
            let rowIndex = inputElement.data('index');
            let requestQuantity = parseFloat(inputElement.closest('tr').find('.quantity').val());
            let fullfillQuantity = parseFloat(inputElement.val());
            console.log(fullfillQuantity);
            console.log(requestQuantity);
            let errorElement = inputElement.siblings('.validation-error');

            if (fullfillQuantity < 0) {
                errorElement.text('Fullfill quantity cannot be negative / 不能为负数').removeClass('d-none');
                inputElement.addClass('is-invalid');
            } else if (fullfillQuantity > requestQuantity) {
                errorElement.text('Fullfill quantity cannot exceed request quantity / 不能超过请求数量').removeClass('d-none');
                inputElement.addClass('is-invalid');
            } else {
                errorElement.addClass('d-none');
                inputElement.removeClass('is-invalid');
            }
        }
    </script>
@endpush
