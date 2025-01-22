@extends('layouts.admin.app')
@section('title', 'Item Details / 项目详情')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Item Details / 项目详情</h5>
                    <a href="{{ route('item.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back / 返回
                    </a>
                </div>
                <div class="card-body">
                    <!-- Basic Information -->
                    <h6 class="text-primary mb-3">Basic Information / 基本信息</h6>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 25%;">Name / 名称</th>
                                <td>{{ $item->name }}</td>
                            </tr>
                            <tr>
                                <th>Item Type / 项目类型</th>
                                <td>{{ $item->itemType->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Category / 类别</th>
                                <td>{{ $item->itemCategory->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Primary UOM / 主单位</th>
                                <td>{{ $item->primaryUom->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status / 状态</th>
                                <td>
                                    <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->is_active ? 'Active / 活跃' : 'Inactive / 不活跃' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Description / 描述</th>
                                <td>{{ $item->description ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- UOM Details -->
                    <h6 class="text-primary mt-4 mb-3">Unit of Measurements / 单位详情</h6>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">UOM / 单位</th>
                                <th style="width: 15%;">Conversion / 转换</th>
                                <th style="width: 15%;">Price / 价格</th>
                                <th style="width: 15%;">Cost / 成本</th>
                                <th style="width: 15%;">Currency / 货币</th>
                                <th style="width: 15%;">Last Updated / 最近更新</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($item->itemUoms as $uom)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $uom->unitOfMeasurement->name ?? '-' }}</td>
                                    <td>{{ $uom->conversion }}</td>
                                    <td>{{ number_format($uom->latestPrice?->price ?? 0, 2) }}</td>
                                    <td>{{ number_format($uom->latestPrice?->cost ?? 0, 2) }}</td>
                                    <td>{{ $uom->latestPrice?->currency ?? '-' }}</td>
                                    <td>{{ $uom->latestPrice?->updated_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No UOM data available / 无单位数据</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
