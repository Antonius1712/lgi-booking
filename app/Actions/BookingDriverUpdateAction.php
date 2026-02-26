<?php

namespace App\Actions;

use App\Mail\BookingDriverUpdatedMail;
use App\Models\DriverBooking;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingDriverUpdateAction
{
    public function handle(DriverBooking $driverBooking, Request $request): void
    {
        try {
            DB::transaction(function () use ($driverBooking, $request) {
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

                $driverBooking->update([
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

                $this->sendNotifications($driverBooking->fresh());
            });
        } catch (Exception $e) {
            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    private function sendNotifications(DriverBooking $driverBooking): void
    {
        $booker = User::where('NIK', $driverBooking->user_nik)->first();
        $driver = User::where('NIK', $driverBooking->driver_nik)->first();
        $cc = ['gs05.ho@lgi.co.id'];
        $bcc = [
            'it-dba07@lgi.co.id',
        ];

        $testing_email = 'it-dba07@lgi.co.id';

        if ($booker) {
            // Mail::to($booker->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverUpdatedMail($driverBooking, 'booker'));
        }

        if ($driver) {
            // Mail::to($driver->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverUpdatedMail($driverBooking, 'driver'));
        }
    }
}
