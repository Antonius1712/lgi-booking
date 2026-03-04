<?php

namespace App\Http\Controllers;

use App\Actions\BookingDriverCancelAction;
use App\Actions\DriverCompleteAction;
use App\Actions\DriverDepartAction;
use App\Actions\DriverRemindAction;
use App\Models\DriverBooking;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

class DriverTripController extends Controller
{
    public function depart(DriverBooking $driverBooking, #[CurrentUser] User $user, DriverDepartAction $action): RedirectResponse
    {
        abort_if($driverBooking->driver_nik !== $user->NIK, 403);

        $action->handle($driverBooking);

        return back()->with(['success' => 'Keberangkatan berhasil dikonfirmasi.']);
    }

    public function remind(DriverBooking $driverBooking, #[CurrentUser] User $user, DriverRemindAction $action): RedirectResponse
    {
        abort_if($driverBooking->driver_nik !== $user->NIK, 403);

        $action->handle($driverBooking);

        return back()->with(['success' => 'Reminder berhasil dikirim ke pemohon.']);
    }

    public function complete(DriverBooking $driverBooking, #[CurrentUser] User $user, DriverCompleteAction $action): RedirectResponse
    {
        abort_if($driverBooking->driver_nik !== $user->NIK, 403);

        $action->handle($driverBooking);

        return back()->with(['success' => 'Perjalanan berhasil diselesaikan.']);
    }

    public function cancel(DriverBooking $driverBooking, #[CurrentUser] User $user, BookingDriverCancelAction $action): RedirectResponse
    {
        abort_if($driverBooking->driver_nik !== $user->NIK, 403);
        abort_if($driverBooking->reminder_count < 3, 403);

        $action->handle($driverBooking);

        return back()->with(['success' => 'Pemesanan berhasil dibatalkan.']);
    }
}
