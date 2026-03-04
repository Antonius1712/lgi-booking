<?php

namespace App\Actions;

use App\Enums\DriverBookingStatusEnum;
use App\Mail\BookingDriverDepartureMail;
use App\Models\DriverBooking;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DriverDepartAction
{
    public function handle(DriverBooking $booking): void
    {
        $departableStatuses = [
            DriverBookingStatusEnum::BOOKED->value,
            DriverBookingStatusEnum::REMINDER_SENT_1->value,
            DriverBookingStatusEnum::REMINDER_SENT_2->value,
            DriverBookingStatusEnum::REMINDER_SENT_3->value,
        ];

        if (! in_array($booking->status, $departableStatuses)) {
            throw ValidationException::withMessages([
                'depart' => 'Booking tidak dalam status yang valid untuk dikonfirmasi keberangkatannya.',
            ]);
        }

        try {
            DB::transaction(function () use ($booking) {
                $old = $booking->status;

                $booking->update([
                    'status' => DriverBookingStatusEnum::DEPARTURE->value,
                    'actual_pickup_at' => now(),
                ]);

                $booking->log('departed', $old, DriverBookingStatusEnum::DEPARTURE->value);
            });

            $this->sendNotifications($booking->fresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    private function sendNotifications(DriverBooking $booking): void
    {
        $testingEmail = 'it-dba07@lgi.co.id';

        Mail::to($testingEmail)->queue(new BookingDriverDepartureMail($booking, 'booker'));
        Mail::to($testingEmail)->queue(new BookingDriverDepartureMail($booking, 'driver'));
    }
}
