<?php

namespace App\Actions;

use App\Mail\BookingDriverCancelledMail;
use App\Models\DriverBooking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingDriverCancelAction
{
    public function handle(DriverBooking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $old = $booking->status;

            $booking->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->user()->NIK,
                'cancelled_at' => now(),
            ]);

            $booking->log('cancelled', $old, 'cancelled');

            $this->sendNotifications($booking);
        });
    }

    private function sendNotifications(DriverBooking $booking): void
    {
        $booker = User::where('NIK', $booking->user_nik)->first();
        $driver = User::where('NIK', $booking->driver_nik)->first();

        $testing_email = 'it-dba07@lgi.co.id';

        if ($booker) {
            // Mail::to($booker->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverCancelledMail($booking, 'booker'));
        }

        if ($driver) {
            // Mail::to($driver->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverCancelledMail($booking, 'driver'));
        }
    }
}
