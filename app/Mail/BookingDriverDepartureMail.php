<?php

namespace App\Mail;

use App\Models\DriverBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingDriverDepartureMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DriverBooking $booking,
        public string $recipientRole // 'booker' or 'driver'
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.driver-departure')
            ->subject('Anda sudah dalam perjalanan!');
    }
}
