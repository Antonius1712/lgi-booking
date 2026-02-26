<?php

namespace App\Http\Controllers;

use App\Enums\DriverBookingStatusEnum;
use App\Enums\RoleEnum;
use App\Models\DriverBooking;
use App\Models\MeetingRoomBooking;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // ── Detect role ──────────────────────────────────────────────
        $isAdmin = $user->isAdmin();
        $isDriver = $user->UserGroup()
            ->whereHas('Group', fn ($q) => $q
                ->whereHas('App', fn ($q) => $q->where('AppCode', 'lgi-booking'))
                ->where('GroupCode', RoleEnum::DRIVER)
            )->exists();

        // ── DRIVER homepage ──────────────────────────────────────────
        if ($isDriver) {
            return $this->driverHome($user);
        }

        // ── ADMIN homepage ───────────────────────────────────────────
        if ($isAdmin) {
            return $this->adminHome($user);
        }

        // ── USER (booker) homepage ───────────────────────────────────
        return $this->userHome($user);
    }

    // Statuses that mean "still active / upcoming" — show on homepage
    private const ACTIVE_STATUSES = [
        DriverBookingStatusEnum::BOOKED,
        DriverBookingStatusEnum::WAITING_CONFIRMATION,
        DriverBookingStatusEnum::REMINDER_SENT_1,
        DriverBookingStatusEnum::REMINDER_SENT_2,
        DriverBookingStatusEnum::REMINDER_SENT_3,
    ];

    // Statuses that mean "currently on a trip"
    private const ON_TRIP_STATUSES = [
        DriverBookingStatusEnum::DEPARTURE,
        DriverBookingStatusEnum::EXTENDING,
        DriverBookingStatusEnum::RESCHEDULING,
    ];

    // Statuses that mean "done / terminal"
    private const TERMINAL_STATUSES = [
        DriverBookingStatusEnum::COMPLETED,
        DriverBookingStatusEnum::CANCELLED,
        DriverBookingStatusEnum::AUTO_CANCELLED,
        DriverBookingStatusEnum::DRIVER_CHANGED,
    ];

    private function activeStatusValues(): array
    {
        return array_map(fn ($s) => $s->value, self::ACTIVE_STATUSES);
    }

    private function onTripStatusValues(): array
    {
        return array_map(fn ($s) => $s->value, self::ON_TRIP_STATUSES);
    }

    private function terminalStatusValues(): array
    {
        return array_map(fn ($s) => $s->value, self::TERMINAL_STATUSES);
    }

    // ─────────────────────────────────────────────────────────────────
    // USER (Booker)
    // ─────────────────────────────────────────────────────────────────
    private function userHome(User $user): View
    {
        // Upcoming driver bookings — active or on-trip, ordered soonest first
        $upcomingDriverBookings = DriverBooking::query()
            ->where('user_nik', $user->NIK)
            ->whereIn('status', [
                ...$this->activeStatusValues(),
                ...$this->onTripStatusValues(),
            ])
            ->where('scheduled_pickup_date', '>=', today())
            ->with('driver:NIK,Name')
            ->orderBy('scheduled_pickup_date')
            ->orderBy('scheduled_pickup_time')
            ->limit(5)
            ->get();

        // Upcoming meeting room bookings
        $upcomingRoomBookings = MeetingRoomBooking::query()
            ->where('nik', $user->NIK)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('booking_date', '>=', today())
            ->with('meetingRoom:id,name,slug')
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Today's schedule for timeline (exclude terminal)
        $todayDriverBookings = DriverBooking::query()
            ->where('user_nik', $user->NIK)
            ->where('scheduled_pickup_date', today())
            ->whereNotIn('status', $this->terminalStatusValues())
            ->orderBy('scheduled_pickup_time')
            ->get();

        $todayRoomBookings = MeetingRoomBooking::query()
            ->where('nik', $user->NIK)
            ->where('booking_date', today())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->with('meetingRoom:id,name')
            ->orderBy('start_time')
            ->get();

        // Recent activity — last 10 of each, merged & sorted
        $recentDriverActivity = DriverBooking::query()
            ->where('user_nik', $user->NIK)
            ->with('driver:NIK,Name')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'type' => 'driver',
                'status' => $b->status,
                'label' => $this->driverActivityLabel($b),
                'icon_class' => $this->activityIconClass($b->status),
                'time' => $b->updated_at,
            ]);

        $recentRoomActivity = MeetingRoomBooking::query()
            ->where('nik', $user->NIK)
            ->with('meetingRoom:id,name')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'type' => 'room',
                'status' => $b->status,
                'label' => $this->roomActivityLabel($b),
                'icon_class' => $this->activityIconClass($b->status),
                'time' => $b->updated_at,
            ]);

        $recentActivity = $recentDriverActivity
            ->concat($recentRoomActivity)
            ->sortByDesc('time')
            ->take(8)
            ->values();

        return view('home.user', compact(
            'user',
            'upcomingDriverBookings',
            'upcomingRoomBookings',
            'todayDriverBookings',
            'todayRoomBookings',
            'recentActivity',
        ));
    }

    // ─────────────────────────────────────────────────────────────────
    // DRIVER
    // ─────────────────────────────────────────────────────────────────
    private function driverHome(User $user): View
    {
        // Active trip — departure / extending / rescheduling, today
        $activeTrip = DriverBooking::query()
            ->where('driver_nik', $user->NIK)
            ->whereIn('status', $this->onTripStatusValues())
            ->where('scheduled_pickup_date', today())
            ->with('user:NIK,Name,Email,NoTelp')
            ->first();

        // Today's upcoming trips — booked/waiting/reminder, not yet on trip
        $upcomingTrips = DriverBooking::query()
            ->where('driver_nik', $user->NIK)
            ->whereIn('status', $this->activeStatusValues())
            ->where('scheduled_pickup_date', today())
            ->with('user:NIK,Name,Email,NoTelp')
            ->orderBy('scheduled_pickup_time')
            ->get();

        // Full today schedule for sidebar (all non-terminal)
        $todaySchedule = DriverBooking::query()
            ->where('driver_nik', $user->NIK)
            ->where('scheduled_pickup_date', today())
            ->whereNotIn('status', $this->terminalStatusValues())
            ->with('user:NIK,Name')
            ->orderBy('scheduled_pickup_time')
            ->get();

        return view('home.driver', compact(
            'user',
            'activeTrip',
            'upcomingTrips',
            'todaySchedule',
        ));
    }

    // ─────────────────────────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────────────────────────
    private function adminHome(User $user): View
    {
        // Stats for today
        $stats = [
            'total_driver_bookings' => DriverBooking::whereDate('scheduled_pickup_date', today())->count(),
            'on_trip_driver_bookings' => DriverBooking::whereIn('status', $this->onTripStatusValues())->whereDate('scheduled_pickup_date', today())->count(),
            'upcoming_driver_booking' => DriverBooking::whereIn('status', $this->activeStatusValues())->where('scheduled_pickup_date', '>=', today())->count(),
            'driver_booking_completed_today' => DriverBooking::where('status', DriverBookingStatusEnum::COMPLETED->value)->whereDate('scheduled_pickup_date', today())->count(),
            'driver_booking_cancelled_today' => DriverBooking::whereIn('status', [
                DriverBookingStatusEnum::CANCELLED->value,
                DriverBookingStatusEnum::AUTO_CANCELLED->value,
            ])->whereDate('scheduled_pickup_date', today())->count(),
            'rooms_booked' => MeetingRoomBooking::where('status', 'booked')->whereDate('booking_date', today())->count(),
        ];

        // All drivers with current on-trip status (avoid N+1 with eager load)
        $onTripNiks = DriverBooking::whereIn('status', $this->onTripStatusValues())
            ->where('scheduled_pickup_date', today())
        ->pluck('scheduled_end_time', 'driver_nik');

        $drivers = User::query()
            ->whereHas('UserGroup', fn ($q) => $q
                ->whereHas('Group', fn ($q) => $q
                    ->whereHas('App', fn ($q) => $q->where('AppCode', 'lgi-booking'))
                    ->where('GroupCode', RoleEnum::DRIVER)
                )
            )
            ->get()
        ->map(fn (User $driver) => [
            'NIK' => $driver->NIK,
            'Name' => $driver->Name,
            'initials' => $driver->initials(),
            'is_on_trip' => $onTripNiks->has($driver->NIK),
            'ends_at' => $onTripNiks->has($driver->NIK)
                                ? \Carbon\Carbon::parse($onTripNiks[$driver->NIK])->format('H:i')
                                : null,
        ]);

        // Recent driver bookings (latest 10)
        $recentDriverBookings = DriverBooking::query()
            ->with(['user:NIK,Name', 'driver:NIK,Name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentRoomBookings = MeetingRoomBooking::query()
            ->with(['user:NIK,Name', 'meetingRoom:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // System activity log — merge both booking types
        $driverActivity = DriverBooking::query()
            ->with(['user:NIK,Name', 'driver:NIK,Name'])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(fn ($b) => [
                'icon_class' => $this->activityIconClass($b->status),
                'label' => $this->driverActivityLabel($b),
                'time' => $b->updated_at,
            ]);

        $roomActivity = MeetingRoomBooking::query()
            ->with(['user:NIK,Name', 'meetingRoom:id,name'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'icon_class' => $this->activityIconClass($b->status),
                'label' => $this->roomActivityLabel($b),
                'time' => $b->updated_at,
            ]);

        $activityLog = $driverActivity
            ->concat($roomActivity)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return view('home.admin', compact(
            'user',
            'stats',
            'drivers',
            'recentRoomBookings',
            'recentDriverBookings',
            'activityLog',
        ));
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────
    private function driverActivityLabel(DriverBooking $booking): string
    {
        $name = $booking->user?->Name ?? 'Someone';
        $driverName = $booking->driver?->Name ?? 'a driver';
        $dest = $booking->destination ?? '-';

        return match ($booking->status) {
            DriverBookingStatusEnum::BOOKED->value,
            DriverBookingStatusEnum::WAITING_CONFIRMATION->value => "<strong>{$name}</strong> booked Driver {$driverName} to {$dest}",
            DriverBookingStatusEnum::REMINDER_SENT_1->value,
            DriverBookingStatusEnum::REMINDER_SENT_2->value,
            DriverBookingStatusEnum::REMINDER_SENT_3->value => "<strong>System</strong> sent reminder to {$name} for trip to {$dest}",
            DriverBookingStatusEnum::DEPARTURE->value => "<strong>{$name}</strong> trip departed with {$driverName}",
            DriverBookingStatusEnum::EXTENDING->value => "<strong>{$name}</strong> is extending trip with {$driverName}",
            DriverBookingStatusEnum::RESCHEDULING->value => "<strong>{$name}</strong> is rescheduling trip with {$driverName}",
            DriverBookingStatusEnum::DRIVER_CHANGED->value => "<strong>Admin</strong> changed driver for {$name}'s trip to {$dest}",
            DriverBookingStatusEnum::COMPLETED->value => "<strong>{$name}</strong> trip completed — {$dest}",
            DriverBookingStatusEnum::CANCELLED->value => "<strong>{$name}</strong> cancelled booking to {$dest}",
            DriverBookingStatusEnum::AUTO_CANCELLED->value => "<strong>System</strong> auto-cancelled {$name}'s booking — no confirmation",
            default => "<strong>{$name}</strong> updated a booking",
        };
    }

    private function roomActivityLabel(MeetingRoomBooking $booking): string
    {
        $name = $booking->user?->Name ?? 'Someone';
        $roomName = $booking->meetingRoom?->name ?? 'a room';

        return match ($booking->status) {
            'booked' => "<strong>{$name}</strong> booked {$roomName}",
            'departure' => "<strong>{$name}</strong> started session in {$roomName}",
            'completed' => "<strong>{$name}</strong> completed session in {$roomName}",
            'cancelled' => "<strong>{$name}</strong> cancelled booking for {$roomName}",
            default => "<strong>{$name}</strong> updated a booking for {$roomName}",
        };
    }

    private function activityIconClass(string $status): string
    {
        return match ($status) {
            DriverBookingStatusEnum::BOOKED->value,
            DriverBookingStatusEnum::WAITING_CONFIRMATION->value,
            'booked' => 'c', // purple plus
            DriverBookingStatusEnum::REMINDER_SENT_1->value,
            DriverBookingStatusEnum::REMINDER_SENT_2->value,
            DriverBookingStatusEnum::REMINDER_SENT_3->value => 'e', // orange bell
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
            DriverBookingStatusEnum::RESCHEDULING->value => 'd', // green car
            DriverBookingStatusEnum::DRIVER_CHANGED->value => 'e', // orange swap
            DriverBookingStatusEnum::COMPLETED->value,
            'completed' => 'k', // cyan check
            DriverBookingStatusEnum::CANCELLED->value,
            DriverBookingStatusEnum::AUTO_CANCELLED->value,
            'cancelled' => 'x', // red x
            default => 'e', // orange
        };
    }
}
