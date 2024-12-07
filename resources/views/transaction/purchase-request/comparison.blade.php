@extends('layouts.admin.app')
@section('title', 'Comparation Method')
@push('style')
    <style>
        /* CSS for full-width table */
        .table-responsive {
            overflow-x: auto;
            /* Allow horizontal scroll if necessary */
            white-space: nowrap;
            /* Prevent wrapping of table content */
        }

        .table th,
        .table td {
            min-width: 100px;
            /* Set minimum width for columns */
            max-width: 200px;
            /* Optional: Limit max width */
            word-wrap: break-word;
            /* Wrap long text if necessary */
            text-align: center;
            /* Center align for better visibility */
        }

        .table th input,
        .table td input {
            width: 100%;
            /* Ensure inputs span full column width */
            box-sizing: border-box;
            /* Adjust padding and border */
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">@yield('title')</h5>
                        <small class="text-muted">Comparing Prices Between Vendors / 供应商价格比较</small>
                    </div>
                    <button class="btn btn-sm btn-primary" id="add-vendor-btn">Add Vendor / 添加供应商</button>
                </div>

                <div class="card-body">
                    <form id="comparison-form" method="POST" action="{{-- {{ route('comparison.store') }} --}}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered mb-3" id="comparison-table">
                                <thead>
                                    <tr id="vendor-headers">
                                        <th rowspan="2">NO / 编号</th>
                                        <th rowspan="2">ITEM NAME / 名称</th>
                                        <th rowspan="2">UNIT / 单位</th>
                                        <th rowspan="2">QTY / 数量</th>
                                    </tr>
                                    <tr id="vendor-sub-headers"></tr>
                                </thead>
                                <tbody>
                                    @php
                                        $materials = [
                                            ['name' => 'Material A', 'unit' => 'BTG', 'qty' => 10],
                                            ['name' => 'Material B', 'unit' => 'BTG', 'qty' => 5],
                                            ['name' => 'Material C', 'unit' => 'BTG', 'qty' => 8],
                                            ['name' => 'Material D', 'unit' => 'BTG', 'qty' => 3],
                                            ['name' => 'Material E', 'unit' => 'BTG', 'qty' => 7],
                                        ];
                                    @endphp
                                    @foreach ($materials as $index => $material)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $material['name'] }}</td>
                                            <td>{{ $material['unit'] }}</td>
                                            <td>{{ $material['qty'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Section -->
                        <h6>Summary Per Vendor / 每个供应商总结</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="summary-table">
                                <thead>
                                    <tr id="summary-headers">
                                        <th>Vendor Name / 供应商名称</th>
                                        <th>Total / 总计</th>
                                        <th>PPN 11% / 增值税11%</th>
                                        <th>Shipping / 运费</th>
                                        <th>Grand Total / 总金额</th>
                                        <th>Notes / 备注</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Save Comparison / 保存比较</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let vendorCount = 0; // Initial vendor count
            const materials = @json($materials);

            // Add Vendor Button
            document.getElementById('add-vendor-btn').addEventListener('click', function() {
                vendorCount++;
                const vendorName = `Vendor ${vendorCount}`; // Default name for the vendor

                // Update header rows dynamically
                const headerRow = document.getElementById('vendor-headers');
                const subHeaderRow = document.getElementById('vendor-sub-headers');

                // Append new vendor input field to header
                const newVendorHeader = document.createElement('th');
                newVendorHeader.setAttribute('colspan', '2');
                newVendorHeader.classList.add('text-center');
                newVendorHeader.innerHTML = `
            <input type="text" name="vendors[${vendorName}][name]" class="form-control vendor-name-input"
                   data-vendor="${vendorName}" placeholder="Enter Vendor Name / 输入供应商名称" />
        `;
                headerRow.appendChild(newVendorHeader);

                // Append sub-header cells
                subHeaderRow.innerHTML += `
            <th>PRICE / 价格</th>
            <th>TOTAL / 总计</th>
        `;

                // Add input columns for each material row
                const tbody = document.querySelector('#comparison-table tbody');
                Array.from(tbody.rows).forEach((row, index) => {
                    row.innerHTML += `
                <td>
                    <input type="number" name="prices[${vendorName}][${index}]" class="form-control price-input" placeholder="Price" min="0">
                </td>
                <td>
                    <input type="number" name="totals[${vendorName}][${index}]" class="form-control total-input" readonly>
                </td>
            `;
                });

                // Add summary row for the new vendor
                const summaryRow = document.getElementById('summary-table').querySelector('tbody');
                const newSummaryRow = document.createElement('tr');
                newSummaryRow.classList.add('vendor-summary');
                newSummaryRow.setAttribute('data-vendor', vendorName);
                newSummaryRow.innerHTML = `
            <td><input type="text" name="vendors[${vendorName}][name]" class="form-control vendor-name-summary" readonly></td>
            <td><input type="number" name="summary[${vendorName}][total]" class="form-control summary-total" readonly></td>
            <td><input type="number" name="summary[${vendorName}][ppn]" class="form-control ppn-input" readonly></td>
            <td><input type="number" name="summary[${vendorName}][shipping]" class="form-control shipping-input" placeholder="Enter Shipping Cost"></td>
            <td><input type="number" name="summary[${vendorName}][grand_total]" class="form-control grand-total" readonly></td>
            <td><textarea name="summary[${vendorName}][notes]" class="form-control" placeholder="Notes"></textarea></td>
        `;
                summaryRow.appendChild(newSummaryRow);

                // Rebind events to new inputs
                bindPriceInputEvents();
                bindVendorNameSync();
            });

            function bindPriceInputEvents() {
                document.querySelectorAll('.price-input').forEach(input => {
                    input.addEventListener('input', function() {
                        const row = this.closest('tr');
                        const qty = parseInt(row.querySelector('td:nth-child(4)').textContent) || 0;
                        const totalCell = this.parentElement.nextElementSibling.querySelector(
                            '.total-input');
                        const price = parseFloat(this.value) || 0;

                        totalCell.value = (qty * price).toFixed(2);
                        calculateSummary();
                    });
                });
            }

            function calculateSummary() {
                document.querySelectorAll('.vendor-summary').forEach(row => {
                    const vendorName = row.dataset.vendor;
                    let total = 0;

                    document.querySelectorAll(`input[name^="totals[${vendorName}]"]`).forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });

                    const ppn = total * 0.11;
                    const shipping = parseFloat(row.querySelector(
                        `input[name="summary[${vendorName}][shipping]"]`).value) || 0;
                    const grandTotal = total + ppn + shipping;

                    row.querySelector(`input[name="summary[${vendorName}][total]"]`).value = total.toFixed(
                        2);
                    row.querySelector(`input[name="summary[${vendorName}][ppn]"]`).value = ppn.toFixed(2);
                    row.querySelector(`input[name="summary[${vendorName}][grand_total]"]`).value =
                        grandTotal.toFixed(2);
                });
            }

            function bindVendorNameSync() {
                document.querySelectorAll('.vendor-name-input').forEach(input => {
                    input.addEventListener('input', function() {
                        const vendorName = this.dataset.vendor;
                        const summaryRow = document.querySelector(
                            `.vendor-summary[data-vendor="${vendorName}"]`);
                        if (summaryRow) {
                            const summaryNameInput = summaryRow.querySelector(
                                '.vendor-name-summary');
                            summaryNameInput.value = this.value;
                        }
                    });
                });
            }

            bindPriceInputEvents(); // Bind initial events
            bindVendorNameSync(); // Bind vendor name sync events
        });
    </script>
@endpush
