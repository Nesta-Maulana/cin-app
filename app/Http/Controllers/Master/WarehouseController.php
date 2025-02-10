<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Warehouse\WarehouseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class WarehouseController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(WarehouseRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'master.warehouse';
        $this->route = 'warehouse';

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
            'name' => 'required|string|max:255|unique:warehouses,name',
            'location' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'sections' => 'nullable|array',
            'sections.*.name' => 'required|string|max:255',
        ], [
            'name.required' => 'The warehouse name is required. / 仓库名称为必填项。',
            'name.unique' => 'The warehouse name must be unique. / 仓库名称必须是唯一的。',
            'is_active.required' => 'The warehouse status is required. / 仓库状态为必填项。',
            'sections.*.name.required' => 'The section name is required. / 分区名称为必填项。',
            'sections.*.name.max' => 'Section name cannot exceed 255 characters. / 分区名称不能超过255个字符。',
        ]);

        try {
            // Transaksi untuk menyimpan warehouse dan section
            DB::transaction(function () use ($request, $data) {
                // Simpan Warehouse
                $warehouse = $this->repository->create([
                    'name' => $data['name'],
                    'location' => $data['location'] ?? null,
                    'is_active' => $data['is_active'],
                ]);

                // Simpan Warehouse Sections jika ada
                if (!empty($data['sections'])) {
                    $sectionNames = []; // Array untuk validasi nama unik

                    foreach ($data['sections'] as $section) {
                        $sectionName = trim($section['name']);

                        // Cek apakah nama sudah ada dalam warehouse yang sama
                        if (in_array($sectionName, $sectionNames)) {
                            throw new Exception("Section name '{$sectionName}' already exists in this warehouse. / 该仓库中的分区名称 '{$sectionName}' 已存在。");
                        }

                        // Simpan nama untuk validasi berikutnya
                        $sectionNames[] = $sectionName;

                        // Simpan Section ke Warehouse
                        $warehouse->warehouseSections()->create([
                            'name' => $sectionName,
                        ]);
                    }
                }
            });

            // Notifikasi sukses
            alertNotif('save');
        } catch (Exception $e) {
            Log::error('Warehouse Store Error: ' . $e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
        return redirect()->route($this->route . '.index');

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
        // Validasi input
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $id,
            'is_active' => 'required|boolean',
            'location' => 'nullable|string|max:255',
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|exists:warehouse_sections,id',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.is_active' => 'required|boolean',
        ], [
            'name.required' => 'The warehouse name is required. / 仓库名称为必填项。',
            'name.unique' => 'The warehouse name must be unique. / 仓库名称必须是唯一的。',
            'is_active.required' => 'The warehouse status is required. / 仓库状态为必填项。',
            'sections.*.name.required' => 'The section name is required. / 分区名称为必填项。',
            'sections.*.is_active.required' => 'The section status is required. / 分区状态为必填项。',
        ]);

        try {
            DB::transaction(function () use ($request, $id, $data) {
                // ** Update Warehouse **
                $warehouse = $this->repository->update($id, [
                    'name' => $data['name'],
                    'is_active' => $data['is_active'],
                    'location' => $data['location'],
                ]);

                // ** Ambil semua section yang sudah ada dalam warehouse ini **
                $existingSections = $warehouse->warehouseSections()->pluck('name', 'id')->toArray();
                $existingSectionIds = $warehouse->warehouseSections()->pluck('id')->toArray();

                $newSectionNames = []; // Untuk validasi nama yang baru

                if (!empty($data['sections'])) {
                    $updatedSectionIds = []; // Untuk menyimpan section yang diperbarui

                    foreach ($data['sections'] as $section) {
                        $sectionName = trim($section['name']);

                        if (isset($section['id'])) {
                            // ** Update Section yang sudah ada **
                            if (isset($existingSections[$section['id']])) {
                                // Cek apakah ada nama duplikat di warehouse ini
                                if (
                                    in_array($sectionName, $existingSections) &&
                                    $existingSections[$section['id']] !== $sectionName
                                ) {
                                    throw new Exception("Section name '{$sectionName}' already exists in this warehouse. / 该仓库中的分区名称 '{$sectionName}' 已存在。");
                                }

                                // Update data section
                                $warehouse->warehouseSections()->where('id', $section['id'])->update([
                                    'name' => $sectionName,
                                    'is_active' => $section['is_active'],
                                ]);

                                $updatedSectionIds[] = $section['id']; // Simpan ID yang diperbarui
                            }
                        } else {
                            // ** Tambahkan Section Baru **
                            if (in_array($sectionName, $existingSections) || in_array($sectionName, $newSectionNames)) {
                                throw new Exception("Section name '{$sectionName}' already exists in this warehouse. / 该仓库中的分区名称 '{$sectionName}' 已存在。");
                            }

                            $newSectionNames[] = $sectionName;

                            $newSection = $warehouse->warehouseSections()->create([
                                'name' => $sectionName,
                                'is_active' => $section['is_active'],
                            ]);

                            $updatedSectionIds[] = $newSection->id; // Simpan ID yang baru dibuat
                        }
                    }

                    // ** Hapus Section yang tidak dikirim dalam request (soft delete atau hard delete) **
                    $sectionsToDelete = array_diff($existingSectionIds, $updatedSectionIds);
                    $warehouse->warehouseSections()->whereIn('id', $sectionsToDelete)->delete();
                }
            });

            // Notifikasi sukses
            alertNotif('update');
            return redirect()->route($this->route . '.index');
        } catch (Exception $e) {
            // Log error
            Log::error('Warehouse Update Error: ' . $e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->back()->withInput();
        }

        // Redirect ke halaman index warehouse
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

    public function getSectionByWarehouseId(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        try {
            $warehouse = $this->repository->find($warehouseId);
            $sections = $warehouse->warehouseSections()->get(['id', 'name', 'is_active']);
            return response()->json(['success' => true, 'data' => $sections]);
        } catch (Exception $e) {
            Log::error('Error fetching sections by warehouse ID: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }
    public function getWarehouses(Request $request)
    {
        try {
            $warehouses = $this->repository->all();
            return response()->json(['success' => true, 'data' => $warehouses]);
        } catch (Exception $e) {
            Log::error('Error fetching all warehouses: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }
}
