@extends('layouts.admin.app')
@section('title', 'PurchaseRequest')

@push('style')
    <link rel="stylesheet" href="{{ asset('theme/custom.css') }}" />
@endpush

@push('script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
@endpush

@section('content')
    {{-- @livewire('transaction.show-purchase-request', ['title' => $__env->yieldContent('title')]) --}}
    {{--  <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">@yield('title')</h5>
                        <small class="text-muted">Daftar BOM dengan Kekurangan Material / 缺失材料的BOM列表</small>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Daftar BOM -->
                    <h6>Daftar BOM / BOM List</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>BOM Number / BOM编号</th>
                                <th>Project Name / 项目名称</th>
                                <th>Requested By / 申请人</th>
                                <th>Request Date / 请求日期</th>
                                <th>Action / 行动</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Data dummy untuk daftar BOM
                                $bomList = [
                                    [
                                        'request_number' => 'BOM-2023-001',
                                        'project_name' => 'Project Alpha',
                                        'requested_by' => 'John Doe',
                                        'request_date' => '2023-12-01',
                                    ],
                                    [
                                        'request_number' => 'BOM-2023-002',
                                        'project_name' => 'Project Beta',
                                        'requested_by' => 'Jane Smith',
                                        'request_date' => '2023-12-02',
                                    ],
                                    [
                                        'request_number' => 'BOM-2023-003',
                                        'project_name' => 'Project Gamma',
                                        'requested_by' => 'Alice Lee',
                                        'request_date' => '2023-12-03',
                                    ],
                                ];
                            @endphp

                            @foreach ($bomList as $bom)
                                <tr>
                                    <td>{{ $bom['request_number'] }}</td>
                                    <td>{{ $bom['project_name'] }}</td>
                                    <td>{{ $bom['requested_by'] }}</td>
                                    <td>{{ $bom['request_date'] }}</td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">View Details / 查看详情</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="card-body">
                    <!-- Ringkasan Kekurangan Material -->
                    <h6>Ringkasan Kekurangan Material / 缺失材料摘要</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Material / 材料</th>
                                <th>Total Missing Quantity / 总缺失数量</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Data dummy untuk ringkasan kekurangan material
                                $summary = [
                                    ['name' => 'Material A', 'missing_quantity' => 15],
                                    ['name' => 'Material B', 'missing_quantity' => 25],
                                    ['name' => 'Material C', 'missing_quantity' => 10],
                                ];
                            @endphp

                            @foreach ($summary as $material)
                                <tr>
                                    <td>{{ $material['name'] }}</td>
                                    <td>{{ $material['missing_quantity'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">@yield('title')</h5>
                        <small class="text-muted">Processing BOM Requests by Purchasing / 采购处理BOM请求</small>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Berdasarkan Item di BOM -->
                    <h6>Based on Items in BOM / 按BOM项目分类</h6>
                    <form id="generate-po-bom" action="{{-- {{ route('purchasing.po.bom') }} --}}" method="POST">
                        @csrf
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all-bom" /></th>
                                    <th>BOM Number / BOM编号</th>
                                    <th>Material / 材料</th>
                                    <th>Requested Quantity / 请求数量</th>
                                    <th>Available Quantity / 可用数量</th>
                                    <th>Missing Quantity / 缺失数量</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Data dummy untuk tabel BOM dengan lebih banyak item
                                    $bomDetails = [
                                        [
                                            'bom_number' => 'BOM-2023-001',
                                            'material' => 'Material A',
                                            'requested' => 20,
                                            'available' => 10,
                                            'missing' => 10,
                                        ],
                                        [
                                            'bom_number' => 'BOM-2023-001',
                                            'material' => 'Material B',
                                            'requested' => 15,
                                            'available' => 5,
                                            'missing' => 10,
                                        ],
                                        [
                                            'bom_number' => 'BOM-2023-002',
                                            'material' => 'Material C',
                                            'requested' => 30,
                                            'available' => 20,
                                            'missing' => 10,
                                        ],
                                        [
                                            'bom_number' => 'BOM-2023-003',
                                            'material' => 'Material D',
                                            'requested' => 40,
                                            'available' => 30,
                                            'missing' => 10,
                                        ],
                                        [
                                            'bom_number' => 'BOM-2023-004',
                                            'material' => 'Material E',
                                            'requested' => 25,
                                            'available' => 20,
                                            'missing' => 5,
                                        ],
                                        [
                                            'bom_number' => 'BOM-2023-005',
                                            'material' => 'Material F',
                                            'requested' => 50,
                                            'available' => 30,
                                            'missing' => 20,
                                        ],
                                        [
                                            'bom_number' => 'BOM-2023-006',
                                            'material' => 'Material G',
                                            'requested' => 35,
                                            'available' => 15,
                                            'missing' => 20,
                                        ],
                                    ];
                                @endphp

                                @foreach ($bomDetails as $key => $detail)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_bom_items[]" value="{{ $key }}"
                                                class="check-item-bom" />
                                        </td>
                                        <td>{{ $detail['bom_number'] }}</td>
                                        <td>{{ $detail['material'] }}</td>
                                        <td>{{ $detail['requested'] }}</td>
                                        <td>{{ $detail['available'] }}</td>
                                        <td>{{ $detail['missing'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-success mt-3">Generate Purchase Order by BOM /
                            根据BOM生成采购订单</button>
                    </form>
                </div>

                <hr>

                <div class="card-body">
                    <!-- Ringkasan Collective dari Item yang Kurang -->
                    <h6>Collective Summary of Missing Items / 缺失材料的综合摘要</h6>
                    <form id="generate-po-summary" {{-- action="" --}} method="POST">
                        @csrf
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all-summary" /></th>
                                    <th>Material / 材料</th>
                                    <th>Total Missing Quantity / 总缺失数量</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Data dummy untuk tabel summary dengan lebih banyak item
                                    $collectiveSummary = [
                                        ['material' => 'Material A', 'total_missing' => 50],
                                        ['material' => 'Material B', 'total_missing' => 30],
                                        ['material' => 'Material C', 'total_missing' => 25],
                                        ['material' => 'Material D', 'total_missing' => 15],
                                        ['material' => 'Material E', 'total_missing' => 10],
                                        ['material' => 'Material F', 'total_missing' => 20],
                                        ['material' => 'Material G', 'total_missing' => 35],
                                    ];
                                @endphp

                                @foreach ($collectiveSummary as $key => $summary)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_summary_items[]"
                                                value="{{ $key }}" class="check-item-summary" />
                                        </td>
                                        <td>{{ $summary['material'] }}</td>
                                        <td>{{ $summary['total_missing'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('purchasing.po.summary') }}" class="btn btn-primary mt-3">Generate Purchase Order by Summary /
                            根据摘要生成采购订单</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Checkbox "Check All" untuk tabel BOM
        document.getElementById('check-all-bom').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.check-item-bom').forEach(item => {
                item.checked = isChecked;
            });
        });

        // Checkbox "Check All" untuk tabel Summary
        document.getElementById('check-all-summary').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.check-item-summary').forEach(item => {
                item.checked = isChecked;
            });
        });
    </script>
@endpush
