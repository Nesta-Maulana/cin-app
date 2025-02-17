<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\Transaction\ItemNeedToPurchase\ItemNeedToPurchaseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ItemNeedToPurchaseController extends Controller
{
    public $view, $route;
    protected $repository, $customerOrderRepository;
    public function __construct(
        ItemNeedToPurchaseRepositoryInterface $repository,
        CustomerOrderRepositoryInterface $customerOrderRepository
    ) {
        $this->repository = $repository;
        $this->customerOrderRepository = $customerOrderRepository;
        $this->view = 'transaction.item-need-to-purchase';
        $this->route = 'item-need-to-purchase';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }
    public function show($customerOrderId)
    {
        $customerOrder = $this->customerOrderRepository->find($customerOrderId);
        return view("{$this->view}.show", compact('customerOrder'));
    }


    public function getItemByCustomerOrder()
    {
        $itemNeedToPurchases = $this->repository->getData(
            [],
            [
                'customerOrder',
                'itemNeedToPurchaseDetail',
                'itemNeedToPurchaseDetail.itemRequestDetail',
                'itemNeedToPurchaseDetail.itemRequestDetail',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemPriceHistory',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemRequest',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemPriceHistory.itemUom',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemPriceHistory.itemUom.item',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
            ],
            [],
            null,
            [['process_status', '=', 'Waiting Process Purchasing']],
            'all'
        );
        $items = [];
        foreach ($itemNeedToPurchases as $key => $customerOrder) {
            foreach ($customerOrder->itemNeedToPurchaseDetail as $key => $itemNeedToPurchaseDetail) {
                $item['item_id'] = $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->item->id;
                $item['item_name'] = $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->item->name;
                $requestedQuantity = $itemNeedToPurchaseDetail->itemRequestDetail->quantity;
                $receivedQuantity = $itemNeedToPurchaseDetail->itemRequestDetail->deliveryOrderDetails->sum('quantity');
                $pendingQuantity = $requestedQuantity - $receivedQuantity;
                $purchaseQuantity = $itemNeedToPurchaseDetail->itemRequestDetail->purchaseOrderDetails->sum('quantity');

                $requestedUOM = $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->id;
                $baseUOM = $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->item->unitOfMeasurement->id;
                if ($requestedUOM != $baseUOM) {
                    $pendingQuantity *= $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->conversion;
                    $receivedQuantity *= $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->conversion;
                }
                $needToBuyQuantity = $pendingQuantity - $purchaseQuantity;
                $item['need_to_buy_quantity'] = $needToBuyQuantity;
                $item['uom_id'] = $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->item->unitOfMeasurement->id;
                $item['uom_name'] = $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->item->unitOfMeasurement->name;
                $existingItem = array_filter($items, function ($item) use ($itemNeedToPurchaseDetail) {
                    return $item['item_id'] == $itemNeedToPurchaseDetail->itemRequestDetail->itemPriceHistory->itemUom->item->id;
                });

                if (count($existingItem) > 0) {
                    $items = array_map(function ($item) use ($existingItem, $needToBuyQuantity) {
                        if ($item['item_id'] == $existingItem[0]['item_id']) {
                            $item['need_to_buy_quantity'] += $needToBuyQuantity;
                        }
                        return $item;
                    }, $items);
                } else {
                    $items[] = $item;
                }
            }
        }
        return response()->json(['success' => true, 'data' => ['items' => $items, 'itemNeedToPurchases' => $itemNeedToPurchases]]);
    }
    public function create()
    {
        return view("{$this->view}.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            //
        ]);
        try {
            DB::transaction(function () use ($request, $data) {
                $this->repository->create($data);
            });
            alertNotif('save');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
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
