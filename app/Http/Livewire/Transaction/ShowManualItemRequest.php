<?php

namespace App\Http\Livewire\Transaction;

use Livewire\Component;
use App\Models\ManualItemRequest as Model;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Transaction\ManualItemRequest\ManualItemRequestRepositoryInterface;
use App\Imports\ManualItemRequestImport;
use Maatwebsite\Excel\Facades\Excel;

class ShowManualItemRequest extends Component
{
    use LivewireAlert;
    use WithPagination;
    use WithFileUploads; // Add this trait for file uploads

    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $model, $modelId;

    // Properties for the details modal
    public $showDetailsModal = false;
    public $selectedItemRequest = null;
    public $itemDetails = [];

    // Property for file upload
    public $file;

    protected $repository;

    protected $listeners = [
        'closeModal' => 'closeModal',
        'closeDetailsModal' => 'closeDetailsModal'
    ];

    // Add validation rules
    protected $rules = [
        'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
    ];

    public function mount(ManualItemRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate()
    {
        $this->repository = app(ManualItemRequestRepositoryInterface::class);
    }

    public function render()
    {
        try {
            $scope = [];
            $orderBy = ['id' => 'desc']; // Changed to desc to show newest first
            $with = ['customerOrder', 'details']; // Add relationships needed

            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }

            $table = $this->repository->getData($scope, $with, $orderBy, $this->paginate);

            return view('livewire.transaction.show-manual-item-request', [
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching data: ' . $e->getMessage());
            return view('livewire.transaction.show-manual-item-request', [
                'table' => collect([]),
            ]);
        }
    }

    // Method to import Excel
    public function importExcel()
    {
        $this->validate();

        try {
            Excel::import(new ManualItemRequestImport, $this->file);

            $this->file = null;
            $this->dispatchBrowserEvent('close-modal');
            $this->alert('success', 'Item requests imported successfully / 物品请求导入成功');
        } catch (\Exception $e) {
            $this->alert('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // Method to show item details modal
    public function showItemDetails($itemRequestId)
    {
        try {
            // Get the item request with its relationships
            $this->selectedItemRequest = $this->repository->find($itemRequestId);

            if ($this->selectedItemRequest) {
                $this->itemDetails = $this->selectedItemRequest->details;
                $this->showDetailsModal = true;
                $this->dispatchBrowserEvent('open-details-modal');
            } else {
                $this->alert('error', 'Item request not found / 找不到物品请求');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error loading item details: ' . $e->getMessage());
        }
    }

    // Method to close details modal
    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedItemRequest = null;
        $this->itemDetails = [];
    }

    // Delete method
    public function delete($id)
    {
        try {
            $result = $this->repository->delete($id);
            if ($result) {
                $this->alert('success', 'Item request deleted successfully / 物品请求已成功删除');
            } else {
                $this->alert('error', 'Failed to delete item request / 删除物品请求失败');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error deleting item request: ' . $e->getMessage());
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $data = ['modelId', 'file'];
        foreach ($data as $item) {
            $this->$item = null;
        }
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetErrorBag();
        $this->resetCreateForm();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function modelId($id)
    {
        $this->repositoryId = $id;
    }

    public function updatedSelectAll($value)
    {
        $model = $this->repository
            // ->filter($this->search)
            ->getData();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }
}
