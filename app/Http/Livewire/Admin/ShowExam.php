<?php

namespace App\Http\Livewire\Admin;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Exam as Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowExam extends Component
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
    public $subject_id, $grade_id, $teacher_id, $publish, $status = false;

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        $table = $this->model
            ->when($this->subject_id, function($query) {
                $query->where('subject_id', $this->subject_id);
            })
            ->when($this->teacher_id, function($query) {
                $query->where('teacher_id', $this->teacher_id);
            })
            ->when($this->grade_id, function($query) {
                $query->where('grade_id', $this->grade_id);
            })
            ->when($this->publish, function($query) {
                $query->where('status', $this->publish);
            })
            ->filter($this->search)
            ->paginate($this->paginate);

        $subject = Subject::select('id', 'code', 'name')->get();
        $teacher = Teacher::active()->select('id', 'name', 'number_id')->get();
        $grade = Grade::orderBy('sort', 'asc')->pluck('name', 'id');

        return view('livewire.admin.show-exam', [
            'table' => $table,
            'subject' => $subject,
            'teacher' => $teacher,
            'grade' => $grade,
        ]);
    }

    // Misc
    public function resetCreateForm()
    {   
        $data = ['modelId',];
        foreach ($data as $item) {
            $this->$item = "";
        }
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
            ->filter($this->search)
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }

    // update
    public function updateStatus()
    {
        $model = $this->model->find($this->modelId);
        $data = $this->validate([
            'status' => ''
        ]);

        $data['status'] = !$data['status'] ? false : true;
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

        $data['status'] = !$data['status'] ? false : true;
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