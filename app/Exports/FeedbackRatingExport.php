<?php

namespace App\Exports;

use App\Models\DriverBookingFeedback;
use App\Models\FeedbackTag;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FeedbackRatingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /** @var array<int, string> */
    protected array $tagMap;

    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
        protected ?string $driverNik = null,
    ) {
        $this->tagMap = FeedbackTag::pluck('label', 'id')->toArray();
    }

    public function collection()
    {
        $query = DriverBookingFeedback::query()
            ->with(['driverBooking:id,booking_number,driver_nik,scheduled_pickup_date,destination,purpose_of_trip',
                    'driverBooking.driver:NIK,Name',
                    'driverBooking.user:NIK,Name'])
            ->orderByDesc('created_at');

        if ($this->dateFrom || $this->dateTo) {
            $query->whereHas('driverBooking', function ($q) {
                if ($this->dateFrom) {
                    $q->where('scheduled_pickup_date', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->where('scheduled_pickup_date', '<=', $this->dateTo);
                }
            });
        }

        if ($this->driverNik) {
            $query->whereHas('driverBooking', fn ($q) =>
                $q->where('driver_nik', $this->driverNik)
            );
        }

        return $query->get()->map(fn ($f) => [
            $f->driverBooking?->booking_number,
            $f->driverBooking?->scheduled_pickup_date?->format('d/m/Y'),
            $f->driverBooking?->driver?->Name ?? $f->driverBooking?->driver_nik,
            $f->driverBooking?->user?->Name ?? $f->user_nik,
            $f->user_nik,
            $f->rating,
            implode(', ', array_map(
                fn ($id) => $this->tagMap[$id] ?? "Tag #{$id}",
                $f->feedback_tag_ids ?? []
            )),
            $f->notes,
            $f->driverBooking?->destination,
            $f->driverBooking?->purpose_of_trip,
            $f->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function headings(): array
    {
        return [
            'Booking No.',
            'Trip Date',
            'Driver Name',
            'Booker Name',
            'Booker NIK',
            'Rating (1-5)',
            'Feedback Tags',
            'Notes',
            'Destination',
            'Purpose of Trip',
            'Submitted At',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
