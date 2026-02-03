<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function handle(array $data): Booking
    {
        try {
            return DB::transaction(function () use ($data) {
                $timeSlot = Carbon::createFromFormat('Y-m-d H:i', $data['start_time'])->format('H:i').' - '.Carbon::createFromFormat('Y-m-d H:i', $data['end_time'])->format('H:i');

                return Booking::create([
                    'meeting_room_id' => $data['meeting_room_id'],
                    'nik' => auth()->id(),
                    'title' => $data['title'],
                    'booking_date' => $data['start_time'],
                    'time_slot' => $timeSlot,
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'all_day' => $data['all_day'] ?? false,
                    'calendar_type' => $data['calendar_type'],
                    'event_url' => $data['event_url'] ?? null,
                    'location' => $data['location'] ?? null,
                    'description' => $data['description'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'status' => 'confirmed',
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
