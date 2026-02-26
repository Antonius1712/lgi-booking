<?php

namespace App\Actions\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Models\DriverBooking;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfirmDriverBookingAction
{
    public function handle(DriverBooking $driverBooking): void
    {
        try {
            DB::transaction(function () use ($driverBooking) {
                $old = $driverBooking->status;

                $driverBooking->update([
                    'status' => DriverBookingStatusEnum::DEPARTURE->value,
                    'actual_pickup_at' => now(),
                ]);

                $driverBooking->log('confirmed', $old, DriverBookingStatusEnum::DEPARTURE->value);
            });

            // TODO: SendDepartureEmailToUser::dispatch($driverBooking->fresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
