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
                                    <option value="{{ $customerOrder->id }}">
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
                                        <th>Order Quantity / 交付数量</th>
                                        <th>UOM / 单位</th>
                                        <th>Remarks / 备注</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Supplier Offers Section -->
                        <h6 class="text-primary mt-4">Supplier Offers / 供应商报价</h6>
                        <button type="button" class="btn btn-secondary mb-2" id="addSupplierOffer">Add Supplier Offer /
                            添加供应商报价</button>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="offersTable">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier / 供应商</th>
                                        <th>Offered Price / 报价</th>
                                        <th>Shipping Cost / 运输费</th>
                                        <th>Other Cost / 其他费用</th>
                                        <th>Remarks / 备注</th>
                                        <th>Action / 操作</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('purchase-order.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
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
            let detailIndex = 0;
            let supplierIndex = 0;
            let selectedItems = []; // Menyimpan item yang dipilih
            let selectedSuppliers = []; // Menyimpan supplier yang dipilih

            // Ketika memilih customer order, ambil item terkait
            $('#customer_order_id').change(function() {
                let orderId = $(this).val();
                let detailsTable = $('#detailsTable tbody');

                if (!orderId) return;

                $.ajax({
                    url: '{{ route('get-items-by-customer-order') }}',
                    type: 'GET',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        detailsTable.empty();
                        response.data.itemNeedToPurchases.forEach((item) => {
                            addItemRow(item);
                        });
                    },
                    error: function() {
                        detailsTable.html(
                            '<tr><td colspan="6" class="text-center text-danger">Failed to fetch data</td></tr>'
                        );
                    }
                });
            });


            function addItemRow(itemRequests) {
                itemRequests.item_need_to_purchase_detail.forEach(itemNeedToPurchaseDetail => {
                    let newRow = `
                        <tr id="item-${detailIndex}">
                            <td>${detailIndex + 1}</td>
                            <td>
                                <input type="hidden" name="details[${detailIndex}][item_request_detail_id]" value="${itemNeedToPurchaseDetail.item_request_detail.id}">
                                ${itemNeedToPurchaseDetail.item_request_detail.item_request.request_number}
                            </td>
                            <td>
                                <input type="hidden" name="details[${detailIndex}][item_id]" value="${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.item_id}">
                                ${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.item.name}
                            </td>
                            <td>
                                <input type="hidden" name="details[${detailIndex}][request_quantity]" class="form-control" value="${itemNeedToPurchaseDetail.item_request_detail.quantity}" readonly class="form-control requested_quantity">
                                ${itemNeedToPurchaseDetail.item_request_detail.quantity}
                            </td>
                            <td>
                                <input type="number" name="details[${detailIndex}][fullfill_quantity]" class="form-control fullfill-quantity" min="0" required>
                            </td>
                            <td>
                                <input type="hidden" name="details[${detailIndex}][uom_id]" class="form-control" value="${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.unit_of_measurement_id}">
                                <input type="text" name="details[${detailIndex}][uom]" class="form-control" value="${itemNeedToPurchaseDetail.item_request_detail.item_price_history.item_uom.unit_of_measurement.name}" readonly>
                            </td>
                            <td>
                                <input type="text" name="details[${detailIndex}][remarks]" class="form-control">
                            </td>
                            </tr>
                            `;
                    /* <td>
                        <button type="button" class="btn btn-danger remove-item" data-id="item-${detailIndex}">Remove</button>
                    </td> */
                    $('#detailsTable tbody').append(newRow);
                    detailIndex++;
                });
            }


            // Fungsi untuk menambahkan supplier offer
            $('#addSupplierOffer').click(function() {
                $.ajax({
                    url: '{{ route('get-suppliers') }}',
                    type: 'GET',
                    success: function(response) {
                        addSupplierOfferRow(response.data);
                    },
                    error: function() {
                        alert('Failed to fetch supplier data');
                    }
                });
            });

            function addSupplierOfferRow(suppliers) {
                let supplierOptions = suppliers.map(supplier =>
                    `<option value="${supplier.id}">${supplier.name}</option>`
                ).join('');

                let supplierRow = `<tr id="supplier-${supplierIndex}">
                                        <td>${supplierIndex + 1}</td>
                                        <td><select name="offers[${supplierIndex}][supplier_id]" class="form-select supplier-select">
                                            <option value="" disabled selected>Please choose supplier</option>
                                            ${supplierOptions}
                                        </select></td>
                                        <td>
                                            <table class="table table-bordered offer-details" id="offer-details-${supplierIndex}">
                                                <thead>
                                                    <tr><th>Item</th><th>Quantity</th><th>UOM</th><th>Offered Price</th></tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </td>
                                        <td><input type="number" name="offers[${supplierIndex}][shipping_cost]" class="form-control" step="0.01" min="0"></td>
                                        <td><input type="number" name="offers[${supplierIndex}][other_cost]" class="form-control" step="0.01" min="0"></td>
                                        <td><input type="text" name="offers[${supplierIndex}][remarks]" class="form-control"></td>
                                        <td><button type="button" class="btn btn-danger remove-supplier" data-id="supplier-${supplierIndex}">Remove</button></td>
                                    </tr>`;

                $('#offersTable tbody').append(supplierRow);
                selectedSuppliers.push(supplierIndex);
                updateOfferItemRows(supplierIndex);
                supplierIndex++;
            }

            function updateOfferItemRows(supplierIndex) {
                let offerTable = $(`#offer-details-${supplierIndex} tbody`);
                offerTable.empty();
                selectedItems.forEach(itemId => {
                    let itemRow = `<tr id="offer-item-${supplierIndex}-${itemId}">
                        <td>${$(`#item-${itemId} td:nth-child(3)`).text()}</td>
                        <td>${$(`#item-${itemId} td:nth-child(4)`).text()}</td>
                        <td>${$(`#item-${itemId} td:nth-child(6) input`).val()}</td>
                        <td><input type="number" name="offers[${supplierIndex}][items][${itemId}][price]" class="form-control" step="0.01" min="0"></td>
                    </tr>`;
                    offerTable.append(itemRow);
                });
            }

            function updateAllSupplierOfferTables() {
                selectedSuppliers.forEach(supplierIndex => {
                    updateOfferItemRows(supplierIndex);
                });
            }


            // Remove item row
            $(document).on('click', '.remove-item', function() {
                let itemId = $(this).data('id');
                $(`#${itemId}`).remove();
            });

            // Remove supplier offer row
            $(document).on('click', '.remove-supplier', function() {
                let supplierId = $(this).data('id');
                $(`#${supplierId}`).remove();
            });
        });
    </script>
@endpush
