<?php

namespace App\Actions;

use App\Mail\BookingRoomUpdatedMail;
use App\Models\MeetingRoomBooking;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingMeetingRoomUpdateAction
{
    public function handle(MeetingRoomBooking $booking, Request $request): void
    {
        try {
            DB::transaction(function () use ($booking, $request) {
                $stime = $request->e_stime;
                $etime = $request->e_etime;
                $description = $request->e_description;
                $guests = json_decode($request->input('participants_json', '[]'), true) ?: [];

                $time_slot = "$stime - $etime";
                $start_time = $stime;
                $end_time = $etime;

                $booking->update([
                    'time_slot' => $time_slot,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => 'Booked',
                    'description' => $description,
                    'guest_emails' => $guests,
                ]);

                $this->sendNotifications($booking->fresh());
            });
        } catch (Exception $e) {
            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    private function sendNotifications(MeetingRoomBooking $booking): void
    {
        $testing_email = 'it-dba07@lgi.co.id';

        $guestEmails = collect($booking->guest_emails ?? [])
            ->pluck('email')
            ->filter();

        $recipients = collect([$testing_email])
            ->merge($guestEmails)
            ->unique();

        foreach ($recipients as $email) {
            Mail::to($email)->queue(new BookingRoomUpdatedMail($booking));
        }
    }
}
