@extends('layouts.admin.app')
@section('title', 'Warehouse - Request Details')
@section('breadcumb')
    {{ $data->request_number }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">@yield('title')</h5>
                        <small class="text-muted">Detail Permintaan BOM / 需求详细信息</small>
                    </div>
                    <a href="{{ route('warehouse.requested-bom.index') }}" class="btn btn-sm btn-secondary">Back / 返回</a>
                </div>

                <div class="card-body">
                    <!-- Informasi Request -->
                    <h6>Request Information / 请求信息</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Request Number / 请求编号:</strong></label>
                                <input type="text" class="form-control" value="{{ $data->request_number }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Project Name / 项目名称:</strong></label>
                                <input type="text" class="form-control" value="{{ $data->project_name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Requested By / 申请人:</strong></label>
                                <input type="text" class="form-control" value="{{ $data->requested_by }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Request Date / 请求日期:</strong></label>
                                <input type="date" class="form-control" value="{{ $data->request_date }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Description / 描述:</strong></label>
                                <textarea class="form-control" rows="3" readonly>{{ $data->description }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Material Details -->
                    <h6>Material Details / 材料详情</h6>
                    <form action="{{ route('warehouse.requested-bom.update', 1) }}" method="POST" id="bom-form">
                        @csrf
                        @method('PUT')

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Material / 材料</th>
                                    <th>Requested Quantity / 请求数量</th>
                                    <th>Unit / 单元</th>
                                    <th>Remarks (备注)</th>
                                    <th>Available Quantity / 可用数量</th>
                                    <th>Missing Quantity / 缺失数量</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->bomDetails as $i => $item)
                                    <tr>
                                        <td>Material {{ $item->material->name }} ({{ $item->material->name_mandarin }})</td>
                                        <td>
                                            <input type="number" class="form-control requested-quantity"
                                                value="{{ $item->quantity }}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control requested-quantity"
                                                value="{{ $item->material->unit->name }}({{ $item->material->unit->name_mandarin }})"
                                                readonly />
                                        </td>
                                        <td>
                                            <textarea class="form-control requested-quantity" rows="2" readonly>{{ $item->description }}</textarea>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control available-quantity"
                                                name="available_quantity[{{ $item->id }}]" value="0"
                                                min="0" />
                                        </td>
                                        <td>
                                            <input type="number" class="form-control missing-quantity text-danger"
                                                value="0" readonly />
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td>Material {{ $i }}</td>
                                        <td>
                                            <input type="number" class="form-control requested-quantity"
                                                value="{{ rand(5, 15) }}" readonly />
                                        </td>
                                        <td>
                                            <input type="number" class="form-control available-quantity"
                                                name="available_quantity[{{ $i }}]" value="0"
                                                min="0" />
                                        </td>
                                        <td>
                                            <input type="number" class="form-control missing-quantity text-danger"
                                                value="0" readonly />
                                        </td>
                                    </tr>
                                @endfor --}}
                            </tbody>
                        </table>

                        <!-- Submit Buttons -->
                        <div class="mt-3" id="button-container">
                            <!-- Buttons will be dynamically rendered here -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Fungsi untuk menghitung kekurangan stok dan menampilkan tombol dinamis
        function calculateMissingQuantity() {
            let missingExists = false; // Flag untuk cek apakah ada missing quantity

            document.querySelectorAll('tr').forEach(row => {
                const requestedInput = row.querySelector('.requested-quantity');
                const availableInput = row.querySelector('.available-quantity');
                const missingInput = row.querySelector('.missing-quantity');

                if (requestedInput && availableInput && missingInput) {
                    const requested = parseInt(requestedInput.value) || 0;
                    const available = parseInt(availableInput.value) || 0;

                    // Validasi: Available Quantity tidak boleh melebihi Requested Quantity
                    if (available > requested) {
                        availableInput.value = requested; // Set value ke max (Requested Quantity)
                        alert(`Available Quantity cannot exceed Requested Quantity (${requested}).`);
                    }

                    const missing = Math.max(0, requested - available);
                    missingInput.value = missing;

                    // Jika ada kekurangan, set flag ke true
                    if (missing > 0) {
                        missingExists = true;
                    }
                }
            });

            // Render tombol sesuai kondisi
            const buttonContainer = document.getElementById('button-container');
            buttonContainer.innerHTML = ''; // Clear existing buttons

            if (missingExists) {
                buttonContainer.innerHTML = `
                <button type="submit" class="btn btn-warning" id="update-notify">
                    Update and Notify Procurement / 更新并通知采购
                </button>
            `;
            } else {
                buttonContainer.innerHTML = `
                <button type="submit" class="btn btn-success" id="process-request">
                    Process BOM Request / 处理请求
                </button>
            `;
            }
        }

        // Event listener untuk menghitung ulang saat stok tersedia diubah
        document.querySelectorAll('.available-quantity').forEach(input => {
            input.addEventListener('input', calculateMissingQuantity);
        });

        // Hitung ulang saat halaman dimuat
        calculateMissingQuantity();
    </script>
@endpush
