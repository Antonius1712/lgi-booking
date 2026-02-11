<?php

namespace App\Actions;

use App\Enums\DriverBookingStatusEnum;
use App\Models\DriverBooking;
use App\Services\BookingNumberGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingDriverStoreAction
{
    public function handle(Request $request): void
    {
        DB::transaction(function () use ($request) {
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

            $booking_number = BookingNumberGenerator::generate();
            $scheduled_time_slot = "$stime - $etime";

            DriverBooking::create([
                'booking_number' => $booking_number,
                'user_nik' => auth()->user()->NIK,
                'driver_nik' => $driver_nik,
                'status' => DriverBookingStatusEnum::BOOKED,

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
    }
}
