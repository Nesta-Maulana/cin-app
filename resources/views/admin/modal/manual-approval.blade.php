<div class="modal fade" id="modalApprove{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('item-request-approval') }}" method="POST">
                @csrf
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalCenterTitle">
                        Approval Request: {{ $item->request_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <p class="text-center">
                        <strong>Are you sure you want to approve item request with number {{ $item->request_number }}?</strong><br>
                    </p>
                    <input type="hidden" name="item_request_id" value="{{ $item->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <div>
                        <button type="submit" class="btn btn-outline-danger me-2" value="reject"
                            name="approval_status">
                            <i class="fa fa-ban me-1"></i> Reject
                        </button>
                        <button type="submit" class="btn btn-success" value="approved" name="approval_status">
                            <i class="fa fa-check me-1"></i> Approve
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
