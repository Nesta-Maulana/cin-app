<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\User\UserRepositoryInterface;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;


class ManageUserController extends Controller
{
    protected $userRepository;
    protected $roleRepository;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->middleware('can:create-user')->only('create', 'store');
        $this->middleware('can:read-user')->only('index');
        $this->middleware('can:update-user')->only('edit', 'update');
        $this->middleware('can:delete-user')->only('destroy');
    }

    public function index()
    {
        return view('admin.user.index');
    }

    public function create()
    {
        try {
            switch (Auth::user()->getRoleNames()->first()) {
                case 'Tech Lead':
                    $roles = $this->roleRepository->getData();
                    break;
                default:
                    $roles = $this->roleRepository->getData(['exceptSuperAdmin' => []]);
                    break;
            }
            $roles = $roles->pluck('name', 'id');
            return view('admin.user.create', compact('roles'));
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'status' => 'required',
        ]);
        $data['password'] = 'password';
        try {
            DB::transaction(function () use ($request, $data) {
                $user = $this->userRepository->create($data);
                $user->assignRole($request->role);
                $user->syncPermissions($request->permission);
            });
            alertNotif('save');
            Cache::flush();
            return redirect()->route('user.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->userRepository->find($id);
            switch (Auth::user()->getRoleNames()->first()) {
                case 'Tech Lead':
                    $roles = $this->roleRepository->getData(['exceptSuperAdmin' => []]);
                    break;
                default:
                    $roles = $this->roleRepository->getData(['exceptSuperAdmin' => []]);
                    break;
            }
            $roles = $roles->pluck('name', 'id');
            $role_id = $data->roles->first() ? $data->roles->first()->id : null;
            $permission = $data->permissions()->pluck('name');
            return view('admin.user.edit', compact('data', 'roles', 'permission', 'role_id'));
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'username' => 'required|unique:users,username,' . $id,
        ]);
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $user = $this->userRepository->update($id, $data);
                $user->syncRoles([$request->role]);
                $user->syncPermissions($request->permission);
            });
            alertNotif('update');
            Cache::flush();
            return redirect()->route('user.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }

    public function destroy($id)
    {
        try {
            $this->userRepository->delete($id);
            alertNotif('delete');
            Cache::flush();
            return redirect()->route('user.index');
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }
}
