@extends('layouts.admin.app')
@section('title', 'Edit Pre-Purchase Order / 编辑预采购单')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Edit Pre-Purchase Order / 编辑预采购单</h5>
                        <small class="text-muted">Update the pre-purchase order information / 更新预采购单信息</small>
                    </div>
                    <a href="{{ route('pre-purchase-order.show', $data->id) }}" class="btn btn-secondary btn-sm"
                        title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('pre-purchase-order.update', $data->id) }}" method="POST">
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

                        <!-- Pre-Purchase Order Header Section -->
                        <h6 class="text-primary mb-3">Pre-Purchase Order Header / 预采购单头部</h6>
                        <div class="mb-3">
                            <label for="pre_po_number" class="form-label">Pre-Purchase Order Number / 预采购单编号</label>
                            <input type="text" id="pre_po_number" name="pre_po_number" class="form-control"
                                value="{{ $data->pre_po_number }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="customer_order_id" class="form-label">Customer Order Number / 客户订单编号</label>
                            <select class="form-select" id="customer_order_id" name="customer_order_id"
                                {{ $data->process_status !== 'pending' ? 'disabled' : '' }}>
                                <option value="" disabled>Select a customer order / 选择客户订单</option>
                                @foreach ($customerOrders as $customerOrder)
                                    <option value="{{ $customerOrder->id }}"
                                        {{ $data->customer_order_id == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($data->process_status !== 'pending')
                                <input type="hidden" name="customer_order_id" value="{{ $data->customer_order_id }}">
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks / 备注</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks / 请输入备注">{{ old('remarks', $data->remarks) }}</textarea>
                        </div>

                        <!-- Pre-Purchase Order Details Section -->
                        <h6 class="text-primary mb-3">Pre-Purchase Order Details / 预采购单详细信息</h6>
                        <!-- Read-only display for non-pending/draft status -->
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Pre-purchase order details cannot be modified as the status is
                            not pending. / 预采购单状态不是待处理，无法修改详细信息。
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
                                    @foreach ($data->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemRequest->request_number : '-' }}
                                            </td>
                                            <td>{{ $detail->itemRequestDetail ? $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name : '-' }}
                                            </td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->uom ? $detail->uom->unitOfMeasurement->name : '-' }}</td>
                                            <td>{{ $detail->remarks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($data->process_status == 'draft')
                            <!-- Quotation Comparison Section -->
                            <h6 class="text-primary mt-4 mb-2">Quotation Comparisons / 报价比较</h6>
                            <button type="button" class="btn btn-secondary mb-3" id="addQuotation">
                                <i class="fa fa-plus"></i> Add Quotation / 添加报价
                            </button>

                            <div id="quotationsContainer">
                                @foreach ($data->quotations as $quotationIndex => $quotation)
                                    <div id="quotation-card-{{ $quotationIndex }}" class="card mb-4 border-1">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Quotation #{{ $quotationIndex + 1 }}</h6>
                                            <div>
                                                @if ($quotation->is_selected)
                                                    <span class="badge bg-success me-2">Selected / 已选择</span>
                                                @endif
                                                <button type="button" class="btn btn-danger btn-sm remove-quotation"
                                                    data-index="{{ $quotationIndex }}">
                                                    <i class="fa fa-trash"></i> Remove / 删除
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3 mt-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">Supplier / 供应商</label>
                                                    <input type="hidden" name="quotations[{{ $quotationIndex }}][id]"
                                                        value="{{ $quotation->id }}">
                                                    <select name="quotations[{{ $quotationIndex }}][supplier_id]"
                                                        class="form-select supplier-select" required
                                                        {{ $quotation->is_selected ? 'disabled' : '' }}>
                                                        <option value="" disabled>Please choose supplier / 请选择供应商
                                                        </option>
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}"
                                                                {{ $quotation->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($quotation->is_selected)
                                                        <input type="hidden"
                                                            name="quotations[{{ $quotationIndex }}][supplier_id]"
                                                            value="{{ $quotation->supplier_id }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Grand Total / 总计</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number"
                                                            name="quotations[{{ $quotationIndex }}][grand_total]"
                                                            class="form-control grand-total" step="0.01"
                                                            min="0" value="{{ $quotation->grand_total }}"
                                                            {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered"
                                                    id="quotation-items-table-{{ $quotationIndex }}">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item / 物品</th>
                                                            <th>Quantity / 数量</th>
                                                            <th>UOM / 单位</th>
                                                            <th>Offered Price / 报价</th>
                                                            <th>Total Price / 总价</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($quotation->quotationDetails as $detailIndex => $detail)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden"
                                                                        name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][id]"
                                                                        value="{{ $detail->id }}">
                                                                    <input type="hidden"
                                                                        name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][pre_purchase_order_detail_id]"
                                                                        value="{{ $detail->pre_purchase_order_detail_id }}">
                                                                    {{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}
                                                                </td>
                                                                <td>
                                                                    {{ $detail->quantity }}
                                                                    <input type="hidden"
                                                                        name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][quantity]"
                                                                        value="{{ $detail->quantity }}">
                                                                </td>
                                                                <td>
                                                                    {{ $detail->prePurchaseOrderDetail->uom->unitOfMeasurement->name }}
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number"
                                                                            name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][offered_price_per_unit]"
                                                                            class="form-control price-per-unit"
                                                                            data-index="{{ $detailIndex }}"
                                                                            step="0.01" min="0"
                                                                            value="{{ $detail->offered_price_per_unit }}"
                                                                            {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number"
                                                                            name="quotations[{{ $quotationIndex }}][details][{{ $detailIndex }}][total_price]"
                                                                            class="form-control total-price" readonly
                                                                            value="{{ $detail->offered_price_per_unit * $detail->quantity }}">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td class="text-end"><strong>Subtotal:</strong></td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="text"
                                                                        class="form-control items-subtotal" readonly
                                                                        value="{{ $quotation->quotationDetails->sum(function ($detail) {return $detail->offered_price_per_unit * $detail->quantity;}) }}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td class="text-end"><strong>Shipping Cost:</strong></td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="number"
                                                                        name="quotations[{{ $quotationIndex }}][shipping_cost]"
                                                                        class="form-control shipping-cost" step="0.01"
                                                                        min="0"
                                                                        value="{{ $quotation->shipping_cost }}"
                                                                        {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td class="text-end"><strong>Other Cost:</strong></td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="number"
                                                                        name="quotations[{{ $quotationIndex }}][other_cost]"
                                                                        class="form-control other-cost" step="0.01"
                                                                        min="0"
                                                                        value="{{ $quotation->other_cost }}"
                                                                        {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label class="form-label">Remarks / 备注</label>
                                                    <input type="text"
                                                        name="quotations[{{ $quotationIndex }}][remarks]"
                                                        class="form-control" value="{{ $quotation->remarks }}"
                                                        {{ $quotation->is_selected ? 'readonly' : '' }}>
                                                </div>
                                            </div>

                                            @if (!$quotation->is_selected && $data->process_status == 'under_review')
                                                <div class="mt-3 text-end">
                                                    <button type="button" class="btn btn-success select-supplier"
                                                        data-index="{{ $quotationIndex }}">
                                                        Select This Supplier / 选择该供应商
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Display Quotation Comparisons in read-only mode -->
                            <h6 class="text-primary mt-4 mb-3">Quotation Comparisons / 报价比较</h6>
                            <div class="row">
                                @foreach ($data->quotations as $quotation)
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 {{ $quotation->is_selected ? 'border-success' : '' }}">
                                            <div
                                                class="card-header {{ $quotation->is_selected ? 'bg-success text-white' : 'bg-light' }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">{{ $quotation->supplier->name }}</h6>
                                                    @if ($quotation->is_selected)
                                                        <span class="badge bg-white text-success">Selected / 已选择</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Item / 物品</th>
                                                                <th>Qty / 数量</th>
                                                                <th>Price / 单价</th>
                                                                <th>Total / 总价</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($quotation->quotationDetails as $detail)
                                                                <tr>
                                                                    <td>{{ $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name }}
                                                                    </td>
                                                                    <td>{{ $detail->quantity }}</td>
                                                                    <td>${{ number_format($detail->offered_price_per_unit, 2) }}
                                                                    </td>
                                                                    <td>${{ number_format($detail->total_price, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" class="text-end">
                                                                    <strong>Subtotal:</strong></td>
                                                                <td>${{ number_format($quotation->quotationDetails->sum('total_price'), 2) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end">
                                                                    <strong>Shipping:</strong></td>
                                                                <td>${{ number_format($quotation->shipping_cost, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end">
                                                                    <strong>Other:</strong></td>
                                                                <td>${{ number_format($quotation->other_cost, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end">
                                                                    <strong>Total:</strong></td>
                                                                <td><strong>${{ number_format($quotation->grand_total, 2) }}</strong>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                @if ($quotation->remarks)
                                                    <div class="mt-2">
                                                        <strong>Remarks / 备注:</strong> {{ $quotation->remarks }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="my-4 d-flex justify-content-between">
                            <div>
                                @if ($data->process_status == 'pending')
                                    <button type="submit" name="submit_type" value="draft"
                                        class="btn btn-secondary px-5 me-2">
                                        Save as Draft / 保存为草稿
                                    </button>
                                @endif
                            </div>
                            <div>
                                @if ($data->process_status == 'pending')
                                    <button type="submit" name="submit_type" value="submit"
                                        class="btn btn-primary px-5 me-2">
                                        Submit for Review / 提交审核
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary px-5 me-2">
                                        Update / 更新
                                    </button>
                                @endif
                                <a href="{{ route('pre-purchase-order.show', $data->id) }}"
                                    class="btn btn-label-secondary">
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
                        <input type="text" id="searchItem" class="form-control"
                            placeholder="Search items... / 搜索物品...">
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

    <!-- Add Quotation Modal -->
    <div class="modal fade" id="addQuotationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Quotation / 添加报价</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Supplier / 供应商</label>
                            <select id="newQuotationSupplier" class="form-select">
                                <option value="" disabled selected>Please choose supplier / 请选择供应商</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel / 取消</button>
                    <button type="button" id="confirmAddQuotation" class="btn btn-primary">Add / 添加</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Select Supplier Confirmation Modal -->
    <div class="modal fade" id="selectSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Supplier Selection / 确认供应商选择</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to select this supplier? This action will mark the selected supplier as the
                        chosen one and finalize the pre-purchase order.</p>
                    <p>您确定要选择此供应商吗？此操作将标记所选供应商为已选择的供应商并完成预采购单。</p>
                    <input type="hidden" id="selectedQuotationIndex">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel / 取消</button>
                    <button type="button" id="confirmSelectSupplier" class="btn btn-success">Confirm / 确认</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let detailIndex = {{ count($data->details) }};
            let quotationIndex = {{ count($data->quotations) }};
            let selectedSuppliers = [];
            let orderItems = []; // Store items with quantity > 0
            let deletedDetails = [];
            let deletedQuotations = [];

            // Initialize selectedSuppliers array with existing quotations
            @foreach ($data->quotations as $index => $quotation)
                selectedSuppliers.push({
                    index: {{ $index }},
                    supplierId: {{ $quotation->supplier_id }}
                });
            @endforeach

            // Function to collect all items with quantity > 0
            function updateOrderItems() {
                orderItems = [];
                let itemMap = new Map(); // Use Map to group by item_id and uom_id

                $('#detailsTable tbody tr').each(function() {
                    const row = $(this);
                    if (row.is(':visible')) { // Only count visible rows
                        const detailId = row.find('input[name^="details"][name$="[id]"]').val() || '';
                        const itemRequestDetailId = row.find(
                            'input[name^="details"][name$="[item_request_detail_id]"]').val();
                        const itemId = row.find('.item-id').val();
                        const uomId = row.find('.uom-id').val();
                        const quantity = parseFloat(row.find('.order-quantity').val()) || 0;

                        if (quantity > 0 && itemId && uomId) {
                            orderItems.push({
                                detailId: detailId,
                                itemRequestDetailId: itemRequestDetailId,
                                itemId: itemId,
                                itemName: row.find('.item-name').text().trim(),
                                uomId: uomId,
                                uomName: row.find('.uom-name').text().trim(),
                                quantity: quantity,
                                prePurchaseOrderDetailId: detailId
                            });
                        }
                    }
                });
            }

            // Update quotation tables when quantity changes
            $(document).on('input', '.order-quantity', function() {
                updateOrderItems();
                updateAllQuotationTables();
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
                updateAllQuotationTables();
            });

            // Add quotation button click
            $('#addQuotation').click(function() {
                // Check if there are items with quantity > 0
                updateOrderItems();

                if (orderItems.length === 0) {
                    alert('Please add order quantities before adding quotations / 请在添加报价前添加订购数量');
                    return;
                }

                // Open the add quotation modal
                $('#addQuotationModal').modal('show');
            });

            // Confirm add quotation
            $('#confirmAddQuotation').click(function() {
                const supplierId = $('#newQuotationSupplier').val();
                const supplierName = $('#newQuotationSupplier option:selected').text();

                if (!supplierId) {
                    alert('Please select a supplier / 请选择供应商');
                    return;
                }

                // Check if supplier already has a quotation
                const supplierExists = selectedSuppliers.some(s => s.supplierId == supplierId);
                if (supplierExists) {
                    alert('This supplier already has a quotation / 该供应商已有报价');
                    return;
                }

                // Add the quotation card
                addQuotationCard(supplierId, supplierName);

                // Close the modal and reset selection
                $('#addQuotationModal').modal('hide');
                $('#newQuotationSupplier').val('');
            });

            function addQuotationCard(supplierId, supplierName) {
                // Create card for quotation
                let quotationCard = `
                <div id="quotation-card-${quotationIndex}" class="card mb-4 border-1">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Quotation #${quotationIndex + 1}</h6>
                        <button type="button" class="btn btn-danger btn-sm remove-quotation" data-index="${quotationIndex}">
                            <i class="fa fa-trash"></i> Remove / 删除
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Supplier / 供应商</label>
                                <select name="quotations[${quotationIndex}][supplier_id]" class="form-select supplier-select" required>
                                    <option value="${supplierId}" selected>${supplierName}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grand Total / 总计</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="quotations[${quotationIndex}][grand_total]" class="form-control grand-total" step="0.01" min="0" value="0" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="quotation-items-table-${quotationIndex}">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item / 物品</th>
                                        <th>Quantity / 数量</th>
                                        <th>UOM / 单位</th>
                                        <th>Offered Price / 报价</th>
                                        <th>Total Price / 总价</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Quotation items will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-end"><strong>Subtotal:</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control items-subtotal" readonly value="0">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-end"><strong>Shipping Cost:</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="quotations[${quotationIndex}][shipping_cost]"
                                                    class="form-control shipping-cost" step="0.01" min="0" value="0">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-end"><strong>Other Cost:</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="quotations[${quotationIndex}][other_cost]"
                                                    class="form-control other-cost" step="0.01" min="0" value="0">
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Remarks / 备注</label>
                                <input type="text" name="quotations[${quotationIndex}][remarks]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                `;

                // Append the card to the container
                $('#quotationsContainer').append(quotationCard);

                // Update selected suppliers array
                selectedSuppliers.push({
                    index: quotationIndex,
                    supplierId: supplierId
                });

                // Fill the items table
                updateQuotationItemsTable(quotationIndex);

                // Setup event handlers for this quotation
                setupQuotationEvents(quotationIndex);

                // Increment quotation index for next offer
                quotationIndex++;
            }

            function updateQuotationItemsTable(quotationIdx) {
                let quotationItemsTable = $(`#quotation-items-table-${quotationIdx} tbody`);
                quotationItemsTable.empty();

                // Add rows for each order item
                orderItems.forEach((item, idx) => {
                    let quotationRow = `
                    <tr>
                        <td>
                            <input type="hidden" name="quotations[${quotationIdx}][details][${idx}][pre_purchase_order_detail_id]" value="${item.prePurchaseOrderDetailId}">
                            ${item.itemName}
                        </td>
                        <td>
                            <input type="hidden" name="quotations[${quotationIdx}][details][${idx}][quantity]" value="${item.quantity}">
                            ${item.quantity}
                        </td>
                        <td>
                            ${item.uomName}
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="quotations[${quotationIdx}][details][${idx}][offered_price_per_unit]"
                                    class="form-control price-per-unit" data-index="${idx}"
                                    step="0.01" min="0" value="0" required>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="quotations[${quotationIdx}][details][${idx}][total_price]"
                                    class="form-control total-price" readonly value="0">
                            </div>
                        </td>
                    </tr>
                    `;
                    quotationItemsTable.append(quotationRow);
                });
            }

            function updateAllQuotationTables() {
                selectedSuppliers.forEach(supplier => {
                    // Skip if quotation card doesn't exist (was deleted)
                    if ($(`#quotation-card-${supplier.index}`).length) {
                        updateQuotationItemsTable(supplier.index);
                    }
                });
            }

            function setupQuotationEvents(quotationIdx) {
                // Calculate total price when price per unit changes
                $(`#quotation-card-${quotationIdx}`).on('input', '.price-per-unit', function() {
                    const row = $(this).closest('tr');
                    const pricePerUnit = parseFloat($(this).val()) || 0;
                    const quantity = parseFloat(row.find('input[name$="[quantity]"]').val()) || 0;
                    const totalPrice = pricePerUnit * quantity;

                    row.find('.total-price').val(totalPrice.toFixed(2));

                    // Update subtotal
                    updateQuotationTotals(quotationIdx);
                });

                // Update totals when shipping or other costs change
                $(`#quotation-card-${quotationIdx}`).on('input', '.shipping-cost, .other-cost', function() {
                    updateQuotationTotals(quotationIdx);
                });
            }

            function updateQuotationTotals(quotationIdx) {
                const card = $(`#quotation-card-${quotationIdx}`);

                // Calculate items subtotal
                let subtotal = 0;
                card.find('.total-price').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                // Update subtotal field
                card.find('.items-subtotal').val(subtotal.toFixed(2));

                // Get shipping and other costs
                const shippingCost = parseFloat(card.find('.shipping-cost').val()) || 0;
                const otherCost = parseFloat(card.find('.other-cost').val()) || 0;

                // Calculate grand total
                const grandTotal = subtotal + shippingCost + otherCost;

                // Update grand total field
                card.find('.grand-total').val(grandTotal.toFixed(2));
            }

            // Remove quotation
            $(document).on('click', '.remove-quotation', function() {
                let quotationIdx = $(this).data('index');
                let quotationId = $(
                    `#quotation-card-${quotationIdx} input[name^="quotations"][name$="[id]"]`).val();

                if (quotationId) {
                    deletedQuotations.push(quotationId);
                    // Add a hidden input to track deleted quotations
                    $('form').append(
                        `<input type="hidden" name="deleted_quotations[]" value="${quotationId}">`);
                }

                $(`#quotation-card-${quotationIdx}`).remove();

                // Remove from selected suppliers array
                selectedSuppliers = selectedSuppliers.filter(s => s.index != quotationIdx);
            });

            // Select supplier button click
            $(document).on('click', '.select-supplier', function() {
                const quotationIdx = $(this).data('index');
                $('#selectedQuotationIndex').val(quotationIdx);
                $('#selectSupplierModal').modal('show');
            });

            // Confirm select supplier
            $('#confirmSelectSupplier').click(function() {
                const quotationIdx = $('#selectedQuotationIndex').val();

                // Add a hidden input to indicate which supplier should be selected
                $('form').append(`<input type="hidden" name="selected_quotation" value="${quotationIdx}">`);

                // Submit the form
                $('form').submit();
            });

            // Add item button click
            $('#addItemBtn').click(function() {
                // Fetch available items from the customer order
                $.ajax({
                    url: '{{ route('get-items-by-customer-order') }}',
                    type: 'GET',
                    data: {
                        customer_order_id: $('#customer_order_id').val()
                    },
                    success: function(response) {
                        // Populate items table
                        let itemsTable = $('#itemsTable tbody');
                        itemsTable.empty();

                        if (response.data.length === 0) {
                            itemsTable.html(
                                '<tr><td colspan="6" class="text-center">No items available / 没有可用的物品</td></tr>'
                            );
                        } else {
                            let rowNumber = 1;
                            response.data.forEach((item) => {
                                // Skip items that are already in the table
                                let itemId = item.item_price_history.item_uom.item_id;
                                let uomId = item.item_price_history.item_uom
                                    .unit_of_measurement_id;

                                // Check if this item+UOM is already in the table
                                let exists = false;
                                $('#detailsTable tbody tr:visible').each(function() {
                                    let existingItemId = $(this).find(
                                        '.item-id').val();
                                    let existingUomId = $(this).find('.uom-id')
                                        .val();

                                    if (existingItemId == itemId &&
                                        existingUomId == uomId) {
                                        exists = true;
                                        return false; // Break the loop
                                    }
                                });

                                if (!exists) {
                                    let requestQuantity = item.quantity;
                                    let availableQuantity = requestQuantity;

                                    if (availableQuantity > 0) {
                                        let itemRow = `
                                        <tr>
                                            <td>${rowNumber}</td>
                                            <td>${item.item_price_history.item_uom.item.name}</td>
                                            <td>${availableQuantity}</td>
                                            <td>${item.item_price_history.item_uom.unit_of_measurement.name}</td>
                                            <td>${item.item_request.request_number}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm add-item-to-po"
                                                    data-item-id="${itemId}"
                                                    data-item-name="${item.item_price_history.item_uom.item.name}"
                                                    data-uom-id="${uomId}"
                                                    data-uom-name="${item.item_price_history.item_uom.unit_of_measurement.name}"
                                                    data-available-quantity="${availableQuantity}"
                                                    data-request-id="${item.id}"
                                                    data-request-number="${item.item_request.request_number}">
                                                    <i class="fa fa-plus"></i> Add / 添加
                                                </button>
                                            </td>
                                        </tr>`;
                                        itemsTable.append(itemRow);
                                        rowNumber++;
                                    }
                                }
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

            // Add item to pre-purchase order from modal
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

                // Update order items and quotation tables
                updateOrderItems();
                updateAllQuotationTables();
            });

            // Setup events for existing quotations
            @foreach ($data->quotations as $quotationIndex => $quotation)
                setupQuotationEvents({{ $quotationIndex }});
            @endforeach

            // Update all totals initially
            $('.price-per-unit').each(function() {
                $(this).trigger('input');
            });

            // Form validation before submit
            $('form').on('submit', function(e) {
                let hasItems = false;
                let hasQuotations = false;
                let submitType = $(document.activeElement).attr('name') === 'submit_type' ?
                    $(document.activeElement).val() : '';

                // Only validate for pending status
                if ('{{ $data->process_status }}' === 'pending') {
                    // Check if at least one item has quantity > 0
                    $('.order-quantity').each(function() {
                        if ($(this).is(':visible') && parseFloat($(this).val()) > 0) {
                            hasItems = true;
                            return false; // Break the loop
                        }
                    });

                    // Check if at least one quotation exists
                    hasQuotations = selectedSuppliers.length > 0;

                    if (!hasItems) {
                        e.preventDefault();
                        alert('Please add at least one item with order quantity / 请至少添加一个有订购数量的物品');
                        return false;
                    }

                    // For submit (not draft), we need quotations
                    if (submitType === 'submit' && !hasQuotations) {
                        e.preventDefault();
                        alert('Please add at least one quotation / 请至少添加一个报价');
                        return false;
                    }
                }

                return true;
            });
        });
    </script>
@endpush
