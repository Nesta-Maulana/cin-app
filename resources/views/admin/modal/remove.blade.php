<div wire:ignore.self class="modal fade" id="modalRemove{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="#">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Apakah Anda yakin akan menghapus data ini?
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a wire:click='remove({{ $item->id }})' type="button" class="btn btn-label-danger"
                        data-bs-dismiss="modal">
                        Ya! Hapus
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>