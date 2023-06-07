<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowUserAdmin extends Component
{
    use WithPagination, LivewireAlert;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';
    
    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $model, $modelId, $password = 'pin12345', $status = false;

    public function mount(User $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        $table = $this->model
            ->administrator()
            ->filter($this->search)
            ->paginate($this->paginate);
        
        return view('livewire.admin.show-user-admin', [
            'table' => $table,
        ]);
    }

    // Misc
    public function resetCreateForm()
    {   
        $data = ['modelId', 'status'];
        foreach ($data as $item) {
            $this->$item = "";
        }
        $this->password = 'pin12345';
        $this->status = false;
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

        $model = $this->model->findOrFail($id);
        $this->status = $model->status;
    }

    public function updatedSelectAll($value)
    {
        $model = $this->model
            ->administrator()
            ->filter($this->search)
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }

    // update
    public function updatePassword()
    {   
        $model = $this->model->find($this->modelId);
        $data = $this->validate([
            'password' => 'required|min:8',
        ]);

        $data['updated_by'] = auth()->user()->id;
        $model->update($data);

        $this->closeModal();
        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
        
    }

    public function updateStatus()
    {
        $model = $this->model->find($this->modelId);
        $data = $this->validate([
            'status' => ''
        ]);

        $data['updated_by'] = auth()->user()->id;
        $model->update($data);

        $this->closeModal();
        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
    }
    
    public function updateSelectedStatus()
    {
        $model = $this->model->whereKey($this->selected);
        $data = $this->validate([
            'status' => ''
        ]);

        $data['updated_by'] = auth()->user()->id;
        $model->update($data);

        $this->closeModal();
        $this->selected = [];
        $this->selectAll = false;
        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
    }


}
