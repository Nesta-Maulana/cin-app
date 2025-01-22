<div class="modal fade" id="modalApprove{{ $approvalRequest->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ route('send-approval') }}" method="POST">
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
                        <button type="submit" class="btn btn-success" value="approve" name="approval_status">
                            <i class="fa fa-check me-1"></i> Approve
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

