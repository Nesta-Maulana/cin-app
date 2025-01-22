<?php

namespace App\Http\Livewire\Admin;

use App\Models\Permission;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowRole extends Component
{
    use LivewireAlert, WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $modelId;
    protected $roleRepository;

    public function mount(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function render()
    {
        try {
            $scope = [
                'exceptSuperAdmin' => []
            ];
            $with = [
                'users',
                'permissions'
            ];
            $table = $this->roleRepository->getData($scope, $with, [], $this->paginate);
            $total_permission = Permission::count('id');
            return view('livewire.admin.show-role', [
                'table' => $table,
                'total_permission' => $total_permission
            ]);
        } catch (\Exception $e) {
            return view('livewire.admin.show-role', [
                'table' => collect([]), // Return empty collection in case of error
            ])->withErrors(['message' => 'Error fetching roles: ' . $e->getMessage()]);
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $data = ['modelId'];
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
        $this->modelId = $id;
    }

    public function updatedSelectAll($value)
    {
        try {
            $conditions = [
                ['id', '!=', 1]
            ];

            $model = $this->roleRepository->getData([], [], [], null, $conditions);

            if ($value) {
                $this->selected = $model->pluck('id')->toArray();
            } else {
                $this->selected = [];
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error updating select all: ' . $e->getMessage(), [
                'showCloseButton' => true,
            ]);
        }
    }
}
