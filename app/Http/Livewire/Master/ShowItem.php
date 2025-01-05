<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;
use App\Models\Item as Model;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Master\Item\ItemRepositoryInterface;

class ShowItem extends Component
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

    protected $repository;

    public function mount(ItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function hydrate()
    {
        $this->repository = app(ItemRepositoryInterface::class);
    }

    public function render()
    {
       try {
            $scope = [];
            $orderBy = ['id' => 'asc'];
            $with  =[];
            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }
            $table = $this->repository->getData($scope, $with, $orderBy, $this->paginate);
            return view('livewire.master.show-item', [
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching permissions: ' . $e->getMessage());
            return view('livewire.master.show-item', [
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


