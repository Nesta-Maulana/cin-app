<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Department\DepartmentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DepartmentController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(DepartmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'master.department';
        $this->route = 'department';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }

    public function create()
    {
        return view("{$this->view}.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'slug' => 'required|string|max:3|unique:departments,slug',
            'is_active' => 'nullable|boolean'
        ], [
            'name.required' => 'The department name is required. / ',
            'name.string' => 'The department name must be a valid string. / ',
            'name.max' => 'The department name cannot exceed 255 characters. /  255 。',
            'slug.required' => 'The slug is required. / ',
            'slug.string' => 'The slug must be a valid string. / ',
            'slug.max' => 'The slug cannot exceed 3 characters. /  3 。',
            'slug.unique' => 'The slug must be unique. /  ',
            'is_active.boolean' => 'The status must be true or false. /  true  false。',
        ]);

        $data['slug'] = strtolower($data['slug']);
        try {
            DB::transaction(function () use ($request, $data) {
                $this->repository->create($data);
            });
            alertNotif('save');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function edit($id)
    {
        try {
            $data = $this->repository->find($id);
            return view("{$this->view}.edit", compact('data'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'slug' => 'required|string|max:3|unique:departments,slug,' . $id,
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The department name is required. / 部门名称是必填项。',
            'name.string' => 'The department name must be a valid string. / 部门名称必须是有效的字符串。',
            'name.max' => 'The department name cannot exceed 255 characters. / 部门名称不能超过 255 个字符。',

            'slug.required' => 'The slug is required. / 网址别名是必填项。',
            'slug.string' => 'The slug must be a valid string. / 网址别名必须是有效的字符串。',
            'slug.max' => 'The slug cannot exceed 3 characters. / 网址别名不能超过 3 个字符。',
            'slug.unique' => 'The slug must be unique. / 网址别名必须是唯一的。',

            'is_active.required' => 'The status is required. / 状态是必填项。',
            'is_active.boolean' => 'The status must be true or false. / 状态必须是 true 或 false。',
        ]);
        $data['slug'] = strtolower($data['slug']);
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $this->repository->update($id, $data);
            });
            alertNotif('update');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $repository->update(['is_active' => false]);
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
