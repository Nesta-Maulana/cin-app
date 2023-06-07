<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Carbon\Carbon;
use App\Models\Grade;
use App\Models\School;
use App\Models\Program;
use Livewire\Component;
use App\Models\StudentClass;

class GraphStudent extends Component
{
    public $year, $schools;
    public $dataSchool = [];

    public function mount()
    {
        $this->year = Carbon::now()->format('Y');

        $this->schools = School::with(['classroom' => function ($query) {
            $query->where('year', $this->year);
        }])->select('id', 'name', 'sort')->orderBy('sort', 'asc')->get();

        foreach ($this->schools as $key => $school) {
            $students = StudentClass::whereHas('classroom', function($query) use($school) {
                $query->where('school_id', $school->id)->where('year', $this->year);
            })->whereHas('student', function($query) {
                $query->where('status', 1);
            })->count();
            
            $this->dataSchool['count'][] = $students;
            $this->dataSchool['name'][] = shortName($school->name);  
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard.graph-student');
    }

    public function updatedYear($value)
    {
        $this->dataSchool = [];
        foreach ($this->schools as $key => $school) {
            $students = StudentClass::whereHas('classroom', function($query) use($school, $value) {
                $query->where('school_id', $school->id)->where('year', $value);
            })->whereHas('student', function($query) {
                $query->where('status', 1);
            })->count();

            $this->dataSchool['count'][] = $students;
            $this->dataSchool['name'][] = shortName($school->name);  
        }

        $this->emit('yearUpdated', [
            'dataSchool' => $this->dataSchool,
        ]);
    }
}
