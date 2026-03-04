<?php

namespace App\Http\Controllers;

use App\Actions\BookingDriverExtensionRequestAction;
use App\Enums\DriverBookingStatusEnum;
use App\Models\DriverBooking;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyBookingDriverController extends Controller
{
    public function index(#[CurrentUser] User $user, Request $request): View
    {
        $filter = $request->input('filter', 'upcoming');

        $query = DriverBooking::query()
            ->where('user_nik', $user->NIK)
            ->with(['driver:NIK,Name,NoTelp'])
            ->orderByDesc('scheduled_pickup_date')
            ->orderByDesc('scheduled_pickup_time');

        $terminalStatuses = [
            DriverBookingStatusEnum::COMPLETED->value,
            DriverBookingStatusEnum::CANCELLED->value,
            DriverBookingStatusEnum::AUTO_CANCELLED->value,
        ];

        $activeStatuses = [
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
        ];

        match ($filter) {
            'active' => $query->whereIn('status', $activeStatuses),
            'completed' => $query->whereIn('status', $terminalStatuses),
            default => $query->whereNotIn('status', [...$activeStatuses, ...$terminalStatuses]),
        };

        $bookings = $query->paginate(10)->withQueryString();

        return view('my-booking.driver', compact('bookings', 'filter'));
    }

    public function requestExtension(DriverBooking $driverBooking, #[CurrentUser] User $user, BookingDriverExtensionRequestAction $action): RedirectResponse
    {
        abort_if($driverBooking->user_nik !== $user->NIK, 403);

        $action->handle($driverBooking, request());

        return back()->with(['success' => 'Permintaan perpanjangan berhasil dikirim ke admin.']);
    }
}
