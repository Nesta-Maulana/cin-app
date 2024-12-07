<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MaterialPrice;
use App\Repositories\Master\Material\MaterialRepositoryInterface;
use App\Repositories\Master\Unit\UnitRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    public $view, $route;
    protected $repository, $unitRepository;
    public function __construct(MaterialRepositoryInterface $repository, UnitRepositoryInterface $unitRepository)
    {
        $this->repository = $repository;
        $this->unitRepository = $unitRepository;
        $this->view = 'master.material';
        $this->route = 'material';

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
        $units = $this->unitRepository->all();
        $lastMaterial = $this->repository->getLastMaterial();
        $date = now();
        $lastNumber = $lastMaterial ? (int) substr($lastMaterial->code, -4) : 0;
        $code = "MTR" .
            str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return view("{$this->view}.create", ['units' => $units,'code'=>$code]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_mandarin' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:materials,code',
            'unit_id' => 'required|integer|exists:units,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,999999999999999999.99',
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                $price = $data['price'];
                unset($data['price']);
                $material = $this->repository->create($data);
                if ($material) {
                    $price = MaterialPrice::create(
                        [
                            'material_id' => $material->id,
                            'price' => $price,
                            'effective_date' => Carbon::now(),
                        ]
                    );
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
            $units = $this->unitRepository->all();
            return view("{$this->view}.edit", compact('data', 'units'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_mandarin' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:materials,code,' . $id, // Excluding the current record from unique check
            'unit_id' => 'nullable|integer|exists:units,id', // Make sure the unit table and foreign key is correct
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,999999999999999999.99',
        ]);

        try {
            DB::transaction(function () use ($request, $id, $data) {
                $price = $data['price'];
                unset($data['price']);
                $material = $this->repository->update($id, $data);
                if ($material) {
                    $latestPrice = $material->materialPrice->price;
                    if ($latestPrice !== $price) {
                        $price = MaterialPrice::create(
                            [
                                'material_id' => $material->id,
                                'price' => $price,
                                'effective_date' => Carbon::now(),
                            ]
                        );
                    }
                }
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
