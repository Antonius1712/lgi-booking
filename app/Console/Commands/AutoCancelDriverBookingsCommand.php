<?php

namespace App\Console\Commands;

use App\Actions\DriverAutoCancelAction;
use App\Enums\DriverBookingStatusEnum;
use App\Models\DriverBooking;
use Illuminate\Console\Command;

class AutoCancelDriverBookingsCommand extends Command
{
    protected $signature = 'driver:auto-cancel';

    protected $description = 'Auto-cancel driver bookings with no departure confirmation 15 minutes past pickup time';

    public function handle(DriverAutoCancelAction $action): void
    {
        $cutoff = now()->subMinutes(15);

        $bookings = DriverBooking::query()
            ->whereIn('status', [
                DriverBookingStatusEnum::BOOKED->value,
                DriverBookingStatusEnum::WAITING_CONFIRMATION->value,
                DriverBookingStatusEnum::REMINDER_SENT_1->value,
                DriverBookingStatusEnum::REMINDER_SENT_2->value,
                DriverBookingStatusEnum::REMINDER_SENT_3->value,
            ])
            ->where(function ($q) use ($cutoff) {
                $q->where('scheduled_pickup_date', '<', $cutoff->toDateString())
                    ->orWhere(function ($q) use ($cutoff) {
                        $q->where('scheduled_pickup_date', $cutoff->toDateString())
                            ->where('scheduled_pickup_time', '<=', $cutoff->format('H:i:s'));
                    });
            })
            ->get();

        $this->info("Found {$bookings->count()} booking(s) to auto-cancel.");

        foreach ($bookings as $booking) {
            $action->handle($booking);
            $this->line("  Auto-cancelled: {$booking->booking_number}");
        }

        $this->info('Done.');
    }
}
