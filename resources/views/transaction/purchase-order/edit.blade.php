@extends('layouts.admin.app')
@section('title', 'Edit Purchase Order / 编辑采购单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Purchase Order / 编辑采购单</h5>
                        <small class="text-muted">Update the purchase order information / 更新采购单信息</small>
                    </div>
                    <a href="{{ route('purchase-order.show', $data->id) }}" class="btn btn-secondary btn-sm" title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('purchase-order.update', $data->id) }}" method="POST">
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

                        <!-- Purchase Order Header Section -->
                        <h6 class="text-primary mb-3">Purchase Order Header / 采购单头部</h6>
                        <div class="mb-3">
                            <label for="po_number" class="form-label">Purchase Order Number / 采购单编号</label>
                            <input type="text" id="po_number" name="po_number" class="form-control"
                                value="{{ $data->po_number }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="customer_order_id" class="form-label">Customer Order Number / 客户订单编号</label>
                            <select class="form-select" id="customer_order_id" name="customer_order_id" {{ $data->process_status !== 'Draft' ? 'disabled' : '' }}>
                                <option value="" disabled>Select a customer order / 选择客户订单</option>
                                @foreach ($customerOrders as $customerOrder)
                                    <option value="{{ $customerOrder->id }}" {{ $data->customer_order_id == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number . ' - ' . $customerOrder->customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($data->process_status !== 'Draft')
                                <input type="hidden" name="customer_order_id" value="{{ $data->customer_order_id }}">
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date / 预计交货日期</label>
                            <input type="date" id="expected_delivery_date" name="expected_delivery_date"
                                class="form-control" value="{{ old('expected_delivery_date', $data->expected_delivery_date ? date('Y-m-d', strtotime($data->expected_delivery_date)) : '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks / 请输入备注">{{ old('remarks', $data->remarks) }}</textarea>
                        </div>

                        <!-- Purchase Order Details Section -->
                        <h6 class="text-primary mb-3">Purchase Order Details / 采购单详细信息</h6>

                        @if($data->process_status == 'Draft')
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
                                            <th>Remarks / 备注</th>
                                            <th>Actions / 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->details as $index => $detail)
                                            <tr id="detail-row-{{ $index }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                                                    <input type="hidden" name="details[{{ $index }}][item_request_detail_id]" value="{{ $detail->item_request_detail_id }}">
                                                    {{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemRequest->request_number : '-' }}
                                                </td>
                                                <td>
                                                    <input type="hidden" name="details[{{ $index }}][item_id]" value="{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item_id : '' }}" class="item-id">
                                                    <span class="item-name">{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name : '-' }}</span>
                                                </td>
                                                <td>
                                                    {{ $detail->itemRequestDetail ? $detail->itemRequestDetail->quantity : '-' }}
                                                </td>
                                                <td>
                                                    <input type="number" name="details[{{ $index }}][order_quantity]" class="form-control order-quantity" min="0.001" step="0.001" value="{{ $detail->quantity }}" required>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="details[{{ $index }}][uom_id]" value="{{ $detail->item_uom_id }}" class="uom-id">
                                                    <span class="uom-name">{{ $detail->uom ? $detail->uom->unitOfMeasurement->name : '-' }}</span>
                                                </td>
                                                <td>
                                                    <input type="text" name="details[{{ $index }}][remarks]" class="form-control" value="{{ $detail->remarks }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-detail" data-index="{{ $index }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex mb-4">
                                <button type="button" class="btn btn-secondary" id="addItemBtn" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                    <i class="fa fa-plus"></i> Add Item / 添加物品
                                </button>
                            </div>

                            <!-- Supplier Offers Section -->
                            <h6 class="text-primary mt-4 mb-2">Supplier Offers / 供应商报价</h6>
                            <button type="button" class="btn btn-secondary mb-3" id="addSupplierOffer">
                                <i class="fa fa-plus"></i> Add Supplier Offer / 添加供应商报价
                            </button>

                            <div id="supplierOffersContainer">
                                @foreach($data->offers as $offerIndex => $offer)
                                    <div id="supplier-card-{{ $offerIndex }}" class="card mb-4 border-1">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Supplier Offer #{{ $offerIndex + 1 }}</h6>
                                            <button type="button" class="btn btn-danger btn-sm remove-supplier" data-index="{{ $offerIndex }}">
                                                <i class="fa fa-trash"></i> Remove / 删除
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3 mt-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">Supplier / 供应商</label>
                                                    <input type="hidden" name="offers[{{ $offerIndex }}][id]" value="{{ $offer->id }}">
                                                    <select name="offers[{{ $offerIndex }}][supplier_id]" class="form-select supplier-select" required>
                                                        <option value="" disabled>Please choose supplier / 请选择供应商</option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}" {{ $offer->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Currency / 货币</label>
                                                    <select name="offers[{{ $offerIndex }}][currency]" class="form-select" required>
                                                        @foreach($currencies as $code => $name)
                                                            <option value="{{ $code }}" {{ $offer->currency == $code ? 'selected' : '' }}>
                                                                {{ strtoupper($code) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="offer-items-table-{{ $offerIndex }}">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item / 物品</th>
                                                            <th>Quantity / 数量</th>
                                                            <th>UOM / 单位</th>
                                                            <th>Offered Price / 报价</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            // Group offer details by item name and UOM
                                                            $groupedOfferDetails = [];
                                                            foreach($offer->offerDetails as $detail) {
                                                                $itemId = $detail->purchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item_id ?? null;
                                                                $itemName = $detail->purchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? 'Unknown Item';
                                                                $uomId = $detail->purchaseOrderDetail->item_uom_id ?? null;
                                                                $uomName = $detail->purchaseOrderDetail->uom->unitOfMeasurement->name ?? 'Unknown UOM';
                                                                $price = $detail->offered_price_per_unit;

                                                                $key = $itemId . '-' . $uomId . '-' . $price;

                                                                if (!isset($groupedOfferDetails[$key])) {
                                                                    $groupedOfferDetails[$key] = [
                                                                        'itemId' => $itemId,
                                                                        'itemName' => $itemName,
                                                                        'uomId' => $uomId,
                                                                        'uomName' => $uomName,
                                                                        'quantity' => 0,
                                                                        'price' => $price
                                                                    ];
                                                                }

                                                                $groupedOfferDetails[$key]['quantity'] += $detail->quantity;
                                                            }
                                                        @endphp

                                                        @forelse($groupedOfferDetails as $itemIndex => $group)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="offers[{{ $offerIndex }}][items][{{ $loop->index }}][item_id]" value="{{ $group['itemId'] }}">
                                                                    {{ $group['itemName'] }}
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="offers[{{ $offerIndex }}][items][{{ $loop->index }}][quantity]" value="{{ $group['quantity'] }}" class="item-quantity">
                                                                    {{ $group['quantity'] }}
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="offers[{{ $offerIndex }}][items][{{ $loop->index }}][uom_id]" value="{{ $group['uomId'] }}">
                                                                    {{ $group['uomName'] }}
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="offers[{{ $offerIndex }}][items][{{ $loop->index }}][price]" class="form-control" step="0.01" min="0" value="{{ round($group['price']*$group['quantity']) }}" required>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center">No items found / 未找到物品</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Shipping Cost / 运输费</label>
                                                    <input type="number" name="offers[{{ $offerIndex }}][shipping_cost]" class="form-control" step="0.01" min="0" value="{{ $offer->shipping_cost }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Other Cost / 其他费用</label>
                                                    <input type="number" name="offers[{{ $offerIndex }}][other_cost]" class="form-control" step="0.01" min="0" value="{{ $offer->other_cost }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Remarks / 备注</label>
                                                    <input type="text" name="offers[{{ $offerIndex }}][remarks]" class="form-control" value="{{ $offer->remarks }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Read-only display for non-Draft status -->
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Purchase order details cannot be modified as the status is not Draft. / 采购单状态不是草稿，无法修改详细信息。
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light text-nowrap">
                                        <tr>
                                            <th>#</th>
                                            <th>Item Request Number / 请求编号</th>
                                            <th>Item Name / 物品</th>
                                            <th>Order Quantity / 订购数量</th>
                                            <th>UOM / 单位</th>
                                            <th>Remarks / 备注</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->details as $index => $detail)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemRequest->request_number : '-' }}</td>
                                                <td>{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name : '-' }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ $detail->uom ? $detail->uom->unitOfMeasurement->name : '-' }}</td>
                                                <td>{{ $detail->remarks }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="my-4 d-flex justify-content-between">
                            <div>
                                @if($data->process_status == 'Draft')
                                    <button type="submit" name="submit_type" value="draft" class="btn btn-secondary px-5 me-2">
                                        Save as Draft / 保存为草稿
                                    </button>
                                @endif
                            </div>
                            <div>
                                @if($data->process_status == 'Draft')
                                    <button type="submit" name="submit_type" value="submit" class="btn btn-primary px-5 me-2">
                                        Submit / 提交
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary px-5 me-2">
                                        Update / 更新
                                    </button>
                                @endif
                                <a href="{{ route('purchase-order.show', $data->id) }}" class="btn btn-label-secondary">
                                    Cancel / 取消
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item / 添加物品</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="searchItem" class="form-control" placeholder="Search items... / 搜索物品...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 30%">Item / 物品</th>
                                    <th style="width: 15%">Available Quantity / 可用数量</th>
                                    <th style="width: 15%">UOM / 单位</th>
                                    <th style="width: 15%">Request Number / 请求编号</th>
                                    <th style="width: 20%">Actions / 操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be loaded here dynamically -->
                                <tr>
                                    <td colspan="6" class="text-center">Searching for items... / 正在搜索物品...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close / 关闭</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let detailIndex = {{ count($data->details) }};
            let supplierIndex = {{ count($data->offers) }};
            let selectedSuppliers = [];
            let orderItems = []; // Store items with quantity > 0
            let deletedDetails = [];
            let deletedOffers = [];

            // Initialize selectedSuppliers array with existing offers
            @foreach($data->offers as $index => $offer)
                selectedSuppliers.push({{ $index }});
            @endforeach

            // Function to collect all items with quantity > 0
            function updateOrderItems() {
                orderItems = [];
                let itemMap = new Map(); // Use Map to group by item_id and uom_id

                $('#detailsTable tbody tr').each(function() {
                    const row = $(this);
                    if (row.is(':visible')) { // Only count visible rows
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
                                    itemName: row.find('.item-name').text() || row.find('td:eq(2)').text().trim(),
                                    uomId: uomId,
                                    uomName: row.find('.uom-name').text() || row.find('td:eq(5)').text().trim(),
                                    quantity: quantity
                                });
                            }
                        }
                    }
                });

                // Convert Map values to array
                orderItems = Array.from(itemMap.values());
            }

            // Update offer tables when quantity changes
            $(document).on('input', '.order-quantity', function() {
                updateOrderItems();
                updateAllSupplierOfferTables();
            });

            // Remove detail row
            $(document).on('click', '.remove-detail', function() {
                const index = $(this).data('index');
                const row = $(`#detail-row-${index}`);
                const detailId = row.find('input[name^="details"][name$="[id]"]').val();

                if (detailId) {
                    deletedDetails.push(detailId);
                    // Add a hidden input to track deleted details
                    $('form').append(`<input type="hidden" name="deleted_details[]" value="${detailId}">`);
                }

                row.hide();
                updateOrderItems();
                updateAllSupplierOfferTables();
            });

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
                        <button type="button" class="btn btn-danger btn-sm remove-supplier" data-index="${supplierIndex}">
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
                    // Skip if supplier card doesn't exist (was deleted)
                    if ($(`#supplier-card-${supplierIdx}`).length) {
                        updateOfferItemsTable(supplierIdx);
                    }
                });
            }

            // Remove supplier offer
            $(document).on('click', '.remove-supplier', function() {
                let supplierIdx = $(this).data('index');
                let offerId = $(`#supplier-card-${supplierIdx} input[name^="offers"][name$="[id]"]`).val();

                if (offerId) {
                    deletedOffers.push(offerId);
                    // Add a hidden input to track deleted offers
                    $('form').append(`<input type="hidden" name="deleted_offers[]" value="${offerId}">`);
                }

                $(`#supplier-card-${supplierIdx}`).remove();

                // Remove from selected suppliers array
                const index = selectedSuppliers.indexOf(parseInt(supplierIdx));
                if (index > -1) {
                    selectedSuppliers.splice(index, 1);
                }
            });

            // Add item button click
            $('#addItemBtn').click(function() {
                // Fetch available items from the customer order
                $.ajax({
                    url: '{{ route('get-items-by-customer-order') }}',
                    type: 'GET',
                    data: {
                        order_id: $('#customer_order_id').val()
                    },
                    success: function(response) {
                        // Populate items table
                        let itemsTable = $('#itemsTable tbody');
                        itemsTable.empty();

                        if (response.data.itemNeedToPurchases.length === 0) {
                            itemsTable.html(
                                '<tr><td colspan="6" class="text-center">No items available / 没有可用的物品</td></tr>'
                            );
                        } else {
                            let rowNumber = 1;
                            response.data.itemNeedToPurchases.forEach((item) => {
                                item.item_need_to_purchase_detail.forEach(detail => {
                                    // Skip items that are already in the table
                                    let itemId = detail.item_request_detail.item_price_history.item_uom.item_id;
                                    let uomId = detail.item_request_detail.item_price_history.item_uom.unit_of_measurement_id;

                                    // Check if this item+UOM is already in the table
                                    let exists = false;
                                    $('#detailsTable tbody tr:visible').each(function() {
                                        let existingItemId = $(this).find('.item-id').val();
                                        let existingUomId = $(this).find('.uom-id').val();

                                        if (existingItemId == itemId && existingUomId == uomId) {
                                            exists = true;
                                            return false; // Break the loop
                                        }
                                    });

                                    if (!exists) {
                                        let requestQuantity = detail.item_request_detail.quantity;
                                        let availableQuantity = requestQuantity - detail.item_request_detail.purchase_order_details_sum_quantity;

                                        if (availableQuantity > 0) {
                                            let itemRow = `
                                            <tr>
                                                <td>${rowNumber}</td>
                                                <td>${detail.item_request_detail.item_price_history.item_uom.item.name}</td>
                                                <td>${availableQuantity}</td>
                                                <td>${detail.item_request_detail.item_price_history.item_uom.unit_of_measurement.name}</td>
                                                <td>${detail.item_request_detail.item_request.request_number}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm add-item-to-po"
                                                        data-item-id="${itemId}"
                                                        data-item-name="${detail.item_request_detail.item_price_history.item_uom.item.name}"
                                                        data-uom-id="${uomId}"
                                                        data-uom-name="${detail.item_request_detail.item_price_history.item_uom.unit_of_measurement.name}"
                                                        data-available-quantity="${availableQuantity}"
                                                        data-request-id="${detail.item_request_detail.id}"
                                                        data-request-number="${detail.item_request_detail.item_request.request_number}">
                                                        <i class="fa fa-plus"></i> Add / 添加
                                                    </button>
                                                </td>
                                            </tr>`;
                                            itemsTable.append(itemRow);
                                            rowNumber++;
                                        }
                                    }
                                });
                            });

                            if (rowNumber === 1) {
                                itemsTable.html(
                                    '<tr><td colspan="6" class="text-center">No available items found / 没有找到可用物品</td></tr>'
                                );
                            }
                        }
                    },
                    error: function() {
                        $('#itemsTable tbody').html(
                            '<tr><td colspan="6" class="text-center text-danger">Failed to fetch data / 获取数据失败</td></tr>'
                        );
                    }
                });
            });

            // Filter items in the add item modal
            $('#searchItem').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $('#itemsTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Add item to purchase order from modal
            $(document).on('click', '.add-item-to-po', function() {
                const itemId = $(this).data('item-id');
                const itemName = $(this).data('item-name');
                const uomId = $(this).data('uom-id');
                const uomName = $(this).data('uom-name');
                const availableQuantity = $(this).data('available-quantity');
                const requestId = $(this).data('request-id');
                const requestNumber = $(this).data('request-number');

                // Add a new row to the details table
                let newRow = `
                <tr id="detail-row-${detailIndex}">
                    <td>${$('#detailsTable tbody tr').length + 1}</td>
                    <td>
                        <input type="hidden" name="details[${detailIndex}][item_request_detail_id]" value="${requestId}">
                        ${requestNumber}
                    </td>
                    <td>
                        <input type="hidden" name="details[${detailIndex}][item_id]" value="${itemId}" class="item-id">
                        <span class="item-name">${itemName}</span>
                    </td>
                    <td>${availableQuantity}</td>
                    <td>
                        <input type="number" name="details[${detailIndex}][quantity]" class="form-control order-quantity" min="0.001" step="0.001" max="${availableQuantity}" value="${availableQuantity}" required>
                    </td>
                    <td>
                        <input type="hidden" name="details[${detailIndex}][item_uom_id]" value="${uomId}" class="uom-id">
                        <span class="uom-name">${uomName}</span>
                    </td>
                    <td>
                        <input type="text" name="details[${detailIndex}][remarks]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-detail" data-index="${detailIndex}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;

                $('#detailsTable tbody').append(newRow);
                detailIndex++;

                // Close the modal
                $('#addItemModal').modal('hide');

                // Update order items and supplier offers
                updateOrderItems();
                updateAllSupplierOfferTables();
            });

            // Form validation before submit
            $('form').on('submit', function(e) {
                let hasItems = false;
                let hasSuppliers = false;
                let submitType = $(document.activeElement).attr('name') === 'submit_type' ?
                                 $(document.activeElement).val() : '';

                // Only validate for draft status
                if ('{{ $data->process_status }}' === 'Draft') {
                    // Check if at least one item has quantity > 0
                    $('.order-quantity').each(function() {
                        if ($(this).is(':visible') && parseFloat($(this).val()) > 0) {
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

                    // For submit (not draft), we need supplier offers
                    if (submitType === 'submit' && !hasSuppliers) {
                        e.preventDefault();
                        alert('Please add at least one supplier offer / 请至少添加一个供应商报价');
                        return false;
                    }
                }

                return true;
            });
        });
    </script>
@endpush
