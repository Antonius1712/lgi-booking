<?php

namespace App\Http\Controllers;

use App\Enums\CalendarType;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Services\MiniCalendars;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingDriverController extends Controller
{
    public $booked;

    public function __construct()
    {
        $this->booked = [
            'lantai-3-baru' => ['08:00', '08:30', '09:00'],
            'e-commerce' => ['09:00']
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $year = $request->year ?? now()->format('Y');
        $month = $request->month ?? now()->format('m');
        $day = $request->day ?? now()->format('d');
        $date = Carbon::parse("$year-$month-$day")->format('Y-m-d');

        $calendarTypes = CalendarType::cases();
        $timeSlots = $this->timeSlot();
        $timeRanges = $this->timeRange();

        $rooms = MeetingRoom::query()
            ->with('location')
            ->orderBy('location_id', 'desc')
            ->orderBy('id', 'desc')
        ->get();

        
        // $booked = Booking::query()
        //     ->where('booking_date', $date)
        //     ->select('booking_date', 'start_time', 'end_time')
        // ->get();

        $booked = Booking::query()
            ->where('booking_date', $date)
            ->with(['meetingRoom:id,slug', 'user:NIK,Name'])
        ->get(['id', 'nik', 'meeting_room_id', 'start_time', 'end_time', 'description']);

        return view('booking.driver.index', compact('calendarTypes', 'rooms', 'timeSlots', 'timeRanges', 'booked'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function timeSlot()
    {
        $timeSlots = [];
        for ($hour = 8; $hour < 17; $hour++) {
            $start = today()->setTime($hour, 0);
            $end = $start->copy()->addHour();
            $timeSlots[] = $start->format('H:i') . ' - ' . $end->format('H:i');
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

    public function fetchData(Request $request)
    {
        $roomId = MeetingRoom::where('slug', $request->room)->value('id');
        if (!$roomId) {
            return response()->json([], 404);
        }

        $bookedTimes = Booking::where('meeting_room_id', $roomId)
            ->where('booking_date', $request->date)
            ->where('status', 'confirmed')
            ->pluck('start_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        // generate slots 08:00–17:00 (30 min)
        $slots = [];
        $start = Carbon::createFromTime(8, 0);
        $end   = Carbon::createFromTime(17, 0);

        while ($start < $end) {
            $time = $start->format('H:i');

            $slots[] = [
                'time'     => $time,
                'label'    => $time,
                'disabled' => in_array($time, $bookedTimes),
            ];

            $start->addMinutes(30);
        }

        return response()->json($slots);
    }
}
