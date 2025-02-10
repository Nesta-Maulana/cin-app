<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Item\ItemRepositoryInterface;
use App\Repositories\Master\Supplier\SupplierRepositoryInterface;
use App\Repositories\Master\Warehouse\WarehouseRepositoryInterface;
use App\Repositories\Transaction\StockEntry\StockEntryRepositoryInterface;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class StockEntryController extends Controller
{
    public $view, $route;
    protected $repository, $warehouseRepository, $itemRepository, $supplierRepository;
    public function __construct(StockEntryRepositoryInterface $repository, ItemRepositoryInterface $itemRepository, WarehouseRepositoryInterface $warehouseRepository, SupplierRepositoryInterface $supplierRepository)
    {
        $this->repository = $repository;
        $this->warehouseRepository = $warehouseRepository;
        $this->itemRepository = $itemRepository;
        $this->supplierRepository = $supplierRepository;
        $this->view = 'transaction.stock-entry';
        $this->route = 'stock-entry';

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
        $warehouses = $this->warehouseRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');

        $items = $this->itemRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');

        $suppliers = $this->supplierRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all');
        return view("{$this->view}.create", compact('warehouses', 'items', 'suppliers'));
    }


    public function store(Request $request)
    {
        // Validasi data global dan tiap baris item
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.section_id' => 'required|exists:warehouse_sections,id',
            'items.*.type' => 'required|in:in,out',
            'items.*.stock_source' => 'required|in:purchase,transfer,adjustment,sale',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.item_uom_id' => 'required|exists:item_uoms,id',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.reference_number' => 'nullable|string',
            // Untuk warehouse destination, validasi menggunakan field warehouse_destination_id
            'items.*.warehouse_destination_id' => 'nullable|exists:warehouses,id',
        ]);

        try {
            DB::transaction(function () use ($data) {
                foreach ($data['items'] as $index => $item) {
                    if ($item['stock_source'] === 'purchase') {
                        if (empty($item['supplier_id'])) {
                            throw new Exception("Supplier is required for purchase stock source.");
                        }
                        if (empty($item['reference_number'])) {
                            throw new Exception("Reference number is required for purchase stock source.");
                        }
                        // Warehouse destination tidak digunakan untuk purchase
                        $item['warehouse_destination_id'] = null;
                    } elseif ($item['stock_source'] === 'transfer') {
                        // Untuk transfer: warehouse_destination_id wajib diisi
                        if (empty($item['warehouse_destination_id'])) {
                            throw new Exception("Warehouse destination is required for transfer stock source.");
                        }
                        // Supplier dan reference number tidak digunakan untuk transfer
                        $item['supplier_id'] = null;
                        $item['reference_number'] = null;
                    } elseif (in_array($item['stock_source'], ['adjustment', 'sale'])) {
                        // Untuk adjustment dan sale: supplier, warehouse destination, dan reference number diabaikan
                        $item['supplier_id'] = null;
                        $item['warehouse_destination_id'] = null;
                        $item['reference_number'] = null;
                    }

                    // Simpan tiap baris data sebagai record tersendiri pada tabel stock_entries
                    $repository     = $this->repository->create([
                        'warehouse_id' => $data['warehouse_id'],
                        'section_id' => $item['section_id'],
                        'item_id' => $item['item_id'],
                        'supplier_id' => $item['supplier_id'] ?? null,
                        'warehouse_destination_id' => $item['warehouse_destination_id'] ?? null,
                        'type' => $item['type'],
                        'stock_source' => $item['stock_source'],
                        'quantity' => $item['quantity'],
                        'item_uom_id' => $item['item_uom_id'],
                        'reference_number' => $item['reference_number'] ?? null,
                        'date' => $data['date'],
                        'notes' => $data['notes'] ?? null,
                        'created_by' => auth()->id(),
                    ]);
                    /* \App\Models\StockEntry::create([
                        'warehouse_id' => $data['warehouse_id'],
                        'section_id' => $item['section_id'],
                        'item_id' => $item['item_id'],
                        'supplier_id' => $item['supplier_id'] ?? null,
                        'warehouse_destination_id' => $item['warehouse_destination_id'] ?? null,
                        'type' => $item['type'],
                        'stock_source' => $item['stock_source'],
                        'quantity' => $item['quantity'],
                        'item_uom_id' => $item['item_uom_id'],
                        'reference_number' => $item['reference_number'] ?? null,
                        'date' => $data['date'],
                        'notes' => $data['notes'] ?? null,
                        'created_by' => auth()->id(),
                    ]); */
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
            //
        ]);
        $data['updated_by'] = auth()->user()->id;
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
