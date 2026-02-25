@extends('layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#2c2c5e">Meeting Room Bookings</h4>
        <p class="text-muted mb-0" style="font-size:.85rem">
            Manage all meeting room bookings — cancel, change room, or reschedule.
        </p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-error me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── FILTER BAR ── --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.meeting-room-bookings.index') }}"
              class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       style="width:200px" placeholder="Name, description..."
                       value="{{ request('search') }}">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">Status</label>
                <select name="status" class="form-select form-select-sm" style="width:160px">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}"
                            {{ in_array($s, (array) request('status', [])) ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">Room</label>
                <select name="room_id" class="form-select form-select-sm" style="width:180px">
                    <option value="">All Rooms</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:.75rem;font-weight:600">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="icon-base bx bx-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.meeting-room-bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                Reset
            </a>
            <div class="ms-auto">
                <a href="{{ route('admin.export.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="icon-base bx bx-download me-1"></i>Export
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="background:#fafafa">
                    @foreach (['#','Employee','Room','Date & Time','Description','Status','Actions'] as $h)
                        <th style="font-size:.72rem;color:#82868b;text-transform:uppercase;
                                   letter-spacing:.5px;font-weight:700;padding:.75rem 1rem;
                                   white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    @php
                        $isTerminal = in_array($booking->status, ['completed', 'cancelled']);
                        $canCancel  = ! $isTerminal;
                    @endphp
                    <tr>
                        {{-- ID / Date --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div style="font-size:.78rem;font-weight:700;color:#7367f0;letter-spacing:.3px">
                                #{{ $booking->id }}
                            </div>
                            <div style="font-size:.7rem;color:#b9b9c3">
                                {{ $booking->created_at?->format('d M Y') }}
                            </div>
                        </td>

                        {{-- Employee --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:50%;
                                            background:rgba(115,103,240,.1);color:#7367f0;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:.7rem;font-weight:700;flex-shrink:0">
                                    {{ $booking->user?->initials() }}
                                </div>
                                <div>
                                    <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                                        {{ $booking->user?->Name }}
                                    </div>
                                    <div style="font-size:.72rem;color:#b9b9c3">
                                        {{ $booking->nik }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Room --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                                {{ $booking->meetingRoom?->name }}
                            </div>
                            <div style="font-size:.72rem;color:#b9b9c3">
                                {{ $booking->meetingRoom?->location?->name }}
                            </div>
                        </td>

                        {{-- Date & Time --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem;white-space:nowrap">
                            <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                                {{ $booking->booking_date?->format('D, d M Y') }}
                            </div>
                            <div style="font-size:.75rem;color:#82868b">
                                {{ $booking->start_time?->format('H:i') }}
                                –
                                {{ $booking->end_time?->format('H:i') }}
                            </div>
                        </td>

                        {{-- Description --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem;max-width:200px">
                            <div style="font-size:.8rem;color:#2c2c5e;white-space:nowrap;
                                        overflow:hidden;text-overflow:ellipsis"
                                 title="{{ $booking->description }}">
                                {{ $booking->description ?? '-' }}
                            </div>
                            @if ($booking->usage_type)
                                <div style="font-size:.72rem;color:#b9b9c3">
                                    {{ Str::limit($booking->usage_type, 35) }}
                                </div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            @php
                                $statusColors = [
                                    'Booked' => 'badge bg-primary',
                                    'Pending'   => 'badge bg-warning',
                                    'Confirmed' => 'badge bg-info',
                                    'Completed' => 'badge bg-success',
                                    'Cancelled' => 'badge bg-danger',
                                ];
                                $class = $statusColors[$booking->status] ?? 'badge bg-secondary';
                            @endphp
                            <span class="{{ $class }}">
                                {{ $booking->status }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td style="vertical-align:middle;padding:.75rem 1rem">
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.meeting-room-bookings.show', $booking) }}"
                                   class="adm-act-btn adm-act-purple" title="View Detail">
                                    <i class="icon-base bx bx-show"></i>
                                </a>

                                @if ($canCancel)
                                    <button type="button" class="adm-act-btn adm-act-red"
                                            title="Cancel Booking"
                                            onclick="openCancel({{ $booking->id }}, '#{{ $booking->id }}')">
                                        <i class="icon-base bx bx-x"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5" style="font-size:.82rem">
                            <i class="icon-base bx bx-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
                            No bookings found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($bookings->hasPages())
        <div class="card-footer">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

{{-- ── CANCEL MODAL ── --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Cancel Booking</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancel-form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p style="font-size:.85rem;color:#5e5873">
                        Cancel booking <strong id="cancel-booking-no"></strong>?
                        This will notify the employee via email.
                    </p>
                    <label class="form-label fw-semibold" style="font-size:.82rem">
                        Reason <span class="text-danger">*</span>
                    </label>
                    <textarea name="cancelation_reason" class="form-control" rows="3"
                              placeholder="Reason for cancellation..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="icon-base bx bx-x me-1"></i>Confirm Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.adm-act-btn {
    width: 30px; height: 30px; border-radius: 7px; font-size: .85rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; border: 1px solid; transition: all .15s;
    background: transparent;
}
.adm-act-red    { border-color: #ea5455; color: #ea5455; }
.adm-act-red:hover    { background: #ea5455; color: #fff; }
.adm-act-purple { border-color: #7367f0; color: #7367f0; }
.adm-act-purple:hover { background: #7367f0; color: #fff; }
</style>

<script>
function openCancel(id, bookingNo) {
    $('#cancel-booking-no').text(bookingNo);
    $('#cancel-form').attr('action', '/admin/meeting-room-bookings/' + id + '/cancel');
    $('#cancelModal').modal('show');
}
</script>

@endsection
