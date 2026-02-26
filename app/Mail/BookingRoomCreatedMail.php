<?php

namespace App\Mail;

use App\Models\MeetingRoomBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRoomCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public MeetingRoomBooking $booking
    ) {}

    public function build(): self
    {
        return $this->view('emails.booking.room-created')
            ->subject('Konfirmasi Pemesanan Ruang Meeting Anda Berhasil!');
    }
}
