<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Repositories\Transaction\BOM\BOMRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BOMController extends Controller
{
    public $view, $route, $routeAlias;
    protected $repository;
    public function __construct(BOMRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $routeAlias = request()->route()?->getName();
        $this->view = 'transaction.bom';
        $this->route = 'bom';

        if (str_starts_with($routeAlias, 'warehouse.')) {
            // Logika untuk semua route dengan prefix warehouse
            $this->routeAlias = 'warehouse';
            $this->route = 'warehouse.requested-bom';
            // $this->view = 'transaction.bom-requested';
        }
        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index",['routeAlias' => $this->routeAlias]);
    }

    public function create()
    {
        $lastBOM = $this->repository->getLastBOM();
        $date = now();
        $lastNumber = $lastBOM ? (int) substr($lastBOM->request_number, -3) : 0;
        $code = "CIN" .
            $date->format('y') .
            $date->format('m') .
            str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $materials = Material::all();
        // $materials = Material::all();
        return view("{$this->view}.create", compact('materials', 'code'));
    }
    public function show($id)
    {
        return view("{$this->view}.show");
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'request_number' => 'required|string|max:255',
            'requested_by' => 'required|string|max:255',
            'request_date' => 'required|date',
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_id' => 'required|array|min:1',
            'material_id.*' => 'required|integer|exists:materials,id', // assuming 'materials' is your database table for materials
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|string|max:255',
            'remarks' => 'nullable|array|min:1',
            'remarks.*' => 'nullable|string'
        ]);
        try {
            DB::transaction(function () use ($request, $data) {
                $header_data = [
                    'request_number' => $data['request_number'],
                    'requested_by' => $data['requested_by'],
                    'request_date' => $data['request_date'],
                    'description' => $data['description'],
                    'project_name' => $data['project_name'],
                    'status' => ($request->action == 'draft') ? 0 : 1,
                ];
                $header = $this->repository->create($header_data);
                $detail = $this->repository->createDetailBOM($header, $data);
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
            $materials = Material::all();
            $data = $this->repository->find($id);
            if ($this->routeAlias == 'warehouse') {
                return view("transaction.bom-requested.edit", compact('data', 'materials'));
            }
            return view("{$this->view}.edit", compact('data', 'materials'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'request_number' => 'required|string|max:255',
            'requested_by' => 'required|string|max:255',
            'request_date' => 'required|date',
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_id' => 'required|array|min:1',
            'material_id.*' => 'required|integer|exists:materials,id', // assuming 'materials' is your database table for materials
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|string|max:255',
            'remarks' => 'nullable|array|min:1',
            'remarks.*' => 'nullable|string'
        ]);
        $data['updated_by'] = auth()->user()->id;
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $header_data = [
                    'request_number' => $data['request_number'],
                    'requested_by' => $data['requested_by'],
                    'request_date' => $data['request_date'],
                    'description' => $data['description'],
                    'project_name' => $data['project_name'],
                    'status' => ($request->action == 'draft') ? 0 : 1,
                ];
                $header = $this->repository->update($id, $header_data);
                $header->bomDetails()->delete();
                $detail = $this->repository->createDetailBOM($header, $data);
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
