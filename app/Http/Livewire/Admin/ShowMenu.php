<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Menu as Model;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowMenu extends Component
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

    public function hydrate()
    {
        $this->emit('dragdrop');
    }

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        $table = $this->model
            ->filter($this->search)
            ->orderBy('main_menu', 'asc')
            ->orderBy('sort', 'asc')
            ->paginate($this->paginate);

        return view('livewire.admin.show-menu', [
            'table' => $table,
        ]);
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
        $this->modelId = $id;
    }

    public function updatedSelectAll($value) 
    {
        $model = $this->model
            ->filter($this->search)
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }

    public function updateParentOrder($parent)
    {
        foreach ($parent as $key => $item) {
            $menu = $this->model->find($item['value']);
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
            $parent = $this->model::find($parent_id);
            $parent->update([
                'sort' => $menu['order'],
            ]);

            $child = $menu['items'];
            if ($child) {
                foreach ($child as $key => $child) {
                    $childMenu = $this->model->find($child['value']);
                    $childMenu->update([
                        'sort' => $child['order'],
                        'main_menu' => $parent_id,
                    ]);
                }
            }
        }

        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
    }
}