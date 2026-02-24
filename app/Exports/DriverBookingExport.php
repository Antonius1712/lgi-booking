<?php

namespace App\Exports;

use App\Models\DriverBooking;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverBookingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
        protected ?string $driverNik = null,
    ) {}

    public function collection()
    {
        $query = DriverBooking::query()
            ->with(['user:NIK,Name', 'driver:NIK,Name'])
            ->orderByDesc('scheduled_pickup_date');

        if ($this->dateFrom) {
            $query->where('scheduled_pickup_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('scheduled_pickup_date', '<=', $this->dateTo);
        }

        if ($this->driverNik) {
            $query->where('driver_nik', $this->driverNik);
        }

        return $query->get()->map(fn ($b) => [
            $b->booking_number,
            $b->user?->Name ?? $b->user_nik,
            $b->user_nik,
            $b->driver?->Name ?? $b->driver_nik,
            $b->driver_nik,
            $b->scheduled_pickup_date?->format('d/m/Y'),
            $b->scheduled_pickup_time?->format('H:i'),
            $b->scheduled_end_time?->format('H:i'),
            round($b->scheduled_duration / 60, 1) . ' hrs',
            $b->destination,
            $b->purpose_of_trip,
            ucfirst(str_replace('_', ' ', $b->status)),
            $b->actual_pickup_at?->format('d/m/Y H:i'),
            $b->actual_end_at?->format('d/m/Y H:i'),
            $b->cancelation_reason,
        ]);
    }

    public function headings(): array
    {
        return [
            'Booking No.',
            'Employee Name',
            'Employee NIK',
            'Driver Name',
            'Driver NIK',
            'Date',
            'Pickup Time',
            'End Time',
            'Duration',
            'Destination',
            'Purpose of Trip',
            'Status',
            'Actual Pickup',
            'Actual End',
            'Cancellation Reason',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
