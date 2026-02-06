<?php

namespace App\Services;

use App\Models\DriverBooking;
use Carbon\Carbon;

class BookingNumberGenerator
{
    public static function generate(): string
    {
        $date = Carbon::now()->format('Ymd');

        $lastBooking = DriverBooking::whereDate('created_at', Carbon::today())
            ->orderByDesc('id')
            ->first();

        $sequence = 1;

        if ($lastBooking && preg_match('/(\d+)$/', $lastBooking->booking_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf(
            'DRV-%s-%06d',
            $date,
            $sequence
        );
    }
}
