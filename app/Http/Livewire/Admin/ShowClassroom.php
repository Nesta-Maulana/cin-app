<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\Program;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Classroom as Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowClassroom extends Component
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
    public $year, $school_id, $grade_id, $program_id, $major_id;
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
            ->filter($this->search)
            ->paginate($this->paginate);

        return view('livewire.admin.show-classroom', [
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
            ->when($this->year, function($query) {
                $query->where('year', $this->year);
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
            $this->program = $school->programs()->pluck('programs.name','programs.id');
            $this->major = $school->majors()->pluck('majors.name','majors.id');
        } else {
            $model = new Model;
            $this->mount($model);
        }
    }
}