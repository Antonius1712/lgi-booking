<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingLog;
use App\Models\MeetingRoom;
use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMeetingRoomBookingController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = MeetingRoomBooking::query()
            ->with(['user:NIK,Name', 'meetingRoom:id,name', 'meetingRoom.location:id,name'])
            ->orderByDesc('booking_date')
            ->orderBy('start_time');

        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->status);
        }
        if ($request->filled('room_id')) {
            $query->where('meeting_room_id', $request->room_id);
        }
        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->whereHas('user', fn ($u) => $u->where('Name', 'like', "%{$s}%"))
                ->orWhere('description', 'like', "%{$s}%")
            );
        }

        $bookings = $query->paginate(20)->withQueryString();
        $rooms = MeetingRoom::with('location:id,name')->orderBy('name')->get(['id', 'name']);
        $statuses = ['booked', 'in_use', 'completed', 'cancelled'];

        return view('admin.bookings.meeting-room.index', compact('bookings', 'rooms', 'statuses'));
    }

    // ── Detail ────────────────────────────────────────────────────────

    public function show(MeetingRoomBooking $meetingRoomBooking): View
    {
        $meetingRoomBooking->load(['user', 'meetingRoom', 'meetingRoom.location', 'logs']);

        $performers = BookingLog::resolvePerformers($meetingRoomBooking->logs);
        $rooms = MeetingRoom::with('location:id,name')->orderBy('name')->get(['id', 'name']);

        $isTerminal = in_array($meetingRoomBooking->status, ['completed', 'cancelled']);
        $isActive = $meetingRoomBooking->status === 'in_use';
        $canCancel = ! $isTerminal;
        $canChange = ! $isTerminal;
        $canExtend = $isActive;
        $canReschedule = ! $isTerminal && ! $isActive;

        return view('admin.bookings.meeting-room.show', compact(
            'meetingRoomBooking', 'rooms', 'performers',
            'isTerminal', 'isActive', 'canCancel', 'canChange', 'canExtend', 'canReschedule',
        ));
    }

    // ── Cancel ────────────────────────────────────────────────────────

    public function cancel(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'cancelation_reason' => ['required', 'string', 'max:500'],
        ]);

        $old = $meetingRoomBooking->status;

        $meetingRoomBooking->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->user()->NIK,
            'cancelled_at' => now(),
            'cancelation_reason' => $request->cancelation_reason,
        ]);

        $meetingRoomBooking->log(
            'cancelled', $old, 'cancelled',
            ['reason' => $request->cancelation_reason],
        );

        // TODO: SendRoomCancelledEmail::dispatch($meetingRoomBooking);

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', 'Booking cancelled successfully.');
    }

    // ── Change Room ───────────────────────────────────────────────────

    public function changeRoom(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'meeting_room_id' => ['required', 'integer', 'exists:meeting_rooms,id'],
        ]);

        $newRoomId = (int) $request->meeting_room_id;
        $oldRoomId = $meetingRoomBooking->meeting_room_id;

        if ($oldRoomId === $newRoomId) {
            return back()->withErrors(['meeting_room_id' => 'New room is the same as current room.']);
        }

        $conflict = $this->roomConflict(
            $newRoomId,
            $meetingRoomBooking->booking_date,
            $meetingRoomBooking->start_time,
            $meetingRoomBooking->end_time,
            $meetingRoomBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'meeting_room_id' => "Room is already booked during this slot (Booking #{$conflict->id}, "
                    .Carbon::parse($conflict->start_time)->format('H:i').'–'
                    .Carbon::parse($conflict->end_time)->format('H:i').').',
            ]);
        }

        $old = $meetingRoomBooking->status;
        $meetingRoomBooking->update(['meeting_room_id' => $newRoomId]);

        $meetingRoomBooking->log(
            'room_changed', $old, $old,
            ['old_room_id' => $oldRoomId, 'new_room_id' => $newRoomId],
        );

        // TODO: SendRoomChangedEmail::dispatch($meetingRoomBooking);

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', 'Room changed successfully.');
    }

    // ── Change Time Slot ──────────────────────────────────────────────

    public function changeTime(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $conflict = $this->roomConflict(
            $meetingRoomBooking->meeting_room_id,
            $meetingRoomBooking->booking_date,
            $request->start_time.':00',
            $request->end_time.':00',
            $meetingRoomBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'start_time' => "Room is already booked during this slot (Booking #{$conflict->id}, "
                    .Carbon::parse($conflict->start_time)->format('H:i').'–'
                    .Carbon::parse($conflict->end_time)->format('H:i').').',
            ]);
        }

        $old = $meetingRoomBooking->status;
        $oldStart = $meetingRoomBooking->start_time;
        $oldEnd = $meetingRoomBooking->end_time;

        $meetingRoomBooking->update([
            'start_time' => $request->start_time.':00',
            'end_time' => $request->end_time.':00',
        ]);

        $meetingRoomBooking->log(
            'time_changed', $old, $old,
            [
                'old_start' => Carbon::parse($oldStart)->format('H:i'),
                'old_end' => Carbon::parse($oldEnd)->format('H:i'),
                'new_start' => $request->start_time,
                'new_end' => $request->end_time,
            ],
        );

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', 'Time slot updated successfully.');
    }

    // ── Change Room + Time ────────────────────────────────────────────

    public function changeRoomAndTime(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'meeting_room_id' => ['required', 'integer', 'exists:meeting_rooms,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $conflict = $this->roomConflict(
            (int) $request->meeting_room_id,
            $meetingRoomBooking->booking_date,
            $request->start_time.':00',
            $request->end_time.':00',
            $meetingRoomBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'meeting_room_id' => "Room is already booked during this slot (Booking #{$conflict->id}, "
                    .Carbon::parse($conflict->start_time)->format('H:i').'–'
                    .Carbon::parse($conflict->end_time)->format('H:i').').',
            ]);
        }

        $old = $meetingRoomBooking->status;

        $meetingRoomBooking->update([
            'meeting_room_id' => (int) $request->meeting_room_id,
            'start_time' => $request->start_time.':00',
            'end_time' => $request->end_time.':00',
        ]);

        $meetingRoomBooking->log(
            'room_changed', $old, $old,
            [
                'old_room_id' => $meetingRoomBooking->getOriginal('meeting_room_id'),
                'new_room_id' => (int) $request->meeting_room_id,
                'new_start' => $request->start_time,
                'new_end' => $request->end_time,
            ],
        );

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', 'Room and time slot updated successfully.');
    }

    // ── Extend Duration ───────────────────────────────────────────────

    public function extend(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'extend_hours' => ['required', 'integer', 'in:1,2,3'],
        ]);

        if ($meetingRoomBooking->status !== 'in_use') {
            return back()->withErrors(['extend_hours' => 'Can only extend a booking that is currently in use.']);
        }

        $hours = (int) $request->extend_hours;
        $oldEnd = Carbon::parse($meetingRoomBooking->end_time);
        $newEnd = $oldEnd->copy()->addHours($hours);
        $old = $meetingRoomBooking->status;

        $conflict = $this->roomConflict(
            $meetingRoomBooking->meeting_room_id,
            $meetingRoomBooking->booking_date,
            $oldEnd->format('H:i:s'),
            $newEnd->format('H:i:s'),
            $meetingRoomBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'extend_hours' => "Cannot extend: room is already booked (#{$conflict->id}) "
                    .'at '.Carbon::parse($conflict->start_time)->format('H:i').'.',
            ]);
        }

        $meetingRoomBooking->update([
            'end_time' => $newEnd->format('H:i:s'),
        ]);

        $meetingRoomBooking->log(
            'extended', $old, $old,
            [
                'hours_added' => $hours,
                'old_end' => $oldEnd->format('H:i'),
                'new_end' => $newEnd->format('H:i'),
            ],
        );

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', "Room booking extended by {$hours}h. New end time: {$newEnd->format('H:i')} WIB.");
    }

    // ── Reschedule ────────────────────────────────────────────────────

    public function reschedule(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        if (in_array($meetingRoomBooking->status, ['completed', 'cancelled', 'in_use'])) {
            return back()->withErrors(['booking_date' => 'Cannot reschedule a booking that is active or already terminal.']);
        }

        $newDate = $request->booking_date;
        $newStartTime = $request->start_time.':00';
        $newEndTime = $request->end_time.':00';

        $conflict = $this->roomConflict(
            $meetingRoomBooking->meeting_room_id,
            $newDate,
            $newStartTime,
            $newEndTime,
            $meetingRoomBooking->id,
        );

        if ($conflict) {
            return back()->withErrors([
                'booking_date' => "Room is already booked during the new slot (Booking #{$conflict->id}, "
                    .Carbon::parse($conflict->start_time)->format('H:i').'–'
                    .Carbon::parse($conflict->end_time)->format('H:i').').',
            ]);
        }

        $old = $meetingRoomBooking->status;
        $oldDate = $meetingRoomBooking->booking_date?->toDateString();
        $oldStart = Carbon::parse($meetingRoomBooking->start_time)->format('H:i');
        $oldEnd = Carbon::parse($meetingRoomBooking->end_time)->format('H:i');

        $meetingRoomBooking->update([
            'booking_date' => $newDate,
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);

        $meetingRoomBooking->log(
            'rescheduled', $old, $old,
            [
                'old_date' => $oldDate,
                'old_start' => $oldStart,
                'old_end' => $oldEnd,
                'new_date' => $newDate,
                'new_start' => $request->start_time,
                'new_end' => $request->end_time,
            ],
        );

        // TODO: SendRescheduledEmail::dispatch($meetingRoomBooking);

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', "Booking rescheduled to {$newDate} {$request->start_time}–{$request->end_time}.");
    }

    // ── Update Guest Emails ───────────────────────────────────────────

    public function updateGuests(Request $request, MeetingRoomBooking $meetingRoomBooking): RedirectResponse
    {
        $request->validate([
            'guest_emails' => ['nullable', 'string'],
        ]);

        $raw = $request->guest_emails ?? '';
        $emails = collect(preg_split('/[\s,]+/', $raw))
            ->map(fn ($e) => trim($e))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values()
            ->toArray();

        $old = $meetingRoomBooking->guest_emails ?? [];

        $meetingRoomBooking->update(['guest_emails' => $emails]);

        $meetingRoomBooking->log(
            'guests_updated', null, null,
            ['old_count' => count($old), 'new_count' => count($emails), 'emails' => $emails],
        );

        return redirect()->route('admin.meeting-room-bookings.show', $meetingRoomBooking)
            ->with('success', 'Guest emails updated ('.count($emails).' recipients).');
    }

    // ── AJAX: Available rooms ─────────────────────────────────────────

    public function availableRooms(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'exclude_booking_id' => ['nullable', 'integer'],
        ]);

        $busyRoomIds = MeetingRoomBooking::query()
            ->where('booking_date', $request->date)
            ->whereNotIn('status', ['cancelled'])
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->when($request->exclude_booking_id, fn ($q) => $q->where('id', '!=', $request->exclude_booking_id)
            )
            ->pluck('meeting_room_id')
            ->toArray();

        $rooms = MeetingRoom::with('location:id,name')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'location' => $r->location?->name ?? '-',
                'busy' => in_array($r->id, $busyRoomIds),
            ]);

        return response()->json($rooms);
    }

    // ── Private ───────────────────────────────────────────────────────

    private function roomConflict(
        int $roomId,
        mixed $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null,
    ): ?MeetingRoomBooking {
        return MeetingRoomBooking::query()
            ->where('meeting_room_id', $roomId)
            ->where('booking_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->first();
    }
}
