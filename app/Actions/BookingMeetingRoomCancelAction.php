<?php

namespace App\Actions;

use App\Mail\BookingRoomCancelledMail;
use App\Models\MeetingRoomBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingMeetingRoomCancelAction
{
    public function handle(MeetingRoomBooking $booking): void
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

    private function sendNotifications(MeetingRoomBooking $booking): void
    {
        $testing_email = 'it-dba07@lgi.co.id';

        // Mail::to($booking->user->email)
        Mail::to($testing_email)
            ->queue(new BookingRoomCancelledMail($booking));
    }
}
