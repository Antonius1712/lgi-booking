<?php

namespace App\Actions\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Http\Requests\Admin\ChangeDriverRequest;
use App\Mail\BookingDriverChangedMail;
use App\Models\DriverBooking;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ChangeDriverAction
{
    public function handle(DriverBooking $driverBooking, ChangeDriverRequest $request): void
    {
        $oldNik = $driverBooking->driver_nik;
        $newNik = $request->driver_nik;

        if ($oldNik === $newNik) {
            throw ValidationException::withMessages([
                'driver_nik' => 'New driver is the same as current driver.',
            ]);
        }

        $conflict = $this->driverConflict(
            $newNik,
            $driverBooking->scheduled_pickup_date,
            $driverBooking->scheduled_pickup_time,
            $driverBooking->scheduled_end_time,
            $driverBooking->id,
        );

        if ($conflict) {
            throw ValidationException::withMessages([
                'driver_nik' => "Driver is already booked during this slot ({$conflict->booking_number}, "
                    .Carbon::parse($conflict->scheduled_pickup_time)->format('H:i').'–'
                    .Carbon::parse($conflict->scheduled_end_time)->format('H:i').').',
            ]);
        }

        try {
            DB::transaction(function () use ($driverBooking, $oldNik, $newNik) {
                $old = $driverBooking->status;

                $driverBooking->update([
                    'driver_nik' => $newNik,
                    'status' => DriverBookingStatusEnum::DRIVER_CHANGED->value,
                ]);

                $driverBooking->log(
                    'driver_changed', $old, DriverBookingStatusEnum::DRIVER_CHANGED->value,
                    ['old_driver_nik' => $oldNik, 'new_driver_nik' => $newNik],
                );
            });

            $this->sendNotifications($driverBooking->fresh(), $oldNik);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    private function sendNotifications(DriverBooking $driverBooking, string $oldNik): void
    {
        $oldDriver = User::where('NIK', $oldNik)->first();
        $booker = User::where('NIK', $driverBooking->user_nik)->first();
        $newDriver = User::where('NIK', $driverBooking->driver_nik)->first();

        $testing_email = 'it-dba07@lgi.co.id';

        if ($oldDriver && $booker) {
            // Mail::to($booker->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverChangedMail($driverBooking, 'booker', $oldDriver));
        }

        if ($oldDriver) {
            // Mail::to($oldDriver->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverChangedMail($driverBooking, 'old_driver', $oldDriver));
        }

        if ($newDriver && $oldDriver) {
            // Mail::to($newDriver->email)
            Mail::to($testing_email)
                ->queue(new BookingDriverChangedMail($driverBooking, 'new_driver', $oldDriver));
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
            ])
            ->where('scheduled_pickup_time', '<', $endTime)
            ->where('scheduled_end_time', '>', $startTime)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->first();
    }
}
