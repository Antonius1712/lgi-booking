<?php

namespace App\Actions;

use App\Enums\DriverBookingStatusEnum;
use App\Mail\BookingDriverCancelledMail;
use App\Models\DriverBooking;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DriverAutoCancelAction
{
    public function handle(DriverBooking $booking): void
    {
        try {
            DB::transaction(function () use ($booking) {
                $old = $booking->status;

                $booking->update([
                    'status' => DriverBookingStatusEnum::AUTO_CANCELLED->value,
                    'cancelled_at' => now(),
                    'cancelation_reason' => 'Otomatis dibatalkan: tidak ada konfirmasi keberangkatan 15 menit setelah waktu penjemputan.',
                ]);

                $booking->log('auto_cancelled', $old, DriverBookingStatusEnum::AUTO_CANCELLED->value);
            });

            $this->sendNotifications($booking->fresh());
        } catch (Exception $e) {
            Log::error("AutoCancel failed for booking {$booking->id}: {$e->getMessage()}");
        }
    }

    private function sendNotifications(DriverBooking $booking): void
    {
        $booker = User::where('NIK', $booking->user_nik)->first();
        $driver = User::where('NIK', $booking->driver_nik)->first();

        $testingEmail = 'it-dba07@lgi.co.id';

        if ($booker) {
            // Mail::to($booker->email)
            Mail::to($testingEmail)->queue(new BookingDriverCancelledMail($booking, 'booker'));
        }

        if ($driver) {
            // Mail::to($driver->email)
            Mail::to($testingEmail)->queue(new BookingDriverCancelledMail($booking, 'driver'));
        }
    }
}
