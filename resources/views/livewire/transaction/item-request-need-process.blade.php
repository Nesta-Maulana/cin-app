<div>
    <div class="card">
        <!-- Card Header -->
        <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
            <!-- Search Input -->
            <div class="my-1 text-center text-md-start">
                <label>
                    <input wire:model.debounce.500ms="search" type="search" class="form-control"
                        placeholder="Search... / 搜索...">
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                <!-- Pagination Dropdown -->
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
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number / 订单编号</th>
                        <th>Order Date / 订单日期</th>
                        <th>Customer / 客户</th>
                        <th>Total Requests / 请求总数</th>
                        <th>Actions / 操作</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($table as $key => $order)
                        <tr wire:key="row{{ $order->id }}">
                            <td>{{ $no++ }}</td>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->order_date->format('Y-m-d') }}</td>
                            <td>{{ $order->customer->name ?? '-' }}</td>
                            <td>{{ $order->totalItemRequest }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Button to toggle collapse -->
                                    <button class="btn btn-primary btn-sm me-2" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOrder{{ $order->id }}" aria-expanded="false"
                                        aria-controls="collapseOrder{{ $order->id }}">
                                        View Item Requests / 查看物品请求
                                    </button>

                                    <!-- Extra Button to redirect to view-detail-item-request page -->
                                    <a href="{{ route('view-detail-item-request', $order->id) }}"
                                        class="btn btn-info btn-sm" title="View Detail / 查看详情">
                                        <i class="fa fa-info-circle fa-sm me-1"></i>
                                        <span>Detail / 详情</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Collapsible Row for Item Requests -->
                        <tr class="collapse" id="collapseOrder{{ $order->id }}">
                            <td colspan="6">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Request Number / 请求编号</th>
                                                <th>Request Date / 请求日期</th>
                                                <th>Status / 状态</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($order->itemRequests as $request)
                                                @if ($request->request_status == 'Waiting On Process Warehouse')
                                                    <tr>
                                                        <td>{{ $request->request_number }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($request->request_date)->format('Y-m-d') }}
                                                        </td>
                                                        <td>{{ $request->request_status }}</td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No item requests found /
                                                        未找到物品请求</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-secondary">
                                    No data found / 没有找到数据
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
            <div class="align-self-start my-2 d-none d-md-block text-muted">
                <small>
                    Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data / 显示
                    {{ $table->firstItem() }} 到 {{ $table->lastItem() }} 的 {{ $table->total() }} 数据
                </small>
            </div>
            {{ $table->links() }}
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('.dropdown-toggle').dropdown('hide');
        });
    </script>
</div>
