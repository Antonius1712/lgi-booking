@extends('layouts.app')

@section('content')

@php
$pillMap = [
    'booked'    => ['color' => '#7367f0', 'label' => 'Booked'],
    'in_use' => ['color' => '#28c76f', 'label' => 'In Use', 'live' => true],
    'completed' => ['color' => '#00cfe8', 'label' => 'Completed'],
    'cancelled' => ['color' => '#ea5455', 'label' => 'Cancelled'],
];
$pill = $pillMap[$meetingRoomBooking->status] ?? ['color' => '#82868b', 'label' => ucfirst($meetingRoomBooking->status)];
@endphp

{{-- ── HEADER ── --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <a href="{{ route('admin.meeting-room-bookings.index') }}"
           style="font-size:.8rem;color:#7367f0;text-decoration:none">
            ← Back to Meeting Room Bookings
        </a>
        <h4 class="fw-bold mb-0 mt-1" style="color:#2c2c5e">
            Booking #{{ $meetingRoomBooking->id }}
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
                Created {{ $meetingRoomBooking->created_at?->format('d M Y, H:i') }}
            </span>
        </div>
    </div>
</div>

{{-- ── TOP INFO CARDS ── --}}
<div class="row g-3 mb-4">

    {{-- Employee --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Employee</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="bkd-avatar" style="--av-bg:rgba(115,103,240,.12);--av-color:#7367f0">
                        {{ $meetingRoomBooking->user?->initials() }}
                    </div>
                    <div>
                        <div class="bkd-name">{{ $meetingRoomBooking->user?->Name ?? '-' }}</div>
                        <div class="bkd-sub">NIK: {{ $meetingRoomBooking->nik }}</div>
                        <div class="bkd-sub">{{ $meetingRoomBooking->user?->Email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Room --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Meeting Room</div>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="bkd-avatar" style="--av-bg:rgba(0,207,232,.12);--av-color:#00cfe8">
                        <i class="icon-base bx bx-door-open" style="font-size:1.1rem"></i>
                    </div>
                    <div>
                        <div class="bkd-name">{{ $meetingRoomBooking->meetingRoom?->name ?? '-' }}</div>
                        <div class="bkd-sub">
                            <i class="icon-base bx bx-map-pin" style="font-size:.75rem"></i>
                            {{ $meetingRoomBooking->meetingRoom?->location?->name ?? '-' }}
                        </div>
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
                                {{ $meetingRoomBooking->booking_date?->format('l, d F Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="bkd-info-row">
                        <i class="icon-base bx bx-time-five" style="color:#28c76f"></i>
                        <div>
                            <div class="bkd-info-label">Time</div>
                            <div class="bkd-info-value">
                                {{ $meetingRoomBooking->start_time?->format('H:i') }}
                                –
                                {{ $meetingRoomBooking->end_time?->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── SECONDARY INFO ROW ── --}}
<div class="row g-3 mb-4">
    @php
        $hasCols = 12 / (2 + ($meetingRoomBooking->cancelation_reason ? 1 : 0) + ($meetingRoomBooking->usage_type ? 0 : 0));
    @endphp

    <div class="col-md-{{ $meetingRoomBooking->cancelation_reason ? '4' : '6' }}">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Description / Purpose</div>
                <div class="mt-2" style="font-size:.85rem;color:#5e5873;line-height:1.5">
                    {{ $meetingRoomBooking->description ?? '-' }}
                </div>
                @if ($meetingRoomBooking->usage_type)
                    <div class="bkd-sub mt-1">Type: {{ $meetingRoomBooking->usage_type }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-{{ $meetingRoomBooking->cancelation_reason ? '4' : '6' }}">
        <div class="card h-100">
            <div class="card-body">
                <div class="bkd-section-label">Guest Emails</div>
                <div class="mt-2">
                    @forelse ($meetingRoomBooking->guest_emails ?? [] as $email)
                        <span style="display:inline-block;font-size:.75rem;background:#f3f2f7;
                                     color:#5e5873;padding:2px 10px;border-radius:20px;margin:2px">
                            {{ $email }}
                        </span>
                    @empty
                        <span style="font-size:.82rem;color:#b9b9c3">No guests invited.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if ($meetingRoomBooking->cancelation_reason)
        <div class="col-md-4">
            <div class="card h-100" style="border-left:3px solid #ea5455">
                <div class="card-body">
                    <div class="bkd-section-label" style="color:#ea5455">Cancellation</div>
                    <div class="mt-2" style="font-size:.85rem;color:#5e5873">
                        {{ $meetingRoomBooking->cancelation_reason }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ── ACTION PANELS + AUDIT LOG ── --}}
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
                    <p style="font-size:.83rem;color:#5e5873;margin-bottom:1rem">
                        Current end time:
                        <strong>{{ $meetingRoomBooking->end_time?->format('H:i') }} WIB</strong>.
                        Select how many hours to add:
                    </p>
                    <form action="{{ route('admin.meeting-room-bookings.extend', $meetingRoomBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ([1, 2, 3] as $h)
                                @php
                                    $newEnd = $meetingRoomBooking->end_time?->copy()->addHours($h)->format('H:i');
                                @endphp
                                <button type="submit" name="extend_hours" value="{{ $h }}"
                                        class="bkd-extend-btn"
                                        onclick="return confirm('Extend by {{ $h }}h? New end: {{ $newEnd }} WIB')">
                                    <span class="bkd-extend-delta">+{{ $h }}h</span>
                                    <span class="bkd-extend-time">→ {{ $newEnd }}</span>
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── CHANGE ROOM ── --}}
        @if ($canChange)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#00cfe8">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-door-open"></i>
                    <h6 class="mb-0 fw-semibold">Change Room</h6>
                </div>
                <div class="card-body">
                    @error('meeting_room_id')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p style="font-size:.83rem;color:#5e5873;margin-bottom:1rem">
                        Current room: <strong>{{ $meetingRoomBooking->meetingRoom?->name }}</strong>.
                        Select a new room for the same date &amp; time:
                    </p>
                    <form action="{{ route('admin.meeting-room-bookings.change-room', $meetingRoomBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <select name="meeting_room_id" class="form-select form-select-sm" required>
                                <option value="">Select room...</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ $room->id === $meetingRoomBooking->meeting_room_id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                        @if ($room->location)· {{ $room->location->name }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm"
                                style="background:#00cfe8;color:#fff;border:none"
                                onclick="return confirm('Change room for this booking?')">
                            <i class="icon-base bx bx-door-open me-1"></i>Apply Room Change
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── CHANGE TIME ── --}}
        @if ($canChange)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#7367f0">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-timer"></i>
                    <h6 class="mb-0 fw-semibold">Change Time Slot</h6>
                </div>
                <div class="card-body">
                    @error('start_time')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p style="font-size:.83rem;color:#5e5873;margin-bottom:1rem">
                        Current time:
                        <strong>
                            {{ $meetingRoomBooking->start_time?->format('H:i') }}–{{ $meetingRoomBooking->end_time?->format('H:i') }} WIB
                        </strong>. Room availability will be checked.
                    </p>
                    <form action="{{ route('admin.meeting-room-bookings.change-time', $meetingRoomBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.8rem">Start Time</label>
                                <input type="time" name="start_time" class="form-control form-control-sm"
                                       value="{{ old('start_time', $meetingRoomBooking->start_time?->format('H:i')) }}"
                                       required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.8rem">End Time</label>
                                <input type="time" name="end_time" class="form-control form-control-sm"
                                       value="{{ old('end_time', $meetingRoomBooking->end_time?->format('H:i')) }}"
                                       required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"
                                onclick="return confirm('Change the time slot for this booking?')">
                            <i class="icon-base bx bx-timer me-1"></i>Apply Time Change
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── RESCHEDULE ── --}}
        @if ($canReschedule)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#ff9f43">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-calendar-edit"></i>
                    <h6 class="mb-0 fw-semibold">Reschedule</h6>
                </div>
                <div class="card-body">
                    @error('booking_date')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p style="font-size:.83rem;color:#5e5873;margin-bottom:1rem">
                        Current schedule:
                        <strong>
                            {{ $meetingRoomBooking->booking_date?->format('d M Y') }}
                            · {{ $meetingRoomBooking->start_time?->format('H:i') }}–{{ $meetingRoomBooking->end_time?->format('H:i') }} WIB
                        </strong>.
                        Room availability will be checked.
                    </p>
                    <form action="{{ route('admin.meeting-room-bookings.reschedule', $meetingRoomBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size:.8rem">New Date</label>
                                <input type="date" name="booking_date" class="form-control form-control-sm"
                                       value="{{ old('booking_date', $meetingRoomBooking->booking_date?->format('Y-m-d')) }}"
                                       min="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.8rem">Start Time</label>
                                <input type="time" name="start_time" class="form-control form-control-sm"
                                       value="{{ old('start_time', $meetingRoomBooking->start_time?->format('H:i')) }}"
                                       required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.8rem">End Time</label>
                                <input type="time" name="end_time" class="form-control form-control-sm"
                                       value="{{ old('end_time', $meetingRoomBooking->end_time?->format('H:i')) }}"
                                       required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm"
                                onclick="return confirm('Reschedule this booking?')">
                            <i class="icon-base bx bx-calendar-edit me-1"></i>Apply Reschedule
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── UPDATE GUESTS ── --}}
        @if (! $isTerminal)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#7367f0">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-envelope"></i>
                    <h6 class="mb-0 fw-semibold">Guest Emails</h6>
                </div>
                <div class="card-body">
                    @error('guest_emails')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p style="font-size:.83rem;color:#5e5873;margin-bottom:1rem">
                        Enter email addresses separated by commas or spaces.
                    </p>
                    <form action="{{ route('admin.meeting-room-bookings.update-guests', $meetingRoomBooking) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <textarea name="guest_emails" class="form-control form-control-sm"
                                      rows="3" style="font-size:.83rem"
                                      placeholder="email1@example.com, email2@example.com">{{ old('guest_emails', implode(', ', $meetingRoomBooking->guest_emails ?? [])) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="icon-base bx bx-envelope me-1"></i>Update Guests
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── CANCEL BOOKING ── --}}
        @if ($canCancel)
            <div class="card mb-3 bkd-action-card" style="--ac-color:#ea5455">
                <div class="card-header bkd-action-header py-3">
                    <i class="icon-base bx bx-x-circle"></i>
                    <h6 class="mb-0 fw-semibold">Cancel Booking</h6>
                </div>
                <div class="card-body">
                    @error('cancelation_reason')
                        <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem">
                            <i class="icon-base bx bx-error me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    <p style="font-size:.83rem;color:#5e5873;margin-bottom:1rem">
                        This will notify the employee via email. Please provide a reason.
                    </p>
                    <form action="{{ route('admin.meeting-room-bookings.cancel', $meetingRoomBooking) }}" method="POST">
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
                    {{ $meetingRoomBooking->logs->count() }}
                </span>
            </div>
            <div class="card-body p-0" style="max-height:520px;overflow-y:auto">
                @forelse ($meetingRoomBooking->logs as $log)
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

                            @if ($log->payload)
                                @if ($log->action === 'room_changed')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        Room ID: {{ $log->payload['old_room_id'] ?? '?' }}
                                        → {{ $log->payload['new_room_id'] ?? '?' }}
                                    </div>
                                @elseif ($log->action === 'time_changed')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        {{ $log->payload['old_start'] ?? '' }}–{{ $log->payload['old_end'] ?? '' }}
                                        → {{ $log->payload['new_start'] ?? '' }}–{{ $log->payload['new_end'] ?? '' }}
                                    </div>
                                @elseif ($log->action === 'extended')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        +{{ $log->payload['hours_added'] }}h ·
                                        {{ $log->payload['old_end'] ?? '' }} → {{ $log->payload['new_end'] ?? '' }}
                                    </div>
                                @elseif ($log->action === 'rescheduled')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        {{ $log->payload['old_date'] ?? '' }}
                                        → {{ $log->payload['new_date'] ?? '' }}
                                    </div>
                                @elseif ($log->action === 'guests_updated')
                                    <div style="font-size:.72rem;color:#82868b;margin-top:2px">
                                        {{ $log->payload['old_count'] ?? 0 }} → {{ $log->payload['new_count'] ?? 0 }} guests
                                    </div>
                                @elseif ($log->action === 'cancelled' && isset($log->payload['reason']))
                                    <div style="font-size:.72rem;color:#ea5455;margin-top:2px">
                                        "{{ \Str::limit($log->payload['reason'], 60) }}"
                                    </div>
                                @endif
                            @endif

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
