<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Master\JobCategory\JobCategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class JobCategoryController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(JobCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'master.job-category';
        $this->route = 'job-category';

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
        // Validasi input / 验证输入
        $data = $request->validate([
            'category_code' => 'required|string|max:50|unique:job_categories,category_code', // Category Code (wajib diisi) / 类别代码（必填）
            'category_name' => 'required|string|max:255', // Category Name (wajib diisi) / 类别名称（必填）
            'description' => 'nullable|string', // Description (opsional) / 描述（可选）
            'is_active' => 'required|boolean', // Status (wajib diisi) / 状态（必填）
        ], [
            // Pesan error / 错误信息
            'category_code.required' => 'Category code is required. / 类别代码是必填项。',
            'category_code.unique' => 'The category code must be unique. / 类别代码必须唯一。',
            'category_name.required' => 'Category name is required. / 类别名称是必填项。',
            'is_active.required' => 'Status is required. / 状态是必填项。',
        ]);

        try {
            // Menyimpan data dalam transaksi database / 在数据库事务中保存数据
            DB::transaction(function () use ($data) {
                $this->repository->create([
                    'category_code' => $data['category_code'],
                    'category_name' => $data['category_name'],
                    'description' => $data['description'] ?? null,
                    'is_active' => $data['is_active'],
                ]);
            });

            // Menampilkan notifikasi berhasil / 显示成功通知
            alertNotif('save');
        } catch (Exception $e) {
            // Log error dan tampilkan notifikasi gagal / 记录错误并显示失败通知
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }

        // Redirect ke halaman index / 重定向到索引页面
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
        // Validate the request data / 验证请求数据
        $data = $request->validate([
            'category_code' => 'required|string|max:50|unique:job_categories,category_code,' . $id, // Category code must be unique / 类别代码必须唯一
            'category_name' => 'required|string|max:255', // Category name is required / 类别名称是必填项
            'description' => 'nullable|string', // Description is optional / 描述是可选项
            'is_active' => 'required|boolean', // Status is required / 状态是必填项
        ], [
            'category_code.required' => 'Category code is required. / 类别代码是必填项。',
            'category_code.unique' => 'Category code must be unique. / 类别代码必须唯一。',
            'category_name.required' => 'Category name is required. / 类别名称是必填项。',
            'is_active.required' => 'Status is required. / 状态是必填项。',
        ]);

        // Add updated_by to the data array / 添加更新者字段到数据数组
        $data['updated_by'] = auth()->user()->id;

        try {
            // Wrap the update operation in a database transaction / 将更新操作包装在数据库事务中
            DB::transaction(function () use ($id, $data) {
                $this->repository->update($id, $data); // Update the job category in the database / 更新数据库中的职位类别
            });

            // Show success notification / 显示成功通知
            alertNotif('update');
        } catch (Exception $e) {
            // Log the error and show error notification / 记录错误并显示错误通知
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }

        // Redirect to the index route / 重定向到索引路由
        return redirect()->route("{$this->route}.index");
    }


    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $repository->update(['is_active' => false]);
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
