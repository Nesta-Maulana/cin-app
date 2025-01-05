<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Item\ItemRepositoryInterface;
use App\Repositories\Master\ItemCategory\ItemCategoryRepositoryInterface;
use App\Repositories\Master\ItemType\ItemTypeRepositoryInterface;
use App\Repositories\Master\ItemUom\ItemUomRepositoryInterface;
use App\Repositories\Master\UnitOfMeasurement\UnitOfMeasurementRepositoryInterface;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ItemController extends Controller
{
    public $view, $route;
    protected $repository, $itemTypeRepository, $itemUomRepository, $itemCategoryRepository, $unitOfMeasurementRepository;
    public function __construct(
        ItemRepositoryInterface $repository,
        ItemTypeRepositoryInterface $itemTypeRepository,
        ItemUomRepositoryInterface $itemUomRepository,
        ItemCategoryRepositoryInterface $itemCategoryRepository,
        UnitOfMeasurementRepositoryInterface $unitOfMeasurementRepository
    ) {
        $this->repository = $repository;
        $this->itemTypeRepository = $itemTypeRepository;
        $this->itemUomRepository = $itemUomRepository;
        $this->itemCategoryRepository = $itemCategoryRepository;
        $this->unitOfMeasurementRepository = $unitOfMeasurementRepository;
        $this->view = 'master.item';
        $this->route = 'item';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }
    public function show($id)
    {
        $item = $this->repository->find($id);
        /* $item = Item::with([
            'itemType',
            'itemCategory',
            'unitOfMeasurement',
            'itemUoms.unitOfMeasurement',
            'itemUoms.latestPrice' => function ($query) {
                $query->where('is_active', true)->latest('updated_at');
            }
        ])->findOrFail($id); */

        return view($this->view . '.show', compact('item'));
    }

    public function create()
    {
        $itemTypes = $this->itemTypeRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->pluck('name', 'id');
        $uoms = $this->unitOfMeasurementRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->pluck('name', 'id');
        $currenciesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json');
        $currencies = $currenciesResponse->json();

        $lastItemId = $this->repository->getData([], [], [], null, [], 'last')->id ?? 0;

        return view("{$this->view}.create", compact('itemTypes', 'uoms', 'currencies'));
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
                            ['item_category_id', '=', $request->item_category_id],
                        ]
                    )->isNotEmpty();

                    if ($exists) {
                        $fail('The name already exists for the same category and item type. / 名称已存在于相同的类别和项目类型中。');
                    }
                },
            ],
            'item_type_id' => 'required|exists:item_types,id',
            'item_category_id' => 'nullable|exists:item_categories,id',
            'uom_id' => 'required|exists:unit_of_measurements,id',
            'is_active' => 'required|boolean',
            'spesification' => 'nullable|string',
            'description' => 'nullable|string',
            'uoms' => 'required|array',
            'uoms.*.uom_id' => 'nullable|exists:unit_of_measurements,id',
            'uoms.*.conversion' => 'required|numeric|min:1',
            'uoms.*.price' => 'required|numeric|min:0',
            'uoms.*.cost' => 'required|numeric|min:0',
            'uoms.*.currency' => 'required|string|max:3',
        ], [
            // Custom Messages for `name`
            'name.required' => 'The name field is required. / 名称字段是必填的。',
            'name.string' => 'The name must be a valid string. / 名称必须是有效的字符串。',
            'name.max' => 'The name may not be greater than 255 characters. / 名称不能超过255个字符。',

            // Other Field Messages
            'item_type_id.required' => 'The item type field is required. / 项目类型字段是必填的。',
            'item_type_id.exists' => 'The selected item type does not exist. / 选择的项目类型不存在。',
            'item_category_id.exists' => 'The selected category does not exist. / 选择的类别不存在。',
            'uom_id.required' => 'The unit of measurement field is required. / 单位字段是必填的。',
            'uom_id.exists' => 'The selected unit of measurement does not exist. / 选择的单位不存在。',
            'is_active.required' => 'The status field is required. / 状态字段是必填的。',
            'is_active.boolean' => 'The status must be true or false. / 状态必须为 true 或 false。',
            'spesification.string' => 'The spesification must be a valid string. / 规格必须是有效的字符串。',
            'description.string' => 'The description must be a valid string. / 描述必须是有效的字符串。',
            'uoms.required' => 'At least one UOM must be added. / 至少需要添加一个单位。',
            'uoms.*.uom_id.exists' => 'The selected UOM does not exist. / 选择的单位不存在。',
            'uoms.*.conversion.required' => 'The conversion field is required. / 转换字段是必填的。',
            'uoms.*.conversion.numeric' => 'The conversion must be a number. / 转换必须是一个数字。',
            'uoms.*.conversion.min' => 'The conversion must be at least 1. / 转换值必须至少为1。',
            'uoms.*.price.required' => 'The price field is required. / 价格字段是必填的。',
            'uoms.*.price.numeric' => 'The price must be a number. / 价格必须是一个数字。',
            'uoms.*.price.min' => 'The price must be at least 0. / 价格必须至少为0。',
            'uoms.*.cost.required' => 'The cost field is required. / 成本字段是必填的。',
            'uoms.*.cost.numeric' => 'The cost must be a number. / 成本必须是一个数字。',
            'uoms.*.cost.min' => 'The cost must be at least 0. / 成本必须至少为0。',
            'uoms.*.currency.required' => 'The currency field is required. / 货币字段是必填的。',
            'uoms.*.currency.string' => 'The currency must be a valid string. / 货币必须是有效的字符串。',
            'uoms.*.currency.max' => 'The currency code must not exceed 3 characters. / 货币代码不能超过3个字符。',
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                $type = $this->itemTypeRepository->find($data['item_type_id']);
                $category = $this->itemCategoryRepository->find($data['item_category_id']);
                $lastItemId = $this->repository->getData([], [], [], null, [], 'last')->id ?? 0;
                $sku = sprintf(
                    '%s-%03d-%03d',
                    $type->name,
                    $category->id,
                    $lastItemId + 1
                );
                $item = $this->repository->create(
                    [
                        'name' => $data['name'],
                        'item_type_id' => $data['item_type_id'],
                        'item_category_id' => $data['item_category_id'],
                        'unit_of_measurement_id' => $data['uom_id'],
                        'is_active' => $data['is_active'],
                        'description' => $data['description'],
                        'spesification' => $data['spesification'],
                        'sku' => $sku
                    ]
                );
                foreach ($data['uoms'] as $key => $uomData) {
                    // Skip rows with null UOM IDs for optional entries
                    if (empty($uomData['uom_id'])) {
                        if ($key == '0') {

                            $uomData['uom_id'] = $item->unit_of_measurement_id;
                        } else {
                            continue;
                        }
                    }

                    $uom = $item->itemUoms()->create([
                        'unit_of_measurement_id' => $uomData['uom_id'] ?? $item->unit_of_measurement_id,
                        'conversion' => $uomData['conversion'],
                    ]);

                    // Simpan ke tabel `item_price_histories`
                    $uom->itemPriceHistories()->update(['is_active' => false]);
                    $uom->itemPriceHistories()->create([
                        'price' => $uomData['price'],
                        'cost' => $uomData['cost'],
                        'currency' => $uomData['currency'],
                        'is_active' => true,
                    ]);
                }

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
            $item = $this->repository->find($id);
            $itemTypes = $this->itemTypeRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->pluck('name', 'id');
            $uoms = $this->unitOfMeasurementRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->pluck('name', 'id');
            $currenciesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json');
            $currencies = $currenciesResponse->json();
            $categories = $this->itemCategoryRepository->find($item->item_category_id)->pluck('name', 'id');
            return view("{$this->view}.edit", compact('item', 'itemTypes', 'uoms', 'currencies', 'categories'));
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
                            ['item_category_id', '=', $request->item_category_id],
                            ['id', '!=', $id]
                        ]
                    )->isNotEmpty();

                    if ($exists) {
                        $fail('The name already exists for the same category and item type. / 名称已存在于相同的类别和项目类型中。');
                    }
                },
            ],
            'spesification' => 'nullable|string|max:255',
            'item_type_id' => 'required|exists:item_types,id',
            'item_category_id' => 'nullable|exists:item_categories,id',
            'uom_id' => 'required|exists:unit_of_measurements,id',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string',
            'uoms' => 'required|array',
            'uoms.*.id' => 'nullable|exists:item_uoms,id',
            'uoms.*.uom_id' => 'nullable|exists:unit_of_measurements,id',
            'uoms.*.conversion' => 'required|numeric|min:1',
            'uoms.*.price' => 'required|numeric|min:0',
            'uoms.*.cost' => 'required|numeric|min:0',
            'uoms.*.currency' => 'required|string|max:3',
        ]);
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $item = $this->repository->update($id, [
                    'name' => $data['name'],
                    'spesification' => $data['spesification'],
                    'item_type_id' => $data['item_type_id'],
                    'item_category_id' => $data['item_category_id'],
                    'unit_of_measurement_id' => $data['uom_id'],
                    'is_active' => $data['is_active'],
                    'description' => $data['description'],
                ]);
                foreach ($data['uoms'] as $uomData) {
                    if (isset($uomData['id'])) {
                        // Update existing UOM
                        $uom = $this->itemUomRepository->find($uomData['id']);
                        // $uom = ItemUom::findOrFail($uomData['id']);
                        $uom->update([
                            'unit_of_measurement_id' => $uomData['uom_id'] ?? $item->unit_of_measurement_id,
                            'conversion' => $uomData['conversion'],
                        ]);

                        // Deactivate existing price history
                        $uom->itemPriceHistories()->update(['is_active' => false]);

                        // Update price history
                        $uom->itemPriceHistories()->create([
                            'price' => $uomData['price'],
                            'cost' => $uomData['cost'],
                            'currency' => $uomData['currency'],
                            'is_active' => true,
                        ]);
                    } else {
                        // Create new UOM
                        $uom = $item->itemUoms()->create([
                            'unit_of_measurement_id' => $uomData['uom_id'] ?? $item->unit_of_measurement_id,
                            'conversion' => $uomData['conversion'],
                        ]);

                        // Create new price history
                        $uom->itemPriceHistories()->create([
                            'price' => $uomData['price'],
                            'cost' => $uomData['cost'],
                            'currency' => $uomData['currency'],
                            'is_active' => true,
                        ]);
                    }
                }

                // Deactivate UOMs not included in the request
                $existingUomIds = collect($data['uoms'])->pluck('id')->filter()->toArray();
                $item->itemUoms()->whereNotIn('id', $existingUomIds)->update(['is_active' => false]);
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
            $repository->itemUoms()->update(['is_active' => false]);
            foreach ($repository->itemUoms as $uom) {
                $uom->itemPriceHistories()->update(['is_active' => false]);
            }

            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
