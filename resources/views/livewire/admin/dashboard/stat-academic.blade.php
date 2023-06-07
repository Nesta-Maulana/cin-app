<div class="card h-100">
    <div class="card-header">
        <div class="d-flex justify-content-between mb-3">
            <h5 class="card-title mb-0">Statistik Umum</h5>
        </div>
    </div>
    <div class="card-body">
        <div class="row gy-3">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center">
                    <div class="badge rounded-pill bg-label-primary me-3 p-2">
                        <i class="ti ti-building ti-sm"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $school->count() }}</h5>
                        <small>Markaz</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center">
                    <div class="badge rounded-pill bg-label-success me-3 p-2">
                        <i class="ti ti-stack ti-sm"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $program->count() }}</h5>
                        <small>Jenjang</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center">
                    <div class="badge rounded-pill bg-label-info me-3 p-2">
                        <i class="ti ti-users ti-sm"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ numberFormat($studentCount) }}</h5>
                        <small>Santri</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center">
                    <div class="badge rounded-pill bg-label-danger me-3 p-2">
                        <i class="ti ti-school ti-sm"></i>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ numberFormat($alumniCount) }}</h5>
                        <small>Alumni</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>