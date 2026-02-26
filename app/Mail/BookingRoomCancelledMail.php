<?php

namespace App\Mail;

use App\Models\MeetingRoomBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRoomCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public MeetingRoomBooking $booking
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.room-cancelled')
            ->subject('Konfirmasi Pembatalan Ruang Meeting Anda!');
    }
}
