@extends('layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#2c2c5e">Driver Bookings</h4>
        <p class="text-muted mb-0" style="font-size:.85rem">
            Manage all driver bookings — confirm, cancel, or change driver.
        </p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-error me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── FILTER BAR ── --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.driver-bookings.index') }}"
              class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       style="width:200px" placeholder="Booking no., name, dest..."
                       value="{{ request('search') }}">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">Status</label>
                <select name="status" class="form-select form-select-sm" style="width:160px">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}"
                            {{ in_array($s->value, (array) request('status', [])) ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s->value)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">Driver</label>
                <select name="driver_nik" class="form-select form-select-sm" style="width:160px">
                    <option value="">All Drivers</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->NIK }}"
                            {{ request('driver_nik') === $driver->NIK ? 'selected' : '' }}>
                            {{ $driver->Name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="icon-base bx bx-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.driver-bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                Reset
            </a>
            <div class="ms-auto">
                <a href="{{ route('admin.export.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="icon-base bx bx-download me-1"></i>Export
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    @foreach (['Booking No.','Employee','Driver','Date & Time','Destination','Status','Actions'] as $h)
                        <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    @php
                        $pillMap = [
                            'booked'               => ['c' => '#7367f0', 'l' => 'Booked'],
                            'waiting_confirmation' => ['c' => '#7367f0', 'l' => 'Waiting'],
                            'reminder_sent_1'      => ['c' => '#ff9f43', 'l' => 'Reminder 1'],
                            'reminder_sent_2'      => ['c' => '#ff9f43', 'l' => 'Reminder 2'],
                            'reminder_sent_3'      => ['c' => '#ff9f43', 'l' => 'Reminder 3'],
                            'departure'            => ['c' => '#28c76f', 'l' => 'On Trip',      'live' => true],
                            'extending'            => ['c' => '#28c76f', 'l' => 'Extending',    'live' => true],
                            'rescheduling'         => ['c' => '#00cfe8', 'l' => 'Rescheduling'],
                            'driver_changed'       => ['c' => '#00cfe8', 'l' => 'Driver Changed'],
                            'completed'            => ['c' => '#00cfe8', 'l' => 'Completed'],
                            'cancelled'            => ['c' => '#ea5455', 'l' => 'Cancelled'],
                            'auto_cancelled'       => ['c' => '#ea5455', 'l' => 'Auto Cancelled'],
                        ];
                        $pill = $pillMap[$booking->status] ?? ['c' => '#82868b', 'l' => ucfirst($booking->status)];

                        $isTerminal = in_array($booking->status, ['completed', 'cancelled', 'auto_cancelled']);
                        $isActive   = in_array($booking->status, ['departure', 'extending', 'rescheduling']);
                        $canConfirm = in_array($booking->status, ['booked','waiting_confirmation','reminder_sent_1','reminder_sent_2','reminder_sent_3']);
                        $canCancel  = ! $isTerminal;
                        $canChange  = ! $isTerminal && ! $isActive;
                    @endphp
                    <tr>
                        {{-- Booking No --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div style="font-size:.78rem;font-weight:700;color:#7367f0;letter-spacing:.3px">
                                <a href="{{ route('admin.driver-bookings.show', $booking) }}" title="View Detail">
                                    {{ $booking->booking_number }}
                                </a>
                            </div>
                            <div style="font-size:.7rem;color:#b9b9c3">
                                {{ $booking->created_at?->format('d M Y') }}
                            </div>
                        </td>

                        {{-- Employee --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:50%;
                                            background:rgba(115,103,240,.1);color:#7367f0;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:.7rem;font-weight:700;flex-shrink:0">
                                    {{ $booking->user?->initials() }}
                                </div>
                                <div>
                                    <div class="fw-bold text-black small">
                                        {{ $booking->user?->Name }}
                                    </div>
                                    <div style="font-size:.72rem;color:#b9b9c3">
                                        {{ $booking->user_nik }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Driver --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:50%;
                                            background:rgba(40,199,111,.1);color:#28c76f;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:.7rem;font-weight:700;flex-shrink:0">
                                    {{ $booking->driver?->initials() }}
                                </div>
                                <div>
                                    <div class="fw-bold text-black small">
                                        {{ $booking->driver?->Name }}
                                    </div>
                                    <div style="font-size:.72rem;color:#b9b9c3">
                                        {{ $booking->driver?->NIK }}
                                        /
                                        {{ $booking->driver?->NoTelp ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Date & Time --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem;white-space:nowrap">
                            <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                                {{ $booking->scheduled_pickup_date?->format('D, d M Y') }}
                            </div>
                            <div style="font-size:.75rem;color:#82868b">
                                {{ $booking->scheduled_pickup_time?->format('H:i') }}
                                –
                                {{ $booking->scheduled_end_time?->format('H:i') }}
                                · {{ $booking->scheduled_duration }}m
                            </div>
                        </td>

                        {{-- Destination --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem;max-width:180px">
                            <div style="font-size:.8rem;color:#2c2c5e;white-space:nowrap;
                                        overflow:hidden;text-overflow:ellipsis"
                                 title="{{ $booking->destination }}">
                                {{ $booking->destination }}
                            </div>
                            <div style="font-size:.72rem;color:#b9b9c3">
                                {{ Str::limit($booking->purpose_of_trip, 35) }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <span style="display:inline-flex;align-items:center;gap:3px;
                                         font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                         text-transform:uppercase;letter-spacing:.4px;
                                         background:color-mix(in srgb, {{ $pill['c'] }} 12%, white);
                                         color:{{ $pill['c'] }}">
                                @if (!empty($pill['live']))
                                    <span style="width:6px;height:6px;border-radius:50%;
                                                 background:{{ $pill['c'] }};
                                                 animation:home-pulse 1.5s infinite;display:inline-block"></span>
                                @endif
                                {{ $pill['l'] }}
                            </span>
                            @if ($booking->extention_requested_at && ! $booking->extension_approved_at)
                                <br>
                                <span style="display:inline-flex;align-items:center;gap:3px;
                                             font-size:.68rem;font-weight:700;padding:2px 7px;border-radius:20px;
                                             background:rgba(255,159,67,.15);color:#ff9f43;margin-top:3px">
                                    <i class="icon-base bx bx-bell" style="font-size:.75rem"></i>
                                    Ext. Pending
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div class="d-flex gap-1">
                                {{-- View Detail --}}
                                <a href="{{ route('admin.driver-bookings.show', $booking) }}"
                                   class="adm-act-btn adm-act-purple" title="View Detail">
                                    <i class="icon-base bx bx-show"></i>
                                </a>

                                {{-- Confirm --}}
                                @if ($canConfirm)
                                    <button type="button" class="adm-act-btn adm-act-green"
                                            title="Confirm Departure"
                                            onclick="openConfirm(
                                                {{ $booking->id }},
                                                '{{ $booking->booking_number }}',
                                                '{{ $booking->driver?->Name }}'
                                            )">
                                        <i class="icon-base bx bx-check"></i>
                                    </button>
                                @endif

                                {{-- Change Driver --}}
                                @if ($canChange)
                                    <button type="button" class="adm-act-btn adm-act-blue"
                                            title="Change Driver"
                                            onclick="openChangeDriver(
                                                {{ $booking->id }},
                                                '{{ $booking->booking_number }}',
                                                '{{ $booking->scheduled_pickup_date?->toDateString() }}',
                                                '{{ $booking->scheduled_pickup_time?->format('H:i') }}',
                                                '{{ $booking->scheduled_end_time?->format('H:i') }}',
                                                '{{ $booking->driver?->NIK }}',
                                                '{{ addslashes($booking->driver?->Name ?? '') }}'
                                            )">
                                        <i class="icon-base bx bx-transfer"></i>
                                    </button>
                                @endif

                                {{-- Cancel --}}
                                @if ($canCancel)
                                    <button type="button" class="adm-act-btn adm-act-red"
                                            title="Cancel Booking"
                                            onclick="openCancel({{ $booking->id }}, '{{ $booking->booking_number }}')">
                                        <i class="icon-base bx bx-x"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5" style="font-size:.82rem">
                            <i class="icon-base bx bx-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
                            No bookings found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($bookings->hasPages())
        <div class="card-footer">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

{{-- ── CANCEL MODAL ── --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Cancel Booking</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancel-form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p style="font-size:.85rem;color:#5e5873">
                        Cancel booking <strong id="cancel-booking-no"></strong>?
                        This will notify the employee via email.
                    </p>
                    <label class="form-label fw-semibold" style="font-size:.82rem">
                        Reason <span class="text-danger">*</span>
                    </label>
                    <textarea name="cancelation_reason" class="form-control" rows="3"
                              placeholder="Reason for cancellation..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="icon-base bx bx-x me-1"></i>Confirm Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── CONFIRM MODAL ── --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Confirm Departure</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="confirm-form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p style="font-size:.85rem;color:#5e5873;margin-bottom:0">
                        Confirm departure for booking <strong id="confirm-booking-no"></strong>?<br>
                        Driver <strong id="confirm-driver-name"></strong> will be notified via email.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="icon-base bx bx-check me-1"></i>Confirm Departure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── CHANGE DRIVER MODAL ── --}}
<div class="modal fade" id="changeDriverModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Change Driver</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="change-driver-form" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="driver_nik" id="selected-driver-nik">
                <div class="modal-body">
                    <p id="change-driver-desc" style="font-size:.85rem;color:#5e5873;margin-bottom:1rem"></p>
                    <div id="driver-list" class="row g-2">
                        <div class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm"></div>
                            Loading drivers…
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" id="change-driver-submit"
                            disabled onclick="openChangeDriverConfirm()">
                        <i class="icon-base bx bx-transfer me-1"></i>Change Driver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── CHANGE DRIVER CONFIRM MODAL ── --}}
<div class="modal fade" id="changeDriverConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:36px;height:36px;border-radius:50%;background:rgba(0,207,232,.12);
                                 display:flex;align-items:center;justify-content:center">
                        <i class="icon-base bx bx-transfer" style="color:#00cfe8;font-size:1.1rem"></i>
                    </span>
                    <h6 class="modal-title mb-0 fw-semibold" style="color:#2c2c5e">Confirm Driver Change</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted mb-3" style="font-size:.88rem">
                    You are about to assign a new driver to this booking.
                </p>
                <div class="rounded-2 p-3 mb-0" style="background:#f8f9fa;font-size:.85rem">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Current Driver</span>
                        <strong id="cd-confirm-current"></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">New Driver</span>
                        <strong id="cd-confirm-new" style="color:#00cfe8"></strong>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted" style="font-size:.82rem">
                    Both the employee and the new driver will be notified via email.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm px-3 py-2"
                        style="background:#00cfe8;color:#fff;border:none"
                        onclick="$('#change-driver-form').submit()">
                    <i class="icon-base bx bx-transfer me-1"></i>Apply Driver Change
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.adm-act-btn {
    width: 30px; height: 30px; border-radius: 7px; font-size: .85rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; border: 1px solid; transition: all .15s;
    background: transparent;
}
.adm-act-green { border-color: #28c76f; color: #28c76f; }
.adm-act-green:hover { background: #28c76f; color: #fff; }
.adm-act-blue  { border-color: #00cfe8; color: #00cfe8; }
.adm-act-blue:hover  { background: #00cfe8; color: #fff; }
.adm-act-red   { border-color: #ea5455; color: #ea5455; }
.adm-act-red:hover   { background: #ea5455; color: #fff; }
.adm-act-purple { border-color: #7367f0; color: #7367f0; }
.adm-act-purple:hover { background: #7367f0; color: #fff; }

.drv-select-card {
    border: 2px solid #ebe9f1; border-radius: 10px; padding: .7rem 1rem;
    cursor: pointer; transition: all .15s; display: flex; align-items: center; gap: .75rem;
}
.drv-select-card:hover:not(.drv-busy) { border-color: #7367f0; background: #f1f0ff; }
.drv-select-card.selected { border-color: #7367f0; background: #f1f0ff; }
.drv-select-card.drv-busy    { opacity: .5; cursor: not-allowed; background: #f8f7fa; }
.drv-select-card.drv-current { border-color: #7367f0; }
</style>

<script>
// ── Confirm modal ─────────────────────────────────────────────────────
function openConfirm(id, bookingNo, driverName) {
    const confirmUrl = @json(route('admin.driver-bookings.confirm', '__ID__'));
    $('#confirm-booking-no').text(bookingNo);
    $('#confirm-driver-name').text(driverName);
    $('#confirm-form').attr('action', confirmUrl.replace('__ID__', id));
    $('#confirmModal').modal('show');
}

// ── Cancel modal ──────────────────────────────────────────────────────
function openCancel(id, bookingNo) {
    const cancelUrl = @json(route('admin.driver-bookings.cancel', '__ID__'));
    $('#cancel-booking-no').text(bookingNo);
    $('#cancel-form').attr('action', cancelUrl.replace('__ID__', id));
    $('#cancelModal').modal('show');
}

// ── Change driver modal ───────────────────────────────────────────────
var _currentDriverNik  = '';
var _currentDriverName = '';
var _selectedDriverName = '';

function openChangeDriver(id, bookingNo, date, timeStart, timeEnd, currentDriverNik, currentDriverName) {
    _currentDriverNik  = currentDriverNik;
    _currentDriverName = currentDriverName;
    _selectedDriverName = '';

    $('#change-driver-desc').html(
        'Booking <strong>' + bookingNo + '</strong> · ' + date + ' · ' + timeStart + '–' + timeEnd
    );
    const changeDriverUrl = @json(route('admin.driver-bookings.change-driver', '__ID__'));
    $('#change-driver-form').attr('action', changeDriverUrl.replace('__ID__', id));
    $('#selected-driver-nik').val('');
    $('#change-driver-submit').prop('disabled', true);

    $('#driver-list').html(
        '<div class="text-center text-muted py-4 col-12">' +
        '<div class="spinner-border spinner-border-sm"></div> Loading drivers…</div>'
    );

    $.get('{{ route('admin.driver-bookings.available-drivers') }}', {
        date: date, time_start: timeStart, time_end: timeEnd, exclude_booking_id: id
    }, function (drivers) {
        var $list = $('#driver-list').empty();
        $.each(drivers, function (i, d) {
            var isCurrent = d.NIK === currentDriverNik;
            var bgColor   = d.busy ? 'rgba(234,84,85,.1)'  : 'rgba(115,103,240,.1)';
            var txtColor  = d.busy ? '#ea5455'              : '#7367f0';
            var avColor   = d.busy ? '#ea5455'              : '#28c76f';
            var statusTxt = d.busy ? '● Busy this slot'    : '● Available';
            var currentBadge = isCurrent
                ? '<div style="font-size:.68rem;color:#7367f0;font-weight:600">Current</div>'
                : '';

            var $col  = $('<div class="col-sm-6"></div>');
            var $card = $(
                '<div class="drv-select-card' + (d.busy ? ' drv-busy' : '') + (isCurrent ? ' drv-current' : '') + '" data-nik="' + d.NIK + '">' +
                    '<div style="width:36px;height:36px;border-radius:50%;background:' + bgColor + ';color:' + txtColor + ';' +
                         'display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0">' +
                        d.initials +
                    '</div>' +
                    '<div style="flex:1;min-width:0">' +
                        '<div style="font-size:.85rem;font-weight:600;color:#2c2c5e">' + d.Name + '</div>' +
                        '<div style="font-size:.75rem;color:' + avColor + '">' + statusTxt + '</div>' +
                        currentBadge +
                    '</div>' +
                '</div>'
            );

            if (!d.busy) {
                $card.on('click', function () { selectDriver(d.NIK, d.Name, this); });
            }

            $col.append($card);
            $list.append($col);
        });
    });

    $('#changeDriverModal').modal('show');
}

function selectDriver(nik, name, el) {
    $('.drv-select-card').removeClass('selected');
    $(el).addClass('selected');
    $('#selected-driver-nik').val(nik);
    _selectedDriverName = name;
    $('#change-driver-submit').prop('disabled', false);
}

function openChangeDriverConfirm() {
    $('#cd-confirm-current').text(_currentDriverName || '-');
    $('#cd-confirm-new').text(_selectedDriverName || 'Unknown');
    var cdModal = bootstrap.Modal.getInstance(document.getElementById('changeDriverModal'));
    if (cdModal) { cdModal.hide(); }
    setTimeout(function () {
        new bootstrap.Modal(document.getElementById('changeDriverConfirmModal')).show();
    }, 400);
}
</script>

@endsection