<?php

namespace App\Enums;

enum RoleEnum: string
{
    case USER = 'user-lgi-booking';
    case ADMIN = 'admin-lgi-booking';
    case DRIVER = 'driver-lgi-booking';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function color(): string
    {
        return match ($this) {
            self::USER => 'success',
            self::ADMIN => 'danger',
            self::DRIVER => 'primary',
        };
    }
}
