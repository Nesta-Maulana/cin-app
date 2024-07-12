<?php

namespace App\Http\Livewire\Admin;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Permission;

class SetUserPermission extends Component
{
    public $permission, $selected = [], $selectAll = false, $role_id = null, $permission_by_role;

    public function mount()
    {
        $this->initializePermissions();
    }

    public function hydrate()
    {
        $this->loadPermissions();
    }

    public function render()
    {
        $this->loadPermissions();
        $this->checkSelectAll();

        return view('livewire.admin.set-user-permission');
    }

    private function initializePermissions()
    {
        $this->permission = Permission::query();
        if (in_array(Auth::user()->getRoleNames()->first(), ['Super Admin', 'Administrator'])) {
            $this->permission = Permission::byRole($this->role_id);
        }
    }

    public function loadPermissions()
    {
        // Ambil permission berdasarkan role
        if ($this->role_id) {
            $permissionsByRole = Permission::byRole($this->role_id)
                ->select('id', 'name', 'group')
                ->orderBy('name', 'asc')
                ->get();
            $this->permission_by_role = $permissionsByRole->groupBy('group')->toArray();

            // Ambil semua permission dan eksklusi yang sudah ada di permission_by_role
            $allPermissions = Permission::select('id', 'name', 'group')
                ->orderBy('name', 'asc')
                ->get();

            $filteredPermissions = $allPermissions->reject(function ($permission) {
                return collect($this->permission_by_role)->flatten(1)->contains('id', $permission->id);
            });

            $this->permission = $filteredPermissions->groupBy('group')->toArray();
        } else {
            $this->permission_by_role = [];
            $this->permission = [];
        }
    }

    public function updatedSelectAll($value)
    {
        // $this->loadPermissions();

        // $permission = $this->permission->get();
        if ($value) {
            $this->selected = collect($this->permission)->flatten(1)->pluck('name')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $this->checkSelectAll();
    }

    private function checkSelectAll()
    {
        // $this->loadPermissions();
        if (collect($this->permission)->flatten(1)->count() == count($this->selected) && count($this->selected) > 0) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'role_id') {
            $role = Role::where('name', $this->role_id)->first();
            $this->role_id = is_null($role) ? null : $role->id;
            $this->selected = [];
            $this->loadPermissions(); // Refresh permissions when role_id is updated
        }
    }
}
