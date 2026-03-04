<?php

namespace App\Actions\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Mail\BookingDriverCompletedMail;
use App\Models\DriverBooking;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ForceCompleteDriverBookingAction
{
    public function handle(DriverBooking $booking): void
    {
        $terminalStatuses = [
            DriverBookingStatusEnum::COMPLETED->value,
            DriverBookingStatusEnum::CANCELLED->value,
            DriverBookingStatusEnum::AUTO_CANCELLED->value,
        ];

        if (in_array($booking->status, $terminalStatuses)) {
            throw ValidationException::withMessages([
                'force_complete' => 'Booking ini sudah dalam status terminal dan tidak dapat diselesaikan.',
            ]);
        }

        try {
            DB::transaction(function () use ($booking) {
                $old = $booking->status;

                $booking->update([
                    'status' => DriverBookingStatusEnum::COMPLETED->value,
                    'actual_end_at' => now(),
                ]);

                $booking->log('completed', $old, DriverBookingStatusEnum::COMPLETED->value, [
                    'force_completed_by_admin' => true,
                ]);
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
