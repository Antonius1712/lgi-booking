<?php

namespace App\Actions;

use App\Enums\DriverBookingStatusEnum;
use App\Mail\BookingDriverReminderMail;
use App\Models\DriverBooking;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DriverRemindAction
{
    public function handle(DriverBooking $booking): void
    {
        if ($booking->reminder_count >= 3) {
            throw ValidationException::withMessages([
                'remind' => 'Maximum reminders already sent. You may now cancel this booking.',
            ]);
        }

        try {
            DB::transaction(function () use ($booking) {
                $newCount = $booking->reminder_count + 1;

                $newStatus = match ($newCount) {
                    1 => DriverBookingStatusEnum::REMINDER_SENT_1->value,
                    2 => DriverBookingStatusEnum::REMINDER_SENT_2->value,
                    default => DriverBookingStatusEnum::REMINDER_SENT_3->value,
                };

                $old = $booking->status;

                $booking->update([
                    'reminder_count' => $newCount,
                    'last_reminder_sent_at' => now(),
                    'status' => $newStatus,
                ]);

                $booking->log('reminder_sent', $old, $newStatus, ['reminder_count' => $newCount]);
            });

            $this->sendNotifications($booking->fresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    private function sendNotifications(DriverBooking $booking): void
    {
        $booker = User::where('NIK', $booking->user_nik)->first();

        $testing_email = 'it-dba07@lgi.co.id';

        if ($booker) {
            // Mail::to($booker->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverReminderMail($booking, 'booker'));
        }
    }
}
