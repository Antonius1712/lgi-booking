@extends('layouts.app')

@section('content')

{{-- ── ADMIN BANNER ── --}}
<div class="welcome-banner welcome-banner-admin mb-4">
    <div style="position:relative;z-index:1">
        <p class="welcome-title">
            Good {{ now()->format('G') < 12 ? 'Morning' : (now()->format('G') < 17 ? 'Afternoon' : 'Evening' ) }}, {{
                    explode(' ', $user->Name)[0] }} 🛡️
        </p>
        <p class="welcome-sub">
            System overview — <strong>{{ $stats['total_driver_bookings'] }} driver bookings</strong> today,
                    <strong>{{ $stats['on_trip'] }} on trip</strong> right now.
        </p>
        <p class="welcome-date"><i class="icon-base bx bx-calendar me-1"></i>{{ now()->format('l, d F Y') }}</p>
    </div>
    <i class="icon-base bx bx-shield-check welcome-icon"></i>
</div>

{{-- ── STAT CARDS ── --}}
<p class="home-section-title">Today's Overview</p>
<div class="home-stat-grid mb-4">
    <div class="card home-stat-card">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="home-stat-icon home-si-dark"><i class="icon-base bx bx-calendar-check"></i></div>
            <div>
                <div class="home-stat-value">{{ $stats['total_driver_bookings'] }}</div>
                <div class="home-stat-label">Driver Bookings</div>
            </div>
        </div>
    </div>
    <div class="card home-stat-card">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="home-stat-icon home-si-green"><i class="icon-base bx bx-car"></i></div>
            <div>
                <div class="home-stat-value">{{ $stats['on_trip'] }}</div>
                <div class="home-stat-label">On Trip Now</div>
            </div>
        </div>
    </div>
    <div class="card home-stat-card">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="home-stat-icon home-si-purple"><i class="icon-base bx bx-hourglass"></i></div>
            <div>
                <div class="home-stat-value">{{ $stats['upcoming'] }}</div>
                <div class="home-stat-label">Upcoming</div>
            </div>
        </div>
    </div>
    <div class="card home-stat-card">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="home-stat-icon home-si-cyan"><i class="icon-base bx bx-check-circle"></i></div>
            <div>
                <div class="home-stat-value">{{ $stats['completed_today'] }}</div>
                <div class="home-stat-label">Completed</div>
            </div>
        </div>
    </div>
    <div class="card home-stat-card">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="home-stat-icon home-si-red"><i class="icon-base bx bx-x-circle"></i></div>
            <div>
                <div class="home-stat-value">{{ $stats['cancelled_today'] }}</div>
                <div class="home-stat-label">Cancelled</div>
            </div>
        </div>
    </div>
    <div class="card home-stat-card">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="home-stat-icon home-si-orange"><i class="icon-base bx bx-door-open"></i></div>
            <div>
                <div class="home-stat-value">{{ $stats['rooms_in_use'] }}</div>
                <div class="home-stat-label">Rooms In Use</div>
            </div>
        </div>
    </div>
</div>

{{-- ── MAIN GRID ── --}}
<div class="home-main-grid">

    {{-- LEFT COLUMN --}}
    <div>

        {{-- DRIVER AVAILABILITY --}}
        <p class="home-section-title">Driver Availability — Right Now</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold">All Drivers</h6>
                <a href="{{ route('admin.drivers.index') }}" class="home-widget-link">Manage →</a>
            </div>
            <div class="card-body">
                <div class="home-driver-avail-grid">
                    @forelse ($drivers as $driver)
                    @php
                    $statusClass = $driver['is_on_trip'] ? 'busy' : 'available';
                    @endphp
                    <div class="home-driver-avail-card home-drv-{{ $statusClass }}">
                        <div class="home-drv-avatar home-drv-av-{{ $driver['is_on_trip'] ? 'bz' : 'av' }}">
                            {{ \Str::upper(\Str::substr(explode(' ', $driver['Name'])[0], 0, 1))
                            . \Str::upper(\Str::substr(explode(' ', $driver['Name'])[1] ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <div class="home-drv-name">{{ $driver['Name'] }}</div>
                            @if ($driver['is_on_trip'])
                            <div class="home-drv-status-text home-drv-bz">
                                <i class="icon-base bx bx-car me-1"></i>On Trip · ends {{ $driver['ends_at'] }}
                            </div>
                            @else
                            <div class="home-drv-status-text home-drv-av">
                                <i class="icon-base bx bx-check-circle me-1"></i>Available
                            </div>
                            @endif
                        </div>
                        <div class="home-drv-dot home-drv-dot-{{ $driver['is_on_trip'] ? 'bz' : 'av' }}"></div>
                    </div>
                    @empty
                    <div class="home-empty-sm" style="grid-column:1/-1">
                        <i class="icon-base bx bx-car"></i>No drivers found
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RECENT DRIVER BOOKINGS TABLE --}}
        <p class="home-section-title">Recent Driver Bookings</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold">Latest Activity</h6>
                <a href="{{ route('admin.driver-bookings.index') }}" class="home-widget-link">View all →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Employee</th>
                            <th class="d-none d-md-table-cell"
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Driver</th>
                            <th
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Time</th>
                            <th class="d-none d-md-table-cell"
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Destination</th>
                            <th
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Status</th>
                            <th style="padding:.75rem 1rem"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentDriverBookings as $booking)
                        <tr>
                            <td style="vertical-align:middle;padding:.75rem 1rem">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="home-mini-av">{{ $booking->user?->initials() }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#2c2c5e;font-size:.8rem">{{
                                            $booking->user?->Name }}</div>
                                        <div style="font-size:.7rem;color:#b9b9c3">NIK: {{ $booking->user_nik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell" style="vertical-align:middle;padding:.75rem 1rem">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="home-mini-av home-mini-av-green">{{ $booking->driver?->initials() }}
                                    </div>
                                    <div style="font-weight:600;color:#2c2c5e;font-size:.8rem">
                                        {{ $booking->driver?->Name }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"
                                style="vertical-align:middle;padding:.75rem 1rem;white-space:nowrap;font-size:.8rem">
                                {{ $booking->scheduled_pickup_time?->format('H:i') }}–{{
                                $booking->scheduled_end_time?->format('H:i') }}
                            </td>
                            <td class="d-none d-md-table-cell text-center"
                                style="vertical-align:middle;padding:.75rem 1rem;max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.8rem">
                                {{ $booking->destination }}
                            </td>
                            <td class="text-center" style="vertical-align:middle;padding:.75rem 1rem">
                                @php
                                $pillMap = [
                                'booked' => ['class' => 'booked', 'label' => 'Booked'],
                                'waiting_confirmation'=> ['class' => 'booked', 'label' => 'Waiting'],
                                'reminder_sent_1' => ['class' => 'booked', 'label' => 'Reminder 1'],
                                'reminder_sent_2' => ['class' => 'booked', 'label' => 'Reminder 2'],
                                'reminder_sent_3' => ['class' => 'booked', 'label' => 'Reminder 3'],
                                'departure' => ['class' => 'departure', 'label' => 'On Trip', 'live' => true],
                                'extending' => ['class' => 'departure', 'label' => 'Extending', 'live' => true],
                                'rescheduling' => ['class' => 'departure', 'label' => 'Rescheduling', 'live' => true],
                                'completed' => ['class' => 'completed', 'label' => 'Completed'],
                                'driver_changed' => ['class' => 'completed', 'label' => 'Driver Changed'],
                                'cancelled' => ['class' => 'cancelled', 'label' => 'Cancelled'],
                                'auto_cancelled' => ['class' => 'cancelled', 'label' => 'Auto Cancelled'],
                                ];
                                $pill = $pillMap[$booking->status] ?? ['class' => 'booked', 'label' =>
                                ucfirst($booking->status)];
                                @endphp
                                <span class="home-status-pill home-pill-{{ $pill['class'] }}">
                                    @if(!empty($pill['live']))
                                    <span class="home-live-dot" style="width:6px;height:6px;margin-right:3px"></span>
                                    @endif
                                    {{ $pill['label'] }}
                                </span>
                            </td>
                            <td style="vertical-align:middle;padding:.75rem 1rem">
                                <button type="button" class="home-act-btn drv-view-btn" title="View Detail"
                                    data-booking-number="{{ $booking->booking_number }}"
                                    data-status="{{ $booking->status }}" data-pill-class="{{ $pill['class'] }}"
                                    data-pill-label="{{ $pill['label'] }}"
                                    data-pill-live="{{ !empty($pill['live']) ? '1' : '0' }}"
                                    data-user-name="{{ $booking->user?->Name }}"
                                    data-user-nik="{{ $booking->user_nik }}"
                                    data-driver-name="{{ $booking->driver?->Name }}"
                                    data-driver-nik="{{ $booking->driver_nik }}"
                                    data-date="{{ $booking->scheduled_pickup_date?->format('D, d M Y') }}"
                                    data-pickup="{{ $booking->scheduled_pickup_time?->format('H:i') }}"
                                    data-end="{{ $booking->scheduled_end_time?->format('H:i') }}"
                                    data-duration="{{ $booking->scheduled_duration }}"
                                    data-destination="{{ $booking->destination }}"
                                    data-purpose="{{ $booking->purpose_of_trip }}"
                                    data-actual-pickup="{{ $booking->actual_pickup_at?->format('d M Y H:i') }}"
                                    data-actual-end="{{ $booking->actual_end_at?->format('d M Y H:i') }}"
                                    data-cancelled-by="{{ $booking->cancelled_by }}"
                                    data-cancel-reason="{{ $booking->cancelation_reason }}"
                                    data-created="{{ $booking->created_at?->format('d M Y H:i') }}">
                                    <i class="icon-base bx bx-show"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem">
                                No bookings found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RECENT MEETING ROOM BOOKINGS TABLE --}}
        <p class="home-section-title">Recent Meeting Room Bookings</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold">Latest Activity</h6>
                <a href="{{ route('admin.meeting-room-bookings.index') }}" class="home-widget-link">View all →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Employee</th>
                            <th class="d-none d-md-table-cell"
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Meeting Room</th>
                            <th
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Time</th>
                            <th class="d-none d-md-table-cell"
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Location</th>
                            <th
                                style="font-size:.72rem;color:#82868b;text-transform:uppercase;letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">
                                Status</th>
                            <th style="padding:.75rem 1rem"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRoomBookings as $booking)
                        @php
                        $roomPillMap = [
                        'booked' => ['class' => 'booked', 'label' => 'Booked'],
                        'departure' => ['class' => 'departure', 'label' => 'In Use', 'live' => true],
                        'completed' => ['class' => 'completed', 'label' => 'Completed'],
                        'cancelled' => ['class' => 'cancelled', 'label' => 'Cancelled'],
                        ];
                        $roomPill = $roomPillMap[$booking->status] ?? ['class' => 'booked', 'label' =>
                        ucfirst($booking->status)];
                        @endphp
                        <tr>
                            <td style="vertical-align:middle;padding:.75rem 1rem">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="home-mini-av">{{ $booking->user?->initials() }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#2c2c5e;font-size:.8rem">{{
                                            $booking->user?->Name }}</div>
                                        <div style="font-size:.7rem;color:#b9b9c3">NIK: {{ $booking->nik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell text-center"
                                style="vertical-align:middle;padding:.75rem 1rem;font-size:.8rem;font-weight:600;color:#2c2c5e">
                                {{ $booking->meetingRoom?->name }}
                            </td>
                            <td class="text-center"
                                style="vertical-align:middle;padding:.75rem 1rem;white-space:nowrap;font-size:.8rem">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}–{{
                                \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td class="d-none d-md-table-cell text-center"
                                style="vertical-align:middle;padding:.75rem 1rem;font-size:.8rem;color:#5e5873">
                                {{ str()->of($booking->location)->replace('-', ' ')->title() ?? '-' }}
                            </td>
                            <td class="text-center" style="vertical-align:middle;padding:.75rem 1rem">
                                <span class="home-status-pill home-pill-{{ $roomPill['class'] }}">
                                    @if(!empty($roomPill['live']))
                                    <span class="home-live-dot" style="width:6px;height:6px;margin-right:3px"></span>
                                    @endif
                                    {{ $roomPill['label'] }}
                                </span>
                            </td>
                            <td style="vertical-align:middle;padding:.75rem 1rem">
                                <button type="button" class="home-act-btn room-view-btn" title="View Detail"
                                    data-user-name="{{ $booking->user?->Name }}" data-user-nik="{{ $booking->nik }}"
                                    data-room="{{ $booking->meetingRoom?->name }}"
                                    data-location="{{ str()->of($booking->location)->replace('-', ' ')->title() ?? '-' }}"
                                    data-date="{{ $booking->booking_date?->format('D, d M Y') }}"
                                    data-start="{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}"
                                    data-end="{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}"
                                    data-usage="{{ $booking->usage_type }}"
                                    data-description="{{ $booking->description }}" data-status="{{ $booking->status }}"
                                    data-pill-class="{{ $roomPill['class'] }}"
                                    data-pill-label="{{ $roomPill['label'] }}"
                                    data-pill-live="{{ !empty($roomPill['live']) ? '1' : '0' }}"
                                    data-guest-emails="{{ is_array($booking->guest_emails) ? implode(', ', $booking->guest_emails) : ($booking->guest_emails ?? '') }}"
                                    data-created="{{ $booking->created_at?->format('d M Y H:i') }}">
                                    <i class="icon-base bx bx-show"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem">
                                No room bookings found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div>

        {{-- ACTIVITY LOG --}}
        <p class="home-section-title">System Activity</p>
        <div class="card mb-3">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold">Recent Events</h6>
            </div>
            <div class="card-body p-0">
                @forelse ($activityLog as $activity)
                <div class="home-activity-item">
                    <div class="home-act-icon home-act-{{ $activity['icon_class'] }}">
                        @switch($activity['icon_class'])
                        @case('c') <i class="icon-base bx bx-plus"></i> @break
                        @case('d') <i class="icon-base bx bx-car"></i> @break
                        @case('k') <i class="icon-base bx bx-check"></i> @break
                        @case('x') <i class="icon-base bx bx-x"></i> @break
                        @default <i class="icon-base bx bx-refresh"></i>
                        @endswitch
                    </div>
                    <div>
                        <div class="home-act-label">{!! $activity['label'] !!}</div>
                        <div class="home-act-time">{{ $activity['time']?->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="home-empty-sm">
                    <i class="icon-base bx bx-history"></i>
                    No activity yet today
                </div>
                @endforelse
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <p class="home-section-title">Quick Actions</p>
        <div class="card">
            <div class="card-body d-flex flex-column gap-2">
                @foreach ([
                ['route' => 'booking.driver.index', 'icon' => 'bx-list-ul', 'label' => 'All Driver Bookings'],
                ['route' => 'booking.meeting-room.index', 'icon' => 'bx-list-ul', 'label' => 'All Meeting Room
                Bookings'],
                ['route' => 'admin.drivers.index', 'icon' => 'bx-car', 'label' => 'Manage Drivers'],
                ['route' => 'admin.meeting-rooms.index', 'icon' => 'bx-door-open', 'label' => 'Manage Meeting Rooms'],
                ] as $action)
                <a href="{{ route($action['route']) }}" class="home-quick-action-link">
                    <i class="icon-base bx {{ $action['icon'] }}"></i>
                    <span>{{ $action['label'] }}</span>
                    <i class="icon-base bx bx-right-arrow-alt ms-auto"></i>
                </a>
                @endforeach
            </div>
        </div>

    </div>

</div>


{{-- ══════════════════════════════════════════════════════
DRIVER BOOKING DETAIL MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="driverDetailModal" tabindex="-1" aria-labelledby="driverDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none">

            <div class="modal-header" style="border-bottom:1px solid #f3f2f7;padding:1.25rem 1.5rem">
                <div>
                    <h6 class="modal-title fw-bold mb-0" style="color:#2c2c5e" id="driverDetailModalLabel">
                        Driver Booking Detail
                    </h6>
                    <div id="drv-modal-booking-number"
                        style="font-size:.72rem;color:#b9b9c3;letter-spacing:.5px;margin-top:2px"></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="drv-modal-status-pill" class="home-status-pill"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body" style="padding:1.5rem">

                {{-- People row --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div style="background:#f8f7fa;border-radius:12px;padding:1rem">
                            <div
                                style="font-size:.7rem;font-weight:700;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                                Employee
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:38px;height:38px;border-radius:50%;
                                            background:rgba(115,103,240,.12);color:#7367f0;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:.8rem;font-weight:700;flex-shrink:0" id="drv-modal-user-av">
                                </div>
                                <div>
                                    <div style="font-size:.85rem;font-weight:600;color:#2c2c5e"
                                        id="drv-modal-user-name"></div>
                                    <div style="font-size:.75rem;color:#82868b" id="drv-modal-user-nik"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#f8f7fa;border-radius:12px;padding:1rem">
                            <div
                                style="font-size:.7rem;font-weight:700;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                                Driver
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:38px;height:38px;border-radius:50%;
                                            background:rgba(40,199,111,.12);color:#28c76f;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:.8rem;font-weight:700;flex-shrink:0" id="drv-modal-driver-av">
                                </div>
                                <div>
                                    <div style="font-size:.85rem;font-weight:600;color:#2c2c5e"
                                        id="drv-modal-driver-name"></div>
                                    <div style="font-size:.75rem;color:#82868b" id="drv-modal-driver-nik"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div
                    style="font-size:.7rem;font-weight:700;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.75rem">
                    Schedule
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-calendar" style="color:#7367f0"></i>
                            <div>
                                <div class="drv-detail-label">Date</div>
                                <div class="drv-detail-value" id="drv-modal-date"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-time-five" style="color:#28c76f"></i>
                            <div>
                                <div class="drv-detail-label">Pickup</div>
                                <div class="drv-detail-value" id="drv-modal-pickup"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-timer" style="color:#00cfe8"></i>
                            <div>
                                <div class="drv-detail-label">End · Duration</div>
                                <div class="drv-detail-value" id="drv-modal-end"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Destination --}}
                <div style="background:rgba(115,103,240,.05);border-left:3px solid #7367f0;
                            border-radius:0 10px 10px 0;padding:.85rem 1rem;margin-bottom:1rem">
                    <div class="drv-detail-label">Destination</div>
                    <div style="font-size:.88rem;font-weight:600;color:#2c2c5e;margin-top:2px"
                        id="drv-modal-destination"></div>
                </div>

                {{-- Purpose --}}
                <div style="background:#f8f7fa;border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem">
                    <div class="drv-detail-label">Purpose of Trip</div>
                    <div style="font-size:.85rem;color:#5e5873;margin-top:2px" id="drv-modal-purpose"></div>
                </div>

                {{-- Actual times (only if filled) --}}
                <div id="drv-modal-actual-wrap" style="display:none">
                    <div
                        style="font-size:.7rem;font-weight:700;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.75rem">
                        Actual Times
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="drv-detail-item">
                                <i class="icon-base bx bx-log-in-circle" style="color:#28c76f"></i>
                                <div>
                                    <div class="drv-detail-label">Actual Pickup</div>
                                    <div class="drv-detail-value" id="drv-modal-actual-pickup"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="drv-detail-item">
                                <i class="icon-base bx bx-log-out-circle" style="color:#00cfe8"></i>
                                <div>
                                    <div class="drv-detail-label">Actual End</div>
                                    <div class="drv-detail-value" id="drv-modal-actual-end"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cancellation reason (only if cancelled) --}}
                <div id="drv-modal-cancel-wrap" style="display:none;background:rgba(234,84,85,.05);border-left:3px solid #ea5455;
                            border-radius:0 10px 10px 0;padding:.85rem 1rem">
                    <div class="drv-detail-label" style="color:#ea5455">Cancellation Reason</div>
                    <div style="font-size:.85rem;color:#5e5873;margin-top:2px" id="drv-modal-cancel-reason"></div>
                    <div style="font-size:.72rem;color:#b9b9c3;margin-top:4px" id="drv-modal-cancelled-by"></div>
                </div>

            </div>

            <div class="modal-footer" style="border-top:1px solid #f3f2f7;padding:.85rem 1.5rem">
                <div style="font-size:.75rem;color:#b9b9c3">
                    Booked on <span id="drv-modal-created"></span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-auto"
                    data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
MEETING ROOM BOOKING DETAIL MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="roomDetailModal" tabindex="-1" aria-labelledby="roomDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">

            <div class="modal-header" style="border-bottom:1px solid #f3f2f7;padding:1.25rem 1.5rem">
                <div>
                    <h6 class="modal-title fw-bold mb-0" style="color:#2c2c5e" id="roomDetailModalLabel">
                        Meeting Room Booking Detail
                    </h6>
                    <div id="room-modal-room-name" style="font-size:.8rem;color:#7367f0;font-weight:600;margin-top:2px">
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="room-modal-status-pill" class="home-status-pill"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body" style="padding:1.5rem">

                {{-- Employee --}}
                <div style="background:#f8f7fa;border-radius:12px;padding:1rem;margin-bottom:1rem">
                    <div
                        style="font-size:.7rem;font-weight:700;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">
                        Employee
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:38px;height:38px;border-radius:50%;
                                    background:rgba(115,103,240,.12);color:#7367f0;
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:.8rem;font-weight:700;flex-shrink:0" id="room-modal-user-av"></div>
                        <div>
                            <div style="font-size:.85rem;font-weight:600;color:#2c2c5e" id="room-modal-user-name"></div>
                            <div style="font-size:.75rem;color:#82868b" id="room-modal-user-nik"></div>
                        </div>
                    </div>
                </div>

                {{-- Room + Location --}}
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-door-open" style="color:#7367f0"></i>
                            <div>
                                <div class="drv-detail-label">Room</div>
                                <div class="drv-detail-value" id="room-modal-room"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-map-pin" style="color:#ff9f43"></i>
                            <div>
                                <div class="drv-detail-label">Location</div>
                                <div class="drv-detail-value" id="room-modal-location"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Date + Time --}}
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-calendar" style="color:#7367f0"></i>
                            <div>
                                <div class="drv-detail-label">Date</div>
                                <div class="drv-detail-value" id="room-modal-date"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-time-five" style="color:#28c76f"></i>
                            <div>
                                <div class="drv-detail-label">Start</div>
                                <div class="drv-detail-value" id="room-modal-start"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-timer" style="color:#00cfe8"></i>
                            <div>
                                <div class="drv-detail-label">End</div>
                                <div class="drv-detail-value" id="room-modal-end"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Usage type + Description --}}
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <div class="drv-detail-item">
                            <i class="icon-base bx bx-category" style="color:#00cfe8"></i>
                            <div>
                                <div class="drv-detail-label">Usage Type</div>
                                <div class="drv-detail-value" id="room-modal-usage"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div style="background:#f8f7fa;border-radius:10px;padding:.7rem .85rem">
                            <div class="drv-detail-label">Description</div>
                            <div style="font-size:.82rem;color:#5e5873;margin-top:2px" id="room-modal-description">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Guest emails (only if any) --}}
                <div id="room-modal-guests-wrap" style="display:none">
                    <div style="background:#f8f7fa;border-radius:10px;padding:.7rem .85rem">
                        <div class="drv-detail-label">Guest Emails</div>
                        <div style="font-size:.82rem;color:#5e5873;margin-top:2px" id="room-modal-guests"></div>
                    </div>
                </div>

            </div>

            <div class="modal-footer" style="border-top:1px solid #f3f2f7;padding:.85rem 1.5rem">
                <div style="font-size:.75rem;color:#b9b9c3">
                    Booked on <span id="room-modal-created"></span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-auto"
                    data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<style>
    .drv-detail-item {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        padding: .6rem .75rem;
        background: #f8f7fa;
        border-radius: 10px;
        height: 100%;
    }

    .drv-detail-item .icon-base {
        font-size: 1rem;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .drv-detail-label {
        font-size: .7rem;
        color: #b9b9c3;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .drv-detail-value {
        font-size: .83rem;
        font-weight: 600;
        color: #2c2c5e;
        margin-top: 2px;
    }
</style>

@endsection

@section('script')
<script>
    function initials(name) {
    if (!name) return '?';
    var parts = $.trim(name).split(' ').filter(Boolean);
    return ((parts[0] || '')[0] || '') + ((parts[1] || '')[0] || '');
}

function pillHtml(pillClass, pillLabel, pillLive) {
    var dot = pillLive === '1'
        ? '<span class="home-live-dot" style="width:6px;height:6px;margin-right:3px;display:inline-block"></span>'
        : '';
    return dot + pillLabel;
}

// ── Driver Booking Detail ─────────────────────────────────────────────
$(document).on('click', '.drv-view-btn', function () {
    var d = $(this).data();

    $('#drv-modal-booking-number').text(d.bookingNumber);
    $('#drv-modal-status-pill')
        .attr('class', 'home-status-pill home-pill-' + d.pillClass)
        .html(pillHtml(d.pillClass, d.pillLabel, String(d.pillLive)));

    $('#drv-modal-user-av').text(initials(d.userName).toUpperCase());
    $('#drv-modal-user-name').text(d.userName  || '-');
    $('#drv-modal-user-nik').text('NIK: ' + (d.userNik  || '-'));
    $('#drv-modal-driver-av').text(initials(d.driverName).toUpperCase());
    $('#drv-modal-driver-name').text(d.driverName || '-');
    $('#drv-modal-driver-nik').text('NIK: ' + (d.driverNik || '-'));

    $('#drv-modal-date').text(d.date || '-');
    $('#drv-modal-pickup').text(d.pickup ? d.pickup + ' WIB' : '-');
    $('#drv-modal-end').text(d.end ? d.end + ' WIB · ' + d.duration + 'm' : '-');
    $('#drv-modal-destination').text(d.destination || '-');
    $('#drv-modal-purpose').text(d.purpose || '-');

    if (d.actualPickup || d.actualEnd) {
        $('#drv-modal-actual-wrap').show();
        $('#drv-modal-actual-pickup').text(d.actualPickup || '-');
        $('#drv-modal-actual-end').text(d.actualEnd || '-');
    } else {
        $('#drv-modal-actual-wrap').hide();
    }

    if (d.cancelReason) {
        $('#drv-modal-cancel-wrap').show();
        $('#drv-modal-cancel-reason').text(d.cancelReason);
        $('#drv-modal-cancelled-by').text(d.cancelledBy ? 'Cancelled by NIK: ' + d.cancelledBy : '');
    } else {
        $('#drv-modal-cancel-wrap').hide();
    }

    $('#drv-modal-created').text(d.created || '-');
    $('#driverDetailModal').modal('show');
});

// ── Room Booking Detail ───────────────────────────────────────────────
$(document).on('click', '.room-view-btn', function () {
    var d = $(this).data();

    $('#room-modal-room-name').text(d.room || '-');
    $('#room-modal-status-pill')
        .attr('class', 'home-status-pill home-pill-' + d.pillClass)
        .html(pillHtml(d.pillClass, d.pillLabel, String(d.pillLive)));

    $('#room-modal-user-av').text(initials(d.userName).toUpperCase());
    $('#room-modal-user-name').text(d.userName  || '-');
    $('#room-modal-user-nik').text('NIK: ' + (d.userNik || '-'));
    $('#room-modal-room').text(d.room     || '-');
    $('#room-modal-location').text(d.location || '-');
    $('#room-modal-date').text(d.date     || '-');
    $('#room-modal-start').text(d.start   ? d.start + ' WIB' : '-');
    $('#room-modal-end').text(d.end       ? d.end   + ' WIB' : '-');
    $('#room-modal-usage').text(d.usage   || '-');
    $('#room-modal-description').text(d.description || '-');
    $('#room-modal-created').text(d.created || '-');

    if (d.guestEmails && $.trim(d.guestEmails) !== '') {
        $('#room-modal-guests-wrap').show();
        $('#room-modal-guests').text(d.guestEmails);
    } else {
        $('#room-modal-guests-wrap').hide();
    }

    $('#roomDetailModal').modal('show');
});
</script>

@endsection