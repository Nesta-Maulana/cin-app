<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Repositories\Transaction\PurchaseOrderNew\PurchaseOrderNewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseOrderNewController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(PurchaseOrderNewRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'transaction.purchase-order-new';
        $this->route = 'purchase-order-new';

        $this->middleware("can:create-{$this->route}")->only('create','store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit','update');
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
            //
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
