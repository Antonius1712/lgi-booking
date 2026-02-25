@extends('layouts.app')

@section('content')

@php
$pillMap = [
    'booked'               => ['color' => '#7367f0', 'label' => 'Booked'],
    'waiting_confirmation' => ['color' => '#7367f0', 'label' => 'Waiting'],
    'reminder_sent_1'      => ['color' => '#ff9f43', 'label' => 'Reminder 1'],
    'reminder_sent_2'      => ['color' => '#ff9f43', 'label' => 'Reminder 2'],
    'reminder_sent_3'      => ['color' => '#ff9f43', 'label' => 'Reminder 3'],
    'departure'            => ['color' => '#28c76f', 'label' => 'On Trip',       'live' => true],
    'extending'            => ['color' => '#28c76f', 'label' => 'Extending',     'live' => true],
    'rescheduling'         => ['color' => '#00cfe8', 'label' => 'Rescheduling',  'live' => true],
    'driver_changed'       => ['color' => '#00cfe8', 'label' => 'Driver Changed'],
    'completed'            => ['color' => '#00cfe8', 'label' => 'Completed'],
    'cancelled'            => ['color' => '#ea5455', 'label' => 'Cancelled'],
    'auto_cancelled'       => ['color' => '#ea5455', 'label' => 'Auto Cancelled'],
];
$pill = $pillMap[$driverBooking->status] ?? ['color' => '#82868b', 'label' => ucfirst($driverBooking->status)];
@endphp

{{-- ── BREADCRUMB + HEADER ── --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <a href="{{ route('admin.driver-bookings.index') }}"
           style="font-size:.8rem;color:#7367f0;text-decoration:none">
            ← Back to Driver Bookings
        </a>
        <h4 class="fw-bold mb-0 mt-1" style="color:#2c2c5e">
            {{ $driverBooking->booking_number }}
        </h4>
        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
            <span style="display:inline-flex;align-items:center;gap:4px;
                         font-size:.72rem;font-weight:700;padding:4px 12px;border-radius:20px;
                         text-transform:uppercase;letter-spacing:.4px;
                         background:color-mix(in srgb, {{ $pill['color'] }} 12%, white);
                         color:{{ $pill['color'] }}">
                @if (!empty($pill['live']))
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $pill['color'] }};
                                 animation:home-pulse 1.5s infinite;display:inline-block"></span>
                @endif
                {{ $pill['label'] }}
            </span>
            <span style="font-size:.75rem;color:#b9b9c3">
                Created {{ $driverBooking->created_at?->format('d M Y, H:i') }}
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     TOP — INFO CARDS (3 columns)
══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Employee --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Employee</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="bkd-avatar" style="--av-bg:rgba(115,103,240,.12);--av-color:#7367f0">
                        {{ $driverBooking->user?->initials() }}
                    </div>
                    <div>
                        <div class="bkd-name">{{ $driverBooking->user?->Name ?? '-' }}</div>
                        <div class="bkd-sub">NIK: {{ $driverBooking->user_nik }}</div>
                        <div class="bkd-sub">{{ $driverBooking->user?->Email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Driver --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Driver</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="bkd-avatar" style="--av-bg:rgba(40,199,111,.12);--av-color:#28c76f">
                        {{ $driverBooking->driver?->initials() }}
                    </div>
                    <div>
                        <div class="bkd-name">{{ $driverBooking->driver?->Name ?? '-' }}</div>
                        <div class="bkd-sub">NIK: {{ $driverBooking->driver_nik }}</div>
                        <div class="bkd-sub">{{ $driverBooking->driver?->NoTelp }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Schedule</div>
                <div class="mt-2 d-flex flex-column gap-2">
                    <div class="bkd-info-row">
                        <i class="icon-base bx bx-calendar" style="color:#7367f0"></i>
                        <div>
                            <div class="bkd-info-label">Date</div>
                            <div class="bkd-info-value">
                                {{ $driverBooking->scheduled_pickup_date?->format('l, d F Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="bkd-info-row">
                        <i class="icon-base bx bx-time-five" style="color:#28c76f"></i>
                        <div>
                            <div class="bkd-info-label">Time</div>
                            <div class="bkd-info-value">
                                {{ $driverBooking->scheduled_pickup_time?->format('H:i') }}
                                –
                                {{ $driverBooking->scheduled_end_time?->format('H:i') }} WIB
                                <span class="bkd-badge">{{ $driverBooking->scheduled_duration }}m</span>
                            </div>
                        </div>
                    </div>
                    @if ($driverBooking->actual_pickup_at)
                        <div class="bkd-info-row">
                            <i class="icon-base bx bx-log-in-circle" style="color:#00cfe8"></i>
                            <div>
                                <div class="bkd-info-label">Actual Pickup</div>
                                <div class="bkd-info-value">
                                    {{ $driverBooking->actual_pickup_at->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Second row: Destination + Purpose + Cancellation --}}
<div class="row g-3 mb-4">
    <div class="col-md-{{ $driverBooking->cancelation_reason ? '4' : '6' }}">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Destination</div>
                <div class="mt-2 d-flex align-items-start gap-2">
                    <i class="icon-base bx bx-map" style="color:#7367f0;font-size:1.1rem;margin-top:2px;flex-shrink:0"></i>
                    <div class="bkd-name" style="line-height:1.4">
                        {{ $driverBooking->destination }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-{{ $driverBooking->cancelation_reason ? '4' : '6' }}">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Purpose of Trip</div>
                <div class="mt-2" style="font-size:.85rem;color:#5e5873;line-height:1.5">
                    {{ $driverBooking->purpose_of_trip ?? '-' }}
                </div>
            </div>
        </div>
    </div>
    @if ($driverBooking->cancelation_reason)
        <div class="col-md-4">
            <div class="card h-100" style="border-left:3px solid #ea5455">
                <div class="card-body">
                    <div class="bkd-section-label" style="color:#ea5455">Cancellation</div>
                    <div class="mt-2" style="font-size:.85rem;color:#5e5873">
                        {{ $driverBooking->cancelation_reason }}
                    </div>
                    @if ($driverBooking->cancelled_at)
                        <div class="bkd-sub mt-1">
                            {{ $driverBooking->cancelled_at->format('d M Y, H:i') }}
                            · by NIK {{ $driverBooking->cancelled_by }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════
     BOTTOM — STACKED ACTION PANELS + AUDIT LOG
══════════════════════════════════════════════════════ --}}
<div class="row g-4">

    {{-- LEFT: action panels --}}
    <div class="col-lg-7">

        @if ($isTerminal)
            <div class="card">
                <div class="card-body text-center py-5" style="color:#b9b9c3">
                    <i class="icon-base bx bx-lock-alt" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
                    <div style="font-size:.85rem">
                        This booking is <strong>{{ $pill['label'] }}</strong> — no further actions available.
                    </div>
                </div>
            </div>
        @endif

        {{-- ── CONFIRM DEPARTURE ── --}}
        @if ($canConfirm)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#28c76f">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-check-circle"></i>
                    <h6 class="mb-0 fw-semibold">Confirm Departure</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3 text-muted">
                        Mark this booking as departed. The actual pickup time will be recorded as <strong>now</strong>,
                        and the employee will receive a confirmation email.
                    </p>
                    <form action="{{ route('admin.driver-bookings.confirm', $driverBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm"
                                onclick="return confirm('Confirm departure for {{ $driverBooking->booking_number }}?')">
                            <i class="icon-base bx bx-check me-1"></i>Confirm Departure Now
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── EXTEND DURATION ── --}}
        @if ($canExtend)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#ff9f43">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-time-five"></i>
                    <h6 class="mb-0 fw-semibold">Extend Duration</h6>
                </div>
                <div class="card-body">
                    @error('extend_hours')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p class="small mb-3 text-muted">
                        Current end time:
                        <strong>{{ $driverBooking->scheduled_end_time?->format('H:i') }} WIB</strong>.
                        Select how many hours to add:
                    </p>
                    <form action="{{ route('admin.driver-bookings.extend', $driverBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ([1, 2, 3] as $h)
                                @php
                                    $newEnd = $driverBooking->scheduled_end_time?->copy()->addHours($h)->format('H:i');
                                @endphp
                                <button type="submit" name="extend_hours" value="{{ $h }}"
                                        class="bkd-extend-btn"
                                        onclick="return confirm('Extend trip by {{ $h }}h? New end: {{ $newEnd }} WIB')">
                                    <span class="bkd-extend-delta">+{{ $h }}h</span>
                                    <span class="bkd-extend-time">→ {{ $newEnd }}</span>
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── CHANGE DRIVER ── --}}
        @if ($canChange)
            <div class="card border-0 border-start border-3 border-warning rounded-3 mb-3">
                <div class="d-flex align-items-center gap-2 border-bottom border-warning-subtle bg-warning-subtle rounded-top py-3 px-3">
                    <i class="icon-base bx bx-transfer"></i>
                    <h6 class="mb-0 fw-semibold">Change Driver</h6>
                </div>
                <div class="card-body">
                    @error('driver_nik')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p class="small mb-3 text-muted">
                        Current driver: <strong>{{ $driverBooking->driver?->Name }}</strong>.
                        Select a replacement:
                    </p>
                    <form action="{{ route('admin.driver-bookings.change-driver', $driverBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-2 mb-3" id="driver-grid">
                            @foreach ($drivers as $d)
                                @php
                                    $isCurrent = $d->NIK === $driverBooking->driver_nik;
                                @endphp
                                <div class="col-sm-6 col-lg-4">
                                    <label class="bkd-driver-card {{ $isCurrent ? 'bkd-driver-current' : '' }}">
                                        <input type="radio" name="driver_nik" value="{{ $d->NIK }}"
                                               class="d-none"
                                               {{ $isCurrent ? 'checked' : '' }}>
                                        <div class="bkd-avatar bkd-av-sm"
                                             style="--av-bg:rgba(40,199,111,.12);--av-color:#28c76f">
                                            {{ $d->initials() }}
                                        </div>
                                        <div style="min-width:0">
                                            <div style="font-size:.8rem;font-weight:600;color:#2c2c5e;
                                                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                {{ $d->Name }}
                                            </div>
                                            @if ($isCurrent)
                                                <div style="font-size:.68rem;color:#7367f0;font-weight:600">
                                                    Current
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-sm"
                                style="background:#00cfe8;color:#fff;border:none"
                                onclick="return confirm('Change driver for this booking?')">
                            <i class="icon-base bx bx-transfer me-1"></i>Apply Driver Change
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── RESCHEDULE ── --}}
        @if ($canReschedule)
            <div class="card border-0 border-start border-3 border-primary rounded-3 mb-3">
                <div class="d-flex align-items-center gap-2 border-bottom border-primary-subtle bg-primary-subtle rounded-top py-3 px-3">
                    <i class="icon-base bx bx-calendar-edit"></i>
                    <h6 class="mb-0 fw-semibold">Reschedule</h6>
                </div>
                <div class="card-body">
                    @error('pickup_date')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p class="small mb-3 text-muted">
                        Current schedule:
                        <strong>
                            {{ $driverBooking->scheduled_pickup_date?->format('d M Y') }}
                            · {{ $driverBooking->scheduled_pickup_time?->format('H:i') }}–{{ $driverBooking->scheduled_end_time?->format('H:i') }} WIB
                        </strong>.
                        Driver availability will be checked against the new slot.
                    </p>
                    <form action="{{ route('admin.driver-bookings.reschedule', $driverBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size:.8rem">New Date</label>
                                <input type="date" name="pickup_date" class="form-control form-control-sm"
                                       value="{{ old('pickup_date', $driverBooking->scheduled_pickup_date?->format('Y-m-d')) }}"
                                       min="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.8rem">Pickup Time</label>
                                <input type="time" name="pickup_time" class="form-control form-control-sm"
                                       value="{{ old('pickup_time', $driverBooking->scheduled_pickup_time?->format('H:i')) }}"
                                       required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.8rem">End Time</label>
                                <input type="time" name="end_time" class="form-control form-control-sm"
                                       value="{{ old('end_time', $driverBooking->scheduled_end_time?->format('H:i')) }}"
                                       required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"
                                onclick="return confirm('Reschedule this booking? The driver will be notified.')">
                            <i class="icon-base bx bx-calendar-edit me-1"></i>Apply Reschedule
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── CANCEL BOOKING ── --}}
        @if ($canCancel)
            <div class="card border-0 border-start border-3 border-danger rounded-3 mb-3">
                <div class="d-flex align-items-center gap-2 border-bottom border-danger-subtle bg-danger-subtle rounded-top py-3 px-3">
                    <i class="icon-base bx bx-x-circle"></i>
                    <h6 class="mb-0 fw-semibold">Cancel Booking</h6>
                </div>
                <div class="card-body">
                    @error('cancelation_reason')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p class="small mb-3 text-muted">
                        This will notify the employee via email. Please provide a reason.
                    </p>

                    <form action="{{ route('admin.driver-bookings.cancel', $driverBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <textarea name="cancelation_reason" class="form-control form-control-sm"
                                      rows="3" placeholder="Reason for cancellation..." required
                                      style="font-size:.83rem">{{ old('cancelation_reason') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Cancel this booking? This cannot be undone.')">
                            <i class="icon-base bx bx-x me-1"></i>Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>

    {{-- RIGHT: audit log --}}
    <div class="col-lg-5">
        <div class="card" style="position:sticky;top:135px">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-history" style="color:#7367f0"></i>
                <h6 class="mb-0 fw-semibold">Activity Log</h6>
                <span class="badge ms-1" style="background:#e8e5ff;color:#7367f0;font-size:.7rem">
                    {{ $driverBooking->logs->count() }}
                </span>
            </div>
            <div class="card-body p-0" style="max-height:520px;overflow-y:auto">
                @forelse ($driverBooking->logs as $log)
                    @php
                        $color = \App\Models\BookingLog::actionColor($log->action);
                        $icon  = \App\Models\BookingLog::actionIcon($log->action);
                        $label = \App\Models\BookingLog::actionLabel($log->action);
                    @endphp
                    <div class="bkd-log-item">
                        <div class="bkd-log-icon" style="--log-color:{{ $color }}">
                            <i class="icon-base bx {{ $icon }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                                {{ $label }}
                            </div>

                            {{-- Status transition --}}
                            @if ($log->from_status && $log->to_status && $log->from_status !== $log->to_status)
                                <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                    <span style="background:#f3f2f7;padding:1px 7px;border-radius:20px">
                                        {{ ucfirst(str_replace('_', ' ', $log->from_status)) }}
                                    </span>
                                    →
                                    <span style="background:#f3f2f7;padding:1px 7px;border-radius:20px">
                                        {{ ucfirst(str_replace('_', ' ', $log->to_status)) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Payload details --}}
                            @if ($log->payload)
                                @if ($log->action === 'driver_changed')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        Driver: {{ $log->payload['old_driver_nik'] ?? '?' }}
                                        → {{ $log->payload['new_driver_nik'] ?? '?' }}
                                    </div>
                                @elseif ($log->action === 'extended')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        +{{ $log->payload['hours_added'] }}h ·
                                        {{ $log->payload['old_end'] ?? '' }} → {{ $log->payload['new_end'] ?? '' }}
                                    </div>
                                @elseif ($log->action === 'cancelled' && isset($log->payload['reason']))
                                    <div style="font-size:.72rem;color:#ea5455;margin-top:2px">
                                        "{{ \Str::limit($log->payload['reason'], 60) }}"
                                    </div>
                                @endif
                            @endif

                            {{-- Who + when --}}
                            <div style="font-size:.7rem;color:#b9b9c3;margin-top:3px">
                                {{ $performers[$log->performed_by] ?? ($log->performed_by ?? ucfirst($log->performed_by_role ?? 'system')) }}
                                · {{ $log->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5" style="font-size:.82rem">
                        <i class="icon-base bx bx-history"
                           style="font-size:1.8rem;display:block;margin-bottom:.4rem;color:#d6d6d6"></i>
                        No activity recorded yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
<script>
// Highlight selected driver card
$(document).on('change', 'input[name="driver_nik"]', function () {
    $('#driver-grid .bkd-driver-card').removeClass('bkd-driver-selected');
    $(this).closest('.bkd-driver-card').addClass('bkd-driver-selected');
});
</script>
@endsection