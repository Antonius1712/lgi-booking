<?php

namespace App\Actions\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Http\Requests\Admin\ExtendDriverBookingRequest;
use App\Models\DriverBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ExtendDriverBookingAction
{
    public function handle(DriverBooking $driverBooking, ExtendDriverBookingRequest $request): void
    {
        if (! in_array($driverBooking->status, [
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
        ])) {
            throw ValidationException::withMessages([
                'extend_hours' => 'Can only extend an active trip.',
            ]);
        }

        $hours = (int) $request->extend_hours;
        $oldEnd = Carbon::parse($driverBooking->scheduled_end_time);
        $newEnd = $oldEnd->copy()->addHours($hours);

        $conflict = DriverBooking::query()
            ->where('driver_nik', $driverBooking->driver_nik)
            ->where('scheduled_pickup_date', $driverBooking->scheduled_pickup_date)
            ->where('id', '!=', $driverBooking->id)
            ->whereNotIn('status', [
                DriverBookingStatusEnum::CANCELLED->value,
                DriverBookingStatusEnum::AUTO_CANCELLED->value,
                DriverBookingStatusEnum::COMPLETED->value,
            ])
            ->where('scheduled_pickup_time', '<', $newEnd->format('H:i:s'))
            ->where('scheduled_end_time', '>', $oldEnd->format('H:i:s'))
            ->first();

        if ($conflict) {
            throw ValidationException::withMessages([
                'extend_hours' => "Cannot extend: driver has another booking ({$conflict->booking_number}) "
                    .'at '.Carbon::parse($conflict->scheduled_pickup_time)->format('H:i').'.',
            ]);
        }

        try {
            DB::transaction(function () use ($driverBooking, $hours, $oldEnd, $newEnd) {
                $old = $driverBooking->status;

                $driverBooking->update([
                    'scheduled_end_time' => $newEnd->format('H:i:s'),
                    'scheduled_duration' => $driverBooking->scheduled_duration + ($hours * 60),
                    'status' => DriverBookingStatusEnum::EXTENDING->value,
                ]);

                $driverBooking->log(
                    'extended', $old, DriverBookingStatusEnum::EXTENDING->value,
                    ['hours_added' => $hours, 'old_end' => $oldEnd->format('H:i'), 'new_end' => $newEnd->format('H:i')],
                );
            });

            // TODO: SendExtensionApprovedEmail::dispatch($driverBooking->fresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
