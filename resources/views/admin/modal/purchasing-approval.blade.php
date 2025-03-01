<div class="modal fade" id="modalApprove{{ $approvalRequest->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <form id="approvalForm" action="{{ route('send-approval') }}" method="POST">
                @csrf
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalCenterTitle">
                        Approval Request: {{ $approvalRequest->approval->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <p class="text-center">
                        <strong>Are you sure you want to proceed with this action?</strong><br>
                        <span class="text-muted">You are about to {{ $approvalRequest->approval->name }} for this
                            data.</span>
                    </p>
                    <input type="hidden" name="approval_request_id" value="{{ $approvalRequest->id }}">
                    <div class="mb-1">
                        <label for="supplier_id" class="form-label">Select Supplier For Process Purchase Order</label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach ($approvalRequest->purchaseOrder->offers as $offer)
                                <option value="{{ $offer->supplier_id }}" data-offer-id="{{ $offer->id }}">
                                    {{ $offer->supplier->name }} ({{ $offer->currency }}
                                    {{ number_format($offer->grand_total, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('download-supplier-offers', $approvalRequest->purchaseOrder->id) }}"
                            class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i> Download Supplier Offers CSV
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <div>
                        <button type="button" id="rejectBtn" class="btn btn-outline-danger me-2">
                            <i class="fa fa-ban me-1"></i> Reject
                        </button>
                        <button type="button" id="approveBtn" class="btn btn-success">
                            <i class="fa fa-check me-1"></i> Approve
                        </button>
                    </div>
                </div>

                <!-- Hidden fields for form submission -->
                <input type="hidden" name="approval_status" id="approval_status" value="">
                <input type="hidden" name="selected_offer_id" id="selected_offer_id" value="">
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal elements
        const form = document.getElementById('approvalForm');
        const approveBtn = document.getElementById('approveBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        const supplierSelect = document.getElementById('supplier_id');
        const approvalStatusInput = document.getElementById('approval_status');
        const selectedOfferIdInput = document.getElementById('selected_offer_id');

        // Handler untuk tombol Approve
        approveBtn.addEventListener('click', async function(e) {
            e.preventDefault();

            // Validasi supplier harus dipilih
            const supplierId = supplierSelect.value;
            if (!supplierId) {
                alert('Please select a supplier first');
                return;
            }

            // Ambil ID offer dari data attribute pada option yang dipilih
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            const offerId = selectedOption.getAttribute('data-offer-id');

            try {
                // 1. Update purchase_order_supplier_offers - set is_selected = true
                await fetch('{{ route('update-supplier-offer-selection') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        offer_id: offerId,
                        purchase_order_id: '{{ $approvalRequest->purchaseOrder->id }}'
                    })
                });

                // 2. Submit form untuk approval
                approvalStatusInput.value = 'approve';
                selectedOfferIdInput.value = offerId;
                form.submit();

            } catch (error) {
                console.error('Error during approval process:', error);
                alert('An error occurred during the approval process. Please try again.');
            }
        });

        // Handler untuk tombol Reject
        rejectBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Langsung submit form untuk reject
            approvalStatusInput.value = 'reject';
            form.submit();
        });
    });
</script>
