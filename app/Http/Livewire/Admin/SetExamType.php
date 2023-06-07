<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ExamType;

class SetExamType extends Component
{
    public $examType, $selected = [], $selectAll = false, $score = [];

    public function mount()
    {
        $this->examType = ExamType::select('id', 'name', 'code')->orderBy('id', 'asc')->get();
    }

    public function render()
    {
        if (count($this->examType) == count($this->selected)) {
            $this->selectAll = true;
        }

        $sumScore = isset($this->score) ? array_sum($this->score) : 0;

        return view('livewire.admin.set-exam-type', compact('sumScore'));
    }

    public function updatedSelectAll($value) 
    {
        $model = ExamType::get();

        if ($value) {
            $this->selected = $model->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected($value, $key)
    {
        if (count($this->examType) == count($this->selected)) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false; 
        }
    }
}
