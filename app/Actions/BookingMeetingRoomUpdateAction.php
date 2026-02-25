<?php

namespace App\Actions;

use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

class BookingMeetingRoomUpdateAction
{
    public function handle($id, Request $request): void
    {
        try {
            DB::transaction(function () use ($id, $request) {
                $room = $request->room;
                $year = $request->year;
                $month = $request->month;
                $day = $request->day;
                $time = $request->time;
                $stime = $request->e_stime;
                $etime = $request->e_etime;
                $description = $request->e_description;

                $booking_date = Carbon::parse("$year-$month-$day")->format('Y-m-d');
                $time_slot = "$stime - $etime";
                $start_time = $stime;
                $end_time = $etime;

                MeetingRoomBooking::where('id', $id)->update([
                    'time_slot' => $time_slot,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => 'Booked',
                    'description' => $description,
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
