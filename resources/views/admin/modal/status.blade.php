<div wire:ignore class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form wire:submit.prevent="updateStatus()" action="#">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Status</h5>
                    <button wire:click="closeModal" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-0 text-center">
                            <label class="switch">
                                <span class="switch-label">Non-Aktif</span>
                                <input wire:model="status" type="checkbox" class="switch-input" value=1>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button wire:click="closeModal" type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div wire:ignore class="modal fade" id="modalSelectedStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form wire:submit.prevent="updateSelectedStatus()" action="#">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Selected Status</h5>
                    <button wire:click="closeModal" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-0 text-center">
                            <label class="switch">
                                <span class="switch-label">Non-Aktif</span>
                                <input wire:model="status" type="checkbox" class="switch-input" value=1>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button wire:click="closeModal" type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>