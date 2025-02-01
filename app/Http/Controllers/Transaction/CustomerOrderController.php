<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Repositories\Master\Customer\CustomerRepositoryInterface;
use App\Repositories\Master\JobCategory\JobCategoryRepositoryInterface;
use App\Repositories\Services\File\FileRepositoryInterface;
use App\Repositories\Transaction\CustomerOrder\CustomerOrderRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Storage;

class CustomerOrderController extends Controller
{
    public $view, $route;
    protected $repository, $jobCategoryRepository, $customerRepository, $fileRepository;
    public function __construct(CustomerOrderRepositoryInterface $repository, JobCategoryRepositoryInterface $jobCategoryRepository, CustomerRepositoryInterface $customerRepository, FileRepositoryInterface $fileRepository)
    {
        $this->repository = $repository;
        $this->fileRepository = $fileRepository;
        $this->jobCategoryRepository = $jobCategoryRepository;
        $this->customerRepository = $customerRepository;
        $this->view = 'transaction.customer-order';
        $this->route = 'customer-order';

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
        $order = $this->repository->find($id); // Fetch order data
        return view("{$this->view}.show", compact('order')); // Return detail view
    }

    public function create()
    {
        // Fetch job categories and customers
        $jobCategories = $this->jobCategoryRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->mapWithKeys(function ($item) {
            return [$item->id => "{$item->category_code} - {$item->category_name}"];
        });
        $customers = $this->customerRepository->getData([], [], [], null, [['is_active', '=', 1]], 'all')->pluck('customer_name', 'id');

        // Generate CO Number
        $year = date('y'); // Two-digit year
        $month = date('m'); // Two-digit month
        $prefix = "CO{$year}{$month}";

        // Count existing orders for the current month
        $count = CustomerOrder::whereYear('order_date', date('Y'))
            ->whereMonth('order_date', date('m'))
            ->count();

        // Increment and format the count as 3 digits (e.g., 001, 002)
        $increment = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $orderNumber = "{$prefix}{$increment}";

        return view("{$this->view}.create", compact('jobCategories', 'customers', 'orderNumber'));
    }

    public function store(Request $request)
    {
        // Validate incoming data with custom messages / 使用自定义消息验证输入数据
        $data = $request->validate([
            'order_number' => 'required|string|unique:customer_orders,order_number|max:255', // Order number
            'customer_id' => 'required|exists:customers,id', // Customer ID must exist
            'project_name' => 'nullable|string|max:255', // Optional project name
            'job_category_id' => 'nullable|exists:job_categories,id', // Job category must exist if provided
            'order_date' => 'required|date', // Order date is required
            'dpp' => 'required|numeric|min:0', // DPP must be numeric and at least 0
            'ppn' => 'required|numeric|min:0|max:100', // PPN percentage
            'description' => 'nullable|string', // Optional description
            'order_status' => 'required|in:Active,Draft', // Order status must be Active or Draft
            'files.customer_order_file' => 'required|file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // Required Customer Order File
            'file_names.*' => 'required|string|max:255', // File names
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // Optional additional files
            'descriptions.*' => 'nullable|string', // File descriptions
        ], [
            'order_number.required' => 'The order number is required. / 订单编号为必填项。',
            'order_number.unique' => 'The order number must be unique. / 订单编号必须唯一。',
            'customer_id.required' => 'Please select a customer. / 请选择客户。',
            'order_date.required' => 'The order date is required. / 订单日期为必填项。',
            'dpp.required' => 'The DPP is required. / 基本税额为必填项。',
            'ppn.required' => 'The PPN percentage is required. / 增值税百分比为必填项。',
            'files.customer_order_file.required' => 'The Customer Order File is required. / 客户订单文件为必填项。',
            'file_names.*.required' => 'File names are required. / 文件名称为必填项。',
            'files.*.mimes' => 'Only PDF, JPG, PNG, or ZIP files are allowed. / 仅支持PDF, JPG, PNG 或 ZIP文件。',
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                // Calculate total amount / 计算总金额
                $data['total_amount'] = $data['dpp'] + ($data['ppn'] / 100 * $data['dpp']);
                $data['person_in_charge'] = $request->person_in_charge_id;

                // Create the customer order / 创建客户订单
                $customerOrder = $this->repository->create($data);

                // Handle file uploads / 处理文件上传
                foreach ($request->file('files', []) as $key => $file) {
                    if ($key === 'customer_order_file' || isset($data['file_names'][$key])) {
                        // Set the storage directory / 设置存储目录
                        $folder = "customer_orders/{$data['order_number']}";
                        $filePath = $file->store($folder);

                        // Save file record / 保存文件记录
                        $customerOrder->files()->create([
                            'file_name' => $data['file_names'][$key],
                            'file_path' => $filePath,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                            'description' => $data['descriptions'][$key] ?? null,
                            'uploaded_at' => now(),
                            'is_active' => true,
                        ]);
                    }
                }
            });

            // Notify success / 通知成功
            alertNotif('save', 'Customer order created successfully! / 客户订单创建成功！');
        } catch (Exception $e) {
            // Log error and notify failure / 记录错误并通知失败
            Log::error($e->getMessage());
            alertNotif('error', 'Failed to create customer order: ' . $e->getMessage() . ' / 创建客户订单失败: ' . $e->getMessage());
        }

        // Redirect to the index page / 重定向到索引页面
        return redirect()->route("{$this->route}.index");
    }



    public function edit($id)
    {
        try {
            // Fetch the customer order by ID / 根据ID获取客户订单
            $order = $this->repository->find($id);

            // Fetch active customers for the dropdown / 获取下拉菜单中的活跃客户
            $customers = $this->customerRepository
                ->getData([], [], [], null, [['is_active', '=', 1]], 'all')
                ->pluck('customer_name', 'id');

            // Fetch active job categories for the dropdown / 获取下拉菜单中的活跃作业类别
            $jobCategories = $this->jobCategoryRepository
                ->getData([], [], [], null, [['is_active', '=', 1]], 'all')
                ->mapWithKeys(function ($item) {
                    return [$item->id => "{$item->category_code} - {$item->category_name}"];
                });

            // Fetch files related to the order / 获取与订单相关的文件
            $orderFiles = $order->files;

            // Pass data to the view / 将数据传递到视图
            return view("{$this->view}.edit", compact('order', 'customers', 'jobCategories', 'orderFiles'));
        } catch (ModelNotFoundException $e) {
            // If order is not found, redirect back with error / 如果未找到订单，重定向回去并显示错误
            alertNotif('error', 'Order not found! / 未找到订单！');
            return redirect()->route("{$this->route}.index");
        } catch (Exception $e) {
            // Log any other exceptions and redirect back with error / 记录其他异常并重定向回去并显示错误
            Log::error($e->getMessage());
            alertNotif('error', 'Failed to load order details: ' . $e->getMessage() . ' / 加载订单详细信息失败: ' . $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'project_name' => 'nullable|string|max:255',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'order_date' => 'required|date',
            'dpp' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'order_status' => 'required|in:Active,Draft',
            'file_names.*' => 'required|string|max:255',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',
            'descriptions.*' => 'nullable|string|max:255',
            'ids.*' => 'nullable|integer|exists:files,id',
        ], [
            'customer_id.required' => 'Customer is required.',
            'order_date.required' => 'Order date is required.',
            'dpp.required' => 'DPP is required.',
            'files.*.mimes' => 'Only PDF, JPG, PNG, or ZIP files are allowed.',
            'files.*.max' => 'Maximum file size is 5MB.',
        ]);

        try {
            DB::transaction(function () use ($request, $id, $data) {
                $order = $this->repository->update($id, [
                    'customer_id' => $data['customer_id'],
                    'project_name' => $data['project_name'] ?? null,
                    'job_category_id' => $data['job_category_id'] ?? null,
                    'order_date' => $data['order_date'],
                    'dpp' => $data['dpp'],
                    'total_amount' => $data['dpp'] + ($data['ppn'] / 100 * $data['dpp']),
                    'description' => $data['description'] ?? null,
                    'order_status' => $data['order_status'],
                ]);

                $activeFileIds = [];

                if ($request->has('file_names')) {
                    foreach ($data['file_names'] as $key => $fileName) {
                        $fileId = $data['ids'][$key] ?? null;
                        $description = $data['descriptions'][$key] ?? null;

                        // Check if a new file is uploaded
                        if ($request->hasFile("files.$key")) {
                            $uploadedFile = $request->file("files.$key");

                            // Create directory based on order number
                            $orderFolder = "customer_orders/{$order->order_number}/Files";
                            if (!Storage::exists($orderFolder)) {
                                Storage::makeDirectory($orderFolder);
                            }

                            // Store file and get path
                            $filePath = $uploadedFile->store($orderFolder);

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
                                $newFile = $order->files()->create([
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
                    $order->files()->whereNotIn('id', $activeFileIds)->update(['is_active' => false]);
                }
            });

            alertNotif('update', 'Customer order updated successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', 'Failed to update customer order: ' . $e->getMessage());
        }

        return redirect()->route("{$this->route}.index");
    }


    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $checkApproval = $this->repository->checkApproval('delete', $id);
            if ($checkApproval['status'] == 200) {
                alertNotif('success', $checkApproval['message']);
            } else {
                dd($checkApproval);
                $repository = $this->repository->update($id, ['is_active' => false, 'order_status' => 'Cancelled'], true);
                $repository->files()->update(['is_active' => false]);
                alertNotif('delete');
            }

        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
