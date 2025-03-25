<?php

namespace App\Http\Livewire\Transaction;

use App\Models\PrePurchaseOrder;
use DB;
use Livewire\Component;
use App\Models\ItemRequest;
use App\Models\ManualItemRequest;
use App\Models\CustomerOrder;
use App\Models\ItemRequestDetail;
use App\Models\ManualItemRequestDetail;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
    public $typeFilter = "all"; // "system", "manual", or "all"

    public $title = "Item Request";
    public $model, $modelId;
    public $newStatus; // For batch status updates
    public $selectedComparisonDetails = []; // For displaying in modal
    public $comparisonNumber; // CT01, CT02, etc.

    protected $listeners = [
        'deleteConfirmed' => 'delete'
    ];

    public function render()
    {
        try {
            // Get customer orders for filter
            $customerOrders = CustomerOrder::orderBy('order_number')->get();

            // Determine which data to fetch based on typeFilter
            if ($this->typeFilter === 'system') {
                $table = $this->getSystemItemRequests();
            } elseif ($this->typeFilter === 'manual') {
                $table = $this->getManualItemRequests();
            } else {
                // Both types - requires special handling
                $table = $this->getCombinedItemRequests();
            }

            return view('livewire.transaction.show-bom-status', [
                'table' => $table,
                'customerOrders' => $customerOrders,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ShowBomStatus render: ' . $e->getMessage());
            $this->alert('error', 'Error fetching data: ' . $e->getMessage());
            return view('livewire.transaction.show-bom-status', [
                'table' => collect([]),
                'customerOrders' => collect([]),
            ]);
        }
    }

    /**
     * Get system item requests directly from model
     */
    protected function getSystemItemRequests()
    {
        $query = ItemRequest::with([
            'details.itemPriceHistory.itemUom.item',
            'details.itemPriceHistory.itemUom.unitOfMeasurement',
            'details.deliveryOrderDetails',
            'createdBy'
        ]);

        // Apply customer order filter
        if (!empty($this->selectedCustomerOrder)) {
            $query->where('customer_order_id', $this->selectedCustomerOrder);
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('request_status', $this->statusFilter);
        }

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('request_number', 'like', "%{$this->search}%")
                    ->orWhereHas('customerOrder', function ($q) {
                        $q->where('order_number', 'like', "%{$this->search}%");
                    });
            });
        }

        // Add request_type to identify in the view
        $query->selectRaw("*, 'system' as request_type");

        return $query->orderBy('id', 'desc')->paginate($this->paginate);
    }

    /**
     * Get manual item requests directly
     */
    protected function getManualItemRequests()
    {
        $query = ManualItemRequest::with([
            'details',
            'customerOrder'
        ]);

        // Apply customer order filter
        if (!empty($this->selectedCustomerOrder)) {
            $query->where('customer_order_id', $this->selectedCustomerOrder);
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('request_status', $this->statusFilter);
        }

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('request_number', 'like', "%{$this->search}%")
                    ->orWhereHas('customerOrder', function ($q) {
                        $q->where('order_number', 'like', "%{$this->search}%");
                    });
            });
        }

        // Add request_type to identify in the view
        $query->selectRaw("*, 'manual' as request_type");

        return $query->orderBy('id', 'desc')->paginate($this->paginate);
    }

    /**
     * Get both system and manual item requests combined
     * Uses manual pagination approach to fix the Collection::total issue
     */
    protected function getCombinedItemRequests()
    {
        // System item requests
        $systemQuery = ItemRequest::with([
            'details.itemPriceHistory.itemUom.item',
            'details.itemPriceHistory.itemUom.unitOfMeasurement',
            'details.deliveryOrderDetails',
            'createdBy'
        ]);

        // Apply filters to system query
        if (!empty($this->selectedCustomerOrder)) {
            $systemQuery->where('customer_order_id', $this->selectedCustomerOrder);
        }

        if (!empty($this->statusFilter)) {
            $systemQuery->where('request_status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $systemQuery->where(function ($q) {
                $q->where('request_number', 'like', "%{$this->search}%")
                    ->orWhereHas('customerOrder', function ($q) {
                        $q->where('order_number', 'like', "%{$this->search}%");
                    });
            });
        }

        // Manual item requests
        $manualQuery = ManualItemRequest::with([
            'details',
            'customerOrder'
        ]);

        // Apply filters to manual query
        if (!empty($this->selectedCustomerOrder)) {
            $manualQuery->where('customer_order_id', $this->selectedCustomerOrder);
        }

        if (!empty($this->statusFilter)) {
            $manualQuery->where('request_status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $manualQuery->where(function ($q) {
                $q->where('request_number', 'like', "%{$this->search}%")
                    ->orWhereHas('customerOrder', function ($q) {
                        $q->where('order_number', 'like', "%{$this->search}%");
                    });
            });
        }

        // Get total count for pagination
        $systemCount = $systemQuery->count();
        $manualCount = $manualQuery->count();
        $totalCount = $systemCount + $manualCount;

        // Calculate pagination details
        $page = $this->page ?: 1;
        $perPage = $this->paginate;
        $offset = ($page - 1) * $perPage;

        // Adjust the queries with limit and offset - we'll manually merge the results
        // Since we want to sort by created_at later, we need to get more items than we need
        // and then slice them after sorting
        $systemItems = $systemQuery->orderBy('created_at', 'desc')->get();
        $manualItems = $manualQuery->orderBy('created_at', 'desc')->get();

        // Transform items to include request type
        $systemItems->each(function ($item) {
            $item->request_type = 'system';
        });

        $manualItems->each(function ($item) {
            $item->request_type = 'manual';
        });

        // Merge and sort by created_at
        $allItems = $systemItems->concat($manualItems)->sortByDesc('created_at');

        // Slice for current page
        $itemsForCurrentPage = $allItems->slice($offset, $perPage)->values();

        // Create a LengthAwarePaginator instance
        $paginator = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $totalCount,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginator;
    }

    // Toggle expand/collapse row
    public function toggleExpand($itemId, $type = null)
    {
        $key = $type ? "{$type}_{$itemId}" : $itemId;

        if (isset($this->expandedRows[$key])) {
            unset($this->expandedRows[$key]);
        } else {
            $this->expandedRows[$key] = true;

            // Load appropriate item details based on type
            if ($type === 'manual' || (isset($this->expandedRows["{$key}_type"]) && $this->expandedRows["{$key}_type"] === 'manual')) {
                $manualItemRequest = ManualItemRequest::with('details')->find($itemId);

                if ($manualItemRequest) {
                    $this->expandedRows["{$key}_type"] = 'manual';

                    // Check existing values for validation - manual item request
                    foreach ($manualItemRequest->details as $detail) {
                        if (isset($this->comparisonPrices["manual_{$detail->id}"]) && $this->comparisonPrices["manual_{$detail->id}"] > 0) {
                            $this->validateQuantity($detail->id, $detail->quantity, 'manual');
                        }
                    }
                }
            } else {
                // Default to system item request
                $itemRequest = ItemRequest::with([
                    'details.itemPriceHistory.itemUom.item',
                    'details.itemPriceHistory.itemUom.unitOfMeasurement',
                    'details.deliveryOrderDetails'
                ])->find($itemId);

                if ($itemRequest) {
                    $this->expandedRows["{$key}_type"] = 'system';

                    // Check existing values for validation - system item request
                    foreach ($itemRequest->details as $detail) {
                        if (isset($this->comparisonPrices["system_{$detail->id}"]) && $this->comparisonPrices["system_{$detail->id}"] > 0) {
                            $this->validateQuantity($detail->id, $detail->quantity, 'system');
                        }
                    }
                }
            }
        }
    }

    // Validate that quantity does not exceed available quantity
    public function validateQuantity($detailId, $maxQuantity, $type = 'system')
    {
        $key = "{$type}_{$detailId}";

        // Remove previous entry if exists
        if (isset($this->validComparisonItems[$key])) {
            unset($this->validComparisonItems[$key]);
        }

        // Remove previous error if exists
        if (isset($this->quantityErrors[$key])) {
            unset($this->quantityErrors[$key]);
        }

        if (!isset($this->comparisonPrices[$key]) || $this->comparisonPrices[$key] <= 0) {
            return;
        }

        $inputQuantity = $this->comparisonPrices[$key];

        if ($inputQuantity > $maxQuantity) {
            $this->quantityErrors[$key] = "Cannot exceed available quantity ($maxQuantity)";
            return;
        }

        // Store valid detail based on type
        if ($type === 'manual') {
            $detail = ManualItemRequestDetail::find($detailId);
            if ($detail) {
                $this->validComparisonItems[$key] = [
                    'detail_id' => $detailId,
                    'type' => 'manual',
                    'item_request_id' => $detail->manual_item_request_id,
                    'item_name' => $detail->item_name,
                    'specification' => $detail->specification ?? '',
                    'unit' => $detail->unit,
                    'quantity' => $inputQuantity,
                ];
            }
        } else {
            // System item request
            $detail = ItemRequestDetail::with(['itemPriceHistory.itemUom.item', 'itemPriceHistory.itemUom.unitOfMeasurement'])->find($detailId);
            if ($detail) {
                $this->validComparisonItems[$key] = [
                    'detail_id' => $detailId,
                    'type' => 'system',
                    'item_request_id' => $detail->item_request_id,
                    'item_name' => $detail->itemPriceHistory->itemUom->item->name,
                    'specification' => $detail->itemPriceHistory->specification ??
                        ($detail->specification ??
                            ($detail->itemPriceHistory->itemUom->item->specification ?? '')),
                    'unit' => $detail->itemPriceHistory->itemUom->unitOfMeasurement->name,
                    'quantity' => $inputQuantity,
                    'item_uom_id' => $detail->itemPriceHistory->item_uom_id
                ];
            }
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
                    'original_details' => [$item['type'] . '_' . $item['detail_id']]
                ];
            } else {
                $mergeMap[$key]['quantity'] += $item['quantity'];
                $mergeMap[$key]['original_details'][] = $item['type'] . '_' . $item['detail_id'];
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

            // Add details from both types of item requests
            foreach ($this->validComparisonItems as $key => $detail) {
                $detailId = $detail['detail_id'];
                $type = $detail['type'];

                if ($type === 'manual') {
                    // Handle manual item request detail
                    $manualDetail = ManualItemRequestDetail::find($detailId);
                    if ($manualDetail) {
                        $comparisonTable->details()->create([
                            'manual_item_request_detail_id' => $detailId,
                            'item_name' => $manualDetail->item_name,
                            'specification' => $manualDetail->specification,
                            'unit' => $manualDetail->unit,
                            'quantity' => $detail['quantity']
                        ]);
                    }
                } else {
                    // Handle system item request detail
                    $systemDetail = ItemRequestDetail::find($detailId);
                    if ($systemDetail) {
                        $comparisonTable->details()->create([
                            'item_request_detail_id' => $detailId,
                            'item_uom_id' => $detail['item_uom_id'],
                            'quantity' => $detail['quantity']
                        ]);
                    }
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
            // Process each selected item by type
            foreach ($this->selected as $selectedItem) {
                // Check if it's in the format "type_id"
                if (strpos($selectedItem, '_') !== false) {
                    list($type, $id) = explode('_', $selectedItem);

                    if ($type === 'manual') {
                        // Update manual item request
                        $manualItemRequest = ManualItemRequest::find($id);
                        if ($manualItemRequest) {
                            $manualItemRequest->update(['request_status' => $this->newStatus]);
                        }
                    } else {
                        // Update system item request
                        $systemItemRequest = ItemRequest::find($id);
                        if ($systemItemRequest) {
                            $systemItemRequest->update(['request_status' => $this->newStatus]);
                        }
                    }
                } else {
                    // Fallback to system item request if no type specified
                    $systemItemRequest = ItemRequest::find($selectedItem);
                    if ($systemItemRequest) {
                        $systemItemRequest->update(['request_status' => $this->newStatus]);
                    }
                }
            }

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
            // Check if it's in the format "type_id"
            if (strpos($this->modelId, '_') !== false) {
                list($type, $id) = explode('_', $this->modelId);

                if ($type === 'manual') {
                    // Delete manual item request
                    $manualItemRequest = ManualItemRequest::find($id);
                    if ($manualItemRequest) {
                        $manualItemRequest->delete();
                    }
                } else {
                    // Delete system item request
                    $systemItemRequest = ItemRequest::find($id);
                    if ($systemItemRequest) {
                        $systemItemRequest->delete();
                    }
                }
            } else {
                // Fallback to system item request if no type specified
                $systemItemRequest = ItemRequest::find($this->modelId);
                if ($systemItemRequest) {
                    $systemItemRequest->delete();
                }
            }

            $this->alert('success', 'Item Request deleted successfully');
        } catch (\Exception $e) {
            $this->alert('error', 'Error deleting Item Request: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id, $type = null)
    {
        $this->modelId = $type ? "{$type}_{$id}" : $id;
        $confirmMessage = $type === 'manual'
            ? 'Are you sure you want to delete this Manual Item Request?'
            : 'Are you sure you want to delete this Item Request?';

        $this->confirm($confirmMessage, [
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
        if (in_array($name, ['search', 'selectedCustomerOrder', 'statusFilter', 'typeFilter'])) {
            $this->resetPage();
        }
    }

    public function modelId($id)
    {
        $this->modelId = $id;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = [];

        if (!$value) {
            return;
        }

        // Select all based on the current type filter
        if ($this->typeFilter === 'system') {
            // System item requests only
            $systemQuery = ItemRequest::query();

            if (!empty($this->selectedCustomerOrder)) {
                $systemQuery->where('customer_order_id', $this->selectedCustomerOrder);
            }

            if (!empty($this->statusFilter)) {
                $systemQuery->where('request_status', $this->statusFilter);
            }

            if (!empty($this->search)) {
                $systemQuery->where(function ($q) {
                    $q->where('request_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function ($q) {
                            $q->where('order_number', 'like', "%{$this->search}%");
                        });
                });
            }

            $systemIds = $systemQuery->pluck('id')->map(function ($id) {
                return "system_{$id}";
            })->toArray();

            $this->selected = $systemIds;

        } elseif ($this->typeFilter === 'manual') {
            // Manual item requests only
            $manualQuery = ManualItemRequest::query();

            if (!empty($this->selectedCustomerOrder)) {
                $manualQuery->where('customer_order_id', $this->selectedCustomerOrder);
            }

            if (!empty($this->statusFilter)) {
                $manualQuery->where('request_status', $this->statusFilter);
            }

            if (!empty($this->search)) {
                $manualQuery->where(function ($q) {
                    $q->where('request_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function ($q) {
                            $q->where('order_number', 'like', "%{$this->search}%");
                        });
                });
            }

            $manualIds = $manualQuery->pluck('id')->map(function ($id) {
                return "manual_{$id}";
            })->toArray();

            $this->selected = $manualIds;

        } else {
            // Both types
            // System item requests
            $systemQuery = ItemRequest::query();

            if (!empty($this->selectedCustomerOrder)) {
                $systemQuery->where('customer_order_id', $this->selectedCustomerOrder);
            }

            if (!empty($this->statusFilter)) {
                $systemQuery->where('request_status', $this->statusFilter);
            }

            if (!empty($this->search)) {
                $systemQuery->where(function ($q) {
                    $q->where('request_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function ($q) {
                            $q->where('order_number', 'like', "%{$this->search}%");
                        });
                });
            }

            $systemIds = $systemQuery->pluck('id')->map(function ($id) {
                return "system_{$id}";
            })->toArray();

            // Manual item requests
            $manualQuery = ManualItemRequest::query();

            if (!empty($this->selectedCustomerOrder)) {
                $manualQuery->where('customer_order_id', $this->selectedCustomerOrder);
            }

            if (!empty($this->statusFilter)) {
                $manualQuery->where('request_status', $this->statusFilter);
            }

            if (!empty($this->search)) {
                $manualQuery->where(function ($q) {
                    $q->where('request_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function ($q) {
                            $q->where('order_number', 'like', "%{$this->search}%");
                        });
                });
            }

            $manualIds = $manualQuery->pluck('id')->map(function ($id) {
                return "manual_{$id}";
            })->toArray();

            // Combine both arrays
            $this->selected = array_merge($systemIds, $manualIds);
        }
    }
}
