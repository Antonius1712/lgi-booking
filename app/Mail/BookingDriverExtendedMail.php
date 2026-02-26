<?php

namespace App\Mail;

use App\Models\DriverBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingDriverExtendedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DriverBooking $booking,
        public string $recipientRole // 'booker' or 'driver'
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.driver-extended')
            ->subject('Konfirmasi penambahan waktu perjalanan anda!');
    }
}
