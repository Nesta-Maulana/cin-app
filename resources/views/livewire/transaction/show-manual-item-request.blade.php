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

                <!-- Import Excel Button -->
                @can('import-manual-item-request')
                    <div class="flex-wrap my-1 me-md-2">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#importModal"
                            class="btn btn-info text-white">
                            <span>
                                <i class="fa fa-file-import me-0 me-sm-1 fa-xs"></i>
                                <span>
                                    Import Excel / 导入Excel
                                </span>
                            </span>
                        </button>
                    </div>
                @endcan

                <!-- Create Button -->
                @can('create-manual-item-request')
                    <div class="flex-wrap my-1">
                        <a href="{{ route('manual-item-request.create') }}"
                            class="btn btn-secondary text-white add-new btn-primary">
                            <span>
                                <i class="fa fa-plus me-0 me-sm-1 fa-xs"></i>
                                <span>
                                    Create / 创建
                                </span>
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
                        <th>Request Number / 请求编号</th>
                        <th>Customer / 客户</th>
                        <th>Date / 日期</th>
                        <th>Status / 状态</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($table as $key => $item)
                        <tr wire:key="row{{ $item->id }}">
                            <td>{{ $item->request_number }}</td>
                            <td>{{ $item->customerOrder->customer->customer_name ?? '-' }} <small
                                    class="text-muted">({{ $item->customerOrder->order_number ?? '-' }})</small>
                            </td>
                            <td>
                                @if (is_string($item->request_date))
                                    {{ $item->request_date }}
                                @else
                                    {{ $item->request_date ? $item->request_date->format('d-m-Y') : '-' }}
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 'pending')
                                    <span class="badge bg-warning">Pending / 待处理</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge bg-success">Approved / 已批准</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge bg-danger">Rejected / 已拒绝</span>
                                @elseif($item->status == 'completed')
                                    <span class="badge bg-info">Completed / 已完成</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Details button - shows details in modal -->
                                    <a href="javascript:;" class="action-btn" title="Details / 详情"
                                        wire:click="showItemDetails({{ $item->id }})">
                                        <i class="fa fa-list fa-sm me-2 fs-5"></i>
                                    </a>

                                    @can('view-manual-item-request')
                                        <a href="{{ route('manual-item-request.show', $item->id) }}" class="action-btn"
                                            title="View / 查看">
                                            <i class="fa fa-eye fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('update-manual-item-request')
                                        <a href="{{ route('manual-item-request.edit', $item->id) }}" class="action-btn"
                                            title="Edit / 编辑">
                                            <i class="fa fa-edit fa-sm me-2 fs-5"></i>
                                        </a>
                                    @endcan

                                    @can('delete-manual-item-request')
                                        <a href="javascript:;" class="action-btn" title="Delete / 删除" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="fa fa-trash fa-sm mx-2 fs-5"></i>
                                        </a>
                                        <!-- Delete Modal -->
                                        <div wire:ignore.self class="modal fade" id="modalDelete{{ $item->id }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete / 确认删除</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this item request? / 您确定要删除此请求吗？
                                                        <p class="mt-2 text-danger">This action cannot be undone. / 此操作无法撤消。
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Cancel / 取消</button>
                                                        <button type="button" wire:click="delete('{{ $item->id }}')"
                                                            class="btn btn-danger" data-bs-dismiss="modal">Delete /
                                                            删除</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center col-md-7 mx-auto px-3 pt-3">
                                    <div class="alert alert-secondary">
                                        Data tidak ditemukan / 未找到数据
                                    </div>
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
                    @if ($table->count() > 0)
                        Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
                    @else
                        No data to display / 没有数据显示
                    @endif
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div wire:ignore.self class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Excel / 导入Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <h6>Excel Import Instructions / Excel导入说明:</h6>
                        <ul class="mb-0">
                            <li>Excel file should contain the following columns / Excel文件应包含以下列:
                                <br>NO BOM, PROYEK BAP, TGL, NAMA BARANG, DESCRIPTION, SPESIFICATION, UNIT, QTY
                            </li>
                            <li>The first row should be the header row / 第一行应为标题行</li>
                            <li>Customer code (PROYEK BAP) should already exist in the system, or a new one will be
                                created / 客户代码 (PROYEK BAP) 应已存在于系统中，或将创建新代码</li>
                        </ul>
                    </div>

                    <!-- Download Template Button -->
                    <div class="text-center mb-3">
                        <a href="{{ route('manual-item-request.download-template') }}" class="btn btn-outline-info">
                            <i class="fa fa-download me-1"></i> Download Template / 下载模板
                        </a>
                    </div>

                    <form wire:submit.prevent="importExcel" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="file">Excel File / Excel文件</label>
                            <div class="custom-file mt-2">
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    id="file" wire:model="file" accept=".xlsx,.xls,.csv" required>
                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Accepted formats / 接受的格式: .xlsx, .xls, .csv
                            </small>
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel /
                                取消</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload me-1"></i> Import / 导入
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Details Modal -->
    <div wire:ignore.self class="modal fade" id="itemDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Item Request Details / 物品请求详情
                        @if ($selectedItemRequest)
                            <span class="text-muted">({{ $selectedItemRequest->request_number }})</span>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeDetailsModal"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedItemRequest)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Request Information / 请求信息</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="120">Request No. / 请求编号</th>
                                            <td>{{ $selectedItemRequest->request_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date / 日期</th>

                                            <td>
                                                @if (is_string($selectedItemRequest->request_date))
                                                    {{ $selectedItemRequest->request_date }}
                                                @else
                                                    {{ $selectedItemRequest->request_date ? $selectedItemRequest->request_date->format('d-m-Y') : '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status / 状态</th>
                                            <td>
                                                @if ($selectedItemRequest->status == 'pending')
                                                    <span class="badge bg-warning">Pending / 待处理</span>
                                                @elseif($selectedItemRequest->status == 'approved')
                                                    <span class="badge bg-success">Approved / 已批准</span>
                                                @elseif($selectedItemRequest->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected / 已拒绝</span>
                                                @elseif($selectedItemRequest->status == 'completed')
                                                    <span class="badge bg-info">Completed / 已完成</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Customer Information / 客户信息</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="120">Code / 代码</th>
                                            <td>{{ $selectedItemRequest->customerOrder->order_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name / 名称</th>
                                            <td>{{ $selectedItemRequest->customerOrder->customer->customer_name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h6>Requested Items / 请求的物品</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr class="table-light">
                                        <th>#</th>
                                        <th>Item Name / 物品名称</th>
                                        <th>Description / 描述</th>
                                        <th>Specification / 规格</th>
                                        <th>Unit / 单位</th>
                                        <th>Quantity / 数量</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($itemDetails as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td>{{ $detail->description ?? '-' }}</td>
                                            <td>{{ $detail->specification ?? '-' }}</td>
                                            <td>{{ $detail->unit }}</td>
                                            <td class="text-end">{{ number_format($detail->quantity, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No items found / 未找到物品</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Loading item request details... / 正在加载物品请求详情...
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        wire:click="closeDetailsModal">Close / 关闭</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:load', function() {
            window.addEventListener('close-modal', event => {
                $('.dropdown-toggle').dropdown('hide');
                $('#modalDelete').modal('hide');
                $('#importModal').modal('hide');
            });

            window.addEventListener('open-details-modal', event => {
                $('#itemDetailsModal').modal('show');
            });
        });
    </script>
</div>
