<div wire:ignore class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form wire:submit.prevent="updatePassword({{ $modelId }})" action="#">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Password</h5>
                    <button wire:click="closeModal" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-0 form-password-toggle">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" wire:model="password"
                                    placeholder="Enter your new password" aria-describedby="password" required
                                    autocomplete="current-password" value="pin12345" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            <div class="form-text">*Default: {{ $password }}</div>
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