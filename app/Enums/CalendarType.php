<?php

namespace App\Enums;

enum CalendarType: string
{
    case MEETING = 'Meeting';
    case INTERVIEW = 'Interview';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function color(): string
    {
        return match ($this) {
            self::MEETING => 'primary',
            self::INTERVIEW => 'success',
        };
    }
}
