<?php

namespace App\Http\Livewire\Admin;

use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowPermission extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $modelId;
    protected $permissionRepository;

    public function mount(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }
    public function hydrate()
    {
        $this->permissionRepository = app(PermissionRepositoryInterface::class);
    }

    public function render()
    {
        try {
            $scope = [];
            $orderBy = ['id' => 'asc', 'group' => 'asc'];
            $with  =[
                'roles'
            ];
            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }
            $table = $this->permissionRepository->getData($scope, $with, $orderBy, $this->paginate);
            return view('livewire.admin.show-permission', [
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching permissions: ' . $e->getMessage());
            return view('livewire.admin.show-permission', [
                'table' => collect([]),
            ]);
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $this->modelId = null;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetCreateForm();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function modelId($id)
    {
        $this->modelId = $id;
    }

    public function updatedSelectAll($value)
    {
        try {
            $model = $this->permissionRepository->getData(
                ['filter' => [$this->search]],
                [],
                [],
                null
            );

            if ($value) {
                $this->selected = $model->pluck('id')->toArray();
            } else {
                $this->selected = [];
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching permissions: ' . $e->getMessage());
        }
    }
}
