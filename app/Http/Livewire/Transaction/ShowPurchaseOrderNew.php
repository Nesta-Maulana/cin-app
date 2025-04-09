<?php

namespace App\Http\Livewire\Transaction;

use Livewire\Component;
use App\Models\PurchaseOrderNew;
use App\Models\PrePurchaseOrder;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowPurchaseOrderNew extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    // Using separate pagination names to avoid conflicts
    public $prePOPage = 1;
    public $poPage = 1;

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;
    public $openedPrePODetails = [];
    public $openedPODetails = [];

    public $title, $modelId;

    // Configure pagination for multiple paginators
    protected $queryString = [
        'prePOPage' => ['except' => 1],
        'poPage' => ['except' => 1],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->title = "Purchase Order";
    }

    public function render()
    {
        try {
            // Get Pre-Purchase Orders that are approved but don't have POs
            $prePOQuery = PrePurchaseOrder::with([
                'customerOrder',
                'customerOrder.customer',
                'quotations.supplier',
                'details'
            ])
            ->where('process_status', 'approved')
            ->whereDoesntHave('purchaseOrderNews');
            // Apply search filter to prePO
            if (!empty($this->search)) {
                $prePOQuery->where(function($query) {
                    $query->where('pre_po_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function($q) {
                            $q->where('project_name', 'like', "%{$this->search}%")
                                ->orWhereHas('customer', function($q2) {
                                    $q2->where('customer_name', 'like', "%{$this->search}%");
                                });
                        })
                        ->orWhereHas('quotations', function($q) {
                            $q->whereHas('supplier', function($q2) {
                                $q2->where('name', 'like', "%{$this->search}%");
                            });
                        });
                });
            }

            $prePOTable = $prePOQuery->orderBy('id', 'desc')->paginate($this->paginate, ['*'], 'prePOPage');

            // Get Purchase Orders
            $poQuery = PurchaseOrderNew::with([
                'supplier',
                'customerOrder',
                'customerOrder.customer',
                'details',
                'prePurchaseOrder'
            ]);

            // Apply search filter to PO
            if (!empty($this->search)) {
                $poQuery->where(function($query) {
                    $query->where('po_number', 'like', "%{$this->search}%")
                        ->orWhereHas('supplier', function($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('customerOrder', function($q) {
                            $q->where('project_name', 'like', "%{$this->search}%")
                                ->orWhereHas('customer', function($q2) {
                                    $q2->where('customer_name', 'like', "%{$this->search}%");
                                });
                        });
                });
            }

            $poTable = $poQuery->orderBy('id', 'desc')->paginate($this->paginate, ['*'], 'poPage');

            return view('livewire.transaction.show-purchase-order-new', [
                'prePOTable' => $prePOTable,
                'poTable' => $poTable,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching data: ' . $e->getMessage());

            // Create empty paginators to avoid method not found errors
            $emptyPrePOTable = new LengthAwarePaginator(
                collect([]), // items
                0,          // total
                $this->paginate, // per page
                $this->prePOPage, // current page
                ['path' => request()->url(), 'pageName' => 'prePOPage'] // options
            );

            $emptyPOTable = new LengthAwarePaginator(
                collect([]), // items
                0,          // total
                $this->paginate, // per page
                $this->poPage, // current page
                ['path' => request()->url(), 'pageName' => 'poPage'] // options
            );

            return view('livewire.transaction.show-purchase-order-new', [
                'prePOTable' => $emptyPrePOTable,
                'poTable' => $emptyPOTable,
            ]);
        }
    }

    public function togglePrePODetails($id)
    {
        if (in_array($id, $this->openedPrePODetails)) {
            $this->openedPrePODetails = array_diff($this->openedPrePODetails, [$id]);
        } else {
            $this->openedPrePODetails[] = $id;
        }
    }

    public function togglePODetails($id)
    {
        if (in_array($id, $this->openedPODetails)) {
            $this->openedPODetails = array_diff($this->openedPODetails, [$id]);
        } else {
            $this->openedPODetails[] = $id;
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $this->modelId = "";
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetErrorBag();
        $this->resetCreateForm();
    }

    public function resetPage($pageName = null)
    {
        $this->prePOPage = 1;
        $this->poPage = 1;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function modelId($id)
    {
        $this->modelId = $id;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = PurchaseOrderNew::pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }
}
