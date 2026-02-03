<?php

namespace App\Http\Controllers;

use App\CalendarType;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;

class BookingMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sdate = $request->sdate;
        $edate = $request->edate;

        $calendarTypes = CalendarType::cases();

        // Fetch meeting rooms with their bookings within the date range
        $rooms = MeetingRoom::with(['location', 'booking' => function ($query) use ($sdate, $edate) {
            $query->whereBetween('booking_date', [$sdate, $edate]);
        }])->get();

        // Group rooms by location
        $groupedRooms = $rooms->groupBy(function ($room) {
            return $room->location->name; // Group by location name
        });

        // Prepare time slots availability
        $availability = [];
        $timeRanges = $this->timeRange();

        foreach ($groupedRooms as $location => $rooms) {
            foreach ($rooms as $room) {
                // Initialize availability for each room
                $availability[$location][$room->name] = [];

                // Loop through each date in the range
                for ($date = strtotime($sdate); $date <= strtotime($edate); $date = strtotime('+1 day', $date)) {
                    $dateString = date('Y-m-d', $date);
                    $availability[$location][$room->name][$dateString] = [];

                    // Check each time range
                    foreach ($timeRanges as $timeRange) {
                        [$start, $end] = explode(' - ', $timeRange);

                        // Use the 'any()' method to check for overlapping bookings
                        $isBooked = $room->booking()->where('booking_date', $dateString)
                            ->where(function ($query) use ($start, $end) {
                                $query->where('start_time', '<', $end)
                                    ->where('end_time', '>', $start);
                            })->exists(); // Check if there are any overlapping bookings

                        // If not booked, add to availability
                        if (! $isBooked) {
                            $availability[$location][$room->name][$dateString][] = $timeRange;
                        }
                    }
                }
            }
        }

        // dd($availability);

        return view('booking.meeting-room.index', compact('availability', 'groupedRooms', 'calendarTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store() {}

    /**
     * Display the specified resource.
     */
    public function show(MeetingRoom $meetingRoom)
    {
        $timeRanges = $this->timeRange();

        return view('booking.meeting-room.show', compact('meetingRoom', 'timeRanges'));
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

    private function timeRange()
    {
        // $timeRanges = [];
        // for ($hour = 0; $hour < 24; $hour++) {
        //     $start = today()->setTime($hour, 0);
        //     $end = $start->copy()->addHour();
        //     $timeRanges[] = $start->format('H:i') . ' - ' . $end->format('H:i');
        // }

        // return $timeRanges;

        $timeRanges = [];
        for ($hour = 8; $hour < 17; $hour++) {
            $start = today()->setTime($hour, 0);
            $end = $start->copy()->addHour();
            $timeRanges[] = $start->format('H:i').' - '.$end->format('H:i');
        }

        return $timeRanges;
    }
}
