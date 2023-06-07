<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\Program;
use Livewire\Component;
use App\Models\Classroom;
use Livewire\WithPagination;
use App\Models\Student as Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowStudent extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $model, $modelId, $password = '11112222', $status = false;
    public $year,  $school_id, $grade_id, $program_id, $major_id, $classroom_id;
    public $schools, $grade, $program, $major;

    public function mount(Model $model)
    {
        $this->model = $model;
        $this->year = Carbon::now()->format('Y');
        $this->schools = School::orderBy('sort', 'asc')->select('name', 'id')->get();
        $this->grade = Grade::orderBy('sort', 'asc')->pluck('name', 'id');
        $this->program = Program::orderBy('sort', 'asc')->pluck('name', 'id');
        $this->major = Major::orderBy('sort', 'asc')->pluck('name', 'id');
    }

    public function render()
    {
        $table = $this->model
            ->when($this->year, function($query) {
                $query->whereHas('studentClass', function($query) {
                    $query->where('year', $this->year);
                });
            })
            ->when($this->school_id, function($query) {
                $query->where('school_id', $this->school_id);
            })
            ->when($this->grade_id, function($query) {
                $query->where('grade_id', $this->grade_id);
            })
            ->when($this->program_id, function($query) {
                $query->where('program_id', $this->program_id);
            })
            ->when($this->major_id, function($query) {
                $query->where('major_id', $this->major_id);
            })
            ->when($this->classroom_id, function($query) {
                $query->when($this->classroom_id == 'empty', function($query) {
                    $query->whereNull('classroom_id');
                }, function($query) {
                    $query->where('classroom_id', $this->classroom_id);
                });
            })
            ->filter($this->search)
            ->paginate($this->paginate);

        $classroom = Classroom::
            when($this->year, function($query) {
                $query->where('year', $this->year);
            })
            ->when($this->school_id, function($query) {
                $query->where('school_id', $this->school_id);
            })
            ->when($this->grade_id, function($query) {
                $query->where('grade_id', $this->grade_id);
            })
            ->when($this->program_id, function($query) {
                $query->where('program_id', $this->program_id);
            })
            ->when($this->major_id, function($query) {
                $query->where('major_id', $this->major_id);
            })
            ->pluck('name', 'id');

        return view('livewire.admin.show-student', [
            'table' => $table,
            'classroom' => $classroom
        ]);
    }

    // Misc
    public function resetCreateForm()
    {   
        $data = ['modelId', 'status'];
        foreach ($data as $item) {
            $this->$item = "";
        }
        $this->password = '11112222';
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
        $this->status = $model->user->status;
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

    public function updatedSchoolId($value)
    {
        if ($value) {
            $school = $this->schools->find($this->school_id);
            $this->grade = $school->grades()->pluck('grades.name','grades.id');
            $this->grade_id = '';
            $this->program = $school->programs()->pluck('programs.name','programs.id');
            $this->program_id = '';
            $this->major = $school->majors()->pluck('majors.name','majors.id');
            $this->major_id = '';
        } else {
            $model = new Model;
            $this->mount($model);
            $this->grade_id = '';
            $this->program_id = '';
            $this->major_id = '';
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
        $model->user->update($data);

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
        $model->user->update($data);

        $this->closeModal();
        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
    }
    
    public function updateSelectedStatus()
    {
        $model = $this->model->whereKey($this->selected)->pluck('user_id')->toArray();
        $data = $this->validate([
            'status' => ''
        ]);

        $data['status'] = !$data['status'] ? false : true;
        $data['updated_by'] = auth()->user()->id;
        User::whereKey($model)->update($data);

        $this->closeModal();
        $this->selected = [];
        $this->selectAll = false;
        return $this->alert('success', alertMsg('update'), [
            'showCloseButton' => true,
        ]);
    }
}