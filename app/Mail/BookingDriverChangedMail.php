<?php

namespace App\Mail;

use App\Models\DriverBooking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingDriverChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DriverBooking $booking,
        public string $recipientRole, // 'booker' | 'old_driver' | 'new_driver'
        public User $oldDriver,
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.driver-changed')
            ->subject('Perubahan Driver Pemesanan Anda!');
    }
}
