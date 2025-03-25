<?php

namespace App\Http\Livewire\Transaction;

use App\Models\PrePurchaseOrder;
use DB;
use Livewire\Component;
use App\Models\ItemRequest;
use App\Models\CustomerOrder;
use App\Models\ItemRequestDetail;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Transaction\ItemRequest\ItemRequestRepositoryInterface;
use Log;

class ShowBomStatus extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;
    public $expandedRows = [];
    public $comparisonPrices = [];
    public $validComparisonItems = [];
    public $quantityErrors = [];
    public $mergedComparisonItems = []; // For displaying in modal view only

    // Filters
    public $selectedCustomerOrder = "";
    public $statusFilter = "";

    public $title = "Item Request";
    public $model, $modelId;
    public $newStatus; // For batch status updates
    public $selectedComparisonDetails = []; // For displaying in modal
    public $comparisonNumber; // CT01, CT02, etc.

    protected $repository;

    protected $listeners = [
        'deleteConfirmed' => 'delete'
    ];

    public function mount(ItemRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate()
    {
        $this->repository = app(ItemRequestRepositoryInterface::class);
    }

    public function render()
    {
        try {
            $scope = [];
            $orderBy = ['id' => 'desc'];
            $with = ['details.itemPriceHistory.itemUom.item', 'details.itemPriceHistory.itemUom.unitOfMeasurement', 'details.deliveryOrderDetails', 'createdBy']; // Load related details
            $where = [];

            // Apply customer order filter
            if (!empty($this->selectedCustomerOrder)) {
                $where[] = ['customer_order_id', '=', $this->selectedCustomerOrder];
            }

            // Apply status filter
            if (!empty($this->statusFilter)) {
                $where[] = ['request_status', '=', $this->statusFilter];
            }

            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }

            $table = $this->repository->getData($scope, $with, $orderBy, $this->paginate, $where, 'all');

            // Get customer orders for filter
            $customerOrders = CustomerOrder::orderBy('order_number')->get();

            return view('livewire.transaction.show-bom-status', [
                'table' => $table,
                'customerOrders' => $customerOrders,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching data: ' . $e->getMessage());
            return view('livewire.transaction.show-bom-status', [
                'table' => collect([]),
                'customerOrders' => collect([]),
            ]);
        }
    }

    // Toggle expand/collapse row
    public function toggleExpand($itemId)
    {
        if (isset($this->expandedRows[$itemId])) {
            unset($this->expandedRows[$itemId]);
        } else {
            $this->expandedRows[$itemId] = true;

            // Load item details
            $itemRequest = $this->repository->find($itemId);

            // Check existing values for validation
            foreach ($itemRequest->details as $detail) {
                if (isset($this->comparisonPrices[$detail->id]) && $this->comparisonPrices[$detail->id] > 0) {
                    $this->validateQuantity($detail->id, $detail->quantity);
                }
            }
        }
    }

    // Validate that quantity does not exceed available quantity
    public function validateQuantity($detailId, $maxQuantity)
    {
        // Remove previous entry if exists
        if (isset($this->validComparisonItems[$detailId])) {
            unset($this->validComparisonItems[$detailId]);
        }

        // Remove previous error if exists
        if (isset($this->quantityErrors[$detailId])) {
            unset($this->quantityErrors[$detailId]);
        }

        if (!isset($this->comparisonPrices[$detailId]) || $this->comparisonPrices[$detailId] <= 0) {
            return;
        }

        $inputQuantity = $this->comparisonPrices[$detailId];

        if ($inputQuantity > $maxQuantity) {
            $this->quantityErrors[$detailId] = "Cannot exceed available quantity ($maxQuantity)";
            return;
        }

        // Store valid detail
        $detail = ItemRequestDetail::with(['itemPriceHistory.itemUom.item', 'itemPriceHistory.itemUom.unitOfMeasurement'])->find($detailId);
        if ($detail) {
            $this->validComparisonItems[$detailId] = [
                'detail_id' => $detailId,
                'item_request_id' => $detail->item_request_id,
                'item_name' => $detail->itemPriceHistory->itemUom->item->name,
                'specification' => $detail->itemPriceHistory->specification ??
                    ($detail->specification ??
                        ($detail->itemPriceHistory->itemUom->item->specification ?? '')),
                'unit' => $detail->itemPriceHistory->itemUom->unitOfMeasurement->name,
                'quantity' => $inputQuantity,
            ];
        }
    }

    // Create comparison table
    public function createComparisonTable()
    {
        // Check if we have any valid items
        if (count($this->validComparisonItems) === 0) {
            $this->alert('warning', 'Please enter valid quantities for comparison');
            return;
        }

        // Get comparison table number from backend (CT01, CT02, etc.)
        $this->comparisonNumber = $this->generateComparisonNumber();

        // Prepare details for modal
        $this->selectedComparisonDetails = array_values($this->validComparisonItems);

        // Merge items with same name and UOM for display purposes
        $this->mergeComparisonItems();

        $this->dispatchBrowserEvent('open-comparison-modal');
    }

    // Merge items with same name and UOM for display only
    protected function mergeComparisonItems()
    {
        $this->mergedComparisonItems = [];
        $mergeMap = [];

        foreach ($this->validComparisonItems as $item) {
            $key = $item['item_name'] . '_' . $item['unit'];

            if (!isset($mergeMap[$key])) {
                $mergeMap[$key] = [
                    'item_name' => $item['item_name'],
                    'specification' => $item['specification'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'original_details' => [$item['detail_id']]
                ];
            } else {
                $mergeMap[$key]['quantity'] += $item['quantity'];
                $mergeMap[$key]['original_details'][] = $item['detail_id'];
            }
        }

        $this->mergedComparisonItems = array_values($mergeMap);
    }

    // Generate comparison table number
    protected function generateComparisonNumber()
    {
        // Get the latest comparison table number from the database
        $latestNumber = PrePurchaseOrder::latest('id')->first();

        if (!$latestNumber) {
            return 'CT01';
        }

        // Extract the number part and increment
        $number = preg_replace('/[^0-9]/', '', $latestNumber->pre_po_number ?? 'CT00');
        $nextNumber = intval($number) + 1;

        return 'CT' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    public function confirmCreateComparisonTable()
    {
        DB::beginTransaction();

        try {
            if (count($this->validComparisonItems) === 0) {
                $this->alert('warning', 'No valid items for comparison');
                $this->closeModal();
                return;
            }

            // Create the comparison table
            $comparisonTable = PrePurchaseOrder::create([
                'pre_po_number' => $this->comparisonNumber,
                'customer_order_id' => $this->selectedCustomerOrder,
                'process_status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Group details by item request
            foreach ($this->validComparisonItems as $detail) {
                $detailId = $detail['detail_id'];
                $itemRequestDetail = ItemRequestDetail::find($detailId);

                if ($itemRequestDetail) {
                    $comparisonTable->details()->create([
                        'item_request_detail_id' => $detailId,
                        'item_uom_id' => $itemRequestDetail->itemPriceHistory->item_uom_id,
                        'quantity' => $detail['quantity']
                    ]);
                }
            }


            // Reset the collected comparison items
            $this->validComparisonItems = [];
            $this->mergedComparisonItems = [];
            $this->comparisonPrices = [];

            $this->closeModal();
            $this->alert('success', 'Comparison table created successfully');

            // Commit the transaction
            DB::commit();

            // Redirect to comparison table edit page
            return redirect()->route('pre-purchase-order.edit', $comparisonTable->id);

        } catch (\Exception $e) {
            Log::error('Error creating comparison table: ' . $e->getMessage());
            $this->alert('error', 'Error creating comparison table: ' . $e->getMessage());
            // Rollback the transaction
            DB::rollBack();
        }
    }

    public function updateSelectedStatus()
    {
        if (empty($this->selected)) {
            $this->alert('warning', 'No items selected');
            return;
        }

        if (empty($this->newStatus)) {
            $this->alert('warning', 'Please select a status');
            return;
        }

        try {
            $this->repository->updateMultiple($this->selected, ['request_status' => $this->newStatus]);
            $this->alert('success', 'Status updated successfully');
            $this->selected = [];
            $this->selectAll = false;
            $this->newStatus = null;
            $this->closeModal();
        } catch (\Exception $e) {
            $this->alert('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->repository->delete($this->modelId);
            $this->alert('success', 'Item Request deleted successfully');
        } catch (\Exception $e) {
            $this->alert('error', 'Error deleting Item Request: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->modelId = $id;
        $this->confirm('Are you sure you want to delete this Item Request?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes, Delete',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'deleteConfirmed',
        ]);
    }

    // Misc
    public function resetCreateForm()
    {
        $data = ['modelId', 'newStatus', 'selectedComparisonDetails', 'comparisonNumber', 'mergedComparisonItems'];
        foreach ($data as $item) {
            $this->$item = null;
        }
        $this->selectedComparisonDetails = [];
        $this->mergedComparisonItems = [];
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetErrorBag();
        $this->resetCreateForm();
    }

    public function updated($name)
    {
        if (in_array($name, ['search', 'selectedCustomerOrder', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    public function modelId($id)
    {
        $this->modelId = $id;
    }

    public function updatedSelectAll($value)
    {
        $scope = [];
        $where = [];

        // Apply customer order filter
        if (!empty($this->selectedCustomerOrder)) {
            $where[] = ['customer_order_id', '=', $this->selectedCustomerOrder];
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $where[] = ['request_status', '=', $this->statusFilter];
        }

        // Apply search filter
        if (!empty($this->search)) {
            $scope['filter'] = [$this->search];
        }

        $model = $this->repository->getData($scope, $where);

        if ($value) {
            $this->selected = $model->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }
}
