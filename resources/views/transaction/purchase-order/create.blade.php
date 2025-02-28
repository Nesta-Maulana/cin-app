@extends('layouts.admin.app')
@section('title', 'Create Purchase Order / 创建采购单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Purchase Order / 创建采购单</h5>
                        <small class="text-muted">Fill in the form below / 请填写以下表单</small>
                    </div>
                    <a href="{{ route('purchase-order.index') }}" class="btn btn-secondary btn-sm" title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('purchase-order.store') }}" method="POST">
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

                        <!-- Purchase Order Header Section -->
                        <h6 class="text-primary mb-3">Purchase Order Header / 采购单头部</h6>
                        <div class="mb-3">
                            <label for="po_number" class="form-label">Purchase Order Number / 采购单编号</label>
                            <input type="text" id="po_number" name="po_number" class="form-control"
                                value="{{ $orderNumber }}" placeholder="e.g., PO-0001" readonly required>
                        </div>

                        <div class="mb-3">
                            <label for="customer_order_id" class="form-label">Customer Order Number / 客户订单编号</label>
                            <select class="form-select" id="customer_order_id" name="customer_order_id" required>
                                <option value="" disabled selected>Select a customer order / 选择客户订单</option>
                                @foreach ($customerOrders as $customerOrder)
                                    <option value="{{ $customerOrder->id }}"
                                        {{ $orderId == $customerOrder->id || old('customer_order_id') == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number . ' - ' . $customerOrder->customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date / 预计交货日期</label>
                            <input type="date" id="expected_delivery_date" name="expected_delivery_date"
                                class="form-control" value="{{ old('expected_delivery_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks / 请输入备注">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Purchase Order Details Section -->
                        <h6 class="text-primary mb-3">Purchase Order Details / 采购单详细信息</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailsTable">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>#</th>
                                        <th>Item Request Number / 请求编号</th>
                                        <th>Item Name / 物品</th>
                                        <th>Request Quantity / 需求数量</th>
                                        <th>Order Quantity / 订购数量</th>
                                        <th>UOM / 单位</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item rows will be added dynamically -->
                                    <tr>
                                        <td colspan="7" class="text-center">Select a customer order first / 请先选择客户订单</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Supplier Offers Section -->
                        <h6 class="text-primary mt-4 mb-2">Supplier Offers / 供应商报价</h6>
                        <button type="button" class="btn btn-secondary mb-3" id="addSupplierOffer">
                            <i class="fa fa-plus"></i> Add Supplier Offer / 添加供应商报价
                        </button>

                        <div id="supplierOffersContainer">
                            <!-- Supplier offers will be added dynamically here -->
                        </div>

                        <div class="my-4 d-flex justify-content-between">
                            <div>
                                <button type="submit" name="submit_type" value="draft"
                                    class="btn btn-secondary px-5 me-2">
                                    Save as Draft / 保存为草稿
                                </button>
                            </div>
                            <div>
                                <button type="submit" name="submit_type" value="submit" class="btn btn-primary px-5 me-2">
                                    Submit / 提交
                                </button>
                                <a href="{{ route('purchase-order.index') }}" class="btn btn-label-secondary">
                                    Cancel / 取消
                                </a>
                            </div>
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

            // Trigger change only if there's a valid order_id
            if (selectedOrderId && selectedOrderId !== "") {
                setTimeout(() => {
                    $('#customer_order_id').trigger('change');
                }, 1000);
            }

            let detailIndex = 0;
            let supplierIndex = 0;
            let selectedSuppliers = []; // Store selected suppliers
            let orderItems = []; // Store items with quantity > 0

            // When selecting a customer order, fetch related items
            $('#customer_order_id').change(function() {
                let orderId = $(this).val();
                let detailsTable = $('#detailsTable tbody');

                if (!orderId) return;

                detailsTable.html('<tr><td colspan="7" class="text-center">Loading... / 加载中...</td></tr>');

                $.ajax({
                    url: '{{ route('get-items-by-customer-order') }}',
                    type: 'GET',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        detailsTable.empty();
                        if (response.data.itemNeedToPurchases.length === 0) {
                            detailsTable.html(
                                '<tr><td colspan="7" class="text-center">No items to purchase / 没有可购买的物品</td></tr>'
                            );
                        } else {
                            response.data.itemNeedToPurchases.forEach((item) => {
                                addItemRow(item);
                            });
                        }
                    },
                    error: function() {
                        detailsTable.html(
                            '<tr><td colspan="7" class="text-center text-danger">Failed to fetch data / 获取数据失败</td></tr>'
                        );
                    }
                });
            });

            function addItemRow(itemRequests) {
                itemRequests.item_need_to_purchase_detail.forEach(itemNeedToPurchaseDetail => {
                    let newRow = `
                    <tr id="item-${detailIndex}" data-item-id="${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.item_id}">
                        <td>${detailIndex + 1}</td>
                        <td>
                            <input type="hidden" name="details[${detailIndex}][item_request_detail_id]" value="${itemNeedToPurchaseDetail.item_request_detail.id}">
                            <input type="hidden" name="details[${detailIndex}][item_need_to_purchase_detail_id]" value="${itemNeedToPurchaseDetail.id}">
                            ${itemNeedToPurchaseDetail.item_request_detail.item_request.request_number}
                        </td>
                        <td>
                            <input type="hidden" name="details[${detailIndex}][item_id]" value="${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.item_id}" class="item-id">
                            <span class="item-name">${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.item.name}</span>
                        </td>
                        <td>
                            <input type="hidden" name="details[${detailIndex}][request_quantity]" class="form-control request-quantity" value="${itemNeedToPurchaseDetail.item_request_detail.quantity}" readonly>
                            ${itemNeedToPurchaseDetail.item_request_detail.quantity}
                        </td>
                        <td>
                            <input type="number" name="details[${detailIndex}][order_quantity]" class="form-control order-quantity" min="0" value="0" required>
                        </td>
                        <td>
                            <input type="hidden" name="details[${detailIndex}][uom_id]" class="form-control uom-id" value="${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.id}">
                            <span class="uom-name">${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.unit_of_measurement.name}</span>
                        </td>
                    </tr>
                    `;
                    $('#detailsTable tbody').append(newRow);
                    detailIndex++;
                });

                // Add event listener for quantity changes
                $('.order-quantity').off('input').on('input', function() {
                    updateOrderItems();
                    updateAllSupplierOfferTables();
                });
            }

            // Function to collect all items with quantity > 0
            function updateOrderItems() {
                orderItems = [];
                let itemMap = new Map(); // Use Map to group by item_id and uom_id

                $('#detailsTable tbody tr').each(function() {
                    const row = $(this);
                    const itemId = row.find('.item-id').val();
                    const uomId = row.find('.uom-id').val();
                    const quantity = parseFloat(row.find('.order-quantity').val()) || 0;

                    if (quantity > 0 && itemId && uomId) {
                        // Create a unique key that combines BOTH item ID and UOM ID
                        const key = `${itemId}-${uomId}`;

                        if (itemMap.has(key)) {
                            // If this exact item+UOM combination already exists, add to its quantity
                            const existingItem = itemMap.get(key);
                            existingItem.quantity += quantity;
                        } else {
                            // Otherwise create a new entry - different UOM means different entry
                            itemMap.set(key, {
                                itemId: itemId,
                                itemName: row.find('.item-name').text(),
                                uomId: uomId,
                                uomName: row.find('.uom-name').text(),
                                quantity: quantity,
                                detailId: row.attr('id').replace('item-', '')
                            });
                        }
                    }
                });

                // Convert Map values to array
                orderItems = Array.from(itemMap.values());
            }

            // Function to add supplier offer
            $('#addSupplierOffer').click(function() {
                // Check if there are items with quantity > 0
                updateOrderItems();

                if (orderItems.length === 0) {
                    alert('Please add order quantities before adding supplier offers / 请在添加供应商报价前添加订购数量');
                    return;
                }

                $.ajax({
                    url: '{{ route('get-suppliers') }}',
                    type: 'GET',
                    success: function(response) {
                        addSupplierOfferRow(response.data);
                    },
                    error: function() {
                        alert('Failed to fetch supplier data / 获取供应商数据失败');
                    }
                });
            });

            function addSupplierOfferRow(suppliers) {
                let supplierOptions = suppliers.map(supplier =>
                    `<option value="${supplier.id}">${supplier.name}</option>`
                ).join('');

                // Get currencies for dropdown
                let currencyOptions = '';
                @php
                    $currencies = getCurrency();
                @endphp
                @foreach ($currencies as $code => $name)
                    currencyOptions +=
                        `<option value="{{ $code }}" {{ $code == 'idr' ? 'selected' : '' }}>{{ strtoupper($code) }}</option>`;
                @endforeach

                // Create card for each supplier offer
                let supplierCard = `
                <div id="supplier-card-${supplierIndex}" class="card mb-4 border-1">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Supplier Offer #${supplierIndex + 1}</h6>
                        <button type="button" class="btn btn-danger btn-sm remove-supplier" data-id="${supplierIndex}">
                            <i class="fa fa-trash"></i> Remove / 删除
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Supplier / 供应商</label>
                                <select name="offers[${supplierIndex}][supplier_id]" class="form-select supplier-select" required>
                                    <option value="" disabled selected>Please choose supplier / 请选择供应商</option>
                                    ${supplierOptions}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Currency / 货币</label>
                                <select name="offers[${supplierIndex}][currency]" class="form-select currency-select" required>
                                    ${currencyOptions}
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="offer-items-table-${supplierIndex}">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item / 物品</th>
                                        <th>Quantity / 数量</th>
                                        <th>UOM / 单位</th>
                                        <th>Offered Price / 报价</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Offer items will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Shipping Cost / 运输费</label>
                                <input type="number" name="offers[${supplierIndex}][shipping_cost]" class="form-control" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Other Cost / 其他费用</label>
                                <input type="number" name="offers[${supplierIndex}][other_cost]" class="form-control" step="0.01" min="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Remarks / 备注</label>
                                <input type="text" name="offers[${supplierIndex}][remarks]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                `;

                // Append the card to the container
                $('#supplierOffersContainer').append(supplierCard);

                // Update selected suppliers array
                selectedSuppliers.push(supplierIndex);

                // Fill the items table
                updateOfferItemsTable(supplierIndex);

                // Increment supplier index for next offer
                supplierIndex++;
            }

            function updateOfferItemsTable(supplierIdx) {
                let offerItemsTable = $(`#offer-items-table-${supplierIdx} tbody`);
                offerItemsTable.empty();

                // Add rows for each order item
                orderItems.forEach((item, idx) => {
                    let offerRow = `
                    <tr>
                        <td>
                            <input type="hidden" name="offers[${supplierIdx}][items][${idx}][item_id]" value="${item.itemId}">
                            ${item.itemName}
                        </td>
                        <td>
                            <input type="hidden" name="offers[${supplierIdx}][items][${idx}][quantity]" value="${item.quantity}">
                            ${item.quantity}
                        </td>
                        <td>
                            <input type="hidden" name="offers[${supplierIdx}][items][${idx}][uom_id]" value="${item.uomId}">
                            ${item.uomName}
                        </td>
                        <td>
                            <input type="number" name="offers[${supplierIdx}][items][${idx}][price]" class="form-control" step="0.01" min="0" required>
                        </td>
                    </tr>
                    `;
                    offerItemsTable.append(offerRow);
                });
            }

            function updateAllSupplierOfferTables() {
                selectedSuppliers.forEach(supplierIdx => {
                    updateOfferItemsTable(supplierIdx);
                });
            }

            // Remove supplier offer
            $(document).on('click', '.remove-supplier', function() {
                let supplierIdx = $(this).data('id');
                $(`#supplier-card-${supplierIdx}`).remove();

                // Remove from selected suppliers array
                const index = selectedSuppliers.indexOf(supplierIdx);
                if (index > -1) {
                    selectedSuppliers.splice(index, 1);
                }
            });

            // Form validation before submit
            $('form').on('submit', function(e) {
                let hasItems = false;
                let hasSuppliers = false;

                // Check if at least one item has quantity > 0
                $('.order-quantity').each(function() {
                    if (parseFloat($(this).val()) > 0) {
                        hasItems = true;
                        return false; // Break the loop
                    }
                });

                // Check if at least one supplier offer exists
                hasSuppliers = selectedSuppliers.length > 0;

                if (!hasItems) {
                    e.preventDefault();
                    alert('Please add at least one item with order quantity / 请至少添加一个有订购数量的物品');
                    return false;
                }

                // For draft, we don't need supplier offers
                if ($(document.activeElement).attr('name') === 'submit_type' &&
                    $(document.activeElement).val() === 'submit' &&
                    !hasSuppliers) {
                    e.preventDefault();
                    alert('Please add at least one supplier offer / 请至少添加一个供应商报价');
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush
