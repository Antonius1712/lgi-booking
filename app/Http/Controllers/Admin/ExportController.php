<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DriverBookingExport;
use App\Exports\FeedbackRatingExport;
use App\Exports\MeetingRoomBookingExport;
use App\Http\Controllers\Controller;
use App\Models\MeetingRoom;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function index(): View
    {
        $drivers = User::on('LgiGlobal114')
            ->whereHas('UserGroup', fn ($q) => $q->whereHas('Group', fn ($g) => $g->whereHas('App', fn ($a) => $a->where('AppCode', 'DRIVER')
            )
            )
            )
            ->orderBy('Name')
            ->get(['NIK', 'Name']);

        $rooms = MeetingRoom::with('location:id,name')->orderBy('name')->get(['id', 'name']);

        return view('admin.reports.index', compact('drivers', 'rooms'));
    }

    public function export(Request $request): BinaryFileResponse|\Illuminate\Http\Response
    {
        $request->validate([
            'report_type' => ['required', 'in:driver,meeting_room,feedback'],
            'format' => ['required', 'in:excel,pdf'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $type = $request->report_type;
        $format = $request->format;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($type === 'driver') {
            return $this->exportDriver($format, $dateFrom, $dateTo, $request->driver_nik);
        }

        if ($type === 'meeting_room') {
            return $this->exportMeetingRoom($format, $dateFrom, $dateTo, $request->room_id);
        }

        return $this->exportFeedback($format, $dateFrom, $dateTo, $request->driver_nik);
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function exportDriver(
        string $format,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $driverNik,
    ): BinaryFileResponse|\Illuminate\Http\Response {
        $export = new DriverBookingExport($dateFrom, $dateTo, $driverNik);
        $filename = 'driver-bookings-'.now()->format('Ymd');

        if ($format === 'pdf') {
            $bookings = $export->collection();

            return Pdf::loadView('admin.reports.pdf.driver', compact('bookings', 'dateFrom', 'dateTo'))
                ->setPaper('a4', 'landscape')
                ->download("{$filename}.pdf");
        }

        return Excel::download($export, "{$filename}.xlsx");
    }

    private function exportMeetingRoom(
        string $format,
        ?string $dateFrom,
        ?string $dateTo,
        ?int $roomId,
    ): BinaryFileResponse|\Illuminate\Http\Response {
        $export = new MeetingRoomBookingExport($dateFrom, $dateTo, $roomId);
        $filename = 'meeting-room-bookings-'.now()->format('Ymd');

        if ($format === 'pdf') {
            $bookings = $export->collection();

            return Pdf::loadView('admin.reports.pdf.meeting-room', compact('bookings', 'dateFrom', 'dateTo'))
                ->setPaper('a4', 'landscape')
                ->download("{$filename}.pdf");
        }

        return Excel::download($export, "{$filename}.xlsx");
    }

    private function exportFeedback(
        string $format,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $driverNik,
    ): BinaryFileResponse|\Illuminate\Http\Response {
        $export = new FeedbackRatingExport($dateFrom, $dateTo, $driverNik);
        $filename = 'feedback-ratings-'.now()->format('Ymd');

        if ($format === 'pdf') {
            $feedbacks = $export->collection();

            return Pdf::loadView('admin.reports.pdf.feedback', compact('feedbacks', 'dateFrom', 'dateTo'))
                ->setPaper('a4', 'landscape')
                ->download("{$filename}.pdf");
        }

        return Excel::download($export, "{$filename}.xlsx");
    }
}
