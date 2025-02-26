@extends('layouts.admin.app')
@section('title', 'Edit Delivery Order')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Delivery Order / 编辑送货单</h5>
                        <small class="text-muted">Edit the form below / 编辑以下表单</small>
                    </div>
                    <a href="{{ route('delivery-order.index') }}" class="btn btn-secondary btn-sm" title="Back / 返回">
                        <i class="fa fa-x fa-sm text-muted"></i>
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('delivery-order.update', $deliveryOrder->id) }}" method="POST"
                        id="deliveryOrderForm">
                        @csrf
                        @method('PUT')

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
                                value="{{ $deliveryOrder->process_number }}" readonly required>
                        </div>

                        <div class="mb-3">
                            <label for="customer_order_id" class="form-label">Customer Order ID / 客户订单编号</label>
                            <select class="form-select" id="customer_order_id" name="customer_order_id" required>
                                <option value="" disabled>Select a customer order / 选择客户订单</option>
                                @foreach ($customerOrders as $customerOrder)
                                    <option value="{{ $customerOrder->id }}"
                                        {{ $deliveryOrder->customer_order_id == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number . ' - ' . $customerOrder->customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="process_date" class="form-label">Process Date / 处理日期</label>
                            <input type="date" id="process_date" name="process_date" class="form-control"
                                value="{{ old('process_date', $deliveryOrder->process_date) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks / 请输入备注">{{ old('remarks', $deliveryOrder->remarks) }}</textarea>
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
                                        <td colspan="11" class="text-center text-muted">Loading details... / 加载详细信息中...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="my-2">
                            <input type="hidden" name="submit_type" id="submit_type" value="">
                            <button type="submit" class="btn btn-primary px-5 me-2" onclick="setSubmitType('submit')">
                                Submit / 提交
                            </button>
                            <button type="submit" class="btn btn-secondary px-5 me-2" onclick="setSubmitType('Draft')">
                                Save as Draft / 保存草稿
                            </button>
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
        function setSubmitType(type) {
            document.getElementById('submit_type').value = type;
        }

        // Global variable to track validation states
        let validationStates = {};

        $(document).ready(function() {
            let selectedOrderId = $('#customer_order_id').val();
            let deliveryOrderDetails = @json($deliveryOrder->details);

            // Load initial details if we have a selected order
            if (selectedOrderId) {
                setTimeout(() => {
                    loadOrderDetails(selectedOrderId);
                }, 1000);
            }

            // Handle customer order change
            $('#customer_order_id').change(function() {
                let orderId = $(this).val();
                if (orderId) {
                    loadOrderDetails(orderId);
                }
            });

            function loadOrderDetails(orderId) {
                let detailsTable = $('#detailsTable tbody');
                detailsTable.html(
                    '<tr><td colspan="11" class="text-center text-muted">Loading... / 加载中...</td></tr>');

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
                                '<tr><td colspan="11" class="text-center text-muted">No item requests found / 未找到物品请求</td></tr>'
                            );
                            return;
                        }

                        response.data.forEach((item, index) => {
                            // Find existing detail if any
                            let existingDetail = deliveryOrderDetails.find(detail =>
                                detail.item_request_detail_id == item.id
                            );

                            let fulfillQuantity = existingDetail ? existingDetail.quantity : 0;
                            let detailRemarks = existingDetail ? existingDetail.remarks : '';

                            detailsTable.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_request_detail_id]" value="${item.id}" readonly>
                                        <input type="text" name="details[${index}][item_request_number]" class="form-control" value="${item.request_number}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_id]" class="form-control item_id" value="${item.item_id}" readonly>
                                        <input type="text" name="details[${index}][item_name]" class="form-control" value="${item.item_name}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="details[${index}][quantity]" class="form-control quantity" value="${item.quantity}" step="0.001" min="0" readonly required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_uom_id]" value="${item.item_uom_id}" readonly>
                                        <input type="text" name="details[${index}][item_uom]" class="form-control" value="${item.item_uom}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][stock]" class="form-control item_stock_${item.item_id}" value="${item.stock}" readonly>
                                        <p class="item_textual_${item.id}">${item.stock_textual}</p>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][warehouse_id]" value="${item.warehouse_id}" readonly>
                                        <input type="text" name="details[${index}][warehouse_name]" class="form-control" value="${item.warehouse_name}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][section_id]" value="${item.section_id}" readonly>
                                        <input type="text" name="details[${index}][section_name]" class="form-control" value="${item.section_name}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="details[${index}][fullfill_quantity]" class="form-control fullfill-quantity" value="${fulfillQuantity}" step="0.001" min="0" required>
                                        <small class="text-danger validation-error d-none">Fullfill quantity cannot exceed request quantity / 数量不能超过请求数量</small>
                                    </td>
                                    <td>
                                        <input type="hidden" name="details[${index}][item_uom_fullfill_id]" class="form-control item_fullfill_uom_id" value="${item.item_uom_id}" readonly>
                                        <input type="text" name="details[${index}][item_uom_fullfill]" class="form-control" value="${item.item_uom}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="details[${index}][remarks]" class="form-control" value="${detailRemarks}" placeholder="Remarks (optional) / 备注（可选）">
                                    </td>
                                </tr>
                            `);
                        });

                        // After loading details, attach validation handlers
                        attachValidationHandlers();
                    },
                    error: function() {
                        detailsTable.html(
                            '<tr><td colspan="11" class="text-center text-danger">Failed to fetch data / 获取数据失败</td></tr>'
                        );
                    }
                });
            }

            function validateFullfillQuantity(inputElement) {
                return new Promise((resolve, reject) => {
                    let row = inputElement.closest('tr');
                    let requestQuantity = parseFloat(row.find('.quantity').val());
                    let itemId = parseFloat(row.find('.item_id').val());
                    let fullfillQuantity = parseFloat(inputElement.val());
                    let itemFullFillUomId = parseFloat(row.find('.item_fullfill_uom_id').val());
                    let stock_actual = parseFloat(row.find(`.item_stock_${itemId}`).val());
                    let errorElement = inputElement.siblings('.validation-error');

                    // Reset validation state
                    validationStates[itemId] = false;
                    if (fullfillQuantity < 0) {
                        errorElement.text('Fullfill quantity cannot be negative / 不能为负数').removeClass(
                            'd-none');
                        inputElement.addClass('is-invalid');
                        resolve(false);
                        return;
                    }

                    if (fullfillQuantity > requestQuantity) {
                        errorElement.text('Fullfill quantity cannot exceed request quantity / 不能超过请求数量')
                            .removeClass('d-none');
                        inputElement.addClass('is-invalid');
                        resolve(false);
                        return;
                    }

                    $.ajax({
                        url: '{{ route('check-stock-availability') }}',
                        type: 'GET',
                        data: {
                            item_id: itemId,
                            item_uom_fullfill_id: itemFullFillUomId,
                            fullfill_quantity: fullfillQuantity,
                            stock_actual: stock_actual,
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                errorElement.addClass('d-none');
                                inputElement.removeClass('is-invalid');
                                // Update available stock display
                                row.find(`.item_stock_${itemId}`).val(response.available_stock);
                                row.find('.item_textual_' + itemId).text(response
                                    .available_stock.toFixed(3));
                                validationStates[itemId] = true;
                                resolve(true);
                            } else {
                                errorElement.text(response.message).removeClass('d-none');
                                inputElement.addClass('is-invalid');
                                validationStates[itemId] = false;
                                resolve(false);
                            }
                        },
                        error: function(xhr) {
                            let response = xhr.responseJSON || {
                                message: 'Validation failed / 验证失败'
                            };
                            errorElement.text(response.message).removeClass('d-none');
                            inputElement.addClass('is-invalid');
                            validationStates[itemId] = false;
                            resolve(false);
                        }
                    });
                });
            }

            function attachValidationHandlers() {
                let debounceTimeout;
                $('.fullfill-quantity').off('input').on('input', function() {
                    clearTimeout(debounceTimeout);
                    const inputElement = $(this);
                    debounceTimeout = setTimeout(() => {
                        validateFullfillQuantity(inputElement);
                    }, 500);
                });
            }

            // Form submission handling
            $('#deliveryOrderForm').on('submit', async function(e) {
                e.preventDefault();

                // Get submit type
                const submitType = $('#submit_type').val();

                // If it's a draft, allow submission without validation
                if (submitType === 'draft') {
                    this.submit();
                    return;
                }

                // Validate all fulfill quantities
                const validationPromises = $('.fullfill-quantity').map(function() {
                    return validateFullfillQuantity($(this));
                }).get();

                try {
                    const results = await Promise.all(validationPromises);
                    const isValid = results.every(result => result === true);

                    if (isValid) {
                        // All validations passed, submit the form
                        this.submit();
                    } else {
                        alertNotif('error', 'Please check the fulfill quantities / 请检查完成数量');
                    }
                } catch (error) {
                    console.error('Validation error:', error);
                    alertNotif('error', 'Validation failed / 验证失败');
                }
            });
        });
    </script>
@endpush
