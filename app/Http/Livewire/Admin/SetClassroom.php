<?php

namespace App\Http\Livewire\Admin;

use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\Program;
use Livewire\Component;

class SetClassroom extends Component
{
    public $is_generate, $school_id, $grade = [], $program = [], $major = [];

    public function render()
    {
        $schools = School::orderBy('sort', 'asc')->get();

        if ($this->school_id) {
            $school = $schools->find($this->school_id);
            $this->grade = $school->grades()->orderBy('grades.sort', 'asc')->pluck('grades.name', 'grades.id');
            $this->program = $school->programs()->orderBy('programs.sort', 'asc')->pluck('programs.name', 'programs.id');
            $this->major = $school->majors()->orderBy('majors.sort', 'asc')->pluck('majors.name', 'majors.id');
        }
        
        return view('livewire.admin.set-classroom', compact('schools'));
    }
}
