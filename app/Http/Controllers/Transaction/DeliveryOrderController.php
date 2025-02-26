<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\DeliveryOrder;
use App\Models\Warehouse;
use App\Models\WarehouseSectionStock;
use App\Repositories\Master\Item\ItemRepositoryInterface;
use App\Repositories\Master\ItemUom\ItemUomRepositoryInterface;
use App\Repositories\Master\Warehouse\WarehouseRepositoryInterface;
use App\Repositories\Master\WarehouseSection\WarehouseSectionRepositoryInterface;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\Transaction\DeliveryOrder\DeliveryOrderRepositoryInterface;
use App\Repositories\Transaction\ItemNeedToPurchase\ItemNeedToPurchaseRepositoryInterface;
use App\Repositories\Transaction\ItemNeedToPurchaseDetail\ItemNeedToPurchaseDetailRepositoryInterface;
use App\Repositories\Transaction\ItemRequestDetail\ItemRequestDetailRepository;
use App\Repositories\Transaction\ItemRequestDetail\ItemRequestDetailRepositoryInterface;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DeliveryOrderController extends Controller
{
    public $view, $route;
    protected $repository,
    $warehouseRepository,
    $warehouseSectionRepository,
    $itemRepository,
    $itemUomRepository,
    $customerOrderRepository,
    $itemRequestDetailRepository,
    $itemNeedToPurchaseRepository,
    $itemNeedToPurchaseDetailRepository;
    public function __construct(
        DeliveryOrderRepositoryInterface $repository,
        WarehouseRepositoryInterface $warehouseRepository,
        WarehouseSectionRepositoryInterface $warehouseSectionRepository,
        ItemRepositoryInterface $itemRepository,
        ItemUomRepositoryInterface $itemUomRepository,
        CustomerOrderRepositoryInterface $customerOrderRepository,
        ItemRequestDetailRepositoryInterface $itemRequestDetailRepository,
        ItemNeedToPurchaseRepositoryInterface $itemNeedToPurchaseRepository,
        ItemNeedToPurchaseDetailRepositoryInterface $itemNeedToPurchaseDetailRepository
    ) {
        $this->repository = $repository;
        $this->view = 'transaction.delivery-order';
        $this->route = 'delivery-order';
        $this->warehouseRepository = $warehouseRepository;
        $this->warehouseSectionRepository = $warehouseSectionRepository;
        $this->itemRepository = $itemRepository;
        $this->itemUomRepository = $itemUomRepository;
        $this->customerOrderRepository = $customerOrderRepository;
        $this->itemRequestDetailRepository = $itemRequestDetailRepository;
        $this->itemNeedToPurchaseRepository = $itemNeedToPurchaseRepository;
        $this->itemNeedToPurchaseDetailRepository = $itemNeedToPurchaseDetailRepository;
        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }
    public function show($id)
    {
        $deliveryOrder = $this->repository->find($id);
        return view($this->view . '.show', compact('deliveryOrder'));
    }


    public function getItemRequestsByCustomerOrder(Request $request)
    {
        $customerOrder = $this->customerOrderRepository->find($request->order_id);


        if (!$customerOrder) {
            return response()->json(['data' => []]);
        }

        $allDetails = collect();
        foreach ($customerOrder->itemRequests as $itemRequest) {
            foreach ($itemRequest->details as $detail) {
                if (in_array($itemRequest->request_status, ['Waiting On Process Warehouse', 'Partial Delivery by Warehouse'])) {
                    if (!empty($detail->itemPriceHistory->itemUom->item->warehouseStocks)) {
                        // Add a custom property for the request number.
                        $stock = $detail->itemPriceHistory->itemUom->item->warehouseStocks->sum('current_stock');
                        $detail->request_number = $itemRequest->request_number;
                        $detail->item_uom_id = $detail->itemPriceHistory->itemUom->id;
                        $detail->item_uom = $detail->itemPriceHistory->itemUom->unitOfMeasurement->name;
                        $detail->item_name = $detail->itemPriceHistory->itemUom->item->name;
                        $detail->item_id = $detail->itemPriceHistory->itemUom->item->id;
                        $detail->stock = $stock;
                        $detail->stock_textual = getQuantityByItem($detail->itemPriceHistory->itemUom->item, $stock, $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->name);
                        $detail->warehouse_id = $detail->itemPriceHistory->itemUom->item->warehouseStocks->first()->warehouse_id;
                        $detail->section_id = $detail->itemPriceHistory->itemUom->item->warehouseStocks->first()->section_id;
                        $detail->warehouse_name = $detail->itemPriceHistory->itemUom->item->warehouseStocks->first()->warehouse->name;
                        $detail->section_name = $detail->itemPriceHistory->itemUom->item->warehouseStocks->first()->warehouseSection->name;
                        $detail->stock_uom = $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->name;
                        $detail->stock_uom_id = $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->id;
                        $processedDeliveryQuantity = $detail->deliveryOrderDetails
                            ->filter(function ($deliveryDetail) {
                                return $deliveryDetail->header->process_status != 'Draft';
                            })
                            ->sum('quantity');

                        $detail->quantity -= $processedDeliveryQuantity;
                        if (
                            $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->id ==
                            $detail->itemPriceHistory->itemUom->unitOfMeasurement->id
                        ) {

                            if ($stock >= $detail->quantity || $stock > 0) {
                                $allDetails->push($detail);
                            }
                        } else {
                            $requestQuantity =
                                $detail->quantity *
                                $detail->itemPriceHistory->itemUom->conversion;
                            if ($stock >= $requestQuantity || $stock > 0) {
                                $allDetails->push($detail);
                            }
                        }
                    }
                }
            }
        }
        return response()->json(['data' => $allDetails]);
    }

    public function checkStock(Request $request)
    {
        $itemId = $request->item_id;
        $item = $this->itemRepository->find($itemId);
        $uomId = $request->item_uom_fullfill_id;
        $fullfillQuantity = (float) $request->fullfill_quantity;
        $itemUomId = $request->item_uom_fullfill_id;
        $itemUom = $this->itemUomRepository->find($itemUomId);
        $stock = (float) $request->stock_actual;
        if ($uomId !== $item->unitOfMeasurement->id) {
            $fullfillQuantity *= $itemUom->conversion;
        }
        // Ambil stok dari database berdasarkan item_id dan unit of measurement (UOM)
        //$stock = getQuantityByItem($itemId); // Fungsi ini harus tersedia di helper atau model

        if ($fullfillQuantity > $stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fullfill quantity exceeds available stock / 数量超过可用库存',
                'available_stock' => $stock
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stock is sufficient / 库存充足',
            'available_stock' => $stock
        ]);
    }
    public function notifyPurchasing()
    {

        try {
            DB::beginTransaction();
            $customer_order_id = request()->input('customer_order_id');
            $orderBy = ['id' => 'asc'];
            $with = [];
            $scope = ['requestStatus' => ['Waiting On Process Warehouse']];

            $customerData = $this->customerOrderRepository->getData(
                $scope,
                $with,
                $orderBy,
                null,
                [
                    ['id', '=', $customer_order_id]
                ],
                'first',
                function ($order) {
                    return $order->totalItemNeedToProcess > 0;
                }
            );
            $itemNeedToPurchase = $this->itemNeedToPurchaseRepository->create([
                'customer_order_id' => $customer_order_id,
                'request_date' => now(),
                'created_by' => Auth()->user()->id
            ]);
            $checkApproval = $this->itemNeedToPurchaseRepository->checkApproval('create', $itemNeedToPurchase->id);
            if ($checkApproval['status'] == 200) {
                $itemNeedToPurchase->process_status = $checkApproval['message'];
                $itemNeedToPurchase->save();
            }
            foreach ($customerData->itemRequests->whereIn('request_status', ['Waiting On Process Warehouse']) as $key => $itemRequest) {
                foreach ($itemRequest->details as $k => $data) {
                    $itemNeedToPurchaseDetail = $this->itemNeedToPurchaseDetailRepository->create([
                        'header_id' => $itemNeedToPurchase->id,
                        'item_request_detail_id' => $data->id,
                    ]);
                }
            }
            DB::commit();
            alertNotif('success', 'Request for purchase has been created');

        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->back()->withInput();
    }
    public function create()
    {
        // Get the customer order ID from the query string, if present.
        $orderId = request()->query('customer_order_id');
        $customerOrders = $this->customerOrderRepository->getData([], [], [], null, [], 'all');
        // Retrieve the necessary data for the form dropdowns.
        $warehouses = $this->warehouseRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
        $sections = $this->warehouseSectionRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
        $items = $this->itemRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
        $itemUoms = $this->itemUomRepository->getData(
            [],
            [
                'unitOfMeasurement'
            ],
            [],
            null,
            [['is_active', '=', 1]],
            'all'
        );

        $year = date('y'); // Two-digit year
        $month = date('m'); // Two-digit month
        $prefix = "DO{$year}{$month}";
        // Count existing orders for the current month
        $count = DeliveryOrder::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        // Increment and format the count as 3 digits (e.g., 001, 002)
        $increment = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $orderNumber = "{$prefix}{$increment}";

        return view("{$this->view}.create", compact('orderId', 'warehouses', 'sections', 'items', 'itemUoms', 'customerOrders', 'orderNumber'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        $messages = [
            'process_number.required' => 'Process Number is required. / 处理编号是必填项。',
            'process_number.unique' => 'Process Number must be unique. / 处理编号必须唯一。',
            'customer_order_id.required' => 'Customer Order is required. / 客户订单是必填项。',
            'customer_order_id.exists' => 'Selected Customer Order does not exist. / 选择的客户订单不存在。',
            'process_date.required' => 'Process Date is required. / 处理日期是必填项。',
            'process_date.date' => 'Invalid date format. / 无效的日期格式。',
            'remarks.max' => 'Remarks must not exceed 255 characters. / 备注不能超过255个字符。',
            'details.required' => 'At least one item is required. / 至少需要一个物品。',
            'details.*.item_request_detail_id.required' => 'Item Request ID is required. / 物品请求ID是必填项。',
            'details.*.item_request_detail_id.exists' => 'Item Request ID does not exist. / 物品请求ID不存在。',
            'details.*.item_id.required' => 'Item is required. / 物品是必填项。',
            'details.*.item_id.exists' => 'Selected item does not exist. / 选择的物品不存在。',
            'details.*.quantity.required' => 'Request Quantity is required. / 请求数量是必填项。',
            'details.*.quantity.numeric' => 'Request Quantity must be a valid number. / 请求数量必须是有效数字。',
            'details.*.item_uom_id.required' => 'Unit of Measurement is required. / 计量单位是必填项。',
            'details.*.item_uom_id.exists' => 'Selected Unit of Measurement does not exist. / 选择的计量单位不存在。',
            'details.*.stock.required' => 'Stock is required. / 库存是必填项。',
            'details.*.stock.numeric' => 'Stock must be a valid number. / 库存必须是有效数字。',
            'details.*.warehouse_id.required' => 'Warehouse is required. / 仓库是必填项。',
            'details.*.warehouse_id.exists' => 'Selected Warehouse does not exist. / 选择的仓库不存在。',
            'details.*.section_id.required' => 'Warehouse Section is required. / 仓库区域是必填项。',
            'details.*.section_id.exists' => 'Selected Warehouse Section does not exist. / 选择的仓库区域不存在。',
            'details.*.fullfill_quantity.required' => 'Fullfill Quantity is required. / 完成数量是必填项。',
            'details.*.fullfill_quantity.numeric' => 'Fullfill Quantity must be a valid number. / 完成数量必须是有效数字。',
            'details.*.fullfill_quantity.min' => 'Fullfill Quantity must be at least 0. / 完成数量必须至少为0。',
            'details.*.item_uom_fullfill_id.required' => 'Fullfill Unit of Measurement is required. / 完成单位是必填项。',
            'details.*.item_uom_fullfill_id.exists' => 'Selected Fullfill Unit of Measurement does not exist. / 选择的完成单位不存在。',
            'details.*.remarks.max' => 'Remarks must not exceed 255 characters. / 备注不能超过255个字符。',
        ];
        $data = $request->validate([
            'process_number' => 'required|string|max:20|unique:delivery_orders,process_number',
            'customer_order_id' => 'required|exists:customer_orders,id',
            'process_date' => 'required|date',
            'remarks' => 'nullable|string|max:255',
            'details' => 'required|array|min:1',
            'details.*.item_request_detail_id' => 'required|exists:item_request_details,id',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.quantity' => 'required|numeric|min:0.001',
            'details.*.item_uom_id' => 'required|exists:item_uoms,id',
            'details.*.stock' => 'required|numeric|min:0',
            'details.*.warehouse_id' => 'required|exists:warehouses,id',
            'details.*.section_id' => 'required|exists:warehouse_sections,id',
            'details.*.fullfill_quantity' => [
                'required',
                'numeric',
                'min:0', // Changed from 0.001 to 0
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/\d+/', $attribute, $matches);
                    $index = $matches[0] ?? null;

                    if ($index !== null && isset($request->details[$index])) {
                        $requestQuantity = $request->details[$index]['quantity'];

                        if ($value > $requestQuantity) {
                            $fail("The fullfill quantity ({$value}) cannot exceed the request quantity ({$requestQuantity}). / 完成数量 ({$value}) 不能超过请求数量 ({$requestQuantity})。");
                        }
                    }
                }
            ],
            'details.*.item_uom_fullfill_id' => 'required|exists:item_uoms,id',
            'details.*.remarks' => 'nullable|string|max:255',
        ], $messages);
        try {
            // Determine status based on submit type
            $status = $request->input('submit_type') === 'draft' ? 'Draft' : 'Waiting Approval Manager';

            // Create the Delivery Order header
            $deliveryOrder = $this->repository->create([
                'process_number' => $data['process_number'],
                'customer_order_id' => $data['customer_order_id'],
                'process_date' => $data['process_date'],
                'process_status' => $status,
                'remarks' => $data['remarks'],
                'created_by' => auth()->id(),
            ]);

            if ($status === 'Waiting Approval Manager') {
                $checkApproval = $this->repository->checkApproval('create', $deliveryOrder->id);
            }

            // Track if any details were added
            $detailsAdded = false;

            // Save Delivery Order details
            foreach ($data['details'] as $detail) {
                // Skip details with fullfill_quantity of 0
                if (floatval($detail['fullfill_quantity']) <= 0) {
                    continue;
                }

                $detailsAdded = true;

                $DeliveryOrderDetail = $deliveryOrder->details()->create([
                    'header_id' => $deliveryOrder->id,
                    'item_request_detail_id' => $detail['item_request_detail_id'],
                    'item_id' => $detail['item_id'],
                    'quantity' => $detail['fullfill_quantity'],
                    'item_uom_id' => $detail['item_uom_fullfill_id'],
                    'warehouse_id' => $detail['warehouse_id'],
                    'section_id' => $detail['section_id'],
                    'remarks' => $detail['remarks'],
                ]);

                // Update item request status
                $itemRequestDetail = $this->itemRequestDetailRepository->find($detail['item_request_detail_id']);
                $itemRequest = $itemRequestDetail->itemRequest;
                $itemDetailNeedFullfill = 0;

                foreach ($itemRequest->details as $reqDetail) {
                    // Get the total fulfilled quantity across all delivery orders
                    $totalFulfilledQuantity = $reqDetail->deliveryOrderDetails()
                        ->whereHas('header', function ($query) {
                            $query->where('process_status', '!=', 'Draft');
                        })
                        ->sum('quantity');

                    // Calculate what's still needed
                    $stillNeeded = $reqDetail->quantity - $totalFulfilledQuantity;
                    if ($stillNeeded > 0) {
                        $itemDetailNeedFullfill += $stillNeeded;
                    }
                }

                $itemRequest->update([
                    'request_status' => $itemDetailNeedFullfill == 0
                        ? 'On Proccess Delivery by Warehouse'
                        : 'Partial Delivery by Warehouse'
                ]);


                // Only update stock if not a draft
                if ($status === 'Waiting Approval Manager') {
                    $stockEntry = WarehouseSectionStock::where('item_id', $detail['item_id'])
                        ->where('warehouse_id', $detail['warehouse_id'])
                        ->where('section_id', $detail['section_id'])
                        ->latest()
                        ->first();

                    if ($stockEntry) {
                        $fullFillUOM = $this->itemUomRepository->find($detail['item_uom_fullfill_id']);
                        $fullFillQuantity = $detail['fullfill_quantity'];

                        $item = $this->itemRepository->find($detail['item_id']);
                        if ($fullFillUOM->id !== $item->unitOfMeasurement->id) {
                            $fullFillQuantity *= $fullFillUOM->conversion;
                        }

                        $stockEntry->update([
                            'current_stock' => $stockEntry->current_stock - $fullFillQuantity
                        ]);
                    } else {
                        throw new Exception("Stock entry not found for item ID {$detail['item_id']} in warehouse ID {$detail['warehouse_id']}. / 找不到物品ID {$detail['item_id']} 在仓库ID {$detail['warehouse_id']} 的库存记录。");
                    }
                }
            }

            // If no details were added (all were 0 quantity), throw an error
            if (!$detailsAdded) {
                throw new Exception("At least one item must have a fulfill quantity greater than 0. / 至少一个物品的完成数量必须大于0。");
            }

            $message = $status === 'Draft'
                ? 'Delivery Order saved as draft! / 送货单已保存为草稿！'
                : 'Delivery Order created successfully! / 送货单创建成功！';

            DB::commit();
            alertNotif('success', $message);
            return redirect()->route('delivery-order.index');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Delivery Order Store Error: ' . $e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }



    public function edit($id)
    {
        try {
            $deliveryOrder = $this->repository->find($id);

            // Check if the delivery order can be edited
            $allowedStatuses = ['Draft', 'Waiting Approval Manager', 'Rejected'];
            if (!in_array($deliveryOrder->process_status, $allowedStatuses)) {
                alertNotif('warning', 'Cannot edit this delivery order. Only Draft, Waiting Approval, or Rejected status can be edited. / 无法编辑此送货单。只能编辑草稿、等待审批或被拒绝的状态。');
                return redirect()->route("{$this->route}.index");
            }

            $customerOrders = $this->customerOrderRepository->getData([], [], [], null, [], 'all');
            $warehouses = $this->warehouseRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
            $sections = $this->warehouseSectionRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
            $items = $this->itemRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
            $itemUoms = $this->itemUomRepository->getData(
                [],
                ['unitOfMeasurement'],
                [],
                null,
                [['is_active', '=', 1]],
                'all'
            );

            return view("{$this->view}.edit", compact(
                'deliveryOrder',
                'customerOrders',
                'warehouses',
                'sections',
                'items',
                'itemUoms'
            ));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $messages = [
            'process_number.required' => 'Process Number is required. / 处理编号是必填项。',
            'process_number.unique' => 'Process Number must be unique. / 处理编号必须唯一。',
            'customer_order_id.required' => 'Customer Order is required. / 客户订单是必填项。',
            'customer_order_id.exists' => 'Selected Customer Order does not exist. / 选择的客户订单不存在。',
            'process_date.required' => 'Process Date is required. / 处理日期是必填项。',
            'process_date.date' => 'Invalid date format. / 无效的日期格式。',
            'remarks.max' => 'Remarks must not exceed 255 characters. / 备注不能超过255个字符。',
            'details.required' => 'At least one item is required. / 至少需要一个物品。',
            'details.*.item_request_detail_id.required' => 'Item Request ID is required. / 物品请求ID是必填项。',
            'details.*.item_request_detail_id.exists' => 'Item Request ID does not exist. / 物品请求ID不存在。',
            'details.*.item_id.required' => 'Item is required. / 物品是必填项。',
            'details.*.item_id.exists' => 'Selected item does not exist. / 选择的物品不存在。',
            'details.*.quantity.required' => 'Request Quantity is required. / 请求数量是必填项。',
            'details.*.quantity.numeric' => 'Request Quantity must be a valid number. / 请求数量必须是有效数字。',
            'details.*.quantity.min' => 'Request Quantity must be at least 0.001. / 请求数量必须至少为0.001。',
            'details.*.item_uom_id.required' => 'Unit of Measurement is required. / 计量单位是必填项。',
            'details.*.item_uom_id.exists' => 'Selected Unit of Measurement does not exist. / 选择的计量单位不存在。',
            'details.*.stock.required' => 'Stock is required. / 库存是必填项。',
            'details.*.stock.numeric' => 'Stock must be a valid number. / 库存必须是有效数字。',
            'details.*.warehouse_id.required' => 'Warehouse is required. / 仓库是必填项。',
            'details.*.warehouse_id.exists' => 'Selected Warehouse does not exist. / 选择的仓库不存在。',
            'details.*.section_id.required' => 'Warehouse Section is required. / 仓库区域是必填项。',
            'details.*.section_id.exists' => 'Selected Warehouse Section does not exist. / 选择的仓库区域不存在。',
            'details.*.fullfill_quantity.required' => 'Fullfill Quantity is required. / 完成数量是必填项。',
            'details.*.fullfill_quantity.numeric' => 'Fullfill Quantity must be a valid number. / 完成数量必须是有效数字。',
            'details.*.item_uom_fullfill_id.required' => 'Fullfill Unit of Measurement is required. / 完成单位是必填项。',
            'details.*.item_uom_fullfill_id.exists' => 'Selected Fullfill Unit of Measurement does not exist. / 选择的完成单位不存在。',
            'details.*.remarks.max' => 'Remarks must not exceed 255 characters. / 备注不能超过255个字符。',
        ];

        try {
            $deliveryOrder = $this->repository->find($id);

            // Validate the request
            $data = $request->validate([
                'process_number' => 'required|string|max:20|unique:delivery_orders,process_number,' . $id,
                'customer_order_id' => 'required|exists:customer_orders,id',
                'process_date' => 'required|date',
                'remarks' => 'nullable|string|max:255',
                'details' => 'required|array|min:1',
                'details.*.item_request_detail_id' => 'required|exists:item_request_details,id',
                'details.*.item_id' => 'required|exists:items,id',
                'details.*.quantity' => 'required|numeric|min:0.001',
                'details.*.item_uom_id' => 'required|exists:item_uoms,id',
                'details.*.stock' => 'required|numeric|min:0',
                'details.*.warehouse_id' => 'required|exists:warehouses,id',
                'details.*.section_id' => 'required|exists:warehouse_sections,id',
                'details.*.fullfill_quantity' => [
                    'required',
                    'numeric',
                    'min:0', // Changed from 0.001 to 0
                    function ($attribute, $value, $fail) use ($request) {
                        preg_match('/\d+/', $attribute, $matches);
                        $index = $matches[0] ?? null;

                        if ($index !== null && isset($request->details[$index])) {
                            $requestQuantity = $request->details[$index]['quantity'];

                            if ($value > $requestQuantity) {
                                $fail("The fullfill quantity ({$value}) cannot exceed the request quantity ({$requestQuantity}). / 完成数量 ({$value}) 不能超过请求数量 ({$requestQuantity})。");
                            }
                        }
                    }
                ],
                'details.*.item_uom_fullfill_id' => 'required|exists:item_uoms,id',
                'details.*.remarks' => 'nullable|string|max:255',
            ], $messages);

            // Determine the status based on submit type
            $status = $request->input('submit_type') === 'Draft' ? 'Draft' : 'Waiting Approval Manager';

            // Update delivery order header
            $deliveryOrder->update([
                'customer_order_id' => $data['customer_order_id'],
                'process_date' => $data['process_date'],
                'process_status' => $status,
                'remarks' => $data['remarks'],
                'updated_by' => auth()->id(),
            ]);

            // If not draft, handle stock updates and item request status updates
            if ($status !== 'Draft') {
                // Check for approval requirements
                $checkApproval = $this->repository->checkApproval('update', $deliveryOrder->id);

                // Get existing details for stock reversal
                $existingDetails = $deliveryOrder->details()->with('itemUom')->get();

                // Reverse previous stock changes
                foreach ($existingDetails as $detail) {
                    $stockEntry = WarehouseSectionStock::where('item_id', $detail->item_id)
                        ->where('warehouse_id', $detail->warehouse_id)
                        ->where('section_id', $detail->section_id)
                        ->latest()
                        ->first();

                    if ($stockEntry) {
                        // Convert quantity back to base unit if necessary
                        $returnQuantity = $detail->quantity;
                        if ($detail->itemUom && $detail->item && $detail->itemUom->id !== $detail->item->unit_of_measurement_id) {
                            $returnQuantity *= $detail->itemUom->conversion;
                        }

                        $stockEntry->update([
                            'current_stock' => $stockEntry->current_stock + $returnQuantity
                        ]);
                    }
                }
            }

            // Delete existing details
            $deliveryOrder->details()->delete();

            // Track if any details were added
            $detailsAdded = false;

            // Create new details
            foreach ($data['details'] as $detail) {
                // Skip details with fullfill_quantity of 0
                if (floatval($detail['fullfill_quantity']) <= 0) {
                    continue;
                }

                $detailsAdded = true;

                $DeliveryOrderDetail = $deliveryOrder->details()->create([
                    'header_id' => $deliveryOrder->id,
                    'item_request_detail_id' => $detail['item_request_detail_id'],
                    'item_id' => $detail['item_id'],
                    'quantity' => $detail['fullfill_quantity'],
                    'item_uom_id' => $detail['item_uom_fullfill_id'],
                    'warehouse_id' => $detail['warehouse_id'],
                    'section_id' => $detail['section_id'],
                    'remarks' => $detail['remarks'] ?? null,
                ]);
                $detailsToKeep[] = $DeliveryOrderDetail->id;

                // Only process stock updates if not draft
                if ($status !== 'Draft') {
                    // Update stock in warehouse
                    $stockEntry = WarehouseSectionStock::where('item_id', $detail['item_id'])
                        ->where('warehouse_id', $detail['warehouse_id'])
                        ->where('section_id', $detail['section_id'])
                        ->latest()
                        ->first();

                    if ($stockEntry) {
                        $fullFillUOM = $this->itemUomRepository->find($detail['item_uom_fullfill_id']);
                        $fullFillQuantity = $detail['fullfill_quantity'];

                        $item = $this->itemRepository->find($detail['item_id']);
                        if ($fullFillUOM->id !== $item->unitOfMeasurement->id) {
                            $fullFillQuantity *= $fullFillUOM->conversion;
                        }

                        $stockEntry->update([
                            'current_stock' => $stockEntry->current_stock - $fullFillQuantity
                        ]);
                    } else {
                        throw new \Exception("Stock entry not found for item ID {$detail['item_id']} in warehouse ID {$detail['warehouse_id']}. / 找不到物品ID {$detail['item_id']} 在仓库ID {$detail['warehouse_id']} 的库存记录。");
                    }

                    // Update item request status
                    $itemRequestDetail = $this->itemRequestDetailRepository->find($detail['item_request_detail_id']);
                    $itemRequest = $itemRequestDetail->itemRequest;
                    $itemDetailNeedFullfill = 0;

                    foreach ($itemRequest->details as $reqDetail) {
                        // Get the total fulfilled quantity across all delivery orders
                        $totalFulfilledQuantity = $reqDetail->deliveryOrderDetails()
                            ->whereHas('header', function ($query) {
                                $query->where('process_status', '!=', 'Draft');
                            })
                            ->sum('quantity');

                        // Calculate what's still needed
                        $stillNeeded = $reqDetail->quantity - $totalFulfilledQuantity;
                        if ($stillNeeded > 0) {
                            $itemDetailNeedFullfill += $stillNeeded;
                        }
                    }

                    $itemRequest->update([
                        'request_status' => $itemDetailNeedFullfill == 0
                            ? 'On Proccess Delivery by Warehouse'
                            : 'Partial Delivery by Warehouse'
                    ]);

                }
            }

            // If no details were added (all were 0 quantity), throw an error
            if (!$detailsAdded) {
                throw new Exception("At least one item must have a fulfill quantity greater than 0. / 至少一个物品的完成数量必须大于0。");
            }
            if (count($detailsToKeep) > 0) {
                $deliveryOrder->details()->whereNotIn('id', $detailsToKeep)->delete();
            } else {
                // All details were set to 0, don't delete what we just added
                $deliveryOrder->details()->where('id', '<', 0)->delete(); // Won't delete anything, just a placeholder
            }
            DB::commit();

            $message = $status === 'Draft'
                ? 'Delivery Order saved as draft! / 送货单已保存为草稿！'
                : 'Delivery Order updated successfully! / 送货单更新成功！';

            alertNotif('success', $message);
            return redirect()->route("{$this->route}.index");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Delivery Order Update Error: ' . $e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $repository->delete();
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
