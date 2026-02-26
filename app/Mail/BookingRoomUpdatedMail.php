<?php

namespace App\Mail;

use App\Models\MeetingRoomBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRoomUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public MeetingRoomBooking $booking
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.room-updated')
            ->subject('Informasi Perubahan Pemesanan Ruang Meeting Anda');
    }
}
