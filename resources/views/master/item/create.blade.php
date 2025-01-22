@extends('layouts.admin.app')
@section('title', 'Item / 项目')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Create Item / 创建项目</h5>
                        <small class="text-muted">Add a new item to the system / 向系统添加新项目</small>
                    </div>
                    <a href="{{ route('item.index') }}" class="btn p-0" title="Back / 返回">
                        <i class="ti ti-x ti-sm text-muted"></i>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('item.store') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There are some problems with your input. / 输入存在一些问题。<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Item Details -->
                        <div class="row">
                            <!-- Name -->
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name / 名称</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Enter item name / 输入项目名称" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Spefication / 规格</label>
                                <input class="form-control" type="text" id="spesification" name="spesification"
                                    value="{{ old('spesification') }}" placeholder="Enter item spesification / 输入项目规格" />
                            </div>
                        </div>

                        <div class="row">
                            <!-- Item Type -->
                            <div class="mb-3 col-md-6">
                                <label for="item_type_id" class="form-label">Item Type / 项目类型</label>
                                <select class="form-select" id="item_type_id" name="item_type_id" required>
                                    <option value="" disabled selected>Select item type / 选择项目类型</option>
                                    @foreach ($itemTypes as $idItemType => $itemType)
                                        <option value="{{ $idItemType }}"
                                            {{ old('item_type_id') == $idItemType ? 'selected' : '' }}>
                                            {{ $itemType }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Category -->
                            <div class="mb-3 col-md-6">
                                <label for="item_category_id" class="form-label">Item Category / 类别</label>
                                <select class="form-select" id="item_category_id" name="item_category_id">
                                    <option value="" selected>No category or select category / 无类别或选择类别</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">


                            <!-- UOM -->
                            <div class="mb-3 col-md-6">
                                <label for="uom_id" class="form-label">Unit of Measurement / 单位</label>
                                <select class="form-select" id="uom_id" name="uom_id" required>
                                    <option value="" disabled selected>Select UOM / 选择单位</option>
                                    @foreach ($uoms as $idUom => $uom)
                                        <option value="{{ $idUom }}" {{ old('uom_id') == $idUom ? 'selected' : '' }}>
                                            {{ $uom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Status --}}
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status / 状态</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active / 活跃
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive / 不活跃
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description (Optional) / 描述（可选）</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter item description / 输入项目描述">{{ old('description') }}</textarea>
                            </div>
                        </div>


                        <!-- Dynamic Rows for UOMs -->
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Unit of Measurements / 单位</label>
                                <p class="text-muted small">
                                    Add multiple units of measurement with their conversions, prices, and costs. The first
                                    row is linked to the primary UOM. /
                                    添加多个测量单位及其转换、价格和成本。第一行与主单位相关联。
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="uom-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 25%;">UOM / 单位</th>
                                                <th style="width: 15%;">Conversion / 转换</th>
                                                <th style="width: 15%;">Price / 价格</th>
                                                <th style="width: 15%;">Cost / 成本</th>
                                                <th style="width: 20%;">Currency / 货币</th>
                                                <th style="width: 10%;">Action / 操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="uom-container">
                                            <!-- First Row (Primary UOM) -->
                                            <tr class="uom-row">
                                                <td>
                                                    <select class="form-select" id="primary-uom" name="uoms[0][uom_id]"
                                                        disabled required>
                                                        <option value="0" disabled selected>Select UOM / 选择单位</option>
                                                        @foreach ($uoms as $idUom => $uom)
                                                            <option value="{{ $idUom }}">{{ $uom }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="uoms[0][conversion]" value="1" readonly required />
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="uoms[0][price]" placeholder="e.g., 100.00 / 例如 100.00"
                                                        required />
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="uoms[0][cost]" placeholder="e.g., 80.00 / 例如 80.00"
                                                        required />
                                                </td>
                                                <td>
                                                    <select class="form-select" name="uoms[0][currency]" required>
                                                        <option value="" disabled selected>Select Currency / 选择货币
                                                        </option>
                                                        @foreach ($currencies as $key => $currency)
                                                            <option value="{{ $key }}">
                                                                {{ $key . ' - ' . $currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-uom-row"
                                                        disabled>
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm mt-3" id="add-uom-row">
                                    <i class="fa fa-plus-circle"></i> Add UOM / 添加单位
                                </button>
                            </div>
                        </div>




                        <!-- Submit Button -->
                        <div class="my-2">
                            <button type="submit" class="btn btn-primary px-5 me-2">Submit / 提交</button>
                            <a href="{{ route('item.index') }}" class="btn btn-label-secondary">Cancel / 取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('script')
    <script>
        // Fetch parent categories based on item type
        $(document).ready(function() {
            $('#item_type_id').on('change', function() {
                var itemTypeId = $(this).val();

                if (itemTypeId) {
                    // Lakukan request ke server
                    $.ajax({
                        url: "{{ route('get-categories-by-item-type') }}", // Endpoint untuk get data
                        type: "GET",
                        data: {
                            item_type_id: itemTypeId,
                        },
                        success: function(data) {
                            $('#item_category_id').empty();
                            $('#item_category_id').append(
                                '<option value="" selected>Select Category / 选择类别</option>'
                            );
                            $.each(data, function(key, value) {
                                $('#item_category_id').append('<option value="' + key +
                                    '">' +
                                    value + '</option>');
                            });
                        },
                        error: function() {
                            alert('Failed to fetch data! / 数据获取失败！');
                        }
                    });
                } else {
                    $('#item_category_id').empty();
                    $('#item_category_id').append(
                        '<option value="" selected>No Category  / 无类别</option>');
                }
            });
        });
    </script>

    <script>
        let uomIndex = 1;

        // Update primary UOM row when UOM is selected
        document.getElementById('uom_id').addEventListener('change', function() {
            const selectedUom = this.value;
            const primaryUom = document.getElementById('primary-uom');
            primaryUom.value = selectedUom;
            // document.querySelector('input[name="uoms[0][uom_id]"]').value = selectedUom;
        });

        // Add new UOM row
        document.getElementById('add-uom-row').addEventListener('click', function() {
            const container = document.getElementById('uom-container');
            const firstRow = container.querySelector('.uom-row'); // Get first row
            const newRow = firstRow.cloneNode(true); // Clone the first row

            // Reset input fields and attributes
            newRow.querySelectorAll('input, select').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, `[${uomIndex}]`); // Update name attribute
                input.value = ''; // Clear value for new row

                // Remove `disabled` and `readonly` for the new row
                if (input.hasAttribute('disabled')) {
                    input.removeAttribute('disabled');
                }
                if (input.hasAttribute('readonly')) {
                    input.removeAttribute('readonly');
                }
            });

            // Enable the remove button for the new row
            const removeButton = newRow.querySelector('.remove-uom-row');
            removeButton.disabled = false;

            container.appendChild(newRow); // Append the new row to the container
            uomIndex++;
        });

        // Remove UOM row
        document.addEventListener('click', function(event) {
            if (event.target.closest('.remove-uom-row')) {
                const row = event.target.closest('.uom-row');
                if (document.querySelectorAll('.uom-row').length > 1) {
                    row.remove(); // Remove row if more than one row exists
                } else {
                    alert('At least one UOM is required! / 至少需要一个单位！');
                }
            }
        });
    </script>

    {{-- <script>
        let uomIndex = 1;
        let selectedUoms = new Set(); // Menyimpan UOM yang sudah dipilih

        // Update opsi untuk semua dropdown UOM
        function updateUomOptions() {
            const allUoms = @json($uoms); // UOM dari server
            const uomRows = document.querySelectorAll('.uom-row');

            uomRows.forEach(row => {
                const uomDropdown = row.querySelector('select[name*="[uom_id]"]');
                const selectedValue = uomDropdown.value; // Nilai yang sudah dipilih di dropdown ini

                // Reset dropdown
                uomDropdown.innerHTML = '<option value="" disabled>Select UOM / 选择单位</option>';

                // Tambahkan opsi UOM yang belum dipilih atau yang sudah terpilih di dropdown ini
                for (const [id, name] of Object.entries(allUoms)) {
                    if (!selectedUoms.has(id) || id === selectedValue) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = name;
                        if (id === selectedValue) {
                            option.selected = true; // Tetapkan opsi yang sudah dipilih
                        }
                        uomDropdown.appendChild(option);
                    }
                }
            });
        }

        // Ketika UOM Primary dipilih
        document.getElementById('uom_id').addEventListener('change', function() {
            const primaryUomValue = this.value;

            if (primaryUomValue) {
                // Isi row pertama (Primary UOM)
                const primaryRow = document.querySelector('.uom-row:first-child');
                const primaryDropdown = primaryRow.querySelector('select[name*="[uom_id]"]');
                primaryDropdown.value = primaryUomValue;
                primaryDropdown.dataset.previousValue = primaryUomValue; // Simpan nilai sebelumnya
                primaryDropdown.disabled = true; // Primary row tidak bisa diubah
                selectedUoms.add(primaryUomValue); // Tambahkan ke UOM yang sudah dipilih

                updateUomOptions(); // Perbarui opsi untuk semua dropdown
            }
        });

        // Tambah baris baru
        document.getElementById('add-uom-row').addEventListener('click', function() {
            const container = document.getElementById('uom-container');
            const firstRow = container.querySelector('.uom-row');
            const newRow = firstRow.cloneNode(true);

            // Reset input fields
            newRow.querySelectorAll('input, select').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/,
                `[${uomIndex}]`); // Ubah nama atribut untuk baris baru
                input.value = ''; // Kosongkan nilai untuk baris baru

                if (input.hasAttribute('disabled')) {
                    input.removeAttribute('disabled'); // Hilangkan disabled untuk row baru
                }
                if (input.hasAttribute('readonly')) {
                    input.removeAttribute('readonly'); // Hilangkan readonly untuk row baru
                }

                if (input.tagName === 'SELECT') {
                    input.dataset.previousValue = ''; // Reset nilai sebelumnya
                }
            });

            const removeButton = newRow.querySelector('.remove-uom-row');
            removeButton.disabled = false; // Aktifkan tombol hapus untuk row baru

            container.appendChild(newRow);
            uomIndex++;

            updateUomOptions(); // Perbarui dropdown UOM
        });

        // Hapus baris
        document.addEventListener('click', function(event) {
            if (event.target.closest('.remove-uom-row')) {
                const row = event.target.closest('.uom-row');
                const uomDropdown = row.querySelector('select[name*="[uom_id]"]');
                const uomValue = uomDropdown.value;

                if (uomValue) {
                    selectedUoms.delete(uomValue); // Hapus dari UOM yang sudah dipilih
                }

                if (document.querySelectorAll('.uom-row').length > 1) {
                    row.remove(); // Hapus baris
                    updateUomOptions(); // Perbarui dropdown
                } else {
                    alert('At least one UOM is required! / 至少需要一个单位！');
                }
            }
        });

        // Perbarui dropdown saat UOM dipilih
        document.addEventListener('change', function(event) {
            if (event.target.matches('select[name*="[uom_id]"]')) {
                const dropdown = event.target;
                const previousValue = dropdown.dataset.previousValue || '';
                const currentValue = dropdown.value;

                // Perbarui UOM yang sudah dipilih
                if (previousValue) selectedUoms.delete(previousValue);
                if (currentValue) selectedUoms.add(currentValue);

                dropdown.dataset.previousValue = currentValue; // Simpan nilai sebelumnya

                updateUomOptions(); // Perbarui opsi untuk semua dropdown
            }
        });

        // Inisialisasi
        updateUomOptions();
    </script> --}}
@endpush
