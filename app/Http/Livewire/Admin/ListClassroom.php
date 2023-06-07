<?php

namespace App\Http\Livewire\Admin;

use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\Program;
use Livewire\Component;
use App\Models\Classroom;

class ListClassroom extends Component
{
    public $year,  $school_id, $grade_id, $program_id, $major_id, $classroom_id;
    public $schools, $grade, $program, $major, $classroom, $nsm, $no_induk;

    public function mount()
    {
        $this->schools = School::orderBy('sort', 'asc')->select('name', 'id')->get();
        $this->grade = $this->school_id ? $this->schools->find($this->school_id)->grades()->pluck('grades.name','grades.id') : collect();
        $this->classroom = $this->school_id && $this->year ? Classroom::where('year', $this->year)->where('school_id', $this->school_id)->where('grade_id', $this->grade_id)->pluck('name', 'id') : collect();
    }

    public function render()
    {
        return view('livewire.admin.list-classroom');
    }

    public function updatedYear($value)
    {
        $this->classroom = Classroom::where('year', $this->year)->where('school_id', $this->school_id)->where('grade_id', $this->grade_id)->pluck('name', 'id');
        $this->classroom_id = '';
    }

    public function updatedSchoolId($value)
    {
        $school = $this->schools->find($this->school_id);
        $this->grade = $school->grades()->pluck('grades.name','grades.id');
        $this->grade_id = '';
        $this->classroom_id = '';
        $this->nsm = $school->nsm;
    }

    public function updatedGradeId($value)
    {
        $this->classroom = Classroom::where('year', $this->year)->where('school_id', $this->school_id)->where('grade_id', $this->grade_id)->pluck('name', 'id');
        $this->classroom_id = '';
    }
}
