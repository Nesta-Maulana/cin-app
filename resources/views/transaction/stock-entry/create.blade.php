@extends('layouts.admin.app')
@section('title', 'Create Stock Entry / 添加库存记录')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Stock Entry / 添加库存记录</h5>
                        <small class="text-muted">Fill in stock details / 填写库存详情</small>
                    </div>
                    <a href="{{ route('stock-entry.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="fa fa-x fa-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('stock-entry.store') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops! / 哎呀!</strong> There were some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Warehouse Selection -->
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">Warehouse / 仓库 <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                <option value="" selected disabled>Choose Warehouse / 选择仓库</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Date and Notes -->
                        <div class="mb-3">
                            <label for="date" class="form-label">Date / 日期 <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes / 备注</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>

                        <!-- Stock Details (Multiple Items) -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="stock-entries-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-nowrap">Item / 物品 <span class="text-danger">*</span></th>
                                        <th class="text-nowrap">Type / 类型</th>
                                        <th class="text-nowrap">Stock Source / 库存来源</th>
                                        <th class="text-nowrap">Quantity / 数量 <span class="text-danger">*</span></th>
                                        <th class="text-nowrap">Item Uom / 物品单位</th>
                                        <th class="text-nowrap">Section / 仓库区域 <span class="text-danger">*</span></th>
                                        <th class="text-nowrap">Supplier / 供应商</th>
                                        <th class="text-nowrap">Warehouse Destination</th>
                                        <th class="text-nowrap">Reference Number</th>
                                        <th class="text-nowrap">Action / 操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris item akan ditambahkan secara dinamis di sini -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-success btn-sm mt-2" id="add-item-row">
                            <i class="fa fa-plus-circle"></i> Add Item / 添加物品
                        </button>

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('stock-entry.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const warehouseDropdown = $('#warehouse_id');

            warehouseDropdown.on('change', function() {
                let warehouseId = $(this).val();

                // Fetch sections for selected warehouse
                $('.section-select').each(function() {
                    $(this).html('<option value="" selected disabled>Loading...</option>');
                    $.ajax({
                        url: "{{ route('get-section-by-warehouse-id') }}",
                        type: "GET",
                        data: {
                            warehouse_id: warehouseId
                        },
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>Choose Section / 选择区域</option>';
                            $.each(data.data, function(index, section) {
                                options +=
                                    `<option value="${section.id}">${section.name}</option>`;
                            });
                            $('.section-select').html(options);
                        },
                        error: function() {
                            alert('Failed to fetch sections! / 无法获取区域！');
                        }
                    });
                });

                // Filter Warehouse Destination for Transfers
                $('.warehouse-destination').each(function() {
                    let $this = $(this);
                    $this.html('<option value="" selected disabled>Loading...</option>');
                    $.ajax({
                        url: "{{ route('get-warehouses') }}",
                        type: "GET",
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>Choose Destination / 选择目的仓库</option>';
                            $.each(data, function(index, warehouse) {
                                if (warehouse.id != warehouseId) {
                                    options +=
                                        `<option value="${warehouse.id}">${warehouse.name}</option>`;
                                }
                            });
                            $this.html(options);
                        },
                        error: function() {
                            alert('Failed to fetch warehouses! / 无法获取仓库！');
                        }
                    });
                });
            });
            // Tambah baris stock entry baru
            $('#add-item-row').click(function() {
                let tableBody = $('#stock-entries-table tbody');
                let rowCount = $('.stock-entry-row').length;
                let newRow = `
                    <tr class="stock-entry-row">
                        <td>
                            <select class="form-select item-select" name="items[${rowCount}][item_id]" required>
                                <option value="" selected disabled>Choose Item / 选择物品</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select type-select" name="items[${rowCount}][type]" required>
                                <option value="in">IN</option>
                                <option value="out">OUT</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select stock-source-select" name="items[${rowCount}][stock_source]" required>
                                <option value="" selected disabled>Choose Stock Source / 选择库存来源</option>
                                <option value="purchase">Purchase</option>
                                <option value="transfer">Transfer</option>
                                <option value="adjustment">Adjustment</option>
                                <option value="sale">Sale</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity-input" name="items[${rowCount}][quantity]" step="0.001" min="0" required>
                        </td>
                        <td>
                            <!-- Tambahkan class item-uom-select agar bisa diambil oleh AJAX -->
                            <select class="form-select item-uom-select" name="items[${rowCount}][item_uom_id]" required>
                                <option value="" selected disabled>Choose UOM / 选择单位</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select section-select" name="items[${rowCount}][section_id]" required>
                                <option value="" selected disabled>Choose Section / 选择区域</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select supplier-select" name="items[${rowCount}][supplier_id]" disabled>
                                <option value="" selected disabled>Choose Supplier / 选择供应商</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select warehouse-destination" name="items[${rowCount}][warehouse_destination]" disabled>
                                <option value="" selected disabled>Choose Destination / 选择目的仓库</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control reference-number" name="items[${rowCount}][reference_number]" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tableBody.append(newRow);
                // Jika sudah ada warehouse yang dipilih, trigger event change untuk memperbarui dropdown Section
                let warehouseId = $('#warehouse_id').val();
                if (warehouseId) {
                    // Update dropdown Section (sama seperti sebelumnya)
                    let newSectionSelect = tableBody.find('tr:last').find('.section-select');
                    newSectionSelect.html('<option value="" selected disabled>Loading...</option>');
                    $.ajax({
                        url: "{{ route('get-section-by-warehouse-id') }}",
                        type: "GET",
                        data: {
                            warehouse_id: warehouseId
                        },
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>Choose Section / 选择区域</option>';
                            $.each(data.data, function(index, section) {
                                options +=
                                    `<option value="${section.id}">${section.name}</option>`;
                            });
                            newSectionSelect.html(options);
                        },
                        error: function() {
                            alert('Failed to fetch sections! / 无法获取区域！');
                        }
                    });

                    // Update dropdown Warehouse Destination khusus untuk baris baru
                    let newWarehouseDestination = tableBody.find('tr:last').find('.warehouse-destination');
                    newWarehouseDestination.html('<option value="" selected disabled>Loading...</option>');
                    $.ajax({
                        url: "{{ route('get-warehouses') }}",
                        type: "GET",
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>Choose Destination / 选择目的仓库</option>';
                            console.log(data);
                            $.each(data, function(index, warehouse) {
                                // Hilangkan warehouse yang sudah dipilih pada dropdown utama
                                if (warehouse.id != warehouseId) {
                                    options +=
                                        `<option value="${warehouse.id}">${warehouse.name}</option>`;
                                }
                            });
                            newWarehouseDestination.html(options);
                        },
                        error: function() {
                            alert('Failed to fetch warehouses! / 无法获取仓库！');
                        }
                    });
                }

            });

            // Delegated event: Ambil UOM berdasarkan item yang dipilih
            $('#stock-entries-table').on('change', '.item-select', function() {
                let itemId = $(this).val();
                let uomDropdown = $(this).closest('tr').find('.item-uom-select');
                uomDropdown.html('<option value="" selected disabled>Loading...</option>');
                $.ajax({
                    url: "{{ route('get-uom-by-item') }}",
                    type: "GET",
                    data: {
                        item_id: itemId
                    },
                    success: function(data) {
                        if (data.success) {
                            let options =
                                '<option value="" selected disabled>Choose UOM / 选择单位</option>';
                            $.each(data.uoms, function(index, uom) {
                                options +=
                                    `<option value="${uom.id}">${uom.unit_of_measurement.name}</option>`;
                            });
                            uomDropdown.html(options);
                        } else {
                            alert('No UOM found for the selected item! / 未找到对应单位！');
                            uomDropdown.html(
                                '<option value="" selected disabled>No UOM Available</option>'
                            );
                        }
                    },
                    error: function() {
                        alert('Failed to fetch UOM! / 无法获取单位！');
                        uomDropdown.html(
                            '<option value="" selected disabled>Error loading UOM</option>');
                    }
                });
            });

            // Delegated event: Ubah properti field berdasarkan stock source
            $('#stock-entries-table').on('change', '.stock-source-select', function() {
                let row = $(this).closest('tr');
                let stockSource = $(this).val();
                let refNumberField = row.find('.reference-number');
                let warehouseDestinationField = row.find('.warehouse-destination');
                let supplierField = row.find('.supplier-select');

                if (stockSource === 'purchase') {
                    refNumberField.prop('readonly', false).prop('required', true);
                    warehouseDestinationField.prop('disabled', true).val('');
                    supplierField.prop('disabled', false).prop('required', true);
                } else if (stockSource === 'transfer') {
                    refNumberField.prop('readonly', true).val('');
                    warehouseDestinationField.prop('disabled', false).prop('required', true);
                    supplierField.prop('disabled', true).val('');
                } else if (stockSource === 'adjustment' || stockSource === 'sale') {
                    refNumberField.prop('readonly', true).val('');
                    warehouseDestinationField.prop('disabled', true).val('');
                    supplierField.prop('disabled', true).val('');
                }
            });

            // Delegated event: Hapus baris item
            $('#stock-entries-table').on('click', '.remove-row', function() {
                if ($('.stock-entry-row').length > 1) {
                    $(this).closest('tr').remove();
                }
            });
        });
    </script>
@endpush
