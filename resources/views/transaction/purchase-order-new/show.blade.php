@extends('layouts.admin.app')
@section('title', 'Purchase Order Details / 采购单详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Purchase Order: {{ $purchaseOrder->po_number }}</h5>
                        <small class="text-muted">
                            Status:
                            <span class="badge bg-{{ getStatusColor($purchaseOrder->process_status) }}">
                                {{ $purchaseOrder->process_status }}
                            </span>
                        </small>
                    </div>
                    <div>
                        <a href="{{ route('purchase-order-new.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                            <i class="fa fa-arrow-left"></i> Back / 返回
                        </a>
                        @if ($purchaseOrder->process_status == 'Draft')
                            <a href="{{ route('purchase-order-new.edit', $purchaseOrder->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit / 编辑
                            </a>
                        @endif
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cog"></i> Actions / 操作
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="printPO()">
                                        <i class="fa fa-print"></i> Print / 打印
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportPDF()">
                                        <i class="fa fa-file-pdf"></i> Export as PDF / 导出为PDF
                                    </a>
                                </li>
                                @if ($purchaseOrder->process_status == 'Draft')
                                    <li>
                                        <form action="{{ route('purchase-order-new.submit', $purchaseOrder->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa fa-paper-plane"></i> Submit for Approval / 提交审批
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if (in_array($purchaseOrder->process_status, ['Approved', 'Partially Received']))
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fa fa-truck"></i> Receive Items / 接收物品
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Purchase Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Purchase Order Details / 采购单详情</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td width="40%"><strong>PO Number / 采购单编号:</strong></td>
                                            <td>{{ $purchaseOrder->po_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Order Date / 订单日期:</strong></td>
                                            <td>{{ $purchaseOrder->order_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expected Delivery / 预期交货日期:</strong></td>
                                            <td>{{ $purchaseOrder->expected_delivery_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status / 状态:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ getStatusColor($purchaseOrder->process_status) }}">
                                                    {{ $purchaseOrder->process_status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created By / 创建者:</strong></td>
                                            <td>{{ $purchaseOrder->createdBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        @if ($purchaseOrder->approvedBy)
                                            <tr>
                                                <td><strong>Approved By / 审批者:</strong></td>
                                                <td>{{ $purchaseOrder->approvedBy->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Approved At / 审批时间:</strong></td>
                                                <td>{{ $purchaseOrder->approved_at ? $purchaseOrder->approved_at->format('Y-m-d H:i') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Supplier & Customer Information / 供应商和客户信息</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td width="40%"><strong>Supplier / 供应商:</strong></td>
                                            <td>{{ $purchaseOrder->supplier->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier Address / 供应商地址:</strong></td>
                                            <td>{{ $purchaseOrder->supplier->address }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier Contact / 供应商联系人:</strong></td>
                                            <td>{{ $purchaseOrder->supplier->contact_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer / 客户:</strong></td>
                                            <td>{{ $purchaseOrder->customerOrder->customer->customer_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer Order / 客户订单:</strong></td>
                                            <td>{{ $purchaseOrder->customerOrder->order_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Project / 项目:</strong></td>
                                            <td>{{ $purchaseOrder->customerOrder->project_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order Items -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Purchase Order Items / 采购单物品</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="20%">Item / 物品</th>
                                            <th width="20%">Specifications / 规格</th>
                                            <th width="10%">Unit / 单位</th>
                                            <th width="10%">Quantity / 数量</th>
                                            <th width="10%">Price / 价格</th>
                                            <th width="10%">Discount / 折扣</th>
                                            <th width="15%">Subtotal / 小计</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchaseOrder->details as $index => $detail)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $detail->item_name }}</td>
                                                <td>{{ $detail->specification }}</td>
                                                <td>{{ $detail->unit }}</td>
                                                <td>{{ number_format($detail->quantity, 3) }}</td>
                                                <td>{{ $purchaseOrder->currency }} {{ number_format($detail->price, 2) }}
                                                </td>
                                                <td>
                                                    @if ($detail->discount_percentage > 0)
                                                        {{ $detail->discount_percentage }}%
                                                        ({{ $purchaseOrder->currency }}
                                                        {{ number_format($detail->discount_amount, 2) }})
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $purchaseOrder->currency }}
                                                    {{ number_format($detail->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <!-- Remarks -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Remarks / 备注</h6>
                                </div>
                                <div class="card-body">
                                    {{ $purchaseOrder->remarks ?? 'No remarks provided. / 没有提供备注。' }}
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Attachments / 附件</h6>
                                </div>
                                <div class="card-body">
                                    @if ($purchaseOrder->attachments->count() > 0)
                                        <ul class="list-group">
                                            @foreach ($purchaseOrder->attachments as $attachment)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>{{ $attachment->file_name }}</span>
                                                    <a href="{{ Storage::url($attachment->file_path) }}"
                                                        class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fa fa-download"></i> Download / 下载
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="mb-0">No attachments available. / 没有可用的附件。</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Approval Status -->
                            {{-- @if ($approvalRequest)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Approval Status / 审批状态</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>
                                                <strong>Current Status:</strong>
                                                <span
                                                    class="badge bg-{{ $approvalRequest->status == 'pending' ? 'warning' : ($approvalRequest->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($approvalRequest->status) }}
                                                </span>
                                            </span>
                                            @if ($approvalRequest->status == 'pending' && $approvalRequest->currentLevel)
                                                <span>
                                                    <strong>Current Level:</strong>
                                                    Level {{ $approvalRequest->currentLevel->hierarchy_order }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Show approval history -->
                                        @if ($approvalRequest->logs->count() > 0)
                                            <div class="mt-3">
                                                <h6>Approval History:</h6>
                                                <ul class="list-group">
                                                    @foreach ($approvalRequest->logs->sortBy('created_at') as $log)
                                                        <li class="list-group-item">
                                                            <div class="d-flex justify-content-between">
                                                                <span>
                                                                    <strong>{{ $log->approver->name }}</strong>
                                                                    <span
                                                                        class="badge bg-{{ $log->action == 'approve' ? 'success' : 'danger' }}">
                                                                        {{ ucfirst($log->action) }}
                                                                    </span>
                                                                </span>
                                                                <small>{{ $log->created_at->format('Y-m-d H:i') }}</small>
                                                            </div>
                                                            @if ($log->remarks)
                                                                <div class="mt-1">
                                                                    <small>{{ $log->remarks }}</small>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <!-- Current user can approve/reject -->
                                        @if (
                                            $approvalRequest->status == 'pending' &&
                                                $approvalRequest->currentLevel &&
                                                ((Auth::user()->id == $approvalRequest->currentLevel->approver_reference_id &&
                                                    $approvalRequest->currentLevel->class_name_approver_type == 'App\\Models\\User') ||
                                                    (Auth::user()->hasRole($approvalRequest->currentLevel->approver_reference_id) &&
                                                        $approvalRequest->currentLevel->class_name_approver_type == 'App\\Models\\Role')))
                                            <div class="mt-3">
                                                <h6>Your Approval:</h6>
                                                <form action="{{ route('send-approval') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="approval_request_id"
                                                        value="{{ $approvalRequest->id }}">
                                                    <div class="mb-3">
                                                        <label for="remarks" class="form-label">Remarks / 备注</label>
                                                        <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                                                    </div>
                                                    <div class="btn-group" role="group">
                                                        <button type="submit" name="approval_status" value="approve"
                                                            class="btn btn-success">
                                                            <i class="fa fa-check"></i> Approve / 批准
                                                        </button>
                                                        <button type="submit" name="approval_status" value="reject"
                                                            class="btn btn-danger">
                                                            <i class="fa fa-times"></i> Reject / 拒绝
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif --}}
                        </div>

                        <!-- Financial Summary -->
                        <div class="col-md-6">
                            <!-- Summary Card -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Financial Summary / 财务摘要</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th>Subtotal / 小计:</th>
                                            <td class="text-end">{{ $purchaseOrder->currency }}
                                                {{ number_format($purchaseOrder->subtotal_price, 2) }}</td>
                                        </tr>

                                        <!-- Before Tax Costs -->
                                        @if ($beforeTaxCosts->count() > 0)
                                            <tr>
                                                <th colspan="2" class="bg-light">Before Tax Costs / 税前费用</th>
                                            </tr>
                                            @foreach ($beforeTaxCosts as $cost)
                                                <tr>
                                                    <td class="ps-4">{{ $cost->description }}
                                                        ({{ ucfirst($cost->type) }}):</td>
                                                    <td class="text-end">{{ $purchaseOrder->currency }}
                                                        {{ number_format($cost->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        <!-- Tax -->
                                        <tr>
                                            <th>
                                                Tax / 税
                                                @if ($purchaseOrder->tax_type == 'percentage')
                                                    ({{ $purchaseOrder->tax_value }}%)
                                                @endif
                                            </th>
                                            <td class="text-end">{{ $purchaseOrder->currency }}
                                                {{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                                        </tr>

                                        <!-- After Tax Costs -->
                                        @if ($afterTaxCosts->count() > 0)
                                            <tr>
                                                <th colspan="2" class="bg-light">After Tax Costs / 税后费用</th>
                                            </tr>
                                            @foreach ($afterTaxCosts as $cost)
                                                <tr>
                                                    <td class="ps-4">{{ $cost->description }}
                                                        ({{ ucfirst($cost->type) }}):</td>
                                                    <td class="text-end">{{ $purchaseOrder->currency }}
                                                        {{ number_format($cost->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        <!-- Total -->
                                        <tr class="table-active">
                                            <th>Total / 总计:</th>
                                            <td class="text-end fw-bold">{{ $purchaseOrder->currency }}
                                                {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Status History Card -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Status History / 状态历史</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date / 日期</th>
                                                    <th>From / 从</th>
                                                    <th>To / 到</th>
                                                    <th>By / 操作者</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($purchaseOrder->statusHistory->sortByDesc('created_at') as $history)
                                                    <tr>
                                                        <td>{{ $history->created_at }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ getStatusColor($history->from_status) }}">
                                                                {{ $history->from_status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ getStatusColor($history->to_status) }}">
                                                                {{ $history->to_status }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $history->changedBy->name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function printPO() {
            window.print();
        }

        function exportPDF() {
            // Implement PDF export functionality
            window.location.href = "{{ route('purchase-order-new.export-pdf', $purchaseOrder->id) }}";
        }


    </script>
@endpush
