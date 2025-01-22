<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\ItemRequest;
use App\Repositories\Master\Customer\CustomerRepositoryInterface;
use App\Repositories\Master\Item\ItemRepositoryInterface;
use App\Repositories\Master\ItemUom\ItemUomRepositoryInterface;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\Transaction\ItemRequest\ItemRequestRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ItemRequestController extends Controller
{
    public $view, $route;
    protected $repository, $customerOrderRepository, $itemRepository, $itemUomRepository;
    public function __construct(
        ItemRequestRepositoryInterface $repository,
        CustomerOrderRepositoryInterface $customerOrderRepository,
        ItemRepositoryInterface $itemRepository,
        ItemUomRepositoryInterface $itemUomRepository
    ) {
        $this->repository = $repository;
        $this->customerOrderRepository = $customerOrderRepository;
        $this->itemRepository = $itemRepository;
        $this->itemUomRepository = $itemUomRepository;
        $this->view = 'transaction.item-request';
        $this->route = 'item-request';

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
        $itemRequest = $this->repository->find($id);
        return view($this->view . '.show', compact('itemRequest'));
    }

    public function create()
    {
        // Generate Request Number / 生成请求编号
        $currentYear = now()->format('y'); // 2-digit year / 两位数年份
        $currentMonth = now()->format('m'); // 2-digit month / 两位数月份

        // Count existing item requests for the current month and year / 统计当前月份和年份的现有请求数量
        $itemCount = ItemRequest::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Increment the count by 1 for the new request / 新请求计数加1
        $incrementalNumber = str_pad($itemCount + 1, 3, '0', STR_PAD_LEFT); // Pad with leading zeros / 用前导零填充

        // Format request number / 格式化请求编号
        $requestNumber = "BOM{$currentYear}{$currentMonth}{$incrementalNumber}";

        // Retrieve related data / 检索相关数据
        $customerOrders = $this->customerOrderRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->mapWithKeys(function ($category) {

            $name = $category->order_number . " - " . $category->project_name;
            return [$category->id => $name];
        });

        $items = $this->itemRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->mapWithKeys(function ($item) {
            $name = $item->name;
            return [$item->id => $name];
        });
        // Return view with required data / 返回视图与所需数据
        return view($this->view . '.create', compact('requestNumber', 'customerOrders', 'items'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'request_number' => 'required|string|unique:item_requests,request_number',
            'request_date' => 'required|date',
            'customer_order_id' => 'required|exists:customer_orders,id',
            'remark' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*' => 'required|exists:items,id',
            'uoms' => 'required|array',
            'uoms.*' => 'required|exists:item_uoms,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
            'request_status' => 'required|in:Need Approval Manager,Draft',
        ], [
            'items.required' => 'At least one item is required. / 至少需要一个物品。',
            'quantities.*.required' => 'Quantity is required for all items. / 所有物品的数量是必填的。',
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                // Create the main Item Request record
                $itemRequest = $this->repository->create([
                    'request_number' => $data['request_number'],
                    'request_date' => $data['request_date'],
                    'customer_order_id' => $data['customer_order_id'],
                    'remark' => $data['remark'] ?? null,
                    'request_status' => $data['request_status'],
                ]);
                // Save Item Request Details
                foreach ($data['items'] as $index => $itemId) {
                    $itemUom = $this->itemUomRepository->find($data['uoms'][$index]);
                    $itemRequest->details()->create([
                        'item_request_id' => $itemRequest->id,
                        'item_price_history_id' => $itemUom->latestPrice->id,
                        'quantity' => $data['quantities'][$index],
                        'remarks' => $data['remarks'][$index] ?? null,
                    ]);
                }
            });

            alertNotif('save'); // Success notification

        } catch (Exception $e) {
            Log::error($e->getMessage()); // Log the error
            alertNotif('error', $e->getMessage()); // Error notification
        }
        return redirect()->route("{$this->route}.index");
    }


    public function edit($id)
    {
        try {
            $data = $this->repository->find($id);
            return view("{$this->view}.edit", compact('data'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            //
        ]);
        $data['updated_by'] = auth()->user()->id;
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $this->repository->update($id, $data);
            });
            alertNotif('update');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function approval(Request $request)
    {
        try {
            $repository = $this->repository->find($request->item_request_id);

            if (strpos(auth()->user()->roles->first()->name, 'Manager') !== false) {
                if ($request->approval_status == 'approved') {
                    $repository->request_status = 'Waiting Approval Director';
                } else {
                    $repository->request_status = 'Rejected';
                }
            } elseif (auth()->user()->roles->first()->name == 'Director') {
                if ($request->approval_status == 'approved') {
                    $repository->request_status = 'Approved';
                } else {
                    $repository->request_status = 'Rejected';
                }
            }
            $repository->save();
            alertNotif('update');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
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
