<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CancelDriverBookingAction;
use App\Actions\Admin\ChangeDriverAction;
use App\Actions\Admin\ConfirmDriverBookingAction;
use App\Actions\Admin\ExtendDriverBookingAction;
use App\Actions\Admin\RescheduleDriverBookingAction;
use App\Enums\DriverBookingStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelDriverBookingRequest;
use App\Http\Requests\Admin\ChangeDriverRequest;
use App\Http\Requests\Admin\ExtendDriverBookingRequest;
use App\Http\Requests\Admin\RescheduleDriverBookingRequest;
use App\Models\BookingLog;
use App\Models\DriverBooking;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDriverBookingController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = DriverBooking::query()
            ->with(['user:NIK,Name', 'driver:NIK,Name'])
            ->orderByDesc('scheduled_pickup_date')
            ->orderByDesc('scheduled_pickup_time');

        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->status);
        }
        if ($request->filled('driver_nik')) {
            $query->where('driver_nik', $request->driver_nik);
        }
        if ($request->filled('date_from')) {
            $query->where('scheduled_pickup_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('scheduled_pickup_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('booking_number', 'like', "%{$s}%")
                ->orWhere('destination', 'like', "%{$s}%")
                ->orWhereHas('user', fn ($u) => $u->where('Name', 'like', "%{$s}%"))
            );
        }

        $bookings = $query->paginate(20)->withQueryString();
        $drivers = $this->allDrivers();
        $statuses = DriverBookingStatusEnum::cases();

        return view('admin.bookings.driver.index', compact('bookings', 'drivers', 'statuses'));
    }

    // ── Detail ────────────────────────────────────────────────────────

    public function show(DriverBooking $driverBooking): View
    {
        $driverBooking->load(['user', 'driver', 'logs']);

        $performers = BookingLog::resolvePerformers($driverBooking->logs);
        $drivers = $this->allDrivers();

        $isTerminal = in_array($driverBooking->status, [
            DriverBookingStatusEnum::COMPLETED->value,
            DriverBookingStatusEnum::CANCELLED->value,
            DriverBookingStatusEnum::AUTO_CANCELLED->value,
        ]);
        $isActive = in_array($driverBooking->status, [
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
            DriverBookingStatusEnum::RESCHEDULING->value,
        ]);
        $isReminded = in_array($driverBooking->status, [
            DriverBookingStatusEnum::REMINDER_SENT_1->value,
            DriverBookingStatusEnum::REMINDER_SENT_2->value,
            DriverBookingStatusEnum::REMINDER_SENT_3->value,
        ]);
        $isUpcoming = in_array($driverBooking->status, [
            DriverBookingStatusEnum::BOOKED->value,
            DriverBookingStatusEnum::WAITING_CONFIRMATION->value,
        ]);

        $canConfirm = $isUpcoming || $isReminded;
        $canCancel = ! $isTerminal;
        $canChange = ! $isTerminal && ! $isActive;
        $canExtend = $isActive;
        $canReschedule = ! $isTerminal && ! $isActive;

        return view('admin.bookings.driver.show', compact(
            'driverBooking', 'drivers', 'performers',
            'isTerminal', 'isActive',
            'canConfirm', 'canCancel', 'canChange', 'canExtend', 'canReschedule',
        ));
    }

    // ── Confirm ───────────────────────────────────────────────────────

    public function confirm(DriverBooking $driverBooking, ConfirmDriverBookingAction $action): RedirectResponse
    {
        $action->handle($driverBooking);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Booking {$driverBooking->booking_number} confirmed as departed.");
    }

    // ── Cancel ────────────────────────────────────────────────────────

    public function cancel(CancelDriverBookingRequest $request, DriverBooking $driverBooking, CancelDriverBookingAction $action): RedirectResponse
    {
        $action->handle($driverBooking, $request);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Booking {$driverBooking->booking_number} cancelled.");
    }

    // ── Change Driver ─────────────────────────────────────────────────

    public function changeDriver(ChangeDriverRequest $request, DriverBooking $driverBooking, ChangeDriverAction $action): RedirectResponse
    {
        $action->handle($driverBooking, $request);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Driver changed for booking {$driverBooking->booking_number}.");
    }

    // ── Extend Duration ───────────────────────────────────────────────

    public function extend(ExtendDriverBookingRequest $request, DriverBooking $driverBooking, ExtendDriverBookingAction $action): RedirectResponse
    {
        $action->handle($driverBooking, $request);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Trip extended. New end time: {$driverBooking->fresh()->scheduled_end_time} WIB.");
    }

    // ── Reschedule ────────────────────────────────────────────────────

    public function reschedule(RescheduleDriverBookingRequest $request, DriverBooking $driverBooking, RescheduleDriverBookingAction $action): RedirectResponse
    {
        $action->handle($driverBooking, $request);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Booking {$driverBooking->booking_number} rescheduled to {$request->pickup_date} {$request->pickup_time}–{$request->end_time}.");
    }

    // ── AJAX: Available drivers ───────────────────────────────────────

    public function availableDrivers(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'time_start' => ['required'],
            'time_end' => ['required'],
            'exclude_booking_id' => ['nullable', 'integer'],
        ]);

        $busyNiks = DriverBooking::query()
            ->where('scheduled_pickup_date', $request->date)
            ->whereNotIn('status', [
                DriverBookingStatusEnum::CANCELLED->value,
                DriverBookingStatusEnum::AUTO_CANCELLED->value,
                DriverBookingStatusEnum::COMPLETED->value,
            ])
            ->where(fn ($q) => $q->whereBetween('scheduled_pickup_time', [$request->time_start, $request->time_end])
                ->orWhereBetween('scheduled_end_time', [$request->time_start, $request->time_end])
            )
            ->when($request->exclude_booking_id, fn ($q) => $q->where('id', '!=', $request->exclude_booking_id)
            )
            ->pluck('driver_nik')
            ->toArray();

        $drivers = $this->allDrivers()->map(fn ($d) => [
            'NIK' => $d->NIK,
            'Name' => $d->Name,
            'initials' => $d->initials(),
            'busy' => in_array($d->NIK, $busyNiks),
        ]);

        return response()->json($drivers);
    }

    // ── Private ───────────────────────────────────────────────────────

    private function allDrivers()
    {
        return User::query()
            ->whereHas('UserGroup', fn ($q) => $q
                ->whereHas('Group', fn ($q) => $q
                    ->whereHas('App', fn ($q) => $q->where('AppCode', 'lgi-booking'))
                    ->where('GroupCode', RoleEnum::DRIVER)
                )
            )
            ->get(['NIK', 'Name']);
    }
}
