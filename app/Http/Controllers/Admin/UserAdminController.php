<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserAdminController extends Controller
{
    public $model;

    public function __construct(User $model)
    {
        $this->model = $model;

        $this->middleware('can:create-user-admin')->only('create','store');
        $this->middleware('can:read-user-admin')->only('index');
        $this->middleware('can:update-user-admin')->only('edit','update');
        $this->middleware('can:delete-user-admin')->only('destroy');
    }

    public function index()
    {
        return view('admin.user-admin.index');
    }

    public function create()
    {
        $role = Role::whereIn('name', ['admin', 'operator'])->pluck('name', 'id');
        return view('admin.user-admin.create', compact('role'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8',
            'status' => 'required',
        ]);

        DB::transaction(function () use($request, $data) {
            $user = $this->model->create($data);
            $user->assignRole($request->role);
        });

        alertNotif('save');
        return redirect()->route('user-admin.index');
    }

    public function edit($id)
    {
        $data = $this->model->findOrFail($id);
        $role = Role::whereIn('name', ['admin', 'operator'])->pluck('name', 'id');
        return view('admin.user-admin.edit', compact('data', 'role'));
    }

    public function update(Request $request, $id)
    {
        $model = $this->model->find($id);
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$model->id,
            'username' => 'required|unique:users,username,'.$model->id,
        ]);

        $data['updated_by'] = auth()->user()->id;
        DB::transaction(function () use($request, $model, $data) {
            $model->update($data);
            $model->syncRoles([$request->role]);
        });

        alertNotif('update');
        return redirect()->route('user-admin.index');
    }

    public function destroy($id)
    {
        $model = $this->model->find($id);
        $model->delete();
        alertNotif('delete');
        return redirect()->route('user-admin.index');
    }
}
