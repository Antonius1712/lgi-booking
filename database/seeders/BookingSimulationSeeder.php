<?php

namespace Database\Seeders;

use App\Enums\DriverBookingStatusEnum;
use App\Models\DriverBooking;
use App\Models\MeetingRoom;
use App\Models\MeetingRoomBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSimulationSeeder extends Seeder
{
    // ─────────────────────────────────────────────────────────────────
    // CONFIGURE THESE with real NIKs from your DB
    // Run: php artisan tinker --execute="App\Models\User::select('NIK','Name')->get()->each(fn(\$u) => print \$u->NIK.' - '.\$u->Name.PHP_EOL);"
    // ─────────────────────────────────────────────────────────────────
    private array $bookerNiks = [
        '2018113915', // Antonius Christian — replace with your real booker NIKs
        '2018073867',
        '2018033810',
        '2019013939',
    ];

    private array $driverNiks = [
        '2013029906', // replace with your real driver NIKs
        '2012039921',
        '2024059780',
    ];

    private array $destinations = [
        'Gedung Sudirman Tower Lt. 12, Jl. Jend. Sudirman Kav. 52-53, Jakarta Pusat',
        'Bandara Soekarno-Hatta Terminal 3, Tangerang, Banten',
        'Kantor Pusat BCA, Jl. M.H. Thamrin No.1, Jakarta Pusat',
        'Wisma 46, Jl. Jend. Sudirman Kav. 1, Jakarta Pusat',
        'Plaza Indonesia, Jl. M.H. Thamrin Kav. 28-30, Jakarta Pusat',
        'Gedung Summarecon, Jl. Boulevard Ahmad Yani, Bekasi',
        'RS Pondok Indah, Jl. Metro Duta Kav. UE, Jakarta Selatan',
        'Mal Kelapa Gading, Jl. Bulevar Kelapa Gading, Jakarta Utara',
        'Kantor Kementerian Keuangan, Jl. Dr. Wahidin No. 1, Jakarta Pusat',
        'Pacific Place Mall, Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan',
        'Hotel Mulia Senayan, Jl. Asia Afrika, Jakarta Pusat',
        'Graha CIMB Niaga, Jl. Jend. Sudirman Kav. 58, Jakarta Selatan',
    ];

    private array $purposes = [
        'Client Meeting – Q3 Review',
        'Airport Drop-off',
        'Vendor Visit',
        'Bank Coordination Meeting',
        'Internal Audit Support',
        'Client Demo Presentation',
        'Document Submission to BPKP',
        'Procurement Site Visit',
        'HR Interview Session',
        'Board of Directors Meeting',
        'Government Liaison Visit',
        'Annual Report Presentation',
    ];

    private array $roomDescriptions = [
        'Q3 Budget Review Meeting',
        'HR Interview Session',
        'Weekly Team Sync',
        'Board of Directors Briefing',
        'Vendor Evaluation Meeting',
        'Project Kickoff',
        'Annual Planning Session',
        'Training Workshop',
        'Client Presentation',
        'Performance Review',
    ];

    // ─────────────────────────────────────────────────────────────────

    public function run(): void
    {
        $this->command->info('🚗 Seeding Driver Bookings...');
        $this->seedDriverBookings();

        $this->command->info('🏢 Seeding Meeting Room Bookings...');
        $this->seedMeetingRoomBookings();

        $this->command->info('✅ Done! Booking simulation data seeded.');
    }

    // ─────────────────────────────────────────────────────────────────
    // DRIVER BOOKINGS
    // ─────────────────────────────────────────────────────────────────
    private function seedDriverBookings(): void
    {
        // Validate NIKs exist
        $bookers = User::whereIn('NIK', $this->bookerNiks)->pluck('NIK')->toArray();
        $drivers = User::whereIn('NIK', $this->driverNiks)->pluck('NIK')->toArray();

        if (empty($bookers)) {
            $this->command->warn('⚠️  No booker NIKs found. Update $bookerNiks in the seeder.');

            return;
        }
        if (empty($drivers)) {
            $this->command->warn('⚠️  No driver NIKs found. Update $driverNiks in the seeder.');

            return;
        }

        $scenarios = [

            // ── TODAY: Active scenarios (visible on homepage) ────────

            // 1. Currently on a trip right now
            [
                'status' => DriverBookingStatusEnum::DEPARTURE,
                'date' => today(),
                'pickup' => now()->subHour()->format('H:i'),
                'end' => now()->addHour()->format('H:i'),
                'booker' => $bookers[0],
                'driver' => $drivers[0],
                'dest_idx' => 0,
                'purp_idx' => 0,
            ],

            // 2. Extending an active trip
            [
                'status' => DriverBookingStatusEnum::EXTENDING,
                'date' => today(),
                'pickup' => now()->subHours(2)->format('H:i'),
                'end' => now()->addMinutes(30)->format('H:i'),
                'booker' => $bookers[1] ?? $bookers[0],
                'driver' => $drivers[1] ?? $drivers[0],
                'dest_idx' => 1,
                'purp_idx' => 1,
            ],

            // 3. Upcoming today — booked, pickup in 1.5 hours
            [
                'status' => DriverBookingStatusEnum::BOOKED,
                'date' => today(),
                'pickup' => now()->addHours(2)->startOfHour()->format('H:i'),
                'end' => now()->addHours(4)->startOfHour()->format('H:i'),
                'booker' => $bookers[2] ?? $bookers[0],
                'driver' => $drivers[2] ?? $drivers[0],
                'dest_idx' => 2,
                'purp_idx' => 2,
            ],

            // 4. Upcoming today — waiting confirmation
            [
                'status' => DriverBookingStatusEnum::WAITING_CONFIRMATION,
                'date' => today(),
                'pickup' => now()->addHours(3)->startOfHour()->format('H:i'),
                'end' => now()->addHours(5)->startOfHour()->format('H:i'),
                'booker' => $bookers[3] ?? $bookers[0],
                'driver' => $drivers[0],
                'dest_idx' => 3,
                'purp_idx' => 3,
            ],

            // 5. Reminder sent — 15 min before pickup
            [
                'status' => DriverBookingStatusEnum::REMINDER_SENT_1,
                'date' => today(),
                'pickup' => now()->addMinutes(15)->format('H:i'),
                'end' => now()->addHours(2)->addMinutes(15)->format('H:i'),
                'booker' => $bookers[0],
                'driver' => $drivers[1] ?? $drivers[0],
                'dest_idx' => 4,
                'purp_idx' => 4,
                'reminder_count' => 1,
                'last_reminder' => now()->subMinutes(2),
            ],

            // ── TODAY: Terminal scenarios ────────────────────────────

            // 6. Completed earlier today
            [
                'status' => DriverBookingStatusEnum::COMPLETED,
                'date' => today(),
                'pickup' => '08:00',
                'end' => '10:00',
                'actual_pickup' => today()->setTime(8, 3),
                'actual_end' => today()->setTime(10, 12),
                'booker' => $bookers[1] ?? $bookers[0],
                'driver' => $drivers[0],
                'dest_idx' => 5,
                'purp_idx' => 5,
            ],

            // 7. Cancelled by user today
            [
                'status' => DriverBookingStatusEnum::CANCELLED,
                'date' => today(),
                'pickup' => '09:00',
                'end' => '11:00',
                'booker' => $bookers[2] ?? $bookers[0],
                'driver' => $drivers[1] ?? $drivers[0],
                'dest_idx' => 6,
                'purp_idx' => 6,
                'cancelled_by' => $bookers[2] ?? $bookers[0],
                'cancelled_at' => today()->setTime(8, 30),
                'cancel_reason' => 'Meeting rescheduled to next week',
            ],

            // 8. Auto-cancelled — no confirmation after 3 reminders
            [
                'status' => DriverBookingStatusEnum::AUTO_CANCELLED,
                'date' => today(),
                'pickup' => '10:30',
                'end' => '12:00',
                'booker' => $bookers[3] ?? $bookers[0],
                'driver' => $drivers[2] ?? $drivers[0],
                'dest_idx' => 7,
                'purp_idx' => 7,
                'reminder_count' => 3,
                'last_reminder' => today()->setTime(10, 0),
            ],

            // 9. Driver changed by admin
            [
                'status' => DriverBookingStatusEnum::DRIVER_CHANGED,
                'date' => today()->addDay(),
                'pickup' => '13:00',
                'end' => '15:00',
                'booker' => $bookers[0],
                'driver' => $drivers[2] ?? $drivers[0],
                'dest_idx' => 8,
                'purp_idx' => 8,
            ],

            // ── TOMORROW: Upcoming ───────────────────────────────────

            // 10. Booked for tomorrow morning
            [
                'status' => DriverBookingStatusEnum::BOOKED,
                'date' => today()->addDay(),
                'pickup' => '08:00',
                'end' => '10:30',
                'booker' => $bookers[0],
                'driver' => $drivers[0],
                'dest_idx' => 9,
                'purp_idx' => 9,
            ],

            // 11. Booked for tomorrow afternoon
            [
                'status' => DriverBookingStatusEnum::BOOKED,
                'date' => today()->addDay(),
                'pickup' => '13:00',
                'end' => '16:00',
                'booker' => $bookers[1] ?? $bookers[0],
                'driver' => $drivers[1] ?? $drivers[0],
                'dest_idx' => 10,
                'purp_idx' => 10,
            ],

            // 12. Booked for day after tomorrow
            [
                'status' => DriverBookingStatusEnum::BOOKED,
                'date' => today()->addDays(2),
                'pickup' => '09:00',
                'end' => '11:00',
                'booker' => $bookers[2] ?? $bookers[0],
                'driver' => $drivers[2] ?? $drivers[0],
                'dest_idx' => 11,
                'purp_idx' => 11,
            ],

            // ── PAST: History ────────────────────────────────────────

            // 13–20: Past completed bookings spread over last 2 weeks
            ...collect(range(1, 8))->map(fn ($i) => [
                'status' => DriverBookingStatusEnum::COMPLETED,
                'date' => today()->subDays($i + 1),
                'pickup' => collect(['08:00', '09:00', '10:00', '13:00', '14:00'])->random(),
                'end' => collect(['10:00', '11:00', '12:00', '15:00', '16:00'])->random(),
                'actual_pickup' => today()->subDays($i + 1)->setTimeFromTimeString('08:05'),
                'actual_end' => today()->subDays($i + 1)->setTimeFromTimeString('10:15'),
                'booker' => $bookers[$i % count($bookers)],
                'driver' => $drivers[$i % count($drivers)],
                'dest_idx' => $i % count($this->destinations),
                'purp_idx' => $i % count($this->purposes),
            ])->toArray(),

            // 21–23: Past cancelled bookings
            ...collect(range(1, 3))->map(fn ($i) => [
                'status' => DriverBookingStatusEnum::CANCELLED,
                'date' => today()->subDays($i + 10),
                'pickup' => '10:00',
                'end' => '12:00',
                'booker' => $bookers[$i % count($bookers)],
                'driver' => $drivers[$i % count($drivers)],
                'dest_idx' => ($i + 2) % count($this->destinations),
                'purp_idx' => ($i + 2) % count($this->purposes),
                'cancelled_by' => $bookers[$i % count($bookers)],
                'cancelled_at' => today()->subDays($i + 10)->setTime(9, 0),
                'cancel_reason' => collect([
                    'Meeting rescheduled',
                    'Client cancelled visit',
                    'Internal event postponed',
                ])->get($i - 1),
            ])->toArray(),
        ];

        $seq = 1;
        foreach ($scenarios as $s) {
            $date = Carbon::parse($s['date']);
            $pickup = Carbon::parse("{$date->format('Y-m-d')} {$s['pickup']}");
            $end = Carbon::parse("{$date->format('Y-m-d')} {$s['end']}");

            // Clamp times to 08:00–17:00
            $pickup = $pickup->hour < 8 ? $pickup->setTime(8, 0) : $pickup;
            $pickup = $pickup->hour >= 17 ? $pickup->setTime(16, 0) : $pickup;
            $end = $end->hour > 17 ? $end->setTime(17, 0) : $end;

            $duration = $pickup->diffInMinutes($end);

            $data = [
                'booking_number' => sprintf('DRV-%s-%06d', $date->format('Ymd'), $seq++),
                'user_nik' => $s['booker'],
                'driver_nik' => $s['driver'],
                'status' => $s['status']->value,
                'destination' => $this->destinations[$s['dest_idx']],
                'purpose_of_trip' => $this->purposes[$s['purp_idx']],

                'scheduled_pickup_at' => $pickup,
                'scheduled_pickup_date' => $pickup->toDateString(),
                'scheduled_pickup_time' => $pickup->toTimeString(),

                'scheduled_end_at' => $end,
                'scheduled_end_date' => $end->toDateString(),
                'scheduled_end_time' => $end->toTimeString(),

                'scheduled_time_slot' => $pickup->format('H:i').'-'.$end->format('H:i'),
                'scheduled_duration' => $duration,

                // Actual times for completed/departure
                'actual_pickup_at' => $s['actual_pickup'] ?? null,
                'actual_end_at' => $s['actual_end'] ?? null,

                // Reminders
                'reminder_count' => $s['reminder_count'] ?? 0,
                'last_reminder_sent_at' => $s['last_reminder'] ?? null,

                // Cancellation
                'cancelled_by' => $s['cancelled_by'] ?? null,
                'cancelled_at' => $s['cancelled_at'] ?? null,
                'cancelation_reason' => $s['cancel_reason'] ?? null,
            ];

            DriverBooking::create($data);
        }

        $this->command->line('   Created '.count($scenarios).' driver bookings.');
    }

    // ─────────────────────────────────────────────────────────────────
    // MEETING ROOM BOOKINGS
    // ─────────────────────────────────────────────────────────────────
    private function seedMeetingRoomBookings(): void
    {
        $bookers = User::whereIn('NIK', $this->bookerNiks)->pluck('NIK')->toArray();

        if (empty($bookers)) {
            $this->command->warn('⚠️  No booker NIKs found. Update $bookerNiks in the seeder.');

            return;
        }

        $rooms = MeetingRoom::all();

        if ($rooms->isEmpty()) {
            $this->command->warn('⚠️  No meeting rooms found. Please seed MeetingRooms first, or add them via Admin > Setting Meeting Room.');

            return;
        }

        $usageTypes = ['Meeting', 'Interview', 'Other'];

        $scenarios = [

            // ── TODAY ─────────────────────────────────────────────────

            // Currently in use (departure)
            [
                'status' => 'departure',
                'date' => today(),
                'start' => now()->subHour()->startOfHour()->format('H:i'),
                'end' => now()->addHour()->startOfHour()->format('H:i'),
                'nik' => $bookers[0],
                'room' => $rooms->get(0),
                'usage' => 'Meeting',
                'desc_idx' => 0,
            ],

            // Upcoming today
            [
                'status' => 'booked',
                'date' => today(),
                'start' => now()->addHours(2)->startOfHour()->format('H:i'),
                'end' => now()->addHours(3)->startOfHour()->format('H:i'),
                'nik' => $bookers[1] ?? $bookers[0],
                'room' => $rooms->get(1) ?? $rooms->get(0),
                'usage' => 'Interview',
                'desc_idx' => 1,
            ],

            // Another upcoming today
            [
                'status' => 'booked',
                'date' => today(),
                'start' => now()->addHours(3)->startOfHour()->format('H:i'),
                'end' => now()->addHours(5)->startOfHour()->format('H:i'),
                'nik' => $bookers[2] ?? $bookers[0],
                'room' => $rooms->get(2) ?? $rooms->get(0),
                'usage' => 'Meeting',
                'desc_idx' => 2,
            ],

            // Completed earlier today
            [
                'status' => 'completed',
                'date' => today(),
                'start' => '08:00',
                'end' => '09:30',
                'nik' => $bookers[0],
                'room' => $rooms->get(0),
                'usage' => 'Meeting',
                'desc_idx' => 3,
            ],

            // Cancelled today
            [
                'status' => 'cancelled',
                'date' => today(),
                'start' => '10:00',
                'end' => '11:00',
                'nik' => $bookers[3] ?? $bookers[0],
                'room' => $rooms->get(1) ?? $rooms->get(0),
                'usage' => 'Other',
                'desc_idx' => 4,
            ],

            // ── TOMORROW ─────────────────────────────────────────────

            [
                'status' => 'booked',
                'date' => today()->addDay(),
                'start' => '09:00',
                'end' => '11:00',
                'nik' => $bookers[0],
                'room' => $rooms->get(0),
                'usage' => 'Meeting',
                'desc_idx' => 5,
            ],
            [
                'status' => 'booked',
                'date' => today()->addDay(),
                'start' => '13:00',
                'end' => '14:30',
                'nik' => $bookers[1] ?? $bookers[0],
                'room' => $rooms->get(1) ?? $rooms->get(0),
                'usage' => 'Interview',
                'desc_idx' => 6,
            ],

            // ── NEXT WEEK ────────────────────────────────────────────

            [
                'status' => 'booked',
                'date' => today()->addDays(3),
                'start' => '10:00',
                'end' => '12:00',
                'nik' => $bookers[2] ?? $bookers[0],
                'room' => $rooms->get(0),
                'usage' => 'Meeting',
                'desc_idx' => 7,
            ],
            [
                'status' => 'booked',
                'date' => today()->addDays(5),
                'start' => '14:00',
                'end' => '16:00',
                'nik' => $bookers[3] ?? $bookers[0],
                'room' => $rooms->get(2) ?? $rooms->get(0),
                'usage' => 'Other',
                'desc_idx' => 8,
            ],
        ];

        // Past bookings — 12 completed spread over last 2 weeks
        $pastBookings = collect(range(1, 12))->map(fn ($i) => [
            'status' => $i % 5 === 0 ? 'cancelled' : 'completed',
            'date' => today()->subDays($i),
            'start' => collect(['08:00', '09:00', '10:00', '13:00', '14:00', '15:00'])->get($i % 6),
            'end' => collect(['09:30', '10:30', '11:30', '14:30', '15:30', '16:30'])->get($i % 6),
            'nik' => $bookers[$i % count($bookers)],
            'room' => $rooms->get($i % $rooms->count()),
            'usage' => $usageTypes[$i % 3],
            'desc_idx' => $i % count($this->roomDescriptions),
        ])->toArray();

        $allScenarios = array_merge($scenarios, $pastBookings);

        foreach ($allScenarios as $s) {
            if (! $s['room']) {
                continue;
            }

            $date = Carbon::parse($s['date']);
            $start = Carbon::parse("{$date->format('Y-m-d')} {$s['start']}");
            $end = Carbon::parse("{$date->format('Y-m-d')} {$s['end']}");

            // Clamp to 08:00-17:00
            $start = $start->hour < 8 ? $start->setTime(8, 0) : $start;
            $end = $end->hour > 17 ? $end->setTime(17, 0) : $end;

            MeetingRoomBooking::create([
                'meeting_room_id' => $s['room']->id,
                'nik' => $s['nik'],
                'booking_date' => $date->toDateString(),
                'start_time' => $start->toTimeString(),
                'end_time' => $end->toTimeString(),
                'time_slot' => $start->format('H:i').' - '.$end->format('H:i'),
                'status' => $s['status'],
                'usage_type' => $s['usage'],
                'description' => $this->roomDescriptions[$s['desc_idx']],
                'guest_emails' => [],
            ]);
        }

        $this->command->line('   Created '.count($allScenarios).' meeting room bookings.');
    }
}
