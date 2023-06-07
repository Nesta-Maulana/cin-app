<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Carbon\Carbon;
use App\Models\School;
use App\Models\Program;
use App\Models\Student;
use Livewire\Component;

class StatAcademic extends Component
{
    public $year, $school, $program, $studentCount, $alumniCount;

    public function mount()
    {
        $this->year = Carbon::now()->format('Y');
        $this->school = School::select('id', 'name', 'logo', 'sort')->get();
        $this->program = Program::select('id', 'name')->get();
        
        $student = new Student;
        $this->studentCount = $student->where('status', 1)->count();
        $this->alumniCount = $student->where('status', 2)->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.stat-academic');
    }
}
