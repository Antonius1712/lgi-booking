<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'driver_booking_days_ahead',
                'label' => 'Driver Booking Days Ahead',
                'value' => '14',
            ],
            [
                'key' => 'meeting_room_booking_days_ahead',
                'label' => 'Meeting Room Booking Days Ahead',
                'value' => '14',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
