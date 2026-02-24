@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h4 class="mb-1 fw-bold" style="color:#2c2c5e">Reports & Export</h4>
    <p class="text-muted mb-0" style="font-size:.85rem">
        Download reports in Excel or PDF format.
    </p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">

    {{-- ── Driver Booking Report ── --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(115,103,240,.12);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="icon-base bx bx-car" style="color:#7367f0;font-size:1.2rem"></i>
                </div>
                <h6 class="mb-0 fw-semibold">Driver Booking Report</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.export.download') }}" method="POST">
                    @csrf
                    <input type="hidden" name="report_type" value="driver">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.82rem">Date Range</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                   placeholder="From">
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                   placeholder="To">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.82rem">Driver (optional)</label>
                        <select name="driver_nik" class="form-select form-select-sm">
                            <option value="">All Drivers</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->NIK }}">{{ $driver->Name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="format" value="excel"
                                class="btn btn-sm btn-success flex-fill">
                            <i class="icon-base bx bx-spreadsheet me-1"></i>Excel
                        </button>
                        <button type="submit" name="format" value="pdf"
                                class="btn btn-sm btn-danger flex-fill">
                            <i class="icon-base bx bxs-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Meeting Room Report ── --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,207,232,.12);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="icon-base bx bx-door-open" style="color:#00cfe8;font-size:1.2rem"></i>
                </div>
                <h6 class="mb-0 fw-semibold">Meeting Room Report</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.export.download') }}" method="POST">
                    @csrf
                    <input type="hidden" name="report_type" value="meeting_room">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.82rem">Date Range</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                   placeholder="From">
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                   placeholder="To">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.82rem">Room (optional)</label>
                        <select name="room_id" class="form-select form-select-sm">
                            <option value="">All Rooms</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">
                                    {{ $room->name }}
                                    @if ($room->location)· {{ $room->location->name }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="format" value="excel"
                                class="btn btn-sm btn-success flex-fill">
                            <i class="icon-base bx bx-spreadsheet me-1"></i>Excel
                        </button>
                        <button type="submit" name="format" value="pdf"
                                class="btn btn-sm btn-danger flex-fill">
                            <i class="icon-base bx bxs-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Feedback / Rating Report ── --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,159,67,.12);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="icon-base bx bx-star" style="color:#ff9f43;font-size:1.2rem"></i>
                </div>
                <h6 class="mb-0 fw-semibold">Feedback / Rating Report</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.export.download') }}" method="POST">
                    @csrf
                    <input type="hidden" name="report_type" value="feedback">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.82rem">Date Range</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                   placeholder="From">
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                   placeholder="To">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.82rem">Driver (optional)</label>
                        <select name="driver_nik" class="form-select form-select-sm">
                            <option value="">All Drivers</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->NIK }}">{{ $driver->Name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="format" value="excel"
                                class="btn btn-sm btn-success flex-fill">
                            <i class="icon-base bx bx-spreadsheet me-1"></i>Excel
                        </button>
                        <button type="submit" name="format" value="pdf"
                                class="btn btn-sm btn-danger flex-fill">
                            <i class="icon-base bx bxs-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
