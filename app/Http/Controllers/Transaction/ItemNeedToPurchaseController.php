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


    public function getItemByCustomerOrder(Request $request)
    {
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json(['success' => false, 'message' => 'Customer order ID is required']);
        }

        $itemNeedToPurchases = $this->repository->getData(
            [],
            [
                'customerOrder',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemRequest',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemPriceHistory.itemUom.item',
                'itemNeedToPurchaseDetail.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
                'itemNeedToPurchaseDetail.itemRequestDetail.deliveryOrderDetails.header',
                'itemNeedToPurchaseDetail.itemRequestDetail.purchaseOrderDetails',
            ],
            [],
            null,
            [
                ['process_status', '=', 'Waiting Process Purchasing'],
                ['customer_order_id', '=', $orderId]
            ],
            'all'
        );

        // Filter out item need to purchase details with needToBuyQuantity <= 0
        foreach ($itemNeedToPurchases as $itemNeedToPurchase) {
            $validDetails = collect();

            foreach ($itemNeedToPurchase->itemNeedToPurchaseDetail as $detail) {
                // Calculate required quantities
                $requestedQuantity = $detail->itemRequestDetail->quantity;

                $receivedQuantity = $detail->itemRequestDetail->deliveryOrderDetails()
                    ->whereHas('header', function ($query) {
                        $query->where('process_status', '<>', 'Draft');
                    })
                    ->sum('quantity');

                $pendingQuantity = $requestedQuantity - $receivedQuantity;
                $purchaseQuantity = $detail->itemRequestDetail->purchaseOrderDetails->sum('quantity');

                // Apply UOM conversion if needed
                $requestedUOM = $detail->itemRequestDetail->itemPriceHistory->itemUom->unitOfMeasurement->id;
                $baseUOM = $detail->itemRequestDetail->itemPriceHistory->itemUom->item->unitOfMeasurement->id;

                if ($requestedUOM != $baseUOM) {
                    $pendingQuantity *= $detail->itemRequestDetail->itemPriceHistory->itemUom->conversion;
                    $receivedQuantity *= $detail->itemRequestDetail->itemPriceHistory->itemUom->conversion;
                }

                $needToBuyQuantity = $pendingQuantity - $purchaseQuantity;

                // Only keep details where needToBuyQuantity > 0
                if ($needToBuyQuantity > 0) {
                    // If needed, you could add this calculated value to the detail object
                    $detail->needToBuyQuantity = $needToBuyQuantity;
                    $validDetails->push($detail);
                }
            }

            // Replace the original details collection with the filtered one
            $itemNeedToPurchase->setRelation('itemNeedToPurchaseDetail', $validDetails);
        }

        // Filter out any itemNeedToPurchases that now have no valid details
        $itemNeedToPurchases = $itemNeedToPurchases->filter(function ($item) {
            return $item->itemNeedToPurchaseDetail->isNotEmpty();
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'itemNeedToPurchases' => $itemNeedToPurchases
            ]
        ]);
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
