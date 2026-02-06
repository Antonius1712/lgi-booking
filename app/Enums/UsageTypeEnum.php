<?php

namespace App\Enums;

enum UsageTypeEnum: string
{
    case MEETING = 'Meeting';
    case INTERVIEW = 'Interview';
    case OTHER = 'Other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function color(): string
    {
        return match ($this) {
            self::MEETING => 'primary',
            self::INTERVIEW => 'success',
            self::OTHER => 'danger',
        };
    }
}
