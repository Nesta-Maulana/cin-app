<div class="modal fade" id="modalDelete{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="{{ $updateRoute }}" method="POST">
                @csrf
                @method('DELETE')

                <!-- Modal Header -->
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="modalCenterTitle">
                        <i class="fa fa-exclamation-circle me-2"></i> Delete Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body text-center ">
                    <p class="">
                        <strong>Are you sure?</strong><br>
                        <span class="text-muted">You are about to delete this data.</span>
                    </p>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash me-1"></i> Yes, Delete!
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
