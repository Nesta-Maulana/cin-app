<div>
    <div class="card-header border-bottom">
        <div class="row">

            <div class="col-md-3 mb-3">
                <select wire:model="school_id" id="UserPlan" class="form-select text-capitalize">
                    <option value="">Semua Markaz</option>
                    @foreach ($schools as $key => $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <select wire:model="grade_id" id="UserPlan" class="form-select text-capitalize">
                    <option value="">Semua Tingkat</option>
                    @foreach ($grade as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select wire:model="program_id" id="UserPlan" class="form-select text-capitalize">
                    <option value="">Semua Jenjang</option>
                    @foreach ($program as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select wire:model="major_id" id="UserPlan" class="form-select text-capitalize">
                    <option value="">Semua Jurusan</option>
                    @foreach ($major as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <select wire:model="year" id="UserRole" class="form-select text-capitalize">
                    <option value="">Semua Tahun</option>
                    @foreach (getYearRange() as $item)
                    @unless ($item >= $data->year )
                    <option value="{{ $item }}">{{ $item }}</option>
                    @endunless
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <select wire:model="classroom_id" class="form-select text-capitalize">
                    <option value="">Semua Kelas</option>
                    @if($year)
                    @foreach ($classroom as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                    @else
                    <option value="empty">Tanpa Kelas</option>
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
        <div class="my-1 text-center text-md-start">
            <label>
                <input wire:model.debounce.500ms="search" type="search" class="form-control" placeholder="Search..">
            </label>
        </div>
        <div class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
            @if ($selected)
            <div class="my-1 me-md-2">
                <span class="px-1">
                    {{ count($selected) }} data terpilih
                </span>
            </div>
            @endif
            <div class="my-1 me-md-2">
                <label>
                    <select wire:model="paginate" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table border-top">
            <thead>
                <tr>
                    @can('update-student-class')
                    <th>
                        <input style="width: 15px; height: 15px;" wire:model="selectAll" type="checkbox"
                            class="form-check-input">
                    </th>
                    @endcan
                    <th>Siswa</th>
                    <th>ID</th>
                    <th>TTL</th>
                    <th>Kelas Terkini</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($table as $key => $item)
                <tr wire:key="row{{ $item->id }}">
                    @can('update-student-class')
                    <td style="width: 5%">
                        <input wire:model="selected" name="student[]" value="{{ $item->id }}" type="checkbox"
                            class="form-check-input" style="width: 15px; height: 15px;">
                    </td>
                    @endcan

                    <td class="col-3">
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    @if($item->photo)
                                    <img src="{{ imageAsset($item->photo) }}" alt="Avatar" class="rounded-circle">
                                    @else
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ userInitial($item->user->id) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-truncate">
                                    <span class="fw-semibold">
                                        {{ $item->name }}
                                    </span>
                                </a>
                                <small class="text-muted">{{ $item->user->email }}</small>
                            </div>
                        </div>
                    </td>

                    <td class="col-2">{{ $item->code }}</td>

                    <td class="col-3">
                        {{ $item->tempat_lahir }}, {{ dateDMY($item->tanggal_lahir) }}
                    </td>

                    <td class="col-2">
                        @if ($item->classroom_id)
                        {{ $item->classroom->name }}
                        <div>
                            <small class="text-muted">
                                TA. {{ $item->classroom->year }} @if($item->classroom->major) - {{
                                $item->classroom->major->name }} @endif
                            </small>
                        </div>
                        @endif
                    </td>

                    <td class="col-2 text-center">
                        {!! isActive($item->user->status) !!}
                    </td>

                </tr>
                @empty
                <div class="text-center col-md-7 mx-auto px-3 pt-3">
                    <div class="alert alert-secondary">
                        Data tidak ditemukan
                    </div>
                </div>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
        <div class="align-self-start my-2 d-none d-md-block text-muted">
            <small>
                Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
            </small>
        </div>
        {{ $table->links() }}
    </div>
</div>