<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class DeleteBookingAction
{
    public function handle(Booking $booking): bool
    {
        return DB::transaction(function () use ($booking) {
            return $booking->delete();
        });
    }
}
