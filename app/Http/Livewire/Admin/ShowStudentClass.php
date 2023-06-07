<?php

namespace App\Http\Livewire\Admin;

use App\Models\School;
use App\Models\Student;
use Livewire\Component;
use App\Models\Classroom;
use App\Models\StudentClass;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\StudentClass as Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowStudentClass extends Component
{
    use LivewireAlert, WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $model, $modelId, $classroom, $classroom_id, $otherClass, $school, $school_id;

    public function mount(Model $model, $id)
    {
        $this->model = $model;
        $this->modelId = $id;
        $this->classroom = Classroom::find($id);
        $this->school = School::orderBy('sort', 'asc')->pluck('name', 'id');
    }

    public function render()
    {
        $table = $this->model
            ->where('classroom_id', $this->modelId)
            ->whereHas('student', function ($query) {
                $query->filter($this->search);
            })
            ->paginate($this->paginate);
        
        $this->otherClass = Classroom::whereNotIn('id', [$this->classroom->id])
            ->when($this->school_id, function($query) {
                $query->where('school_id', $this->school_id);
            })
            ->where('year', $this->classroom->year)
            ->select('name', 'year', 'id')
            ->get();
        
        return view('livewire.admin.show-student-class', [
            'table' => $table,
        ]);
    }

    // Misc
    public function resetCreateForm()
    {   
        $data = ['school_id', 'classroom_id'];
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

    public function updatedSelectAll($value) 
    {
        $model = $this->model
            ->where('classroom_id', $this->modelId)
            ->whereHas('student', function ($query) {
                $query->filter($this->search);
            })
            ->get();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }

    // Remove
    public function remove($id)
    {
        $model = $this->model->findOrFail($id);
        $student_id = $model->student_id;
        
        DB::transaction(function () use($model, $student_id) {
            $student = Student::find($student_id);
            $lastClass = $student->studentClass->wherenotIn('id', [$model->id])->last();
            
            if ($lastClass) {
                $student->update([
                    'program_id' => $lastClass->classroom->program_id,
                    'major_id' => $lastClass->classroom->major_id,
                    'grade_id' => $lastClass->classroom->grade_id,
                    'classroom_id' => $lastClass->classroom_id,
                ]);
            } else {
                $student->update([
                    'program_id' => null,
                    'major_id' => null,
                    'grade_id' => null,
                    'classroom_id' => null,
                ]);
            }
            
            $model->delete();
        });

        $this->alert('success', alertMsg('delete'), [
            'showCloseButton' => true,
        ]);

        return $this->render();
    }

    public function removeSelected()
    {
        $model = $this->model->whereKey($this->selected);
        $students = $model->pluck('student_id');

        DB::transaction(function () use($model, $students) {
            foreach ($students as $item) {
                Student::find($item)->update([
                    'program_id' => null,
                    'major_id' => null,
                    'grade_id' => null,
                    'classroom_id' => null,
                ]);
            }

            $model->delete();
        });

        $this->selected = [];
        $this->selectAll = false;
        $this->alert('success', alertMsg('delete'), [
            'showCloseButton' => true,
        ]);

        return $this->render();
    }

    // Migrate
    public function migrate()
    {
        $data = $this->validate([
            'classroom_id' => 'required',
        ]);

        if ($this->selected) {
            DB::transaction(function () use($data) {
                $studentClass = $this->model->whereKey($this->selected);
                $studentClass->update($data);
                
                $class = Classroom::find($data['classroom_id']);
                $data['school_id'] = $class->school_id; 
                $data['grade_id'] = $class->grade_id; 
                $data['program_id'] = $class->program_id; 
                $data['major_id'] = $class->major_id; 

                $studentIds = $studentClass->pluck('student_id');
                Student::whereIn('id', $studentIds)->update($data);
            });

            $this->selected = [];
            $this->selectAll = false;
            $this->closeModal();

            return $this->alert('success', alertMsg('update'), [
                'showCloseButton' => true,
            ]);
        }
    }
}
