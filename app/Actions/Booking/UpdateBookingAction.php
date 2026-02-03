<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class UpdateBookingAction
{
    public function handle(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $booking->update([
                'title' => $data['title'],
                'booking_date' => $data['start_time'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'all_day' => $data['all_day'] ?? false,
                'calendar_type' => $data['calendar_type'],
                'event_url' => $data['event_url'] ?? null,
                'location' => $data['location'] ?? null,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return $booking->fresh();
        });
    }
}
