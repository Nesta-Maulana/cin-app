<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\Customer\CustomerRepositoryInterface;
use App\Repositories\Services\File\FileRepositoryInterface;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Storage;

class CustomerController extends Controller
{
    public $view, $route;
    protected $repository, $fileRepository;
    public function __construct(CustomerRepositoryInterface $repository, FileRepositoryInterface $fileRepository)
    {
        $this->repository = $repository;
        $this->fileRepository = $fileRepository;
        $this->view = 'master.customer';
        $this->route = 'customer';

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
        $customer = $this->repository->find($id);
        return view($this->view . '.show', compact('customer'));
    }
    public function create()
    {
        return view("{$this->view}.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_code' => 'required|string|max:50|unique:customers,customer_code', // Customer code (必填)
            'customer_name' => 'required|string|max:255', // Customer name (必填)
            'customer_address' => 'nullable|string', // Customer address (可选)
            'customer_tax_number' => 'nullable|string|max:50', // Tax number (可选)
            'customer_contact' => 'nullable|string|max:100', // Contact person (可选)
            'customer_phone_number' => 'nullable|string|max:20', // Phone number (可选)
            'customer_email' => 'nullable|email|max:100', // Email address (可选)
            'is_active' => 'required|boolean', // Status (必填)
            'descriptions.*' => 'nullable|string',
            'file_names.*' => 'nullable|string|max:255',
            'files.*.file_name' => 'required|string|max:255', // File name (必填)
            'files.*.file' => 'required|file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // File upload (必填，格式限制为PDF,图片或ZIP)
        ], [
            'customer_code.required' => 'Customer code is required. / 客户代码是必填项。',
            'customer_name.required' => 'Customer name is required. / 客户名称是必填项。',
            'is_active.required' => 'Status is required. / 状态是必填项。',
            'files.*.file.required' => 'File upload is required. / 文件上传是必填项。',
            'files.*.file.mimes' => 'Only PDF, images, or ZIP files are allowed. / 仅支持上传PDF、图片或ZIP文件。',
        ]);
        try {
            DB::transaction(function () use ($request, $data) {
                $customer = $this->repository->create([
                    'customer_code' => $data['customer_code'],
                    'customer_name' => $data['customer_name'],
                    'customer_address' => $data['customer_address'] ?? null,
                    'customer_tax_number' => $data['customer_tax_number'] ?? null,
                    'customer_contact' => $data['customer_contact'] ?? null,
                    'customer_phone_number' => $data['customer_phone_number'] ?? null,
                    'customer_email' => $data['customer_email'] ?? null,
                    'is_active' => $data['is_active'],
                ]);

                $descriptions = $data['descriptions'] ?? [];
                $fileNames = $data['file_names'] ?? [];

                // Handle file uploads
                if ($request->has('files')) {
                    foreach ($request->file('files') as $key => $fileData) {
                        // Create directory based on customer name
                        $customerFolder = "{$customer->customer_name}/CompanyFiles";
                        if (!Storage::exists($customerFolder)) {
                            Storage::makeDirectory($customerFolder); // Create directory if not exists
                        }

                        // Store file in the specified folder
                        $filePath = $fileData->store($customerFolder);

                        // Save file information in the database
                        $customer->files()->create([
                            'file_name' => $fileNames[$key] ?? $fileData->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $fileData->getClientMimeType(),
                            'file_size' => $fileData->getSize(),
                            'description' => $descriptions[$key] ?? null,
                            'uploaded_at' => now(),
                            'is_active' => true,
                        ]);
                    }
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
            'customer_code' => 'required|string|max:50|unique:customers,customer_code,' . $id,
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'customer_tax_number' => 'nullable|string|max:50',
            'customer_contact' => 'nullable|string|max:100',
            'customer_phone_number' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'is_active' => 'required|boolean',
            'file_names.*' => 'required|string|max:255',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',
            'descriptions.*' => 'nullable|string|max:255',
            'ids.*' => 'nullable|integer|exists:files,id',
        ], [
            'customer_code.required' => 'Customer code is required.',
            'customer_name.required' => 'Customer name is required.',
            'file_names.*.required' => 'File name is required.',
            'files.*.mimes' => 'Only PDF, JPG, PNG, or ZIP files are allowed.',
            'files.*.max' => 'Maximum file size is 5MB.',
        ]);
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $customer = $this->repository->update($id, [
                    'customer_code' => $data['customer_code'],
                    'customer_name' => $data['customer_name'],
                    'customer_address' => $data['customer_address'] ?? null,
                    'customer_tax_number' => $data['customer_tax_number'] ?? null,
                    'customer_contact' => $data['customer_contact'] ?? null,
                    'customer_phone_number' => $data['customer_phone_number'] ?? null,
                    'customer_email' => $data['customer_email'] ?? null,
                    'is_active' => $data['is_active'],
                ]);
                $activeFileIds = [];

                if ($request->has('file_names')) {
                    foreach ($data['file_names'] as $key => $fileName) {
                        $fileId = $data['ids'][$key] ?? null;
                        $description = $data['descriptions'][$key] ?? null;

                        // Check if a new file is uploaded
                        if ($request->hasFile("files.$key")) {
                            $uploadedFile = $request->file("files.$key");

                            // Create directory based on customer name
                            $customerFolder = "{$customer->customer_name}/CompanyFiles";
                            if (!Storage::exists($customerFolder)) {
                                Storage::makeDirectory($customerFolder);
                            }

                            // Store file and get path
                            $filePath = $uploadedFile->store($customerFolder);

                            if ($fileId) {
                                // Update existing file
                                $file = $this->fileRepository->find($fileId);
                                if ($file) {
                                    // Remove old file from storage
                                    Storage::delete($file->file_path);
                                    // Update file details
                                    $file->update([
                                        'file_name' => $fileName,
                                        'file_path' => $filePath,
                                        'file_type' => $uploadedFile->getClientMimeType(),
                                        'file_size' => $uploadedFile->getSize(),
                                        'description' => $description,
                                        'uploaded_at' => now(),
                                        'is_active' => true,
                                    ]);
                                    $activeFileIds[] = $fileId;
                                }
                            } else {
                                // Insert new file
                                $newFile = $customer->files()->create([
                                    'file_name' => $fileName,
                                    'file_path' => $filePath,
                                    'file_type' => $uploadedFile->getClientMimeType(),
                                    'file_size' => $uploadedFile->getSize(),
                                    'description' => $description,
                                    'uploaded_at' => now(),
                                    'is_active' => true,
                                ]);
                                $activeFileIds[] = $newFile->id;
                            }
                        } elseif ($fileId) {
                            // Update file description only if no new file is uploaded
                            $file = $this->fileRepository->find($fileId);
                            if ($file) {
                                $file->update([
                                    'file_name' => $fileName,
                                    'description' => $description,
                                ]);
                                $activeFileIds[] = $fileId;
                            }
                        }
                    }
                    // Update other files to inactive
                    $customer->files()->whereNotIn('id', $activeFileIds)->update(['is_active' => false]);
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
            // Set customer to inactive
            $this->repository->update($id, ['is_active' => false], true);
            // Set related files to inactive
            // $repository->files()->update(['is_active' => false]);

            alertNotif('success','Request customers deleted successfully / 请求客户已成功删除');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
