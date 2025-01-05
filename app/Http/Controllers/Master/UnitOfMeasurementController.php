<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\UnitOfMeasurement\UnitOfMeasurementRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class UnitOfMeasurementController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(UnitOfMeasurementRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'master.unit-of-measurement';
        $this->route = 'unit-of-measurement';

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
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = $this->repository->getData(
                        [],
                        [],
                        [],
                        null,
                        [
                            ['name', '=', $value],
                            ['type', '=', $request->type]
                        ]
                    )->isNotEmpty();
                    /* $exists = \App\Models\Uom::where('name', $value)
                        ->where('type', $request->type)
                        ->exists(); */

                    if ($exists) {
                        $fail("The $attribute already exists for the same type. / 名称在相同类型中已存在。");
                    }
                },
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = $this->repository->getData(
                        [],
                        [],
                        [],
                        null,
                        [
                            ['code', '=', $value],
                            ['type', '=', $request->type]
                        ]
                    )->isNotEmpty();
                    if ($exists) {
                        $fail("The $attribute already exists for the same type. / 代码在相同类型中已存在。");
                    }
                },
            ],
            'type' => 'required|in:weight,volume,quantity,length,area,time,service',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The name field is required. / 名称字段是必填的。',
            'name.string' => 'The name must be a valid string. / 名称必须是有效的字符串。',
            'name.max' => 'The name may not be greater than 255 characters. / 名称不能超过 255 个字符。',

            'code.required' => 'The code field is required. / 代码字段是必填的。',
            'code.string' => 'The code must be a valid string. / 代码必须是有效的字符串。',
            'code.max' => 'The code may not be greater than 50 characters. / 代码不能超过 50 个字符。',

            'type.required' => 'The type field is required. / 类型字段是必填的。',
            'type.in' => 'The selected type is invalid. / 所选类型无效。',

            'description.string' => 'The description must be a valid string. / 描述必须是有效的字符串。',

            'is_active.required' => 'The status field is required. / 状态字段是必填的。',
            'is_active.boolean' => 'The status must be true or false. / 状态必须为 true 或 false。',
        ]);

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
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = $this->repository->getData(
                        [],
                        [],
                        [],
                        null,
                        [
                            ['name', '=', $value],
                            ['type', '=', $request->type],
                            ['id', '<>', $id]
                        ]
                    )->isNotEmpty();
                    if ($exists) {
                        $fail("The $attribute already exists for the same type. / 名称在相同类型中已存在。");
                    }
                },
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = $this->repository->getData(
                        [],
                        [],
                        [],
                        null,
                        [
                            ['code', '=', $value],
                            ['type', '=', $request->type],
                            ['id', '<>', $id]
                        ]
                    )->isNotEmpty();
                    if ($exists) {
                        $fail("The $attribute already exists for the same type. / 代码在相同类型中已存在。");
                    }
                },
            ],
            'type' => 'required|in:weight,volume,quantity,length,area,time,service',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The name field is required. / 名称字段是必填的。',
            'name.string' => 'The name must be a valid string. / 名称必须是有效的字符串。',
            'name.max' => 'The name may not be greater than 255 characters. / 名称不能超过 255 个字符。',

            'code.required' => 'The code field is required. / 代码字段是必填的。',
            'code.string' => 'The code must be a valid string. / 代码必须是有效的字符串。',
            'code.max' => 'The code may not be greater than 50 characters. / 代码不能超过 50 个字符。',

            'type.required' => 'The type field is required. / 类型字段是必填的。',
            'type.in' => 'The selected type is invalid. / 所选类型无效。',

            'description.string' => 'The description must be a valid string. / 描述必须是有效的字符串。',

            'is_active.required' => 'The status field is required. / 状态字段是必填的。',
            'is_active.boolean' => 'The status must be true or false. / 状态必须为 true 或 false。',
        ]);

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
            $repository->delete();
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
