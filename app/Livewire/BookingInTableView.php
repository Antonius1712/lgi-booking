<?php

namespace App\Livewire;

use App\Enums\CalendarType;
use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;

class BookingInTableView extends Component
{
    public $calendarTypes;
    public $rooms;
    public $timeRanges;
    public bool $loaded = false;
    public $description;

    #[Url(as: 'd')]
    public string $date = '';

    public bool $focusInput = false;

    public function mount()
    {
        $this->calendarTypes = CalendarType::cases();
        $this->rooms = MeetingRoom::query()
            ->with('location')
            ->orderBy('location_id', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $this->timeRanges = $this->timeRange();
        $this->loaded = false;
    }

    public function hydrate()
    {
        $this->loaded = true;
    }

    public function render()
    {
        // $test = $this->searchAbailableRoom();
        // dd($test);


        return view('livewire.booking-in-table-view', [
            'booked' => $this->searchAbailableRoom()
        ]);
    }

    private function searchAbailableRoom(): array
    {

        return Booking::query()
            ->where('booking_date', $this->date)
            ->with([
                'meetingRoom' => fn($query) => $query->select('id', 'slug'),
                'user'
            ])
            ->get()
            ->groupBy(function($booking){
                return $booking->meetingRoom->slug;
            })
            ->map(function($bookings){
                return $bookings->map(function ($booking) {
                    return [
                        Carbon::parse($booking->start_time)->format('H:i'),
                        $booking->user->Name,
                    ];
                })->values();
            })
            ->toArray();

        // return Booking::query()
        //     ->where('booking_date', $this->date)
        //     ->with([
        //         'meetingRoom' => fn($query) => $query->select('id', 'slug')
        //     ])
        //     ->get()
        //     // ->mapWithKeys(function ($booking) {
        //     //     if (!$booking->meetingRoom) {
        //     //         return [];
        //     //     }

        //     //     return [$booking->meetingRoom->slug => [$booking->start_time->format('H:i')]];
        //     // })
        //     ->groupBy(fn($booking) => $booking->meetingRoom->slug)
        //     ->map(fn($bookings) => $bookings->pluck('start_time', 'nik')->map(
        //         fn($time) =>
        //         $time->format('H:i')
        //     ))
        //     // ->map(fn ($time) => $time('H:i'))
        //     ->toArray();
    }

    public function submitBook($range): Void
    {
        // $this->validate([
        //     'description' => 'required'
        // ]);

        foreach ($range as $roomSlug => $times) {
            $room = MeetingRoom::query()->where('slug', $roomSlug)->select('id', 'location_id')->first();
            foreach ($times as $time) {
                Booking::create([
                    'meeting_room_id' => $room->id,
                    'nik' => auth()->user()->NIK,
                    'booking_date' => $this->date,
                    'time_slot' => $time,
                    'start_time' => explode(' - ', $time)[0],
                    'end_time' => explode(' - ', $time)[1],
                    'title' => '',
                    'event_url' => '',
                    'location' => $room->location_id,
                    'description' => '',
                    'guest_emails' => '',
                ]);
            }
        }

        $this->dispatch('reload-alpine');
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
