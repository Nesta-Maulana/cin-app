<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role as Model;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public $model, $view, $route;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->view = 'admin.role';
        $this->route = 'role';

        $this->middleware('can:create-' . $this->route)->only('create','store');
        $this->middleware('can:read-' . $this->route)->only('index');
        $this->middleware('can:update-' . $this->route)->only('edit','update');
        $this->middleware('can:delete-' . $this->route)->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->view . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        $this->model->create($data);
        alertNotif('save');

        return redirect()->route($this->route . '.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->model->findOrFail($id);
        $permission = $data->permissions()->pluck('name');
        return view($this->view . '.edit', compact('data', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = $this->model->find($id);
        $data = $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
        ]);

        $data['updated_by'] = auth()->user()->id;
        $model->update($data);
        $model->syncPermissions($request->permission);
        alertNotif('update');

        return redirect()->route($this->route . '.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);
        $model->delete();
        alertNotif('delete');

        return redirect()->route($this->route . '.index');
    }
}
