<?php

namespace App\Actions;

use App\Models\DriverBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingDriverUpdateAction
{
    public function handle(string $id, Request $request): void
    {
        try {
            DB::transaction(function () use ($id, $request) {
                $driver = $request->driver;
                $driver_nik = $request->driver_nik;
                $year = $request->year;
                $month = $request->month;
                $day = $request->day;
                $time = $request->time;
                $stimeParse = Carbon::parse($request->stime);
                $etimeParse = Carbon::parse($request->etime);

                $stime = $stimeParse->format('H:i:s');
                $etime = $etimeParse->format('H:i:s');
                $date = Carbon::parse("$year-$month-$day")->format('Y-m-d');

                $duration_in_minute = $stimeParse->diffInMinutes($etime);
                $destination = $request->destination;
                $purpose_of_trip = $request->purpose_of_trip;

                $scheduled_pickup_at = Carbon::parse("$date $stime");
                $shceduled_end_at = Carbon::parse("$date $etime");

                $scheduled_time_slot = "$stime - $etime";

                DriverBooking::find($id)->update([
                    'destination' => $destination,
                    'scheduled_pickup_at' => $scheduled_pickup_at,
                    'scheduled_pickup_date' => $date,
                    'scheduled_pickup_time' => $stime,

                    'scheduled_end_at' => $shceduled_end_at,
                    'scheduled_end_date' => $date,
                    'scheduled_end_time' => $etime,

                    'scheduled_time_slot' => $scheduled_time_slot,

                    'scheduled_duration' => $duration_in_minute,
                    'purpose_of_trip' => $purpose_of_trip,
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
