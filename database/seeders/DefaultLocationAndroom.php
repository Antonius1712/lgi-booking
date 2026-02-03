<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\MeetingRoom;
use Exception;
use Illuminate\Database\Seeder;

class DefaultLocationAndroom extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Location::create([
                'slug' => 'ho',
                'name' => 'HO',
            ]);

            Location::create([
                'slug' => 'blok-m',
                'name' => 'BLOK M',
            ]);

            MeetingRoom::create([
                'location_id' => 1,
                'slug' => 'mediplus',
                'name' => 'Mediplus',
            ]);

            MeetingRoom::create([
                'location_id' => 2,
                'slug' => 'lantai-2',
                'name' => 'Lantai 2',
            ]);

            MeetingRoom::create([
                'location_id' => 2,
                'slug' => 'lantai-2-ujung',
                'name' => 'Lantai 2 Ujung',
            ]);

            MeetingRoom::create([
                'location_id' => 2,
                'slug' => 'lantai-3',
                'name' => 'Lantai 3',
            ]);

            MeetingRoom::create([
                'location_id' => 1,
                'slug' => 'e-commerce',
                'name' => 'E-Commerce',
            ]);

            MeetingRoom::create([
                'location_id' => 1,
                'slug' => 'lounge',
                'name' => 'Lounge',
            ]);

        } catch (Exception $e) {

        }
    }
}
