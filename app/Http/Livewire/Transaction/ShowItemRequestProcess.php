<?php

namespace App\Http\Livewire\Transaction;

use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use Livewire\Component;
use App\Models\ItemRequestProcess as Model;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Transaction\ItemRequestProcess\ItemRequestProcessRepositoryInterface;

class ShowItemRequestProcess extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $model, $modelId;

    protected $repository, $customerOrderRepository;

    public function mount(ItemRequestProcessRepositoryInterface $repository, CustomerOrderRepositoryInterface $customerOrderRepository)
    {
        $this->repository = $repository;
        $this->customerOrderRepository = $customerOrderRepository;
    }
    public function hydrate()
    {
        $this->repository = app(ItemRequestProcessRepositoryInterface::class);
    }

    public function render()
    {
        try {
            $scope = [];
            $orderBy = ['id' => 'asc'];
            $with = [];
            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }
            $table = $this->customerOrderRepository->getData(
                $scope,
                $with,
                $orderBy,
                $this->paginate,
                [],
                'all',
                function ($order) {
                    return $order->totalItemRequest > 0;
                }
            );
            return view('livewire.transaction.show-item-request-process', [
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching permissions: ' . $e->getMessage());
            return view('livewire.transaction.show-item-request-process', [
                'table' => collect([]),
            ]);
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $data = ['modelId',];
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
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }
}


