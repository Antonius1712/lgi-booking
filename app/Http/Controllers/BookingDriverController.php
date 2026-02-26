<?php

namespace App\Http\Controllers;

use App\Actions\BookingDriverCancelAction;
use App\Actions\BookingDriverStoreAction;
use App\Actions\BookingDriverUpdateAction;
use App\Enums\RoleEnum;
use App\Enums\UsageTypeEnum;
use App\Http\Requests\BookingDriverStoreRequest;
use App\Http\Requests\BookingDriverUpdateRequest;
use App\Models\Booking;
use App\Models\DriverBooking;
use App\Models\MeetingRoom;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingDriverController extends Controller
{
    public $booked;

    public function __construct()
    {
        $this->booked = [
            'lantai-3-baru' => ['08:00', '08:30', '09:00'],
            'e-commerce' => ['09:00'],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = $request->year ?? now()->format('Y');
        $month = $request->month ?? now()->format('m');
        $day = $request->day ?? now()->format('d');
        $date = Carbon::parse("$year-$month-$day")->format('Y-m-d');

        $usageTypes = UsageTypeEnum::cases();
        $timeSlots = $this->timeSlot();
        $timeRanges = $this->timeRange();

        $drivers = User::query()
            ->with('UserGroup')
            ->whereHas('UserGroup', function ($userGroup) {
                $userGroup->whereHas('Group', function ($group) {
                    $group->whereHas('App', function ($app) {
                        $app->where('AppCode', 'lgi-booking');
                    });
                })
                    ->where('GroupCode', RoleEnum::DRIVER);
            })
            ->get();

        // dd($drivers);

        // dd($date, $day, $request->day);
        $booked = DriverBooking::query()
            ->where('scheduled_pickup_date', $date)
            ->where('status', '!=', 'cancelled')
            ->with(['user:NIK,Name', 'driver:NIK,Name'])
            ->get();

        // dd($booked);
        // $booked = [];

        $driverDays = (int) Setting::get('driver_booking_days_ahead', 14);

        return view('booking.driver.index', compact('usageTypes', 'drivers', 'timeSlots', 'timeRanges', 'booked', 'driverDays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingDriverStoreRequest $request, BookingDriverStoreAction $action): RedirectResponse
    {
        try {
            $action->handle($request);
        } catch (Exception $e) {
            Log::error($e->getLine());
            Log::error($e->getFile());
            Log::error($e->getMessage());

            return back()->withErrors(['error' => 'error']);
        }

        return back()->with(['success' => 'Sukses']);
    }

    public function cancel(DriverBooking $driverBooking, #[CurrentUser] User $user, BookingDriverCancelAction $action): RedirectResponse
    {
        abort_if($driverBooking->user_nik !== $user->NIK, 403);
        $action->handle($driverBooking);

        return back()->with(['success' => 'Pemesanan berhasil dibatalkan.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingDriverUpdateRequest $request, DriverBooking $driverBooking, #[CurrentUser] User $user, BookingDriverUpdateAction $action): RedirectResponse
    {
        abort_if($driverBooking->user_nik !== $user->NIK, 403);
        try {
            $action->handle($driverBooking, $request);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with(['success' => 'Sukses']);
    }

    private function timeSlot()
    {
        $timeSlots = [];
        for ($hour = 8; $hour < 17; $hour++) {
            $start = today()->setTime($hour, 0);
            $end = $start->copy()->addHour();
            $timeSlots[] = $start->format('H:i').' - '.$end->format('H:i');
        }

        return $timeSlots;
    }

    private function timeRange()
    {
        $timeRanges = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            foreach ([0, 30] as $minute) {
                $start = today()->setTime($hour, $minute);
                $timeRanges[] = $start->format('H:i');
            }
        }
        $timeRanges[] = today()->setTime(17, 0)->format('H:i');

        return $timeRanges;
    }

    // public function fetchData(Request $request)
    // {
    //     $roomId = MeetingRoom::where('slug', $request->room)->value('id');
    //     if (! $roomId) {
    //         return response()->json([], 404);
    //     }

    //     $bookedTimes = Booking::where('meeting_room_id', $roomId)
    //         ->where('booking_date', $request->date)
    //         ->where('status', 'confirmed')
    //         ->pluck('start_time')
    //         ->map(fn ($t) => Carbon::parse($t)->format('H:i'))
    //         ->toArray();

    //     // generate slots 08:00–17:00 (30 min)
    //     $slots = [];
    //     $start = Carbon::createFromTime(8, 0);
    //     $end = Carbon::createFromTime(17, 0);

    //     while ($start < $end) {
    //         $time = $start->format('H:i');

    //         $slots[] = [
    //             'time' => $time,
    //             'label' => $time,
    //             'disabled' => in_array($time, $bookedTimes),
    //         ];

    //         $start->addMinutes(30);
    //     }

    //     return response()->json($slots);
    // }
}
