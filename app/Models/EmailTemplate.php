<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'email_type',
        'subject',
        'email_body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'booking_driver' => 'Driver Booking Confirmation',
            'reminder_driver' => 'Driver Booking Reminder',
            'cancel_driver' => 'Driver Booking Cancelled',
            'departure_driver' => 'Driver Booking Departure',
            'ending_soon_driver' => 'Driver Booking Ending Soon',
            'extend_driver' => 'Driver Booking Extended',
            'complete_driver' => 'Driver Booking Completed',
            'booking_room' => 'Meeting Room Booking Confirmation',
            'cancel_room' => 'Meeting Room Booking Cancelled',
            'complete_room' => 'Meeting Room Booking Completed',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    public static function allTypes(): array
    {
        return [
            'booking_driver',
            'reminder_driver',
            'cancel_driver',
            'departure_driver',
            'ending_soon_driver',
            'extend_driver',
            'complete_driver',
            'booking_room',
            'cancel_room',
            'complete_room',
        ];
    }
}
