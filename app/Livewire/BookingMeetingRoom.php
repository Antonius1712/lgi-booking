<?php

namespace App\Livewire;

use App\Enums\CalendarType;
use App\Models\MeetingRoom;
use Livewire\Component;

class BookingMeetingRoom extends Component
{
    public $calendarTypes = [];

    public $rooms = [];

    public $timeRanges = [];

    public $pickedTime = [];

    public function mount()
    {
        $this->calendarTypes = CalendarType::cases();
        $this->rooms = MeetingRoom::query()
            ->with('location')
            ->orderBy('location_id', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $this->timeRanges = $this->timeRange();
        // foreach ($this->rooms as $room) {
        //     $this->pickedTime[$room->slug] = [];
        // }
    }

    public function render()
    {
        return view('livewire.booking-meeting-room');
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
}
