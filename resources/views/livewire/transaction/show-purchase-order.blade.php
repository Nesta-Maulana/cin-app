<div>
    <div class="card">
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search / 搜索...">
                </label>
            </div>
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                <div class="my-1 me-md-2">
                    <label>
                        <select wire:model="paginate" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                </div>
                @can('create-purchase-order')
                    <div class="flex-wrap my-1">
                        <a href="{{ route('purchase-order.create') }}"
                            class="btn btn-secondary text-white add-new btn-primary">
                            <span>
                                <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                <span>Create Purchase Order / 创建采购单</span>
                            </span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">PO Number / 采购单号</th>
                        <th style="width: 15%">Customer Order / 客户订单</th>
                        <th style="width: 15%">Expected Delivery / 预计交货日期</th>
                        <th style="width: 10%">Status / 状态</th>
                        <th style="width: 15%">Total / 总价</th>
                        <th style="width: 10%">Created By / 创建人</th>
                        <th style="width: 11%">Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        <tr wire:key="row{{ $item->id }}">
                            <td>{{ $table->firstItem() + $key }}</td>
                            <td>
                                <a href="{{ route('purchase-order.show', $item->id) }}" class="fw-semibold">
                                    {{ $item->po_number }}
                                </a>
                            </td>
                            <td>{{ $item->customerOrder->order_number ?? '-' }}</td>
                            <td>{{ $item->expected_delivery_date ? date('d M Y', strtotime($item->expected_delivery_date)) : '-' }}
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($item->process_status) {
                                        'Draft' => 'bg-secondary',
                                        'Waiting Approval Manager' => 'bg-warning text-dark',
                                        'Approved' => 'bg-success',
                                        'Rejected' => 'bg-danger',
                                        'Completed' => 'bg-info',
                                        'Cancelled' => 'bg-dark',
                                        default => 'bg-primary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $item->process_status }}</span>
                            </td>
                            <td>{{ number_format($item->total_price, 2) }}</td>
                            <td>{{ $item->createdBy->name ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Collapsible Detail Button -->
                                    <a href="javascript:;" class="action-btn me-2" title="Quick View / 快速查看"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->id }}"
                                        aria-expanded="false" aria-controls="collapse{{ $item->id }}">
                                        <i class="fa fa-angle-down fa-sm fs-5"></i>
                                    </a>

                                    <a href="{{ route('purchase-order.show', $item->id) }}" class="action-btn"
                                        title="View / 查看">
                                        <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                    </a>

                                    @can('update-purchase-order')
                                        @if ($item->process_status == 'Draft')
                                            <a href="{{ route('purchase-order.edit', $item->id) }}" class="action-btn"
                                                title="Edit / 编辑">
                                                <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('delete-purchase-order')
                                        @if ($item->process_status == 'Draft')
                                            <a href="javascript:;" class="action-btn" title="Delete / 删除"
                                                data-bs-toggle="modal" data-bs-target="#modalDelete{{ $item->id }}">
                                                <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                            </a>
                                            @include('admin.modal.delete', [
                                                'updateRoute' => route('purchase-order.destroy', $item->id),
                                            ])
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        <!-- Collapsible Detail Row -->
                        <tr class="collapse-row">
                            <td colspan="9" class="p-0">
                                <div wire:key="collapse{{ $item->id }}" class="collapse"
                                    id="collapse{{ $item->id }}">
                                    <div class="card card-body m-2 bg-light">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-2">Purchase Order Details / 采购单详情</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td class="fw-semibold" style="width: 40%">PO Number /
                                                                    采购单号</td>
                                                                <td>{{ $item->po_number }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-semibold">Reference Customer Order / 参考号
                                                                </td>
                                                                <td>{{ $item->customerOrder->order_number ?? '-' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-semibold">Supplier / 供应商</td>
                                                                <td>{{ $item->supplier->name ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-semibold">Created Date / 创建日期</td>
                                                                <td>{{ $item->created_at ? date('d M Y', strtotime($item->created_at)) : '-' }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-2">Items Summary / 项目摘要</h6>
                                                @if ($item->details && count($item->details) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Reference BOM / 参考 BOM</th>
                                                                    <th>Item / 物品</th>
                                                                    <th>Qty / 数量</th>
                                                                    <th>Unit of Measurement / 单位</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($item->details->take(3) as $poItem)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $poItem->itemRequestDetail->itemRequest->request_number ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $poItem->itemRequestDetail->itemPriceHistory->itemUom->item->name ?? '-' }}
                                                                        </td>
                                                                        <td>{{ $poItem->quantity }}</td>
                                                                        <td>{{ $poItem->uom->unitOfMeasurement->name }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                @if (count($item->details) > 3)
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">
                                                                            <a
                                                                                href="{{ route('purchase-order.show', $item->id) }}">
                                                                                View all
                                                                                {{ count($item->details) }}
                                                                                items / 查看全部
                                                                                {{ count($item->details) }}
                                                                                项
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-muted">No items found / 未找到物品</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="alert alert-secondary mt-2">
                                    No data found / 没有找到数据
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() ?? 0 }} to {{ $table->lastItem() ?? 0 }} of
                    {{ $table->total() ?? 0 }} entries
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <!-- Status Update Modal for Multiple Selection -->
    @if (!empty($selected))
        <div wire:ignore.self class="modal fade" id="modalSelectedStatus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status / 更新状态</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="updateSelectedStatus">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Status / 状态</label>
                                    <select wire:model="status" class="form-select" required>
                                        <option value="">Select status / 选择状态</option>
                                        <option value="Cancelled">Cancelled / 已取消</option>
                                        <option value="On Process">On Process / 处理中</option>
                                        <option value="Completed">Completed / 已完成</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Remarks / 备注</label>
                                    <textarea wire:model="remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel /
                                取消</button>
                            <button type="submit" class="btn btn-primary">Update / 更新</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .collapse-row {
            background-color: transparent !important;
        }

        /* Rotate icon when details are expanded */
        [aria-expanded="true"] .fa-angle-down {
            transform: rotate(180deg);
            transition: transform 0.3s;
        }

        [aria-expanded="false"] .fa-angle-down {
            transform: rotate(0deg);
            transition: transform 0.3s;
        }
    </style>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
            $('#modalSelectedStatus').modal('hide');
        });

        // Add Livewire hook to reinitialize collapsible elements after updates
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', () => {
                // Ensure proper styling on collapse toggle
                $('.collapse').on('show.bs.collapse', function() {
                    $(this).closest('tr').prev().addClass('border-bottom-0');
                });

                $('.collapse').on('hide.bs.collapse', function() {
                    $(this).closest('tr').prev().removeClass('border-bottom-0');
                });
            });
        });
    </script>
</div>
