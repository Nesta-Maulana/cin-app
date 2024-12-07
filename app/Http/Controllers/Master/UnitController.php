<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Unit\UnitRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(UnitRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'master.unit';
        $this->route = 'unit';

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
            'name' => 'required|string|max:100|unique:units,name', // Wajib, tipe string, maksimal 100 karakter, unik
            'abbreviation' => 'required|string|max:10', // Wajib, tipe string, maksimal 10 karakter
            'description' => 'nullable|string', // Wajib, tipe string
            'name_mandarin' => 'nullable|string|max:100|unique:units,name_mandarin', // Opsional, tipe string, unik
            'abbreviation_mandarin' => 'nullable|string|max:10', // Opsional, tipe string
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
            'name' => "required|string|max:100|unique:units,name,{$id}", // Wajib, string, maksimal 100 karakter, unik (abaikan ID saat ini)
            'abbreviation' => 'required|string|max:10', // Wajib, string, maksimal 10 karakter
            'name_mandarin' => "nullable|string|max:100|unique:units,name_mandarin,{$id}", // Wajib, string, maksimal 100 karakter, unik (abaikan ID saat ini)
            'abbreviation_mandarin' => 'nullable|string|max:10', // Opsional, string, maksimal 10 karakter
            'description' => 'nullable|string', // Wajib, string
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
