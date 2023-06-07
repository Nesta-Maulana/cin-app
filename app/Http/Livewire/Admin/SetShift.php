<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\CustomShift;
use App\Models\RegularShift;

class SetShift extends Component
{
    public $is_reg_shift = 0, $shifts, $reg_shift_id = null;

    public function mount() 
    {
        if ($this->is_reg_shift) {
            $this->shifts = RegularShift::pluck('name', 'id');
        } else {
            $this->shifts = [];
            $this->reg_shift_id = null;
        }
    }

    public function render()
    {
        // dd($this->reg_shift_id);
        return view('livewire.admin.set-shift');
    }

    public function updatedIsRegShift()
    {
        if ($this->is_reg_shift) {
            $this->shifts = RegularShift::pluck('name', 'id');
            $this->reg_shift_id = null;
        } else {
            $this->shifts = [];
            $this->reg_shift_id = null;
        }
    }
}
