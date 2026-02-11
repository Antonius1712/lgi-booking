<?php

namespace App\Enums;

enum DriverBookingStatusEnum: string
{
    case BOOKED = 'booked';
    case WAITING_CONFIRMATION = 'waiting_confirmation';
    case DEPARTURE = 'departure';
    case EXTENDING = 'extending';
    case RESCHEDULING = 'rescheduling';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case DRIVER_CHANGED = 'driver_changed';
    case REMINDER_SENT_1 = 'reminder_sent_1';
    case REMINDER_SENT_2 = 'reminder_sent_2';
    case REMINDER_SENT_3 = 'reminder_sent_3';
    case AUTO_CANCELLED = 'auto_cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
