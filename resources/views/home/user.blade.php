@extends('layouts.app')

@section('content')

{{-- ── WELCOME BANNER ── --}}
<div class="welcome-banner mb-4">
    <div style="position:relative;z-index:1">
        <p class="welcome-title">Good {{ now()->format('G') < 12 ? 'Morning' : (now()->format('G') < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', $user->Name)[0] }} 👋</p>
        @php
            $totalUpcoming = $upcomingDriverBookings->count() + $upcomingRoomBookings->count();
        @endphp
        <p class="welcome-sub">
            You have <strong>{{ $totalUpcoming }} upcoming booking{{ $totalUpcoming !== 1 ? 's' : '' }}</strong>.
        </p>
        <p class="welcome-date"><i class="icon-base bx bx-calendar me-1"></i>{{ now()->format('l, d F Y') }}</p>
    </div>
    <i class="icon-base bx bx-calendar-check welcome-icon"></i>
</div>

{{-- ── QUICK BOOK ── --}}
<p class="home-section-title">Quick Book</p>
<div class="quick-book-grid mb-4">
    <a href="{{ route('booking.meeting-room.index') }}" class="quick-card quick-card-room">
        <div class="quick-card-icon"><i class="icon-base bx bx-door-open"></i></div>
        <div class="quick-card-text">
            <p class="label">Meeting Room</p>
            <p class="sub">Check availability &amp; reserve</p>
        </div>
        <i class="icon-base bx bx-right-arrow-alt quick-card-arrow"></i>
    </a>
    <a href="{{ route('booking.driver.index') }}" class="quick-card quick-card-driver">
        <div class="quick-card-icon"><i class="icon-base bx bx-car"></i></div>
        <div class="quick-card-text">
            <p class="label">Book a Driver</p>
            <p class="sub">Schedule pickup &amp; destination</p>
        </div>
        <i class="icon-base bx bx-right-arrow-alt quick-card-arrow"></i>
    </a>
</div>

{{-- ── MAIN GRID ── --}}
<div class="home-main-grid">

    {{-- LEFT COLUMN --}}
    <div>

        {{-- UPCOMING DRIVER BOOKINGS --}}
        <p class="home-section-title">Upcoming Driver Bookings</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold">Driver Schedule</h6>
                <a href="{{ route('my-booking.driver') }}" class="home-widget-link">View all →</a>
            </div>
            <div class="card-body p-0">
                @forelse ($upcomingDriverBookings as $booking)
                    <div class="home-booking-item">
                        <div class="home-bk-icon home-bk-icon-driver">
                            <i class="icon-base bx bx-car"></i>
                        </div>
                        <div class="home-bk-info">
                            <div class="home-bk-title">{{ $booking->destination }}</div>
                            <div class="home-bk-meta">
                                Driver: {{ $booking->driver?->Name ?? '-' }}
                                &nbsp;·&nbsp;
                                {{ Str::limit($booking->purpose_of_trip, 40) }}
                            </div>
                        </div>
                        <div class="home-bk-right">
                            <div class="home-bk-time">
                                {{ $booking->scheduled_pickup_time?->format('H:i') }}
                                –
                                {{ $booking->scheduled_end_time?->format('H:i') }}
                            </div>
                            <div class="home-bk-date">
                                {{ $booking->scheduled_pickup_date?->isToday()
                                    ? 'Today'
                                    : $booking->scheduled_pickup_date?->format('D, d M') }}
                            </div>
                            @php
                                $pillMap = [
                                    'booked'              => ['class' => 'booked',    'label' => 'Booked'],
                                    'waiting_confirmation'=> ['class' => 'booked',    'label' => 'Waiting'],
                                    'reminder_sent_1'     => ['class' => 'booked',    'label' => 'Reminder 1'],
                                    'reminder_sent_2'     => ['class' => 'booked',    'label' => 'Reminder 2'],
                                    'reminder_sent_3'     => ['class' => 'booked',    'label' => 'Reminder 3'],
                                    'departure'           => ['class' => 'departure', 'label' => 'On Trip'],
                                    'extending'           => ['class' => 'departure', 'label' => 'Extending'],
                                    'rescheduling'        => ['class' => 'departure', 'label' => 'Rescheduling'],
                                    'completed'           => ['class' => 'completed', 'label' => 'Completed'],
                                    'driver_changed'      => ['class' => 'completed', 'label' => 'Driver Changed'],
                                    'cancelled'           => ['class' => 'cancelled', 'label' => 'Cancelled'],
                                    'auto_cancelled'      => ['class' => 'cancelled', 'label' => 'Auto Cancelled'],
                                ];
                                $pill = $pillMap[$booking->status] ?? ['class' => 'booked', 'label' => ucfirst($booking->status)];
                            @endphp
                            <span class="home-status-pill home-pill-{{ $pill['class'] }} mt-1 d-inline-block">
                                {{ $pill['label'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="home-empty-sm">
                        <i class="icon-base bx bx-car"></i>
                        No upcoming driver bookings
                    </div>
                @endforelse
            </div>
        </div>

        {{-- UPCOMING ROOM BOOKINGS --}}
        <p class="home-section-title">Upcoming Meeting Room Bookings</p>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold">Room Schedule</h6>
                <a href="{{ route('my-booking.meeting-room') }}" class="home-widget-link">View all →</a>
            </div>
            <div class="card-body p-0">
                @forelse ($upcomingRoomBookings as $booking)
                    <div class="home-booking-item">
                        <div class="home-bk-icon home-bk-icon-room">
                            <i class="icon-base bx bx-door-open"></i>
                        </div>
                        <div class="home-bk-info">
                            <div class="home-bk-title">{{ $booking->meetingRoom?->name ?? '-' }}</div>
                            <div class="home-bk-meta">
                                {{ $booking->usage_type }}
                                &nbsp;·&nbsp;
                                {{ Str::limit($booking->description, 40) }}
                            </div>
                        </div>
                        <div class="home-bk-right">
                            <div class="home-bk-time">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                –
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                            <div class="home-bk-date">
                                {{ $booking->booking_date?->isToday()
                                    ? 'Today'
                                    : $booking->booking_date?->format('D, d M') }}
                            </div>
                            <span class="home-status-pill home-pill-{{ $booking->status }} mt-1 d-inline-block">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="home-empty-sm">
                        <i class="icon-base bx bx-door-open"></i>
                        No upcoming room bookings
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RECENT ACTIVITY --}}
        <p class="home-section-title">Recent Activity</p>
        <div class="card mb-3">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold">Your Activity Log</h6>
            </div>
            <div class="card-body p-0">
                @forelse ($recentActivity as $activity)
                    <div class="home-activity-item">
                        <div class="home-act-icon home-act-{{ $activity['icon_class'] }}">
                            @switch($activity['icon_class'])
                                @case('c') <i class="icon-base bx bx-plus"></i> @break
                                @case('d') <i class="icon-base bx bx-car"></i>  @break
                                @case('k') <i class="icon-base bx bx-check"></i> @break
                                @case('x') <i class="icon-base bx bx-x"></i>    @break
                                @default   <i class="icon-base bx bx-refresh"></i>
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
                        No recent activity
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: TODAY'S TIMELINE --}}
    <div>
        <p class="home-section-title">Today at a Glance</p>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold">{{ now()->format('D, d F Y') }}</h6>
                <small class="text-muted">My bookings</small>
            </div>
            <div class="card-body p-0">

                @php
                    // Merge & sort today's bookings into one timeline
                    $timeline = collect();

                    foreach ($todayDriverBookings as $b) {
                        $timeline->push([
                            'type'  => 'driver',
                            'time'  => $b->scheduled_pickup_time?->format('H:i'),
                            'end'   => $b->scheduled_end_time?->format('H:i'),
                            'label' => 'Driver – ' . ($b->driver?->Name ?? '-'),
                            'sub'   => Str::limit($b->destination, 30),
                            'status' => $b->status,
                        ]);
                    }

                    foreach ($todayRoomBookings as $b) {
                        $timeline->push([
                            'type'  => 'room',
                            'time'  => \Carbon\Carbon::parse($b->start_time)->format('H:i'),
                            'end'   => \Carbon\Carbon::parse($b->end_time)->format('H:i'),
                            'label' => $b->meetingRoom?->name ?? 'Room',
                            'sub'   => Str::limit($b->description, 30),
                            'status' => $b->status,
                        ]);
                    }

                    $timeline = $timeline->sortBy('time')->values();
                @endphp

                @forelse ($timeline as $entry)
                    <div class="home-timeline-item">
                        <span class="home-tl-time">{{ $entry['time'] }}</span>
                        <span class="home-tl-dot home-tl-dot-{{ $entry['type'] }} {{ $entry['status'] === 'departure' ? 'home-tl-dot-now' : '' }}"></span>
                        <div>
                            <div class="home-tl-label">{{ $entry['label'] }}</div>
                            <div class="home-tl-sub">{{ $entry['sub'] }} &nbsp;–&nbsp; ends {{ $entry['end'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="home-empty-sm">
                        <i class="icon-base bx bx-calendar-x"></i>
                        No bookings today
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection