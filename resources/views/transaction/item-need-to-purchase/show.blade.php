@extends('layouts.admin.app')
@section('title', 'View Item Need to Purchase Detail / 查看采购需求详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Item Need to Purchase Details / 采购需求详情</h5>
                        <small class="text-muted">View all purchase details / 查看所有采购详情</small>
                    </div>
                    <a href="{{ route('item-need-to-purchase.index') }}" class="btn btn-secondary btn-sm" title="Back / 返回">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Customer Order Details -->
                    <h6 class="text-primary mb-3">Customer Order Details / 客户订单详情</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Customer Order Number / 客户订单编号</th>
                                    <td>{{ $customerOrder->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name / 客户名称</th>
                                    <td>{{ $customerOrder->customer->customer_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Project Name / 项目名称</th>
                                    <td>{{ $customerOrder->project_name ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Item Need to Purchase Requests -->
                    <h6 class="text-primary mb-3">Item Need to Purchase Requests / 采购需求请求</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Request Date / 请求日期</th>
                                    <th>Requested By / 请求人</th>
                                    <th>Status / 状态</th>
                                    <th>Actions / 操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    if (Auth::user()->departments->contains('name', 'Purchasing')) {
                                        $needToPurchase = $customerOrder
                                            ->itemNeedToPurchases()
                                            ->purchasingRole()
                                            ->get();
                                    } else {
                                        $needToPurchase = $customerOrder->itemNeedToPurchases;
                                    }
                                @endphp
                                @forelse ($needToPurchase as $index => $purchase)
                                    @php
                                        $approvalRequest = $purchase->approvalRequest('create')->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $purchase->request_date }}</td>
                                        <td>{{ $purchase->createdBy->name }}</td>
                                        <td>
                                            {{ ucfirst($purchase->process_status) }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapsePurchase{{ $purchase->id }}" aria-expanded="false"
                                                aria-controls="collapsePurchase{{ $purchase->id }}">
                                                <i class="fa fa-eye" title="View Details / 查看详情"></i>
                                            </button>
                                            @can('approve-item-need-to-purchase')
                                                @if (!is_null($approvalRequest))
                                                    @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\User')
                                                        @if (Auth::user()->id == $approvalRequest->currentLevel->approver->approver_reference_id)
                                                            <a href="javascript:;"
                                                                class="btn btn-sm btn-outline-primary action-btn"
                                                                title="Approval Process" data-bs-toggle="modal"
                                                                data-bs-target="#modalApprove{{ $approvalRequest->id }}"
                                                                title="Approval Process">
                                                                <i class="fa fa-list-check"></i> </a>
                                                        @endif
                                                    @endif
                                                    @if ($approvalRequest->currentLevel->class_name_approver_type == 'App\Models\Role')
                                                        @if (Auth::user()->hasRole($approvalRequest->currentLevel->approver->name))
                                                            @if (is_null($approvalRequest->currentLevel->department_id))
                                                                <a href="javascript:;"
                                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                                    title="Approval Process" data-bs-toggle="modal"
                                                                    data-bs-target="#modalApprove{{ $approvalRequest->id }}"
                                                                    title="Approval Process">
                                                                    <i class="fa fa-list-check"></i> </a>
                                                            @else
                                                                @if (count(array_intersect(
                                                                            $purchase->createdBy->departments->pluck('id')->toArray(),
                                                                            auth()->user()->departments->pluck('id')->toArray())) > 0)
                                                                    <a href="javascript:;"
                                                                        class="btn btn-sm btn-outline-primary action-btn"
                                                                        title="Approval Process" data-bs-toggle="modal"
                                                                        data-bs-target="#modalApprove{{ $approvalRequest->id }}"
                                                                        title="Approval Process">
                                                                        <i class="fa fa-list-check"></i> </a>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                    @include('admin.modal.approval')
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                    <!-- Collapsible Row for Purchase Request Details -->
                                    <tr class="collapse" id="collapsePurchase{{ $purchase->id }}">
                                        <td colspan="5">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item Name / 物品名称</th>
                                                            <th>Warehouse / 仓库</th>
                                                            <th>Quantity Needed</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($purchase->itemNeedToPurchaseDetail as $detail)
                                                            <tr>
                                                                <td>{{ $detail->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? '-' }}
                                                                </td>
                                                                <td>{{ $detail->warehouse->name ?? '-' }}</td>
                                                                <td>
                                                                    {{ getQuantity($detail->itemRequestDetail) }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center">No details found /
                                                                    未找到详细信息</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No purchase requests found /
                                            未找到采购请求</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4">
                        <a href="{{ route('item-need-to-purchase.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to List / 返回列表
                        </a>
                        @if (Auth::user()->departments->contains('name', 'Purchasing'))
                            @if ($needToPurchase->count() > 0)
                                <a href="{{ route('purchase-order.create', ['customer_order_id' => $customerOrder->id]) }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Create Purchase Order / 创建采购订单
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
