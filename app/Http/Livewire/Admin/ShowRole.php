<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Role as Model;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowRole extends Component
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
            ->whereNotIn('id', [1])
            ->filter($this->search)
            ->paginate($this->paginate);

        return view('livewire.admin.show-role', [
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
            ->whereNotIn('id', [1])
            ->filter($this->search)
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }
}