<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Department\DepartmentRepositoryInterface;
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
    protected $departmentRepository;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, DepartmentRepositoryInterface $departmentRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->departmentRepository = $departmentRepository;
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
                case 'Super Admin':
                    $roles = $this->roleRepository->getData();
                    break;
                default:
                    $roles = $this->roleRepository->getData(['exceptSuperAdmin' => []]);
                    break;
            }
            $roles = $roles->pluck('name', 'id');
            $departments = $this->departmentRepository->getData()->pluck('name', 'id');
            return view('admin.user.create', compact('roles', 'departments'));
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'status' => 'required|boolean',
            'department_id' => 'required|array', // Harus berupa array
            'department_id.*' => 'exists:departments,id' // Pastikan setiap ID department valid
        ], [
            'name.required' => 'The name is required. / 名称是必填项。',
            'email.required' => 'The email is required. / 电子邮件是必填项。',
            'email.unique' => 'This email is already in use. / 该电子邮件已被使用。',
            'username.required' => 'The username is required. / 用户名是必填项。',
            'username.unique' => 'This username is already taken. / 该用户名已被使用。',
            'status.required' => 'The status is required. / 状态是必填项。',
            'department_id.required' => 'Please select at least one department. / 请选择至少一个部门。',
            'department_id.*.exists' => 'Selected department is invalid. / 选定的部门无效。',
        ]);
        $data['password'] = 'password';
        try {
            DB::transaction(function () use ($request, $data) {
                $user = $this->userRepository->create($data);
                $user->departments()->attach($data['department_id']);
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
                case 'Super Admin':
                    $roles = $this->roleRepository->getData();
                    break;
                default:
                    $roles = $this->roleRepository->getData(['exceptSuperAdmin' => []]);
                    break;
            }
            $departments = $this->departmentRepository->getData()->pluck('name', 'id');
            $roles = $roles->pluck('name', 'id');
            $role_id = $data->roles->first() ? $data->roles->first()->id : null;
            $permission = $data->permissions()->pluck('name');
            return view('admin.user.edit', compact('data', 'roles', 'permission', 'role_id', 'departments'));
        } catch (Exception $e) {
            alertNotif('error', $e->getMessage());
            return redirect()->route('user.index');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'department_id' => 'required|array',
            'department_id.*' => 'exists:departments,id',
            'role' => 'required|string',
        ], [
            'name.required' => 'The name is required. / 名称是必填项。',
            'email.required' => 'The email is required. / 电子邮件是必填项。',
            'email.unique' => 'This email is already in use. / 该电子邮件已被使用。',
            'username.required' => 'The username is required. / 用户名是必填项。',
            'username.unique' => 'This username is already taken. / 该用户名已被使用。',
            'department_id.required' => 'Please select at least one department. / 请选择至少一个部门。',
            'department_id.*.exists' => 'Selected department is invalid. / 选定的部门无效。',
            'role.required' => 'Please select a role. / 请选择一个角色。',
        ]);
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $user = $this->userRepository->update($id, $data);
                $user->syncRoles([$request->role]);
                $user->syncPermissions($request->permission);
                $user->departments()->sync($data['department_id']);
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
