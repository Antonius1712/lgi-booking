<?php

namespace App\Mail;

use App\Models\DriverBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingDriverEndingSoonMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DriverBooking $booking,
        public string $recipientRole // 'booker' or 'driver'
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.driver-ending-soon')
            ->subject('Perjalanan anda akan segera berakhir!');
    }
}
