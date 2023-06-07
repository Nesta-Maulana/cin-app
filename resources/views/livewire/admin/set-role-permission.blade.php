<div class="my-3 col-12">
    <h5>Role Permissions</h5>
    <div class="table-responsive">
        <table class="table table-flush-spacing">
            <tbody>
                <tr>
                    <td class="text-nowrap fw-semibold">All Access
                        <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                            aria-label="Allows a full access to the system"
                            data-bs-original-title="Allows a full access to the system"></i>
                    </td>
                    <td>
                        <div class="form-check">
                            <input wire:model="selectAll" class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                <small>Select All</small>
                            </label>
                        </div>
                    </td>
                </tr>

                @foreach ($permission as $key => $permission)
                <tr>
                    <td class="text-nowrap fw-semibold">{{ $key }}</td>
                    <td>
                        <div class="d-flex text-nowrap">
                            @foreach ($permission as $key => $item)
                            <div class="form-check me-3 me-lg-5" style="width: 25%">
                                <input wire:model="selected" class="form-check-input" name="permission[]"
                                    value="{{ $item['name'] }}" type="checkbox" id="{{ $item['name'] }}">
                                <label class="form-check-label" for="{{ $item['name'] }}">
                                    <small>{{ $item['name'] }}</small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>