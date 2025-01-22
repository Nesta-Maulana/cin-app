<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Repositories\Master\Menu\MenuRepositoryInterface;
use App\Repositories\Master\Permission\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public $menuRepository, $view, $route, $permissionRepository;

    public function __construct(MenuRepositoryInterface $menuRepository, PermissionRepositoryInterface $permissionRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->permissionRepository = $permissionRepository;
        $this->view = 'admin.menu';
        $this->route = 'menu';

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
        try {
            $menu = $this->menuRepository->getData([], [], [], null, [['main_menu', 'IS', null]], 'all')->pluck('name', 'id');
            $selectedPermissions = $this->menuRepository->getData([], [], [], null, [['permission_id', 'IS NOT', null]], 'all')->pluck('permission_id')->toArray();

            $scope = [];
            $with = [];
            $orderBy = ['name' => 'asc'];
            $paginate = null;
            $conditions = [
                ['name', 'LIKE', "%read-%"],
                ['id', 'NOT IN', $selectedPermissions]
            ];

            $permission = $this->permissionRepository->getData($scope, $with, $orderBy, $paginate, $conditions, 'all')->pluck('name', 'id');

            return view($this->view . '.create', compact('menu', 'permission'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error',$e->getMessage());
            return redirect()->route($this->route . '.index');
        }
    }

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

        try {
            $this->menuRepository->create($data);
            alertNotif('save');
            Cache::flush();
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error',$e->getMessage());
            return redirect()->route($this->route . '.index');
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->menuRepository->find($id);
            $menu = $this->menuRepository->getData([], [], [], null, [['main_menu', 'IS', null]], 'all')->pluck('name', 'id');
            $selectedPermissions = $this->menuRepository->getData([], [], [], null, [['id', '!=', $id], ['permission_id', 'IS NOT', null]], 'all')->pluck('permission_id')->toArray();
            $permission = Permission::where('name', 'like', "%read-%")
                ->whereNotIn('id', $selectedPermissions)
                ->pluck('name', 'id');
            return view($this->view . '.edit', compact('data', 'menu', 'permission'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error',$e->getMessage());
            return redirect()->route($this->route . '.index');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|unique:menus,name,' . $id,
            'url' => '',
            'permission_id' => '',
            'main_menu' => '',
            'icon' => '',
            'sort' => '',
        ]);

        $data['updated_by'] = auth()->user()->id;

        try {
            $this->menuRepository->update($id, $data);
            alertNotif('update');
            Cache::flush();
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route($this->route . '.index');
        }
    }

    public function destroy($id)
    {
        try {
            $this->menuRepository->delete($id);
            alertNotif('delete');
            Cache::flush();
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error',$e->getMessage());
            return redirect()->route($this->route . '.index');
        }
    }
}
