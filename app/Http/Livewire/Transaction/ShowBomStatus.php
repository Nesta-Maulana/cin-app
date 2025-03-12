<?php

namespace App\Http\Livewire\Transaction;

use Livewire\Component;
use App\Models\ItemRequest;
use App\Models\CustomerOrder;
use App\Models\ComparisonTable;
use App\Models\ItemRequestDetail;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Transaction\ItemRequest\ItemRequestRepositoryInterface;

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

    // Filters
    public $selectedCustomerOrder = "";
    public $statusFilter = "";

    public $title = "Item Request";
    public $model, $modelId;
    public $newStatus; // For batch status updates
    public $selectedItemRequestId; // For create comparison table

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
            $with = ['details']; // Load related details
            $where = [];
            // Apply customer order filter
            if (!empty($this->selectedCustomerOrder)) {
                $where[] = ['customer_order_id', '=', $this->selectedCustomerOrder];
            }

            // Apply status filter
            if (!empty($this->statusFilter)) {
                $where[] = ['request_status','=',$this->statusFilter];
            }

            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }
            $table = $this->repository->getData($scope, $with, $orderBy, $this->paginate,$where,'all');

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

            // Load item details and existing comparison prices
            $itemRequest = $this->repository->find($itemId);

        }
    }

    // Save comparison price
    public function saveComparisonPrice($detailId)
    {
        try {
            if (!isset($this->comparisonPrices[$detailId])) {
                $this->alert('warning', 'Please enter a price');
                return;
            }

            $price = $this->comparisonPrices[$detailId];

            // Find and update the item detail
            $detail = ItemRequestDetail::findOrFail($detailId);
            $detail->comparison_price = $price;
            $detail->save();

            $this->alert('success', 'Comparison price saved successfully');
        } catch (\Exception $e) {
            $this->alert('error', 'Error saving comparison price: ' . $e->getMessage());
        }
    }

    // Create comparison table
    public function createComparisonTable($itemRequestId)
    {
        $this->selectedItemRequestId = $itemRequestId;
        $this->dispatchBrowserEvent('open-comparison-modal');
    }

    public function confirmCreateComparisonTable()
    {
        try {
            $itemRequest = $this->repository->findById($this->selectedItemRequestId, ['details']);

            // Check if all items have comparison prices
            $missingPrices = false;
            foreach ($itemRequest->details as $detail) {
                if (!isset($detail->comparison_price)) {
                    $missingPrices = true;
                    break;
                }
            }

            if ($missingPrices) {
                $this->alert('warning', 'Please enter comparison prices for all items');
                $this->closeModal();
                return;
            }

            // Create comparison table
            $comparisonTable = ComparisonTable::create([
                'item_request_id' => $this->selectedItemRequestId,
                'created_by' => auth()->id(),
            ]);

            // Redirect to comparison table detail page
            $this->closeModal();
            $this->alert('success', 'Comparison table created successfully');

            // Redirect to comparison table edit page
            return redirect()->route('comparison-table.edit', $comparisonTable->id);

        } catch (\Exception $e) {
            $this->alert('error', 'Error creating comparison table: ' . $e->getMessage());
            $this->closeModal();
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
        $data = ['modelId', 'newStatus', 'selectedItemRequestId'];
        foreach ($data as $item) {
            $this->$item = "";
        }
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

        // Apply customer order filter
        if (!empty($this->selectedCustomerOrder)) {
            $scope['customer_order_id'] = $this->selectedCustomerOrder;
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $scope['request_status'] = $this->statusFilter;
        }

        // Apply search filter
        if (!empty($this->search)) {
            $scope['filter'] = [$this->search];
        }

        $model = $this->repository->getAll($scope);

        if ($value) {
            $this->selected = $model->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }
}
