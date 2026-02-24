<?php

namespace App\Exports;

use App\Models\MeetingRoomBooking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeetingRoomBookingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
        protected ?int $roomId = null,
    ) {}

    public function collection()
    {
        $query = MeetingRoomBooking::query()
            ->with(['user:NIK,Name', 'meetingRoom:id,name', 'meetingRoom.location:id,name'])
            ->orderByDesc('booking_date');

        if ($this->dateFrom) {
            $query->where('booking_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('booking_date', '<=', $this->dateTo);
        }

        if ($this->roomId) {
            $query->where('meeting_room_id', $this->roomId);
        }

        return $query->get()->map(fn ($b) => [
            $b->id,
            $b->user?->Name ?? $b->nik,
            $b->nik,
            $b->meetingRoom?->name,
            $b->meetingRoom?->location?->name,
            $b->booking_date?->format('d/m/Y'),
            $b->start_time?->format('H:i'),
            $b->end_time?->format('H:i'),
            $b->description,
            $b->usage_type,
            implode(', ', $b->guest_emails ?? []),
            ucfirst($b->status),
            $b->cancelation_reason,
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Employee NIK',
            'Room',
            'Location',
            'Date',
            'Start Time',
            'End Time',
            'Description',
            'Usage Type',
            'Guest Emails',
            'Status',
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
