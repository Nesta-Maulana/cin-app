<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Exception;

class PermissionController extends Controller
{
    protected $permissionRepository;
    protected $view;
    protected $route;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->view = 'admin.permission';
        $this->route = 'permission';

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
        $data = $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        try {
            $this->permissionRepository->create($data);
            alertNotif('save');
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->permissionRepository->find($id);
            return view($this->view . '.edit', compact('data'));
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);

        $data['updated_by'] = auth()->user()->id;

        try {
            $this->permissionRepository->update($id, $data);
            alertNotif('update');
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        try {
            $this->permissionRepository->delete($id);
            alertNotif('delete');
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->back();
        }
    }
}
