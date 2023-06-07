<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Schedule;
use Livewire\WithPagination;
use App\Models\Schedule as Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowSchedule extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $employee, $model, $modelId;
    public $thisMonth, $period;
    public $is_reg_shift = true;

    public function mount(Model $model)
    {
        $this->model = $model;
        $this->employee = new Employee;
        $this->thisMonth = Carbon::now()->format('Y-m');
    }
    
    public function render()
    {
        $table = $this->employee
            ->where('status', 1)
            ->where('is_reg_shift', $this->is_reg_shift)
            ->filter($this->search)
            ->paginate($this->paginate);
        
        $this->period = monthlyPeriod($this->thisMonth)->toArray();

        $schedules = Schedule::whereIn('employee_id', $table->pluck('id'))
            ->whereIn('date', $this->period)
            ->get()
            ->groupBy(['employee_id', 'date']);
        
        // dd($schedules);
        
        return view('livewire.admin.show-schedule', [
            'table' => $table,
            'schedules' => $schedules,
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
        $model = $this->employee
            ->where('status', 1)
            ->where('is_reg_shift', $this->is_reg_shift)
            ->filter($this->search)
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }
}