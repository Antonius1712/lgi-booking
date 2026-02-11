<?php

namespace App\Http\Controllers;

use App\Actions\BookingMeetingRoomStoreAction;
use App\Actions\BookingMeetingRoomUpdateAction;
use App\Enums\UsageTypeEnum;
use App\Http\Requests\BookingMeetingRoomStoreRequest;
use App\Http\Requests\BookingMeetingRoomUpdateRequest;
use App\Models\MeetingRoom;
use App\Models\MeetingRoomBooking;
use App\Services\MiniCalendars;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use phpDocumentor\Reflection\Types\Integer;

class BookingMeetingController extends Controller
{
    public $booked;

    public function __construct()
    {
        $this->booked = [
            'lantai-3-baru' => ['08:00', '08:30', '09:00'],
            'e-commerce' => ['09:00'],
        ];
    }

    public function index(Request $request, MiniCalendars $miniCalendars): View
    {
        $year = $request->year ?? now()->format('Y');
        $month = $request->month ?? now()->format('m');
        $day = $request->day ?? now()->format('d');
        $date = Carbon::parse("$year-$month-$day")->format('Y-m-d');

        $calendarTypes = UsageTypeEnum::cases();
        $timeSlots = $this->timeSlot();
        $timeRanges = $this->timeRange();

        $rooms = MeetingRoom::query()
            ->with('location')
            ->orderBy('location_id', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $booked = MeetingRoomBooking::query()
            ->where('booking_date', $date)
            ->with(['meetingRoom:id,slug', 'user:NIK,Name'])
            ->get(['id', 'nik', 'meeting_room_id', 'start_time', 'end_time', 'description']);

        return view('booking.meeting-room.index', compact('calendarTypes', 'rooms', 'timeSlots', 'timeRanges', 'booked', 'miniCalendars'));
    }

    public function store(BookingMeetingRoomStoreRequest $request, BookingMeetingRoomStoreAction $action): RedirectResponse
    {
        $action->handle($request);

        return back()->with(['success' => 'Sukses']);
    }

    public function update(Integer $id, BookingMeetingRoomUpdateRequest $request, BookingMeetingRoomUpdateAction $action): RedirectResponse
    {
        $action->handle($id, $request);

        return back()->with(['success' => 'Sukses']);
    }

    private function timeSlot()
    {
        $timeSlots = [];
        for ($hour = 8; $hour < 17; $hour++) {
            $start = today()->setTime($hour, 0);
            $end = $start->copy()->addHour();
            $timeSlots[] = $start->format('H:i').' - '.$end->format('H:i');
        }

        return $timeSlots;
    }

    private function timeRange()
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

    public function fetchData(Request $request)
    {
        $roomId = MeetingRoom::where('slug', $request->room)->value('id');
        if (! $roomId) {
            return response()->json([], 404);
        }

        $bookedTimes = MeetingRoomBooking::where('meeting_room_id', $roomId)
            ->where('booking_date', $request->date)
            ->where('status', 'confirmed')
            ->pluck('start_time')
            ->map(fn ($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        // generate slots 08:00–17:00 (30 min)
        $slots = [];
        $start = Carbon::createFromTime(8, 0);
        $end = Carbon::createFromTime(17, 0);

        while ($start < $end) {
            $time = $start->format('H:i');

            $slots[] = [
                'time' => $time,
                'label' => $time,
                'disabled' => in_array($time, $bookedTimes),
            ];

            $start->addMinutes(30);
        }

        return response()->json($slots);
    }
}
