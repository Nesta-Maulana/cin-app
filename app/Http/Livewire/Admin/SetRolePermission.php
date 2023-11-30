<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Permission;

class SetRolePermission extends Component
{
    public $permission, $selected = [], $selectAll = false;

    public function mount()
    {
        $this->permission = Permission::select('id', 'name', 'group')->orderBy('name', 'asc')->get()->groupBy('group')->toArray();
    }

    public function render()
    {
        if (Permission::count() == count($this->selected)) {
            $this->selectAll = true;
        }

        return view('livewire.admin.set-role-permission');
    }

    public function updatedSelectAll($value)
    {
        $permission = Permission::get();

        if ($value) {
            $this->selected = $permission->pluck('name');
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        if (Permission::count() == count($this->selected)) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }
}
