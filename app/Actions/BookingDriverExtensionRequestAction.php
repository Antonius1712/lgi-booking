<?php

namespace App\Actions;

use App\Mail\BookingDriverExtensionRequestedMail;
use App\Models\DriverBooking;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class BookingDriverExtensionRequestAction
{
    public function handle(DriverBooking $booking, Request $request): void
    {
        if ($booking->status !== 'departure') {
            throw ValidationException::withMessages([
                'extension' => 'Perpanjangan hanya dapat diminta saat perjalanan sedang berlangsung.',
            ]);
        }

        if ($booking->extention_requested_at !== null) {
            throw ValidationException::withMessages([
                'extension' => 'Permintaan perpanjangan sudah dikirim dan sedang menunggu persetujuan admin.',
            ]);
        }

        $durationMinutes = (int) $request->input('duration', 30);
        $reason = $request->input('reason');

        try {
            DB::transaction(function () use ($booking, $durationMinutes, $reason) {
                $booking->update([
                    'extention_requested_at' => now(),
                    'extension_duration' => $durationMinutes,
                    'extension_request_reason' => $reason,
                ]);

                $booking->log('extension_requested', $booking->status, $booking->status, [
                    'duration_minutes' => $durationMinutes,
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

        Mail::to($testingEmail)->queue(new BookingDriverExtensionRequestedMail($booking, 'booker'));
        Mail::to($testingEmail)->queue(new BookingDriverExtensionRequestedMail($booking, 'admin'));
    }
}
