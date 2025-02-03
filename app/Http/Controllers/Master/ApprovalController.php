<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ApprovalLevel;
use App\Models\ApprovalLog;
use App\Models\ApprovalRequest;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Master\Approval\ApprovalRepositoryInterface;
use App\Repositories\Master\ApprovalLevel\ApprovalLevelRepositoryInterface;
use App\Repositories\Master\Department\DepartmentRepositoryInterface;
use App\Repositories\Master\Role\RoleRepositoryInterface;
use App\Repositories\Master\User\UserRepositoryInterface;
use App\Repositories\Transaction\ApprovalRequest\ApprovalRequestRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ApprovalController extends Controller
{
    public $view, $route;
    protected $repository, $approvalLevelRepository, $roleRepository, $userRepository, $approvalRequest, $departmentRepository;
    public function __construct(
        ApprovalRepositoryInterface $repository,
        ApprovalLevelRepositoryInterface $approvalLevelRepository,
        RoleRepositoryInterface $roleRepository,
        UserRepositoryInterface $userRepository,
        ApprovalRequestRepositoryInterface $approvalRequest,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->repository = $repository;
        $this->approvalLevelRepository = $approvalLevelRepository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->approvalRequest = $approvalRequest;
        $this->departmentRepository = $departmentRepository;
        $this->view = 'master.approval';
        $this->route = 'approval';

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
        $departments = $this->departmentRepository->getData()->pluck('name', 'id');
        return view("{$this->view}.create", compact('departments'));
    }
    public function getColumnsByModel(Request $request)
    {
        $modelClass = $request->model;

        try {
            if (!class_exists($modelClass)) {
                return response()->json(['success' => false, 'message' => 'Model not found.'], 404);
            }

            $modelInstance = new $modelClass;
            $table = $modelInstance->getTable();

            $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);

            return response()->json(['success' => true, 'columns' => $columns]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function getReferences(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'App\\Models\\Role') {
            $references = $this->roleRepository->getData([], [], [], null, []);
        } elseif ($type === 'App\\Models\\User') {
            $references = $this->userRepository->getData([], [], [], null, []);
        } else {
            return response()->json(['references' => []]);
        }

        return response()->json(['references' => $references]);
    }

    /* public function store(Request $request)
    {
        dd($request->all());
        // Validasi input
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_name' => 'required|string',
            'event' => 'required|string',
            'column_update' => 'nullable|string',
            'orders' => 'required|array',
            'approver_types' => 'required|array',
            'reference_ids' => 'required|array',
            'on_approves' => 'required|array',
            'on_rejects' => 'required|array',
            'requireds' => 'nullable|array',
        ], [
            'name.required' => 'The approval name is required.',
            'class_name.required' => 'The model class is required.',
            'event.required' => 'The event is required.',
            'orders.required' => 'Order levels are required.',
            'approver_types.required' => 'Approver types are required.',
            'reference_ids.required' => 'Reference IDs are required.',
            'on_approves.required' => 'On approve actions are required.',
            'on_rejects.required' => 'On reject actions are required.',
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                // Simpan data header (Approval)
                $approval = $this->repository->create([
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'class_name' => $data['class_name'],
                    'event' => $data['event'],
                    'column_update' => $data['column_update'] ?? null,
                    'levels' => count($data['orders']),
                    'is_active' => true,
                ]);

                // Simpan data detail (Approval Levels)
                foreach ($data['orders'] as $index => $order) {
                    $level = [
                        'hierarchy_order' => $order * 1,
                        'class_name_approver_type' => $data['approver_types'][$index],
                        'approver_reference_id' => $data['reference_ids'][$index],
                        'updated_value_on_approve' => $data['on_approves'][$index],
                        'updated_value_on_reject' => $data['on_rejects'][$index],
                        'required' => isset($data['requireds'][$index]) ? (bool) $data['requireds'][$index] : true,
                        'description' => null, // Tambahkan jika ada deskripsi level
                    ];
                    $approval->approvalLevels()->create($level);
                }
            });

            alertNotif('save');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }

        return redirect()->route("{$this->route}.index");
    }
 */
    public function store(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_name' => 'required|string',
            'event' => 'required|string',
            'orders' => 'required|array',
            'approver_types' => 'required|array',
            'reference_ids' => 'required|array',
            'on-approve_columns' => 'required|array',
            'on-approve_values' => 'required|array',
            'on-reject_columns' => 'required|array',
            'on-reject_values' => 'required|array',
            'department_id' => 'array',
            'requireds' => 'nullable|array',
        ], [
            'name.required' => 'The approval name is required.',
            'class_name.required' => 'The model class is required.',
            'event.required' => 'The event is required.',
            'orders.required' => 'Order levels are required.',
            'approver_types.required' => 'Approver types are required.',
            'reference_ids.required' => 'Reference IDs are required.',
            'on-approve_columns.required' => 'On approve columns are required.',
            'on-approve_values.required' => 'On approve values are required.',
            'on-reject_columns.required' => 'On reject columns are required.',
            'on-reject_values.required' => 'On reject values are required.',
        ]);

        try {
            DB::transaction(function () use ($request, $data) {
                // Simpan data header (Approval)
                $approval = $this->repository->create([
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'class_name' => $data['class_name'],
                    'event' => $data['event'],
                    'levels' => count($data['orders']),
                    'is_active' => true,
                ]);

                // Simpan data detail (Approval Levels)
                foreach ($data['orders'] as $index => $order) {
                    // Menyusun JSON data untuk on approve & on reject
                    $onApproveData = [
                        $data['on-approve_columns'][$index] => $data['on-approve_values'][$index]
                    ];

                    $onRejectData = [
                        $data['on-reject_columns'][$index] => $data['on-reject_values'][$index]
                    ];
                    if ($data['department_id'][$index] == '-') {
                        $departmentId = null;
                    } else {
                        $departmentId = $data['department_id'][$index];
                    }
                    // Simpan setiap approval level
                    $approval->approvalLevels()->create([
                        'hierarchy_order' => $order * 1,
                        'class_name_approver_type' => $data['approver_types'][$index],
                        'approver_reference_id' => $data['reference_ids'][$index],
                        'updated_values_on_approve' => $onApproveData,
                        'updated_values_on_reject' => $onRejectData,
                        'department_id' => $departmentId,
                        'required' => isset($data['requireds'][$index]) ? (bool) $data['requireds'][$index] : true,
                        'description' => null, // Jika ada deskripsi per level, bisa ditambahkan
                    ]);
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
            // Fetch the approval data with its levels
            $data = $this->repository->find($id);

            // Fetch additional data for dropdowns if needed
            $roles = $this->roleRepository->getData([], [], [], null, [], 'all')->pluck('name', 'id');
            $users = $this->userRepository->getData([], [], [], null, [], 'all')->pluck('name', 'id');

            return view("{$this->view}.edit", compact('data', 'roles', 'users'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }


    public function update(Request $request, $id)
    {
        // Validasi data utama approval
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event' => 'required|string|max:255',
            'class_name' => 'required|string|max:255',
            'column_update' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Approval name is required / 审批名称是必填项。',
            'name.string' => 'Approval name must be a valid string / 审批名称必须是有效的字符串。',
            'name.max' => 'Approval name cannot exceed 255 characters / 审批名称不能超过255个字符。',
            'event.required' => 'Event is required / 事件是必填项。',
            'event.string' => 'Event must be a valid string / 事件必须是有效的字符串。',
            'event.max' => 'Event cannot exceed 255 characters / 事件不能超过255个字符。',
            'class_name.required' => 'Class name is required / 模型类是必填项。',
            'class_name.string' => 'Class name must be a valid string / 模型类必须是有效的字符串。',
            'class_name.max' => 'Class name cannot exceed 255 characters / 模型类不能超过255个字符。',
            'column_update.string' => 'Column to update must be a valid string / 更新的列必须是有效的字符串。',
            'column_update.max' => 'Column to update cannot exceed 255 characters / 更新的列不能超过255个字符。',
            'is_active.boolean' => 'Is Active must be a valid boolean / 状态必须是有效的布尔值。',
        ]);

        // Validasi data untuk approval levels
        $levels = $request->validate([
            'orders.*' => 'required|integer',
            'approver_types.*' => 'required|string|max:255',
            'reference_ids.*' => 'required|string|max:255',
            'on_approves.*' => 'required|string|max:255',
            'on_rejects.*' => 'required|string|max:255',
            'requireds.*' => 'required|boolean',
        ], [
            'orders.*.required' => 'Order is required for all levels / 每个级别的顺序是必填项。',
            'orders.*.integer' => 'Order must be a valid integer / 顺序必须是有效的整数。',
            'approver_types.*.required' => 'Approver type is required / 审批人类型是必填项。',
            'approver_types.*.string' => 'Approver type must be a valid string / 审批人类型必须是有效的字符串。',
            'approver_types.*.max' => 'Approver type cannot exceed 255 characters / 审批人类型不能超过255个字符。',
            'reference_ids.*.required' => 'Reference ID is required / 参考ID是必填项。',
            'reference_ids.*.string' => 'Reference ID must be a valid string / 参考ID必须是有效的字符串。',
            'reference_ids.*.max' => 'Reference ID cannot exceed 255 characters / 参考ID不能超过255个字符。',
            'on_approves.*.required' => 'On approve value is required / 批准时的更新值是必填项。',
            'on_approves.*.string' => 'On approve value must be a valid string / 批准时的更新值必须是有效的字符串。',
            'on_approves.*.max' => 'On approve value cannot exceed 255 characters / 批准时的更新值不能超过255个字符。',
            'on_rejects.*.required' => 'On reject value is required / 拒绝时的更新值是必填项。',
            'on_rejects.*.string' => 'On reject value must be a valid string / 拒绝时的更新值必须是有效的字符串。',
            'on_rejects.*.max' => 'On reject value cannot exceed 255 characters / 拒绝时的更新值不能超过255个字符。',
            'requireds.*.required' => 'Required status is required for all levels / 每个级别的必需状态是必填项。',
            'requireds.*.boolean' => 'Required status must be a valid boolean / 必需状态必须是有效的布尔值。',
        ]);

        try {
            DB::transaction(function () use ($request, $id, $data, $levels) {
                // Update approval data
                $approval = $this->repository->update($id, $data);

                // Hapus semua approval levels sebelumnya
                $approval->approvalLevels()->delete();

                // Tambahkan approval levels baru
                foreach ($levels['orders'] as $index => $order) {
                    $level = [
                        'hierarchy_order' => $order,
                        'class_name_approver_type' => $levels['approver_types'][$index],
                        'approver_reference_id' => $levels['reference_ids'][$index],
                        'updated_value_on_approve' => $levels['on_approves'][$index],
                        'updated_value_on_reject' => $levels['on_rejects'][$index],
                        'required' => (bool) $levels['requireds'][$index],
                        'description' => $levels['descriptions'][$index] ?? null,
                    ];

                    // Buat approval level baru
                    $approval->approvalLevels()->create($level);
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
            $repository->update(['is_active' => false]);
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
    public function sendApproval(Request $request)
    {
        $validated = $request->validate([
            'approval_request_id' => 'required|exists:approval_requests,id',
            'approval_status' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Ambil approval request yang sesuai
                $approvalRequest = ApprovalRequest::with(['approval', 'currentLevel'])->findOrFail($validated['approval_request_id']);

                // Pastikan approval request berada pada status 'pending'
                if ($approvalRequest->status !== 'pending') {
                    throw new Exception('This approval request is no longer pending.');
                }

                // Dapatkan approval level aktif
                $currentLevel = $approvalRequest->currentLevel;
                if (!$currentLevel) {
                    throw new Exception('No active approval level found.');
                }

                // Periksa apakah user memiliki izin untuk menyetujui/menolak pada level ini
                $user = auth()->user();
                $canApprove = $currentLevel->class_name_approver_type === User::class && $currentLevel->approver_reference_id == $user->id;
                $canApproveRole = $currentLevel->class_name_approver_type === Role::class && $user->roles->contains('id', $currentLevel->approver_reference_id);
                if (!$canApprove && !$canApproveRole) {
                    throw new Exception('You do not have permission to approve/reject this request.');
                }

                // Simpan log approval
                ApprovalLog::create([
                    'approval_request_id' => $approvalRequest->id,
                    'approval_level_id' => $currentLevel->id,
                    'approver_id' => $user->id,
                    'action' => $validated['approval_status'],
                    'remarks' => $validated['remarks'],
                ]);

                // Update status approval request
                if ($validated['approval_status'] === 'approve') {
                    // Cek apakah ini level terakhir
                    $nextLevel = ApprovalLevel::where('approval_id', $approvalRequest->approval_id)
                        ->where('hierarchy_order', '>', $currentLevel->hierarchy_order)
                        ->orderBy('hierarchy_order')
                        ->first();

                    if ($nextLevel) {
                        // Pindah ke level berikutnya
                        $approvalRequest->update([
                            'current_level_id' => $nextLevel->id,
                            'remarks' => "Pending approval for {$nextLevel->approver->name}",
                        ]);
                    } else {
                        // Jika level terakhir, tandai approval request sebagai selesai
                        $approvalRequest->update([
                            'status' => 'approved',
                        ]);

                    }
                    if (count($currentLevel->updated_values_on_approve) > 0) {
                        $referenceModel = app($approvalRequest->class_name);
                        $referenceInstance = $referenceModel::find($approvalRequest->reference_id);

                        if ($referenceInstance) {
                            // Jika ada multiple columns yang diperbarui (JSONB support)
                            $updateData = [];
                            foreach ($currentLevel->updated_values_on_approve as $column => $value) {
                                $updateData[$column] = $value;
                            }

                            // Update model hanya jika ada data yang valid
                            if (!empty($updateData)) {
                                $referenceInstance->update($updateData);
                            }
                        } else {
                            Log::error("Reference model not found: {$approvalRequest->class_name} ID {$approvalRequest->reference_id}");
                        }
                    }

                } elseif ($validated['approval_status'] === 'reject') {
                    // Jika ditolak, tandai approval request sebagai 'rejected'
                    $approvalRequest->update([
                        'status' => 'rejected',
                    ]);

                    if (count($currentLevel->updated_value_on_reject) > 0) {
                        $referenceModel = app($approvalRequest->class_name);
                        $referenceInstance = $referenceModel::find($approvalRequest->reference_id);

                        if ($referenceInstance) {
                            // Jika ada multiple columns yang diperbarui (JSONB support)
                            $updateData = [];
                            foreach ($currentLevel->updated_value_on_reject as $column => $value) {
                                $updateData[$column] = $value;
                            }

                            // Update model hanya jika ada data yang valid
                            if (!empty($updateData)) {
                                $referenceInstance->update($updateData);
                            }
                        } else {
                            Log::error("Reference model not found: {$approvalRequest->class_name} ID {$approvalRequest->reference_id}");
                        }
                    }
                }
            });

            return redirect()->back()->with('success', 'Approval process completed successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
