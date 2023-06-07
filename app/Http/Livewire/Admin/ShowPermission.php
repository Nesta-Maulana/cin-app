<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission as Model;
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

    public $title, $model, $modelId;

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        $table = $this->model
            ->filter($this->search)
            ->orderBy('group', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($this->paginate);

        return view('livewire.admin.show-permission', [
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
}