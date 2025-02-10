<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Supplier\SupplierRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Storage;

class SupplierController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(SupplierRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'master.supplier';
        $this->route = 'supplier';

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
        $supplier = $this->repository->find($id);
        return view("{$this->view}.show", compact('supplier'));
    }

    public function create()
    {
        return view("{$this->view}.create");
    }

    public function store(Request $request)
    {
        // ✅ Validasi Input
        $data = $request->validate([
            'name' => 'required|string|max:255', // Nama Supplier 必填
            'tax_number' => 'nullable|string|max:50', // Nomor Pajak
            'address' => 'required|string', // Alamat (wajib)
            'phone' => 'nullable|string|max:20', // Telepon
            'email' => 'nullable|email|max:100', // Email
            'website' => 'nullable|string|max:255', // Website
            'business_type' => 'required|string|max:255', // Jenis Usaha (wajib)
            'contact_name' => 'nullable|string|max:255', // Nama Kontak Utama
            'contact_position' => 'nullable|string|max:255', // Jabatan Kontak
            'contact_phone' => 'nullable|string|max:20', // Telepon Kontak
            'bank' => 'nullable|string|max:100', // Bank (wajib)
            'account_number' => 'nullable|string|max:50', // Nomor Rekening
            'account_name' => 'nullable|string|max:255', // Nama Akun Bank
            'currency' => 'nullable|string|max:10', // Mata Uang
            'payment_terms' => 'nullable|string|max:50', // Termin Pembayaran
            // ✅ Validasi untuk File Upload
            'file_names' => 'nullable|array', // Pastikan ada array file names
            'files' => 'nullable|array', // Pastikan ada array file uploads
            'files.*' => 'nullable|mimes:pdf,jpg,jpeg,png,zip|max:2048', // Format & Ukuran Max 2MB
            'descriptions' => 'nullable|array', // Pastikan ada array deskripsi
        ]);
        $data['is_active'] = true;
        try {
            DB::transaction(function () use ($request, $data) {
                // ✅ Simpan Supplier ke Database
                $supplier = $this->repository->create($data);

                // ✅ Cek apakah ada file yang diunggah
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $index => $file) {
                        // Pastikan file_name tersedia, jika tidak gunakan nama asli file
                        $fileName = isset($request->file_names[$index]) ? $request->file_names[$index] : $file->getClientOriginalName();

                        // Simpan file ke dalam storage (folder `suppliers`)
                        $filePath = $file->store('Supplier/' . $supplier->name, 'public');

                        // Simpan metadata file ke tabel `supplier_files`
                        $supplier->files()->create([
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                            'description' => $request->descriptions[$index] ?? null,
                            'uploaded_at' => now(),
                            'is_active' => true,
                        ]);
                    }
                }
            });

            // ✅ Notifikasi sukses
            alertNotif('save');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }

        // ✅ Redirect kembali ke halaman index supplier
        return redirect()->route("{$this->route}.index");
    }



    public function edit($id)
    {
        try {
            $supplier = $this->repository->find($id);
            return view("{$this->view}.edit", compact('supplier'));
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
            'tax_number' => 'nullable|string|max:50',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:255',
            'business_type' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'bank' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'payment_terms' => 'nullable|string|max:50',

            // File validations
            'file_names.*' => 'nullable|string|max:255',
            'files.*' => 'nullable|mimes:pdf,jpg,jpeg,png,zip|max:2048',
            'descriptions.*' => 'nullable|string|max:255',
        ]);

        $data['updated_by'] = auth()->user()->id;

        try {
            DB::transaction(function () use ($request, $id, $data) {
                $supplier = $this->repository->update($id, $data);

                // ✅ Handle file updates
                if ($request->has('ids')) {
                    foreach ($request->ids as $fileName => $fileId) {
                        $fileRecord = $supplier->files()->find($fileId);
                        if ($fileRecord) {
                            // ✅ If a new file is uploaded, replace it
                            if ($request->hasFile("files.$fileName")) {
                                //Storage::disk('public')->delete($fileRecord->file_path);
                                $newFilePath = $request->file("files.$fileName")->store('suppliers', 'public');
                                $fileRecord->update([
                                    'file_name' => $request->file_names[$fileName] ?? $fileRecord->file_name,
                                    'file_path' => $newFilePath,
                                    'description' => $request->descriptions[$fileName] ?? $fileRecord->description,
                                ]);
                            } else {
                                // ✅ Just update file name & description
                                $fileRecord->update([
                                    'file_name' => $request->file_names[$fileName] ?? $fileRecord->file_name,
                                    'description' => $request->descriptions[$fileName] ?? $fileRecord->description,
                                ]);
                            }
                        }
                    }
                    if (!empty($supplier->files)) {
                        $ids = $request->ids;

                        $files = $supplier->files()->whereNotIn('id', $ids)->get();
                        foreach ($files as $file) {
                            $file->update([
                                'is_active' => false,
                            ]);
                        }
                    }
                }

                // ✅ Handle new file uploads
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $index => $file) {
                        if (is_numeric($index)) { // Ensure it's a new file, not a replacement
                            $filePath = $file->store('suppliers', 'public');
                            $supplier->files()->create([
                                'file_name' => $request->file_names[$index] ?? $file->getClientOriginalName(),
                                'file_path' => $filePath,
                                'description' => $request->descriptions[$index] ?? null,
                                'file_type' => $file->getClientMimeType(),
                                'file_size' => $file->getSize(),
                                'uploaded_at' => now(),
                                'is_active' => true,
                            ]);
                        }
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
            $repository = $this->repository->update($id, ['is_active' => false], false);
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
