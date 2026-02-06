<?php

namespace App\Actions;

use App\Models\MeetingRoom;
use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingMeetingRoomStoreAction
{
    public function handle(Request $request): void
    {
        try {
            DB::transaction(function () use ($request) {
                $room = $request->room;
                $year = $request->year;
                $month = $request->month;
                $day = $request->day;
                $time = $request->time;
                $stime = $request->stime;
                $etime = $request->etime;
                $description = $request->description;
                $usage_type = $request->usage_type;

                $meeting_room = MeetingRoom::where('slug', $room)->with('location')->first();
                $room_id = $meeting_room->id;
                $location = $meeting_room->location->slug;

                $booking_date = Carbon::parse("$year-$month-$day")->format('Y-m-d');
                $time_slot = "$stime - $etime";
                $start_time = $stime;
                $end_time = $etime;

                MeetingRoomBooking::create([
                    'meeting_room_id' => $room_id,
                    'nik' => auth()->user()->NIK,
                    'booking_date' => $booking_date,
                    'time_slot' => $time_slot,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => 'Booked',
                    'description' => $description,
                    'usage_type' => $usage_type,
                    'location' => $location,
                ]);
            });
        } catch (Exception $e) {
            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }
}
