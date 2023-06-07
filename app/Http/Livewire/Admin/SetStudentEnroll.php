<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\Program;
use Livewire\Component;
use App\Models\Classroom;
use Livewire\WithPagination;
use App\Models\Student as Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SetStudentEnroll extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';
    
    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;
    
    public $students = [], $year, $school_id, $grade_id, $program_id, $major_id, $classroom_id, $data;
    public $schools, $grade, $program, $major;

    public function mount(Model $model)
    {
        $this->model = $model;
        $this->year = $this->data->year - 1;
        $this->schools = School::orderBy('sort', 'asc')->select('name', 'id')->get();

        $school = $this->school_id ? School::find($this->school_id) : null;
        $this->grade = $school ? $school->grades()->pluck('grades.name','grades.id') : Grade::orderBy('sort', 'asc')->pluck('name', 'id');
        $this->program = $school ? $school->programs()->pluck('programs.name','programs.id') : Program::orderBy('sort', 'asc')->pluck('name', 'id');
        $this->major = $school ? $school->majors()->pluck('majors.name','majors.id') : Major::orderBy('sort', 'asc')->pluck('name', 'id');
    }

    public function render()
    {
        $table = $this->model
            ->whereNotIn('id', $this->students)
            ->active()
            ->when($this->year, function($query) {
                $query->whereHas('classroom', function($query) {
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

        return view('livewire.admin.set-student-enroll', [
            'table' => $table,
            'classroom' => $classroom
        ]);
    }

    public function updatedSelectAll($value) 
    {
        $model = $this->model
            ->whereNotIn('id', $this->students)
            ->active()
            ->when($this->year, function($query) {
                $query->whereHas('classroom', function($query) {
                    $query->where('year', $this->year);
                });
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
}
