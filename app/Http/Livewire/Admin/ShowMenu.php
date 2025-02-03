<?php

namespace App\Http\Livewire\Admin;

use App\Repositories\Master\Menu\MenuRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Cache;

class ShowMenu extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 1000;
    public $search = "";
    public $selected = [];
    public $selectAll = false;
    public $title, $modelId;

    protected $menuRepository;

    public function mount(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function hydrate()
    {
        $this->menuRepository = app(MenuRepositoryInterface::class);
        $this->emit('dragdrop');
    }

    public function render()
    {
        $scope = [];
        if (!empty($this->search)) {
            $scope['filter'] = [$this->search];
        }
        $with = [
            'subMenu' => [
                'parent',
                'permission'
            ],
        ];

        $table = $this->menuRepository->getData(
            $scope,
            $with,
            ['main_menu' => 'asc', 'sort' => 'asc'],
            $this->paginate,
        );

        return view('livewire.admin.show-menu', [
            'table' => $table,
        ]);
    }

    public function resetCreateForm()
    {
        $this->modelId = null;
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
        $conditions = [];
        if ($this->search) {
            $conditions[] = ['name', 'like', "%{$this->search}%"];
        }

        $model = $this->menuRepository->getData(
            [],
            [],
            [],
            null,
            $conditions,
            'all'
        );

        if ($value) {
            $this->selected = $model->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updateParentOrder($parent)
    {
        foreach ($parent as $key => $item) {
            $menu = $this->menuRepository->find($item['value']);
            $menu->update([
                'sort' => $item['order'],
            ]);
        }

        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
    }

    public function updateChildGroup($data)
    {
        foreach ($data as $key => $menu) {
            $parent_id = $menu['value'];
            $parent = $this->menuRepository->find($parent_id);
            $parent->update([
                'sort' => $menu['order'],
            ]);

            $child = $menu['items'];
            if ($child) {
                foreach ($child as $key => $childvalue) {
                    $childMenu = $this->menuRepository->find($childvalue['value']);
                    $childMenu->update([
                        'sort' => $childvalue['order'],
                        'main_menu' => $parent_id,
                    ]);
                }
            }
        }

        Cache::flush();
        $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
        $this->syncCache();
    }

    public function syncCache()
    {
        Cache::flush();
        $this->alert('success', 'Cache has been synced successfully.', [
            'showCloseButton' => true,
        ]);
        return redirect()->route('menu.index'); // Redirect to a specific route
    }
}
