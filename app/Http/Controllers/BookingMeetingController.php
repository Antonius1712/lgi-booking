<?php

namespace App\Http\Controllers;

use App\Actions\Admin\MeetingRoomStoreAction;
use App\Http\Requests\MeetingRoomStoreRequest;
use App\Models\Location;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;

class BookingMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeRanges = $this->timeRange();
        $rooms = MeetingRoom::with('location')->get()
            ->groupBy(function ($room) {
                return $room->location->name;
            })->map(function ($group) {
                return $group->pluck('name')->toArray();
            })
        ->toArray();

        return view('booking.meeting-room.index', compact('timeRanges', 'rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        
    }

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
        $timeRanges = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $start = today()->setTime($hour, 0);
            $end = $start->copy()->addHour();
            $timeRanges[] = $start->format('H:i').' - '.$end->format('H:i');
        }

        return $timeRanges;
    }
}
