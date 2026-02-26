<?php

namespace App\Actions\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Http\Requests\Admin\CancelDriverBookingRequest;
use App\Mail\BookingDriverCancelledMail;
use App\Models\DriverBooking;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancelDriverBookingAction
{
    public function handle(DriverBooking $driverBooking, CancelDriverBookingRequest $request): void
    {
        try {
            DB::transaction(function () use ($driverBooking, $request) {
                $old = $driverBooking->status;

                $driverBooking->update([
                    'status' => DriverBookingStatusEnum::CANCELLED->value,
                    'cancelled_by' => auth()->user()->NIK,
                    'cancelled_at' => now(),
                    'cancelation_reason' => $request->cancelation_reason,
                ]);

                $driverBooking->log(
                    'cancelled', $old, DriverBookingStatusEnum::CANCELLED->value,
                    ['reason' => $request->cancelation_reason],
                );
            });

            $this->sendNotifications($driverBooking->fresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    private function sendNotifications(DriverBooking $driverBooking): void
    {
        $booker = User::where('NIK', $driverBooking->user_nik)->first();
        $driver = User::where('NIK', $driverBooking->driver_nik)->first();

        $testing_email = 'it-dba07@lgi.co.id';

        if ($booker) {
            // Mail::to($booker->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverCancelledMail($driverBooking, 'booker'));
        }

        if ($driver) {
            // Mail::to($driver->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverCancelledMail($driverBooking, 'driver'));
        }
    }
}
