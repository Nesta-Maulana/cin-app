<?php

namespace App\Http\Livewire\Admin\Dashboard;

use App\Models\Student;
use Livewire\Component;

class StatGender extends Component
{
    public $student, $dataGender = [];

    public function mount()
    {
        $this->student = Student::where('status', 1)->select('gender', 'id')->get();

        $men = $this->student->where('gender', 'L')->count();
        $women = $this->student->where('gender', 'P')->count();

        $this->dataGender = [ $men, $women ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard.stat-gender');
    }
}
