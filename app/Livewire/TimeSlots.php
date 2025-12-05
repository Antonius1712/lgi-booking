<?php

namespace App\Livewire;

use App\Models\Booking;
use Carbon\Carbon;
use Livewire\Component;

class TimeSlots extends Component
{
    public array $timeRanges = [];

    public array $bookedSlots = [];

    public array $selectedSlot = [];

    public bool $isBooked = false;

    public $meetingRoom = null;

    public array $timeSlots = [];

    public string $date = '';

    public string $error = '';

    public function mount($meetingRoom)
    {
        $this->timeRanges = $this->timeRange();
        $this->isBooked = false;
        $this->meetingRoom = $meetingRoom;
    }

    public function selectDate($date, $meetingRoomId)
    {
        $bookings = Booking::query()
            ->select('time_slot')
            ->where('meeting_room_id', $meetingRoomId)
            ->where('booking_date', $date)
            ->pluck('time_slot')
            ->toArray();

        $slots = [];
        foreach ($bookings as $slot) {
            $start = Carbon::parse(explode(' - ', $slot)[0]);
            $end = Carbon::parse(explode(' - ', $slot)[1]);

            $current = $start->copy();
            while ($current < $end) {
                $next = $current->copy()->addHour();

                $slots[] = $current->format('H:i').' - '.$next->format('H:i');

                $current = $next;
            }
        }

        $this->bookedSlots = $slots;

        // dd($this->bookedSlots, $date);
    }

    public function bookTimeSlot($meetingRoomId)
    {
        // dd($this->timeSlots, $this->date, $meetingRoomId);
        $start = explode(' - ', $this->timeSlots[0])[0];
        $end = explode(' - ', $this->timeSlots[count($this->timeSlots) - 1])[1];

        $slot = $start.' - '.$end;
        // dd($slot);
        Booking::create([
            'meeting_room_id' => $meetingRoomId,
            'nik' => auth()->user()->NIK,
            'booking_date' => $this->date,
            'time_slot' => $slot,
            'start_time' => $start,
            'end_time' => $end,
            'notes',
        ]);

        // Refresh the booked slots for the selected date
        $this->selectDate($this->date, $meetingRoomId);

        // Clear selected time slots
        $this->timeSlots = [];

        // Optional: Show success message
        session()->flash('success', 'Time slot booked successfully!');

        $this->dispatch('show-success', ['message' => 'Time slot booked successfully!']);
    }

    public function checkValidConsecutive()
    {
        if (! $this->isConsecutive($this->timeSlots)) {
            // Find where the break happens and keep only valid consecutive slots
            $validSlots = $this->findValidConsecutiveSlots($this->timeSlots);

            // Keep only the valid consecutive slots
            $this->timeSlots = $validSlots;
            $this->error = 'Time slots must be consecutive. Slots after the gap have been removed.';
            $this->dispatch('show-error', ['message' => $this->error]);
        } else {
            $this->error = ''; // Clear error if valid
        }
    }

    public function render()
    {
        return view('livewire.time-slots');
    }

    private function timeRange()
    {
        $timeRanges = [];
        // for ($hour = 0; $hour < 24; $hour++) {
        //     $start = today()->setTime($hour, 0);
        //     $end = $start->copy()->addHour();
        //     $timeRanges[] = $start->format('H:i') . ' - ' . $end->format('H:i');
        // }

        // loop from 8 to 17 (8 AM to 5 PM)
        for ($hour = 8; $hour < 17; $hour++) {
            $start = today()->setTime($hour, 0);
            $end = $start->copy()->addHour();
            $timeRanges[] = $start->format('H:i').' - '.$end->format('H:i');
        }

        return $timeRanges;
    }

    private function isConsecutive($slots)
    {
        if (count($slots) <= 1) {
            return true; // always valid
        }

        // normalize array to ensure order
        sort($slots);

        for ($i = 0; $i < count($slots) - 1; $i++) {
            [$start1, $end1] = explode('-', $slots[$i]);
            [$start2, $end2] = explode('-', $slots[$i + 1]);

            $end1 = Carbon::parse(trim($end1));
            $start2 = Carbon::parse(trim($start2));

            // If end time of slot N is not equal to start time of slot N+1 → invalid
            if (! $end1->equalTo($start2)) {
                return false;
            }
        }

        return true;
    }

    private function findValidConsecutiveSlots($slots)
    {
        if (count($slots) <= 1) {
            return $slots;
        }

        // Sort to ensure proper order
        sort($slots);

        $validSlots = [$slots[0]]; // Start with first slot

        for ($i = 0; $i < count($slots) - 1; $i++) {
            [$start1, $end1] = explode('-', $slots[$i]);
            [$start2, $end2] = explode('-', $slots[$i + 1]);

            $end1 = Carbon::parse(trim($end1));
            $start2 = Carbon::parse(trim($start2));

            // If consecutive, add to valid slots
            if ($end1->equalTo($start2)) {
                $validSlots[] = $slots[$i + 1];
            } else {
                // Break found - return only valid slots up to this point
                break;
            }
        }

        return $validSlots;
    }
}
