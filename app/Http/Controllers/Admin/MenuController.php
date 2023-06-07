<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Menu as Model;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public $model, $view, $route;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->view = 'admin.menu';
        $this->route = 'menu';

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
        $menu = Menu::whereNull('main_menu')->pluck('name', 'id');
        $selectedPermissions = Menu::whereNotNull('permission_id')->pluck('permission_id')->toArray();
        $permission = Permission::where('name', 'like', "%read-%")            
            ->whereNotIn('id', $selectedPermissions)
            ->pluck('name', 'id');
        return view($this->view . '.create', compact('menu', 'permission'));
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
            'name' => 'required|unique:menus,name',
            'url' => '',
            'permission_id' => '',
            'main_menu' => '',
            'icon' => '',
            'sort' => '',
        ]);

        $this->model->create($data);
        alertNotif('save');

        return redirect()->route($this->route . '.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->model->findOrFail($id);
        $menu = Menu::whereNull('main_menu')->pluck('name', 'id');
        $selectedPermissions = Menu::whereNotIn('id', [$id])->whereNotNull('permission_id')->pluck('permission_id')->toArray();
        $permission = Permission::where('name', 'like', "%read-%")            
            ->whereNotIn('id', $selectedPermissions)
            ->pluck('name', 'id');
        return view($this->view . '.edit', compact('data', 'menu', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = $this->model->find($id);
        $data = $request->validate([
            'name' => 'required|unique:menus,name,'.$id,
            'url' => '',
            'permission_id' => '',
            'main_menu' => '',
            'icon' => '',
            'sort' => '',
        ]);

        $data['updated_by'] = auth()->user()->id;
        $model->update($data);
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
