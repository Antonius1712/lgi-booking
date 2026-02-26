<?php

namespace App\Actions\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Http\Requests\Admin\RescheduleDriverBookingRequest;
use App\Models\DriverBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RescheduleDriverBookingAction
{
    public function handle(DriverBooking $driverBooking, RescheduleDriverBookingRequest $request): void
    {
        if (in_array($driverBooking->status, [
            DriverBookingStatusEnum::COMPLETED->value,
            DriverBookingStatusEnum::CANCELLED->value,
            DriverBookingStatusEnum::AUTO_CANCELLED->value,
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
        ])) {
            throw ValidationException::withMessages([
                'pickup_date' => 'Cannot reschedule a trip that is active or already terminal.',
            ]);
        }

        $newDate = $request->pickup_date;
        $newPickupTime = $request->pickup_time.':00';
        $newEndTime = $request->end_time.':00';

        $conflict = $this->driverConflict(
            $driverBooking->driver_nik,
            $newDate,
            $newPickupTime,
            $newEndTime,
            $driverBooking->id,
        );

        if ($conflict) {
            throw ValidationException::withMessages([
                'pickup_date' => "Driver is already booked during the new slot ({$conflict->booking_number}, "
                    .Carbon::parse($conflict->scheduled_pickup_time)->format('H:i').'–'
                    .Carbon::parse($conflict->scheduled_end_time)->format('H:i').').',
            ]);
        }

        try {
            DB::transaction(function () use ($driverBooking, $request, $newDate, $newPickupTime, $newEndTime) {
                $old = $driverBooking->status;
                $oldDate = $driverBooking->scheduled_pickup_date?->toDateString();
                $oldPickup = Carbon::parse($driverBooking->scheduled_pickup_time)->format('H:i');
                $oldEnd = Carbon::parse($driverBooking->scheduled_end_time)->format('H:i');
                $newDuration = Carbon::parse($newPickupTime)->diffInMinutes(Carbon::parse($newEndTime));

                $driverBooking->update([
                    'scheduled_pickup_date' => $newDate,
                    'scheduled_pickup_time' => $newPickupTime,
                    'scheduled_end_time' => $newEndTime,
                    'scheduled_duration' => $newDuration,
                    'status' => DriverBookingStatusEnum::RESCHEDULING->value,
                ]);

                $driverBooking->log(
                    'rescheduled', $old, DriverBookingStatusEnum::RESCHEDULING->value,
                    [
                        'old_date' => $oldDate,
                        'old_pickup' => $oldPickup,
                        'old_end' => $oldEnd,
                        'new_date' => $newDate,
                        'new_pickup' => $request->pickup_time,
                        'new_end' => $request->end_time,
                    ],
                );
            });

            // TODO: SendRescheduledEmail::dispatch($driverBooking->fresh());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    private function driverConflict(
        string $driverNik,
        mixed $date,
        mixed $startTime,
        mixed $endTime,
        ?int $excludeId = null,
    ): ?DriverBooking {
        return DriverBooking::query()
            ->where('driver_nik', $driverNik)
            ->where('scheduled_pickup_date', $date)
            ->whereNotIn('status', [
                DriverBookingStatusEnum::CANCELLED->value,
                DriverBookingStatusEnum::AUTO_CANCELLED->value,
                DriverBookingStatusEnum::COMPLETED->value,
            ])
            ->where('scheduled_pickup_time', '<', $endTime)
            ->where('scheduled_end_time', '>', $startTime)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->first();
    }
}
