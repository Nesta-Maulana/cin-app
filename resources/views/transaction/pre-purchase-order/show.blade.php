@extends('layouts.admin.app')
@section('title', 'Pre-Purchase Order Details / 预采购单详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Pre-Purchase Order Details / 预采购单详情</h5>
                        <small class="text-muted">View pre-purchase order information / 查看预采购单信息</small>
                    </div>
                    <div>
                        @if ($data->process_status == 'pending' || $data->process_status == 'under_review')
                            <a href="{{ route('pre-purchase-order.edit', $data->id) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit / 编辑
                            </a>
                        @endif
                        <a href="{{ route('pre-purchase-order.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-list"></i> List / 列表
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Status Panel -->
                    <div
                        class="alert alert-{{ $data->process_status == 'approved' ? 'success' : ($data->process_status == 'under_review' ? 'info' : 'warning') }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Status: <strong>{{ ucfirst($data->process_status) }}</strong></h6>
                                <small>
                                    @if ($data->process_status == 'pending')
                                        This pre-purchase order is pending and needs to be submitted for review.
                                    @elseif($data->process_status == 'under_review')
                                        This pre-purchase order is under review. Supplier offers can be added and selected.
                                    @elseif($data->process_status == 'approved')
                                        This pre-purchase order has been approved with a selected supplier.
                                    @endif
                                </small>
                            </div>
                            @if ($data->process_status == 'pending')
                                <form action="{{ route('pre-purchase-order.submit', $data->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Submit for Review / 提交审核</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Pre-Purchase Order Header -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Pre-Purchase Order Header / 预采购单头部</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pre-Purchase Order Number / 预采购单编号</label>
                                        <p>{{ $data->pre_po_number }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Customer Order Number / 客户订单编号</label>
                                        <p>{{ $data->customerOrder->order_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created Date / 创建日期</label>
                                        <p>{{ $data->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Remarks / 备注</label>
                                        <p>{{ $data->remarks ?: 'No remarks' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pre-Purchase Order Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Pre-Purchase Order Details / 预采购单详细信息</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                @php
                                    // Group details by item name and UOM
                                    $groupedDetails = [];
                                    foreach ($data->details as $detail) {
                                        // Get item name
                                        if ($detail->itemRequestDetail) {
                                            $itemName =
                                                $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name;
                                        } elseif ($detail->manualItemRequestDetail) {
                                            $itemName = $detail->manualItemRequestDetail->item_name;
                                        } else {
                                            $itemName = $detail->item_name ?? 'Unknown Item';
                                        }

                                        // Get UOM
                                        if ($detail->uom) {
                                            $uom = $detail->uom->unitOfMeasurement->name;
                                        } elseif ($detail->itemRequestDetail) {
                                            $uom =
                                                $detail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement
                                                    ->name;
                                        } elseif ($detail->manualItemRequestDetail) {
                                            $uom = $detail->manualItemRequestDetail->unit;
                                        } else {
                                            $uom = $detail->unit ?? 'Unknown UOM';
                                        }

                                        // Create unique key for grouping
                                        $key = $itemName . '|' . $uom;

                                        if (!isset($groupedDetails[$key])) {
                                            $groupedDetails[$key] = [
                                                'item_name' => $itemName,
                                                'uom' => $uom,
                                                'quantity' => 0,
                                                'request_numbers' => [],
                                                'types' => [],
                                                'details' => [],
                                            ];
                                        }

                                        // Add quantity
                                        $groupedDetails[$key]['quantity'] += $detail->quantity;

                                        // Add request number if available
                                        if ($detail->itemRequestDetail) {
                                            $requestNumber = $detail->itemRequestDetail->itemRequest->request_number;
                                            if (!in_array($requestNumber, $groupedDetails[$key]['request_numbers'])) {
                                                $groupedDetails[$key]['request_numbers'][] = $requestNumber;
                                            }
                                            if (!in_array('System', $groupedDetails[$key]['types'])) {
                                                $groupedDetails[$key]['types'][] = 'System';
                                            }
                                        } elseif ($detail->manualItemRequestDetail) {
                                            $requestNumber =
                                                $detail->manualItemRequestDetail->manualItemRequest->request_number;
                                            if (!in_array($requestNumber, $groupedDetails[$key]['request_numbers'])) {
                                                $groupedDetails[$key]['request_numbers'][] = $requestNumber;
                                            }
                                            if (!in_array('Manual', $groupedDetails[$key]['types'])) {
                                                $groupedDetails[$key]['types'][] = 'Manual';
                                            }
                                        }

                                        // Store original detail for reference
                                        $groupedDetails[$key]['details'][] = $detail;
                                    }
                                @endphp
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item Request Number / 请求编号</th>
                                            <th>Type / 类型</th>
                                            <th>Item Name / 物品</th>
                                            <th>Order Quantity / 订购数量</th>
                                            <th>UOM / 单位</th>
                                            <th>Remarks / 备注</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedDetails as $index => $groupedDetail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if (count($groupedDetail['request_numbers']) > 0)
                                                        {{ implode(', ', $groupedDetail['request_numbers']) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($groupedDetail['types'] as $type)
                                                        <span
                                                            class="badge bg-{{ $type == 'System' ? 'primary' : 'info' }} me-1">
                                                            {{ $type }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $groupedDetail['item_name'] }}</td>
                                                <td class="text-end">{{ number_format($groupedDetail['quantity'], 2) }}
                                                </td>
                                                <td>{{ $groupedDetail['uom'] }}</td>
                                                <td>
                                                    @php
                                                        $remarks = array_filter(
                                                            array_map(function ($detail) {
                                                                return $detail->remarks;
                                                            }, $groupedDetail['details']),
                                                        );
                                                    @endphp
                                                    @if (count($remarks) > 0)
                                                        {{ implode(', ', $remarks) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @php
                        // Create a comparison table data structure
                        $comparisonItems = [];

                        // First, gather all items from all quotations
                        foreach ($data->quotations as $quotation) {
                            // Calculate tax and cost proportions for this quotation
                            $totalItemsValue = $quotation->quotationDetails->sum(function ($detail) {
                                return $detail->offered_price_per_unit * $detail->quantity +
                                    ($detail->shipping_cost ?? 0);
                            });

                            $beforeTaxCostsTotal = $quotation->beforeTaxCosts->sum('amount') ?? 0;
                            $afterTaxCostsTotal = $quotation->afterTaxCosts->sum('amount') ?? 0;
                            $taxAmount = $quotation->tax_amount ?? 0;

                            // Group quotation details by item name and UOM
                            foreach ($quotation->quotationDetails as $detail) {
                                // Get item name
                                if ($detail->prePurchaseOrderDetail->itemRequestDetail) {
                                    $itemName =
                                        $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom
                                            ->item->name;
                                } elseif ($detail->prePurchaseOrderDetail->manualItemRequestDetail) {
                                    $itemName = $detail->prePurchaseOrderDetail->manualItemRequestDetail->item_name;
                                } else {
                                    $itemName = $detail->prePurchaseOrderDetail->item_name ?? 'Unknown Item';
                                }

                                // Get UOM
                                if ($detail->prePurchaseOrderDetail->uom) {
                                    $uom = $detail->prePurchaseOrderDetail->uom->unitOfMeasurement->name;
                                } elseif ($detail->prePurchaseOrderDetail->itemRequestDetail) {
                                    $uom =
                                        $detail->prePurchaseOrderDetail->itemRequestDetail->itemPriceHistory->itemUom
                                            ->unitOfMeasurement->name;
                                } elseif ($detail->prePurchaseOrderDetail->manualItemRequestDetail) {
                                    $uom = $detail->prePurchaseOrderDetail->manualItemRequestDetail->unit;
                                } else {
                                    $uom = $detail->prePurchaseOrderDetail->unit ?? 'Unknown UOM';
                                }

                                // Create unique key for grouping
                                $key = $itemName . '|' . $uom;

                                if (!isset($comparisonItems[$key])) {
                                    $comparisonItems[$key] = [
                                        'item_name' => $itemName,
                                        'uom' => $uom,
                                        'quantity' => $detail->quantity,
                                        'suppliers' => [],
                                    ];
                                } else {
                                    // Update quantity if needed
                                    $comparisonItems[$key]['quantity'] = max(
                                        $comparisonItems[$key]['quantity'],
                                        $detail->quantity,
                                    );
                                }

                                // Calculate item grand total
                                $itemGrandTotal =
                                    $detail->offered_price_per_unit * $detail->quantity + ($detail->shipping_cost ?? 0);

                                // Calculate proportion of total item value this item represents
                                $itemProportion = $totalItemsValue > 0 ? $itemGrandTotal / $totalItemsValue : 0;

                                // Calculate the allocated costs and tax
                                $allocatedBeforeTaxCosts = $itemProportion * $beforeTaxCostsTotal;
                                $allocatedTaxAmount = $itemProportion * $taxAmount;
                                $allocatedAfterTaxCosts = $itemProportion * $afterTaxCostsTotal;

                                // Calculate new unit price after tax
                                $newUnitPriceAfterTax =
                                    $detail->quantity > 0
                                        ? ($itemGrandTotal +
                                                $allocatedBeforeTaxCosts +
                                                $allocatedTaxAmount +
                                                $allocatedAfterTaxCosts) /
                                            $detail->quantity
                                        : 0;

                                // Add supplier info to this item
                                $comparisonItems[$key]['suppliers'][$quotation->id] = [
                                    'supplier_id' => $quotation->supplier->id,
                                    'supplier_name' => $quotation->supplier->name,
                                    'unit_price_after_tax' => $newUnitPriceAfterTax,
                                    'currency' => $quotation->currency,
                                    'quotation_id' => $quotation->id,
                                    'is_selected' => $quotation->is_selected,
                                ];
                            }
                        }

                        // Sort items by name
                        ksort($comparisonItems);

                        // Find cheapest supplier for each item
                        foreach ($comparisonItems as $key => &$item) {
                            $cheapestPrice = PHP_FLOAT_MAX;
                            $cheapestSupplierId = null;

                            foreach ($item['suppliers'] as $quotationId => $supplier) {
                                if ($supplier['unit_price_after_tax'] < $cheapestPrice) {
                                    $cheapestPrice = $supplier['unit_price_after_tax'];
                                    $cheapestSupplierId = $quotationId;
                                }
                            }

                            $item['cheapest_supplier_id'] = $cheapestSupplierId;
                            $item['cheapest_price'] = $cheapestPrice;
                        }
                        unset($item); // Clear reference

                        // Load item-specific supplier selections
                        $itemSelections = $data->itemSelections()->pluck('quotation_id', 'item_key')->toArray();
                    @endphp

                    <!-- Comparison Table -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Price Comparison Table / 价格比较表</h6>
                            <small class="text-muted">Compare supplier prices and select the best option for each
                                item</small>
                        </div>
                        <div class="card-body p-0">
                            <form action="{{ route('pre-purchase-order.select-items', $data->id) }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item / 物品</th>
                                                <th>UOM / 单位</th>
                                                <th>Quantity / 数量</th>
                                                <th class="">Cheapest Supplier / 最便宜的供应商</th>
                                                @foreach ($data->quotations as $quotation)
                                                    <th
                                                        class="text-center {{ $quotation->is_selected ? 'bg-light text-primary' : '' }}">
                                                        {{ $quotation->supplier->name }}
                                                        @if ($quotation->is_selected)
                                                            <div class="small"><i class="fa fa-check-circle"></i> Selected
                                                            </div>
                                                        @endif
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($comparisonItems as $key => $item)
                                                <tr>
                                                    <td>{{ $item['item_name'] }}</td>
                                                    <td>{{ $item['uom'] }}</td>
                                                    <td class="text-end">{{ number_format($item['quantity'], 2) }}</td>
                                                    <td class="">
                                                        @if (isset($item['cheapest_supplier_id']))
                                                            {{ $item['suppliers'][$item['cheapest_supplier_id']]['supplier_name'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                        ( @if (isset($item['cheapest_supplier_id']))
                                                            {{ strtoupper($item['suppliers'][$item['cheapest_supplier_id']]['currency']) }}
                                                            {{ number_format($item['cheapest_price'], 2) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                        )
                                                    </td>
                                                    @foreach ($data->quotations as $quotation)
                                                        <td
                                                            class="text-center {{ $quotation->is_selected ? 'bg-light' : '' }}">
                                                            @if (isset($item['suppliers'][$quotation->id]))
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <div class="form-check form-check-inline mb-0">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="selected_supplier[{{ $key }}]"
                                                                            id="supplier_{{ $quotation->id }}_{{ $loop->index }}"
                                                                            value="{{ $quotation->id }}"
                                                                            {{ (isset($itemSelections[$key]) && $itemSelections[$key] == $quotation->id
                                                                                    ? 'checked'
                                                                                    : isset($item['cheapest_supplier_id']) &&
                                                                                        $item['cheapest_supplier_id'] == $quotation->id &&
                                                                                        !isset($itemSelections[$key]))
                                                                                ? 'checked'
                                                                                : '' }}
                                                                            {{ $data->process_status == 'approved' ? 'disabled' : '' }}>
                                                                        <label class="form-check-label visually-hidden"
                                                                            for="supplier_{{ $quotation->id }}_{{ $loop->index }}">
                                                                            Select
                                                                        </label>
                                                                    </div>
                                                                    <div
                                                                        class="text-end fw-bold {{ $item['cheapest_supplier_id'] == $quotation->id ? 'text-success' : '' }}">
                                                                        {{ strtoupper($quotation->currency) }}
                                                                        {{ number_format($item['suppliers'][$quotation->id]['unit_price_after_tax'], 2) }}
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($data->process_status != 'approved')
                                    <div class="mt-3 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save Item Selection / 保存所选项目
                                        </button>
                                    </div>
                                @endif
                            </form>

                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> Note:
                                    <ul class="mb-0">
                                        <li>The comparison table shows new unit prices after tax, including all additional
                                            costs.</li>
                                        <li>The cheapest supplier for each item is pre-selected and highlighted in green.
                                        </li>
                                        <li>You can select different suppliers for different items to optimize your
                                            purchase.</li>
                                        <li>Click "Save Item Selection" to update your preferences.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Offers -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Supplier Offers / 供应商报价</h6>
                            @if ($data->process_status != 'approved')
                                <a href="{{ route('pre-purchase-order.edit', $data->id) }}#addSupplierOffer"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> Add Supplier Offer / 添加供应商报价
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if ($data->quotations->isEmpty())
                                <div class="alert alert-info">
                                    No supplier offers yet. Please add supplier offers to proceed.
                                </div>
                            @else
                                <ul class="nav nav-tabs" id="supplierTabs" role="tablist">
                                    @foreach ($data->quotations as $quotationIndex => $quotation)
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link {{ $quotationIndex == 0 ? 'active' : '' }} {{ $quotation->is_selected ? 'text-success' : '' }}"
                                                id="supplier-tab-{{ $quotationIndex }}" data-bs-toggle="tab"
                                                data-bs-target="#supplier-content-{{ $quotationIndex }}" type="button"
                                                role="tab" aria-controls="supplier-content-{{ $quotationIndex }}"
                                                aria-selected="{{ $quotationIndex == 0 ? 'true' : 'false' }}">
                                                {{ $quotation->supplier->name }}
                                                @if ($quotation->is_selected)
                                                    <i class="fa fa-check-circle text-success"></i>
                                                @endif
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content py-3" id="supplierTabContent">
                                    @foreach ($data->quotations as $quotationIndex => $quotation)
                                        <div class="tab-pane fade {{ $quotationIndex == 0 ? 'show active' : '' }}"
                                            id="supplier-content-{{ $quotationIndex }}" role="tabpanel"
                                            aria-labelledby="supplier-tab-{{ $quotationIndex }}">

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div
                                                        class="card h-100 {{ $quotation->is_selected ? 'border-success' : 'border-1' }}">
                                                        <div
                                                            class="card-header {{ $quotation->is_selected ? '' : 'bg-light' }}">
                                                            <h6 class="mb-1">Supplier Information / 供应商信息</h6>
                                                        </div>
                                                        <div class="card-body p-1">
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <td>Name</td>
                                                                    <td>{{ $quotation->supplier->name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Address</td>
                                                                    <td>{{ $quotation->supplier->address }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Currency</td>
                                                                    <td>{{ $quotation->currency }}</td>
                                                                </tr>
                                                            </table>
                                                            @if ($quotation->is_selected)
                                                                <div class="alert alert-success mb-0">
                                                                    <i class="fa fa-check-circle"></i> Selected Supplier /
                                                                    已选择的供应商
                                                                </div>
                                                            @elseif($data->process_status == 'under_review')
                                                                <form
                                                                    action="{{ route('pre-purchase-order.select-supplier', [$data->id, $quotation->id]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success w-100">
                                                                        Select This Supplier / 选择该供应商
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card h-100">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0">Total Summary / 总结</h6>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-borderless">
                                                                <tr>
                                                                    <th>Items Subtotal / 物品小计:</th>
                                                                    <td class="text-end">
                                                                        {{ strtoupper($quotation->currency) }}
                                                                        {{ number_format($quotation->quotationDetails->sum(function ($detail) {return $detail->offered_price_per_unit * $detail->quantity;}),2) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Additional Costs Before Tax / 税前附加费用:</th>
                                                                    <td class="text-end">
                                                                        {{ strtoupper($quotation->currency) }}
                                                                        {{ number_format($quotation->beforeTaxCosts->sum('amount') ?? 0, 2) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tax Amount / 税额:</th>
                                                                    <td class="text-end">
                                                                        {{ strtoupper($quotation->currency) }}
                                                                        {{ number_format($quotation->tax_amount ?? 0, 2) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Additional Costs After Tax / 税后附加费用:</th>
                                                                    <td class="text-end">
                                                                        {{ strtoupper($quotation->currency) }}
                                                                        {{ number_format($quotation->afterTaxCosts->sum('amount') ?? 0, 2) }}
                                                                    </td>
                                                                </tr>
                                                                <tr class="table-active">
                                                                    <th>Total Amount / 总金额:</th>
                                                                    <td class="text-end fw-bold">
                                                                        {{ strtoupper($quotation->currency) }}
                                                                        {{ number_format($quotation->total_amount, 2) }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                                // Calculate total item value for this quotation (sum of all items' grand totals before additional costs)
$totalItemsValue = $quotation->quotationDetails->sum(function (
    $detail,
) {
    return $detail->offered_price_per_unit * $detail->quantity +
        ($detail->shipping_cost ?? 0);
});

// Get before tax costs total
$beforeTaxCostsTotal = $quotation->beforeTaxCosts->sum('amount') ?? 0;

// Get after tax costs total
$afterTaxCostsTotal = $quotation->afterTaxCosts->sum('amount') ?? 0;

// Get tax amount
$taxAmount = $quotation->tax_amount ?? 0;

// Group quotation details by item name and UOM
$groupedQuotationDetails = [];
foreach ($quotation->quotationDetails as $detail) {
    // Get item name
    if ($detail->prePurchaseOrderDetail->itemRequestDetail) {
        $itemName =
            $detail->prePurchaseOrderDetail->itemRequestDetail
                ->itemPriceHistory->itemUom->item->name;
    } elseif (
        $detail->prePurchaseOrderDetail->manualItemRequestDetail
    ) {
        $itemName =
            $detail->prePurchaseOrderDetail->manualItemRequestDetail
                ->item_name;
    } else {
        $itemName =
            $detail->prePurchaseOrderDetail->item_name ??
            'Unknown Item';
    }

    // Get UOM
    if ($detail->prePurchaseOrderDetail->uom) {
        $uom =
            $detail->prePurchaseOrderDetail->uom->unitOfMeasurement
                ->name;
    } elseif ($detail->prePurchaseOrderDetail->itemRequestDetail) {
        $uom =
            $detail->prePurchaseOrderDetail->itemRequestDetail
                ->itemPriceHistory->itemUom->unitOfMeasurement->name;
    } elseif (
        $detail->prePurchaseOrderDetail->manualItemRequestDetail
    ) {
        $uom =
            $detail->prePurchaseOrderDetail->manualItemRequestDetail
                ->unit;
    } else {
        $uom = $detail->prePurchaseOrderDetail->unit ?? 'Unknown UOM';
    }

    // Create unique key for grouping
    $key = $itemName . '|' . $uom;

    if (!isset($groupedQuotationDetails[$key])) {
        $groupedQuotationDetails[$key] = [
            'item_name' => $itemName,
            'uom' => $uom,
            'quantity' => 0,
            'total_price' => 0,
            'shipping_cost' => 0,
            'grand_total' => 0,
            'types' => [],
            'details' => [],
            'remarks' => [],
        ];
    }

    // Add quantity and price info
    $groupedQuotationDetails[$key]['quantity'] += $detail->quantity;
    $groupedQuotationDetails[$key]['total_price'] +=
        $detail->offered_price_per_unit * $detail->quantity;
    $groupedQuotationDetails[$key]['shipping_cost'] +=
        $detail->shipping_cost ?? 0;
    $groupedQuotationDetails[$key]['grand_total'] +=
        $detail->offered_price_per_unit * $detail->quantity +
        ($detail->shipping_cost ?? 0);

    // Add type
    $type = $detail->prePurchaseOrderDetail->itemRequestDetail
        ? 'System'
        : 'Manual';
    if (!in_array($type, $groupedQuotationDetails[$key]['types'])) {
        $groupedQuotationDetails[$key]['types'][] = $type;
    }

    // Add remarks if available
    if ($detail->remarks) {
        $groupedQuotationDetails[$key]['remarks'][] = $detail->remarks;
    }

    // Store original detail for reference
    $groupedQuotationDetails[$key]['details'][] = $detail;
                                                }
                                            @endphp

                                            <!-- Item Details -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Item Details / 物品详情</h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Item / 物品</th>
                                                                    <th>Type / 类型</th>
                                                                    <th>Quantity / 数量</th>
                                                                    <th>UOM / 单位</th>
                                                                    <th>Unit Price / 单价</th>
                                                                    <th>Subtotal Price / 小计</th>
                                                                    <th>Shipping Cost / 运输费</th>
                                                                    <th>Grand Total / 总计</th>
                                                                    <th>New Unit Price Before Tax / 税前新单价</th>
                                                                    <th>New Unit Price After Tax / 税后新单价</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($groupedQuotationDetails as $key => $item)
                                                                    @php
                                                                        // Calculate average unit price
                                                                        $avgUnitPrice =
                                                                            $item['quantity'] > 0
                                                                                ? $item['total_price'] /
                                                                                    $item['quantity']
                                                                                : 0;

                                                                        // Calculate proportion of total item value this item represents
                                                                        $itemProportion =
                                                                            $totalItemsValue > 0
                                                                                ? $item['grand_total'] /
                                                                                    $totalItemsValue
                                                                                : 0;

                                                                        // Calculate the allocated before tax costs for this item
                                                                        $allocatedBeforeTaxCosts =
                                                                            $itemProportion * $beforeTaxCostsTotal;

                                                                        // Calculate the allocated after tax costs for this item
                                                                        $allocatedAfterTaxCosts =
                                                                            $itemProportion * $afterTaxCostsTotal;

                                                                        // Calculate the allocated tax amount for this item
                                                                        $allocatedTaxAmount =
                                                                            $itemProportion * $taxAmount;

                                                                        // Calculate new unit price before tax
                                                                        $newUnitPriceBeforeTax =
                                                                            ($item['grand_total'] +
                                                                                $allocatedBeforeTaxCosts) /
                                                                            $item['quantity'];

                                                                        // Calculate new unit price after tax
                                                                        $newUnitPriceAfterTax =
                                                                            ($item['grand_total'] +
                                                                                $allocatedBeforeTaxCosts +
                                                                                $allocatedTaxAmount +
                                                                                $allocatedAfterTaxCosts) /
                                                                            $item['quantity'];
                                                                    @endphp
                                                                    <tr>
                                                                        <td>
                                                                            {{ $item['item_name'] }}
                                                                            @if (count($item['remarks']) > 0)
                                                                                <div class="small text-muted">
                                                                                    {{ implode(', ', array_unique($item['remarks'])) }}
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @foreach ($item['types'] as $type)
                                                                                <span
                                                                                    class="badge bg-{{ $type == 'System' ? 'primary' : 'info' }} me-1">
                                                                                    {{ $type }}
                                                                                </span>
                                                                            @endforeach
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ number_format($item['quantity'], 2) }}</td>
                                                                        <td>{{ $item['uom'] }}</td>
                                                                        <td class="text-end">
                                                                            {{ number_format($avgUnitPrice, 2) }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ number_format($item['total_price'], 2) }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ number_format($item['shipping_cost'], 2) }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ number_format($item['grand_total'], 2) }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ number_format($newUnitPriceBeforeTax, 2) }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            {{ number_format($newUnitPriceAfterTax, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Additional Costs and Tax Information -->
                                            <div class="row">
                                                <!-- Before Tax Costs -->
                                                <div class="col-md-4">
                                                    <div class="card mb-3 border-1">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0">Additional Costs Before Tax / 税前附加费用</h6>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            @if (isset($quotation->beforeTaxCosts) && $quotation->beforeTaxCosts->count() > 0)
                                                                <div class="">
                                                                    <table class="table table-bordered">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>Description / 描述</th>
                                                                                <th>Type / 类型</th>
                                                                                <th>Amount / 金额</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($quotation->beforeTaxCosts as $cost)
                                                                                <tr>
                                                                                    <td>{{ $cost->description }}</td>
                                                                                    <td>
                                                                                        @if ($cost->type == 'shipping')
                                                                                            Shipping / 运费
                                                                                        @elseif($cost->type == 'handling')
                                                                                            Handling / 装卸费
                                                                                        @elseif($cost->type == 'insurance')
                                                                                            Insurance / 保险
                                                                                        @else
                                                                                            Other / 其他
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        {{ number_format($cost->amount, 2) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr class="table-light">
                                                                                <th colspan="2">Total / 总计</th>
                                                                                <th class="text-end">
                                                                                    {{ number_format($quotation->beforeTaxCosts->sum('amount'), 2) }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <p class="text-muted p-3">No additional costs before tax
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tax Information -->
                                                <div class="col-md-4">
                                                    <div class="card mb-3 border-1">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0">Tax Information / 税务信息</h6>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>Subtotal Before Tax / 税前小计</th>
                                                                    <td class="text-end">
                                                                        {{ number_format($quotation->subtotal_before_tax ?? 0, 2) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tax Type / 税务类型</th>
                                                                    <td>
                                                                        {{ ($quotation->tax_type ?? 'percentage') == 'percentage' ? 'Percentage / 百分比' : 'Fixed Amount / 固定金额' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tax Value / 税值</th>
                                                                    <td class="text-end">
                                                                        {{ number_format($quotation->tax_value ?? 0, 2) }}
                                                                        {{ ($quotation->tax_type ?? 'percentage') == 'percentage' ? '%' : strtoupper($quotation->currency) }}
                                                                    </td>
                                                                </tr>
                                                                <tr class="table-light">
                                                                    <th>Tax Amount / 税额</th>
                                                                    <td class="text-end">
                                                                        {{ number_format($quotation->tax_amount ?? 0, 2) }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- After Tax Costs -->
                                                <div class="col-md-4">
                                                    <div class="card mb-3 border-1">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0">Additional Costs After Tax / 税后附加费用</h6>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            @if (isset($quotation->afterTaxCosts) && $quotation->afterTaxCosts->count() > 0)
                                                                <div class="">
                                                                    <table class="table table-bordered">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>Description / 描述</th>
                                                                                <th>Type / 类型</th>
                                                                                <th>Amount / 金额</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($quotation->afterTaxCosts as $cost)
                                                                                <tr>
                                                                                    <td>{{ $cost->description }}</td>
                                                                                    <td>
                                                                                        @if ($cost->type == 'fee')
                                                                                            Fee / 费用
                                                                                        @elseif($cost->type == 'discount')
                                                                                            Discount / 折扣
                                                                                        @else
                                                                                            Other / 其他
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        {{ number_format($cost->amount, 2) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr class="table-light">
                                                                                <th colspan="2">Total / 总计</th>
                                                                                <th class="text-end">
                                                                                    {{ number_format($quotation->afterTaxCosts->sum('amount'), 2) }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <p class="text-muted p-3">No additional costs after tax</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Remarks -->
                                            @if ($quotation->remarks)
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Remarks / 备注</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        {{ $quotation->remarks }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            @if ($data->process_status == 'pending')
                                <form action="{{ route('pre-purchase-order.submit', $data->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fa fa-paper-plane"></i> Submit for Review / 提交审核
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div>
                            @if ($data->process_status != 'approved')
                                <a href="{{ route('pre-purchase-order.edit', $data->id) }}" class="btn btn-primary">
                                    <i class="fa fa-edit"></i> Edit / 编辑
                                </a>
                            @endif
                            <a href="{{ route('pre-purchase-order.index') }}" class="btn btn-secondary ms-2">
                                <i class="fa fa-list"></i> Back to List / 返回列表
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap tabs if present
            var triggerTabList = [].slice.call(document.querySelectorAll('#supplierTabs button'))
            triggerTabList.forEach(function(triggerEl) {
                new bootstrap.Tab(triggerEl)
            });
        });
    </script>
@endpush
