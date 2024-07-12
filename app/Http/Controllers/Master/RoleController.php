<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Exception;

class RoleController extends Controller
{
    protected $roleRepository;
    protected $view;
    protected $route;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->view = 'admin.role';
        $this->route = 'role';

        $this->middleware('can:create-' . $this->route)->only('create', 'store');
        $this->middleware('can:read-' . $this->route)->only('index');
        $this->middleware('can:update-' . $this->route)->only('edit', 'update');
        $this->middleware('can:delete-' . $this->route)->only('destroy');
    }

    public function index()
    {
        return view($this->view . '.index');
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|unique:roles,name'
            ]);

            $role = $this->roleRepository->create($data);
            $role->syncPermissions($request->permission);
            alertNotif('save');
            Cache::flush();
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->roleRepository->find($id);
            $permission = $data->permissions()->pluck('name');
            return view($this->view . '.edit', compact('data', 'permission'));
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route($this->route . '.index');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|unique:roles,name,' . $id,
            ]);

            $data['updated_by'] = auth()->user()->id;
            $model = $this->roleRepository->update($id, $data);
            $model->syncPermissions($request->permission);
            alertNotif('update');
            Cache::flush();
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            alertNotif('error',$e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        try {
            $this->roleRepository->delete($id);
            alertNotif('delete');
            Cache::flush();

            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            alertNotif('error',$e->getMessage());
            return redirect()->back();
        }
    }
}
