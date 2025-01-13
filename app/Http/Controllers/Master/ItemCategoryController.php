<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\ItemCategory\ItemCategoryRepositoryInterface;
use App\Repositories\Master\ItemType\ItemTypeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ItemCategoryController extends Controller
{
    public $view, $route;
    protected $repository, $itemRepository;
    public function __construct(ItemCategoryRepositoryInterface $repository, ItemTypeRepositoryInterface $itemRepository)
    {
        $this->repository = $repository;
        $this->itemRepository = $itemRepository;
        $this->view = 'master.item-category';
        $this->route = 'item-category';

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
        $itemTypes = $this->itemRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->pluck('name', 'id'); // Hanya tipe item aktif
        return view("{$this->view}.create", compact('itemTypes'));
    }
    public function getParentCategories(Request $request)
    {
        $itemTypeId = $request->input('item_type_id');
        // Ambil parent categories berdasarkan item_type_id dan parent_id null
        $parentCategories = $this->repository->getData(
            ['parent'],
            [],
            [],
            null,
            [
                ['item_type_id', '=', $itemTypeId]
            ],
            'all'
        )->mapWithKeys(function ($category) {

            $name = ($category->parent ? $category->parent->name . ' -> ' : '') . $category->name;
            return [$category->id => $name];
        });
        return response()->json($parentCategories);
    }
    public function getCategoriesByItemType(Request $request)
    {
        $itemTypeId = $request->input(key: 'item_type_id');
        // Ambil parent categories berdasarkan item_type_id dan parent_id null
        $parentCategories = $this->repository->getData(
            ['parent'],
            [],
            [],
            null,
            [
                ['item_type_id', '=', $itemTypeId]
            ],
            'all'
        )->mapWithKeys(function ($category) {
            $name = ($category->parent ? $category->parent->name . ' -> ' : '') . $category->name;
            return [$category->id => $name];
        });
        return response()->json($parentCategories);
    }
    public function getCategories(Request $request)
    {
        // Ambil parent categories berdasarkan item_type_id dan parent_id null
        $parentCategories = $this->repository->getData(
            ['parent'],
            [],
            [],
            null,
            [],
            'all'
        )->mapWithKeys(function ($category) {
            $name = ($category->parent ? $category->parent->name . ' -> ' : '') . $category->name;
            return [$category->id => $name];
        });
        return response()->json($parentCategories);
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
                            ['item_type_id', '=', $request->item_type_id],
                            ['parent_id', '=', $request->parent_id]
                        ]
                    )->isNotEmpty();
                    if ($exists) {
                        $fail('The name already exists with the same item type and parent category. / 名称已存在于相同的项目类型和父类别中。');
                    }
                },
            ],
            'item_type_id' => 'required|exists:item_types,id',
            'parent_id' => 'nullable|exists:item_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The name field is required. / 名称字段是必填的。',
            'name.string' => 'The name must be a valid string. / 名称必须是有效的字符串。',
            'name.max' => 'The name may not be greater than 255 characters. / 名称不能超过 255 个字符。',

            'item_type_id.required' => 'The item type field is required. / 项目类型字段是必填的。',
            'item_type_id.exists' => 'The selected item type does not exist. / 选择的项目类型不存在。',

            'parent_id.exists' => 'The selected parent category does not exist. / 选择的父类别不存在。',

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
            $itemTypes = $this->itemRepository->getData(
                [],
                [],
                [],
                null,
                [
                    ['is_active', '=', 1]
                ],
                'all'
            )->pluck('name', 'id'); // Hanya tipe item aktif
            $parentCategories = $this->repository->getData(
                [],
                [],
                [],
                null,
                [
                    ['parent_id', 'IS', null],
                    ['item_type_id', '=', $data->item_type_id]
                ],
                'all'
            )->pluck('name', 'id');
            return view("{$this->view}.edit", compact('data', 'itemTypes', 'parentCategories'));
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
                            ['item_type_id', '=', $request->item_type_id],
                            ['parent_id', '=', $request->parent_id],
                            ['id', '<>', $id], // Pastikan ID tidak sama dengan data yang sedang diupdate
                        ]
                    )->isNotEmpty();

                    if ($exists) {
                        $fail('The name already exists with the same item type and parent category. / 名称已存在于相同的项目类型和父类别中。');
                    }
                },
            ],
            'item_type_id' => 'required|exists:item_types,id',
            'parent_id' => 'nullable|exists:item_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The name field is required. / 名称字段是必填的。',
            'name.string' => 'The name must be a valid string. / 名称必须是有效的字符串。',
            'name.max' => 'The name may not be greater than 255 characters. / 名称不能超过 255 个字符。',

            'item_type_id.required' => 'The item type field is required. / 项目类型字段是必填的。',
            'item_type_id.exists' => 'The selected item type does not exist. / 选择的项目类型不存在。',

            'parent_id.exists' => 'The selected parent category does not exist. / 选择的父类别不存在。',

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
