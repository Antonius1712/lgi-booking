<?php

namespace App\Actions;

use App\Enums\DriverBookingStatusEnum;
use App\Mail\BookingDriverCompletedMail;
use App\Models\DriverBooking;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DriverCompleteAction
{
    public function handle(DriverBooking $booking): void
    {
        $completableStatuses = [
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
        ];

        if (! in_array($booking->status, $completableStatuses)) {
            throw ValidationException::withMessages([
                'complete' => 'Booking tidak dalam status yang valid untuk diselesaikan.',
            ]);
        }

        try {
            DB::transaction(function () use ($booking) {
                $old = $booking->status;

                $booking->update([
                    'status' => DriverBookingStatusEnum::COMPLETED->value,
                    'actual_end_at' => now(),
                ]);

                $booking->log('completed', $old, DriverBookingStatusEnum::COMPLETED->value);
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

        Mail::to($testingEmail)->queue(new BookingDriverCompletedMail($booking, 'booker'));
        Mail::to($testingEmail)->queue(new BookingDriverCompletedMail($booking, 'driver'));
    }
}
