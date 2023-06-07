<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Setting as Model;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public $model, $view, $route;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->view = 'admin.setting';
        $this->route = 'setting';

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
            'app_name' => 'required',
            'app_version' => '',
            'name' => 'required',
            'address' => '',
            'logo' => 'image|max:800|nullable'
        ]);

        $data['logo'] = storeImage($request, 'logo', 'setting');
        $this->model->create($data);
        alertNotif('save');

        return redirect()->route($this->route . '.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->model->findOrFail($id);
        return view($this->view . '.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = $this->model->find($id);
        $data = $request->validate([
            'app_name' => 'required',
            'app_version' => '',
            'name' => 'required',
            'address' => '',
            'logo' => 'image|mimes:png,jpg,jpeg|max:800|nullable'
        ]);

        $data['logo'] = updateImage($request, 'logo', 'setting', $model->logo);
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

        if ($model->logo) {
            Storage::delete($model->logo);
        }

        $model->delete();
        alertNotif('delete');

        return redirect()->route($this->route . '.index');
    }
}
