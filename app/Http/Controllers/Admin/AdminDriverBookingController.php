<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DriverBookingStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\BookingLog;
use App\Models\DriverBooking;
use App\Models\User;
use Carbon\Carbon;
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

    public function confirm(DriverBooking $driverBooking): RedirectResponse
    {
        $old = $driverBooking->status;

        $driverBooking->update([
            'status' => DriverBookingStatusEnum::DEPARTURE->value,
            'actual_pickup_at' => now(),
        ]);

        $driverBooking->log('confirmed', $old, DriverBookingStatusEnum::DEPARTURE->value);

        // TODO: SendDepartureEmailToUser::dispatch($driverBooking);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Booking {$driverBooking->booking_number} confirmed as departed.");
    }

    // ── Cancel ────────────────────────────────────────────────────────

    public function cancel(Request $request, DriverBooking $driverBooking): RedirectResponse
    {
        $request->validate([
            'cancelation_reason' => ['required', 'string', 'max:500'],
        ]);

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

        // TODO: SendCancelledByAdminEmail::dispatch($driverBooking);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Booking {$driverBooking->booking_number} cancelled.");
    }

    // ── Change Driver ─────────────────────────────────────────────────

    public function changeDriver(Request $request, DriverBooking $driverBooking): RedirectResponse
    {
        $request->validate([
            'driver_nik' => ['required', 'string', 'exists:LgiGlobal114.users,NIK'],
        ]);

        $oldNik = $driverBooking->driver_nik;
        $newNik = $request->driver_nik;

        if ($oldNik === $newNik) {
            return back()->withErrors(['driver_nik' => 'New driver is the same as current driver.']);
        }

        // Check new driver is free for this date + time slot
        $conflict = $this->driverConflict(
            $newNik,
            $driverBooking->scheduled_pickup_date,
            $driverBooking->scheduled_pickup_time,
            $driverBooking->scheduled_end_time,
            $driverBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'driver_nik' => "Driver is already booked during this slot ({$conflict->booking_number}, "
                    .Carbon::parse($conflict->scheduled_pickup_time)->format('H:i').'–'
                    .Carbon::parse($conflict->scheduled_end_time)->format('H:i').').',
            ]);
        }

        $old = $driverBooking->status;

        $driverBooking->update([
            'driver_nik' => $newNik,
            'status' => DriverBookingStatusEnum::DRIVER_CHANGED->value,
        ]);

        $driverBooking->log(
            'driver_changed', $old, DriverBookingStatusEnum::DRIVER_CHANGED->value,
            ['old_driver_nik' => $oldNik, 'new_driver_nik' => $newNik],
        );

        // TODO: SendDriverChangedEmail::dispatch($driverBooking, $oldNik);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Driver changed for booking {$driverBooking->booking_number}.");
    }

    // ── Extend Duration ───────────────────────────────────────────────

    public function extend(Request $request, DriverBooking $driverBooking): RedirectResponse
    {
        $request->validate([
            'extend_hours' => ['required', 'integer', 'in:1,2,3'],
        ]);

        if (! in_array($driverBooking->status, [
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
        ])) {
            return back()->withErrors(['extend_hours' => 'Can only extend an active trip.']);
        }

        $hours = (int) $request->extend_hours;
        $oldEnd = Carbon::parse($driverBooking->scheduled_end_time);
        $newEnd = $oldEnd->copy()->addHours($hours);
        $old = $driverBooking->status;

        // Overlap check
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
            return back()->withErrors([
                'extend_hours' => "Cannot extend: driver has another booking ({$conflict->booking_number}) "
                    .'at '.Carbon::parse($conflict->scheduled_pickup_time)->format('H:i').'.',
            ]);
        }

        $driverBooking->update([
            'scheduled_end_time' => $newEnd->format('H:i:s'),
            'scheduled_duration' => $driverBooking->scheduled_duration + ($hours * 60),
            'status' => DriverBookingStatusEnum::EXTENDING->value,
        ]);

        $driverBooking->log(
            'extended', $old, DriverBookingStatusEnum::EXTENDING->value,
            ['hours_added' => $hours, 'old_end' => $oldEnd->format('H:i'), 'new_end' => $newEnd->format('H:i')],
        );

        // TODO: SendExtensionApprovedEmail::dispatch($driverBooking);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Trip extended by {$hours}h. New end time: {$newEnd->format('H:i')} WIB.");
    }

    // ── Reschedule ────────────────────────────────────────────────────

    public function reschedule(Request $request, DriverBooking $driverBooking): RedirectResponse
    {
        $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:pickup_time'],
        ]);

        if (in_array($driverBooking->status, [
            DriverBookingStatusEnum::COMPLETED->value,
            DriverBookingStatusEnum::CANCELLED->value,
            DriverBookingStatusEnum::AUTO_CANCELLED->value,
            DriverBookingStatusEnum::DEPARTURE->value,
            DriverBookingStatusEnum::EXTENDING->value,
        ])) {
            return back()->withErrors(['pickup_date' => 'Cannot reschedule a trip that is active or already terminal.']);
        }

        $newDate = $request->pickup_date;
        $newPickupTime = $request->pickup_time.':00';
        $newEndTime = $request->end_time.':00';

        // Check driver availability at the new slot
        $conflict = $this->driverConflict(
            $driverBooking->driver_nik,
            $newDate,
            $newPickupTime,
            $newEndTime,
            $driverBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'pickup_date' => "Driver is already booked during the new slot ({$conflict->booking_number}, "
                    .Carbon::parse($conflict->scheduled_pickup_time)->format('H:i').'–'
                    .Carbon::parse($conflict->scheduled_end_time)->format('H:i').').',
            ]);
        }

        $old = $driverBooking->status;
        $oldDate = $driverBooking->scheduled_pickup_date?->toDateString();
        $oldPickup = Carbon::parse($driverBooking->scheduled_pickup_time)->format('H:i');
        $oldEnd = Carbon::parse($driverBooking->scheduled_end_time)->format('H:i');

        // Recalculate duration in minutes
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

        // TODO: SendRescheduledEmail::dispatch($driverBooking);

        return redirect()->route('admin.driver-bookings.show', $driverBooking)
            ->with('success', "Booking {$driverBooking->booking_number} rescheduled to {$newDate} {$request->pickup_time}–{$request->end_time}.");
    }

    // ── AJAX: Available drivers ───────────────────────────────────────

    public function availableDrivers(Request $request): \Illuminate\Http\JsonResponse
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

    /**
     * Check whether a driver has an overlapping booking on a given date+slot.
     * Returns the conflicting booking if found, null if free.
     */
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
