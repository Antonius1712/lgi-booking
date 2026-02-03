<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\DeleteBookingAction;
use App\Actions\Booking\UpdateBookingAction;
use App\Enums\CalendarType;
// use App\CalendarType;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Location;
use App\Models\MeetingRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingMeetingController extends Controller
{
    public function index(Request $request): View
    {
        $calendarTypes = CalendarType::cases();
        $locations = Location::with('meetingRooms')->get();
        // $users = User::whereIn('nik', ['2018113907'])->get();
        $users = User::limit('100')->get();
        $rooms = MeetingRoom::query()
            ->with('location')
            ->orderBy('location_id', 'desc')
            ->orderBy('id', 'desc')
        ->get();

        $timeRanges = $this->timeRange();

        $calendars = $request->input('calendars', []);

        $bookings = Booking::with(['user', 'meetingRoom'])
            ->where('nik', auth()->id())
            ->when(! empty($calendars), function ($query) use ($calendars) {
                $query->whereIn('calendar_type', $calendars);
            })
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'start' => $booking->start_time->toIso8601String(),
                    'end' => $booking->end_time->toIso8601String(),
                    'url' => $booking->event_url,
                    'allDay' => $booking->all_day,
                    'extendedProps' => [
                        'calendar' => $booking->calendar_type,
                        'location' => $booking->location,
                        'description' => $booking->description,
                        'guests' => [], // Add if you implement guests
                    ],
                ];
            });

        // return response()->json($bookings);
        return view('booking.meeting-room.index', compact('bookings', 'calendarTypes', 'locations', 'users'));
    }

    public function fetchData(): JsonResponse
    {
        $calendarTypes = CalendarType::cases();
        $bookings = Booking::with(['user', 'meetingRoom'])
            ->where('nik', auth()->id())
            ->when(! empty($calendarTypes), function ($query) use ($calendarTypes) {
                $query->whereIn('calendar_type', $calendarTypes);
            })
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'start' => $booking->start_time->toIso8601String(),
                    'end' => $booking->end_time->toIso8601String(),
                    'url' => $booking->event_url,
                    'allDay' => $booking->all_day,
                    'extendedProps' => [
                        'calendar' => $booking->calendar_type,
                        'location' => $booking->location,
                        'description' => $booking->description,
                        'guests' => $booking->guest_emails ?? [],
                    ],
                ];
            });

        // ✅ Return array directly, not wrapped in object
        return response()->json($bookings);
    }

    public function store(
        StoreBookingRequest $request,
        CreateBookingAction $action
    ): JsonResponse {
        try {

            // dd($request->validated());
            $booking = $action->handle($request->validated());

            return response()->json([
                'success' => true,
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(
        UpdateBookingRequest $request,
        Booking $booking,
        UpdateBookingAction $action
    ): JsonResponse {
        try {
            $booking = $action->handle($booking, $request->validated());

            return response()->json([
                'success' => true,
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking',
            ], 500);
        }
    }

    public function destroy(
        Booking $booking,
        DeleteBookingAction $action
    ): JsonResponse {
        if ($booking->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $action->handle($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete booking',
            ], 500);
        }
    }

    private function timeRange(): array
    {
        $timeRanges = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            foreach ([0, 30] as $minute) {
                $start = today()->setTime($hour, $minute);
                $timeRanges[] = $start->format('H:i');
            }
        }
        $timeRanges[] = today()->setTime(17, 0)->format('H:i');

        return $timeRanges;
    }
}
