@extends('layouts.app')

@section('content')

{{-- ── DRIVER BANNER ── --}}
<div class="bg-primary p-8 text-white mb-4" style="border-radius: 14px;">
    <div style="position:relative;z-index:1">
        <p class="welcome-title">
            Good {{ now()->format('G') < 12 ? 'Morning' : (now()->format('G') < 17 ? 'Afternoon' : 'Evening' ) }}, {{
                    explode(' ', $user->Name)[0] }} 🚗
        </p>
        @php
            $totalToday = $todaySchedule->count();
        @endphp
        <p class="welcome-sub">
            You have <strong>{{ $totalToday }} trip{{ $totalToday !== 1 ? ' s' : '' }}</strong> scheduled today.
        </p>
        <p class="welcome-date"><i class="icon-base bx bx-calendar me-1"></i>{{ now()->format('l, d F Y') }}</p>
    </div>
    <i class="icon-base bx bx-car welcome-icon"></i>
</div>

{{-- ── MAIN GRID ── --}}
<div class="home-main-grid">

    {{-- LEFT COLUMN --}}
    <div>

        {{-- ACTIVE TRIP --}}
        @if ($activeTrip)
        <p class="home-section-title d-flex align-items-center gap-2">
            <span class="home-live-dot"></span> Active Now
        </p>
        <div class="card mb-3 home-trip-card home-trip-card-now">
            <div class="home-trip-strip home-strip-now"></div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <span class="home-trip-id">{{ $activeTrip->booking_number }}</span>
                    <span class="home-trip-badge home-badge-now">
                        <span class="home-live-dot" style="width:6px;height:6px;margin-right:3px"></span>On Trip
                    </span>
                </div>

                {{-- Booker Info --}}
                <div class="home-booker-row mb-3">
                    <div class="home-booker-avatar">
                        {{ $activeTrip->user?->initials() }}
                    </div>
                    <div>
                        <div class="home-booker-name">{{ $activeTrip->user?->Name }}</div>
                        <div class="home-booker-nik">NIK: {{ $activeTrip->user?->NIK }}</div>
                    </div>
                    @if ($activeTrip->user?->NoTelp)
                    <div class="ms-auto text-end">
                        <div style="font-size:.72rem;color:#82868b">Phone</div>
                        <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                            <a href="tel:{{ $activeTrip->user->NoTelp }}">{{ $activeTrip->user->NoTelp }}</a>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Trip Detail Grid --}}
                <div class="home-trip-details">
                    <div class="home-detail-item">
                        <i class="icon-base bx bx-time-five"></i>
                        <div>
                            <div class="home-detail-label">Pickup Time</div>
                            <div class="home-detail-value">{{ $activeTrip->scheduled_pickup_time?->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                    <div class="home-detail-item">
                        <i class="icon-base bx bx-timer"></i>
                        <div>
                            <div class="home-detail-label">End Time</div>
                            <div class="home-detail-value">{{ $activeTrip->scheduled_end_time?->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                    <div class="home-detail-item">
                        <i class="icon-base bx bx-notepad"></i>
                        <div>
                            <div class="home-detail-label">Purpose</div>
                            <div class="home-detail-value">{{ $activeTrip->purpose_of_trip }}</div>
                        </div>
                    </div>
                    <div class="home-dest-full">
                        <i class="icon-base bx bxs-map"></i>
                        <div>
                            <div class="home-detail-label">Destination</div>
                            <div class="home-detail-value">{{ $activeTrip->destination }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="home-trip-card-footer">
                @php
                $endsIn = now()->diffInMinutes($activeTrip->scheduled_end_at, false);
                @endphp
                @if ($endsIn > 0)
                <span class="home-countdown">
                    <i class="icon-base bx bx-hourglass me-1"></i>
                    Ends in {{ $endsIn }} minute{{ $endsIn !== 1 ? 's' : '' }}
                </span>
                @else
                <span class="home-countdown" style="color:#ea5455">Trip overtime</span>
                @endif
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($activeTrip->destination) }}"
                    target="_blank" class="home-btn-nav">
                    <i class="icon-base bx bx-map me-1"></i> Open Maps
                </a>
            </div>
        </div>
        @endif

        {{-- UPCOMING TRIPS TODAY --}}
        @if ($upcomingTrips->isNotEmpty())
        <p class="home-section-title">Upcoming Today</p>
        @foreach ($upcomingTrips as $trip)
        <div class="card mb-3 home-trip-now">
            <div class="home-trip-strip home-strip-now"></div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <span class="home-trip-id">{{ $trip->booking_number }}</span>
                    <span class="home-trip-badge home-badge-upcoming">Scheduled</span>
                </div>

                <div class="home-booker-row mb-3">
                    <div class="home-booker-avatar">{{ $trip->user?->initials() }}</div>
                    <div>
                        <div class="home-booker-name">{{ $trip->user?->Name }}</div>
                        <div class="home-booker-nik">NIK: {{ $trip->user?->NIK }}</div>
                    </div>
                    @if ($trip->user?->NoTelp)
                    <div class="ms-auto text-end">
                        <div style="font-size:.72rem;color:#82868b">Phone</div>
                        <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                            <a href="tel:{{ $trip->user->NoTelp }}">{{ $trip->user->NoTelp }}</a>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="home-trip-details">
                    <div class="home-detail-item">
                        <i class="icon-base bx bx-time-five"></i>
                        <div>
                            <div class="home-detail-label">Pickup Time</div>
                            <div class="home-detail-value">{{ $trip->scheduled_pickup_time?->format('H:i') }} WIB</div>
                        </div>
                    </div>
                    <div class="home-detail-item">
                        <i class="icon-base bx bx-timer"></i>
                        <div>
                            <div class="home-detail-label">End Time</div>
                            <div class="home-detail-value">{{ $trip->scheduled_end_time?->format('H:i') }} WIB</div>
                        </div>
                    </div>
                    <div class="home-detail-item">
                        <i class="icon-base bx bx-notepad"></i>
                        <div>
                            <div class="home-detail-label">Purpose</div>
                            <div class="home-detail-value">{{ $trip->purpose_of_trip }}</div>
                        </div>
                    </div>
                    <div class="home-dest-full">
                        <i class="icon-base bx bxs-map"></i>
                        <div>
                            <div class="home-detail-label">Destination</div>
                            <div class="home-detail-value">{{ $trip->destination }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="home-trip-card-footer">
                @php
                $startsIn = now()->diffInMinutes($trip->scheduled_pickup_at, false);
                @endphp
                <span style="font-size:.78rem;color:#82868b">
                    Starts in {{ $startsIn > 60
                    ? floor($startsIn/60).'h '.($startsIn%60).'m'
                    : $startsIn.'m' }}
                </span>

                @if ($trip->reminder_count >= 3)
                    <form method="POST" action="{{ route('driver.trips.cancel', $trip) }}"
                        onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="icon-base bx bx-x-circle me-1"></i> Cancel Booking
                        </button>
                    </form>
                @endif

                @if ($trip->reminder_count < 3)
                    <form method="POST" action="{{ route('driver.trips.remind', $trip) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-info">
                            <i class="icon-base bx bx-alarm me-1"></i>
                            Remind Booker
                            @if ($trip->reminder_count > 0)
                                ({{ $trip->reminder_count }}/3)
                            @endif
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-outline-secondary" disabled>
                        <i class="icon-base bx bx-alarm me-1"></i> Reminded (3/3)
                    </button>
                @endif

                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($trip->destination) }}"
                    target="_blank" class="btn btn-outline-success">
                    <i class="icon-base bx bx-map me-1"></i> Open Maps
                </a>
            </div>
        </div>
        @endforeach
        @endif

        {{-- NO TRIPS AT ALL --}}
        @if (!$activeTrip && $upcomingTrips->isEmpty())
        <div class="card">
            <div class="card-body home-empty-sm">
                <i class="icon-base bx bx-car"></i>
                No trips scheduled for today
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT COLUMN: DAILY SCHEDULE --}}
    <div>
        <p class="home-section-title">My Schedule Today</p>
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold">{{ now()->format('D, d F Y') }}</h6>
            </div>
            <div class="card-body p-0">
                @forelse ($todaySchedule as $booking)
                <div class="home-schedule-row">
                    <span class="home-sched-time">
                        {{ $booking->scheduled_pickup_time?->format('H:i') }}
                        –
                        {{ $booking->scheduled_end_time?->format('H:i') }}
                    </span>
                    <div class="home-sched-bar"
                        style="background:{{ $booking->status === 'departure' ? '#ff9f43' : '#7367f0' }}">
                    </div>
                    <div>
                        <div class="home-sched-label">{{ $booking->user?->Name }}</div>
                        <div class="home-sched-sub">{{ Str::limit($booking->destination, 28) }}</div>
                    </div>
                </div>
                @empty
                <div class="home-empty-sm">
                    <i class="icon-base bx bx-calendar-x"></i>
                    No trips today
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection