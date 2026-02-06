@extends('layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Driver Bookings</h4>
            <p class="text-muted mb-0">Manage your scheduled trips</p>
        </div>
        @if( $bookings )
        <a href="{{ route('booking.driver.index') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> New Booking
        </a>
        @endif
    </div>

    {{-- Filter Tabs --}}
    <ul class="nav nav-pills mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="?filter=upcoming" class="nav-link {{ request('filter') === 'upcoming' ? 'active' : '' }}"
                role="tab">
                Upcoming
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="?filter=active" class="nav-link {{ request('filter') === 'active' ? 'active' : '' }}" role="tab">
                Active
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="?filter=completed" class="nav-link {{ request('filter') === 'completed' ? 'active' : '' }}"
                role="tab">
                History
            </a>
        </li>
    </ul>

    {{-- Bookings List --}}
    <div class="row g-4">
    @forelse ($bookings as $booking)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- Status Badge & Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="card-title mb-0">isinya nanti Booking Number</h5>
                                <span class="badge bg-label-secondary">
                                    {{-- {{ ucfirst(str_replace('_', ' ', $booking->status)) }} --}}
                                    isinya status
                                </span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bx bx-calendar me-1"></i>
                                {{-- {{ $booking->scheduled_pickup_at->format('D, M j, Y') }} at {{
                                $booking->scheduled_pickup_at->format('g:i A') }} --}}
                                Date + time Scheduled Pickup
                            </p>
                        </div>

                        <div class="text-end">
                            <p class="fw-medium mb-0">
                                {{-- {{ $booking->driver->name }} --}}
                                Driver Name
                            </p>
                            <small class="text-muted">Driver</small>
                        </div>
                    </div>

                    {{-- Trip Details --}}
                    <div class="row g-3 p-3 mb-3"
                        style="background-color: rgba(var(--bs-body-color-rgb), 0.03); border-radius: 0.375rem;">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block mb-1">Pickup</small>
                            <p class="mb-0 fw-medium">
                                {{-- {{ $booking->pickup_location }} --}}
                                Lokasi
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block mb-1">Destination</small>
                            <p class="mb-0 fw-medium">
                                {{-- {{ $booking->destination ?? 'Not specified' }} --}}
                                Destinasi
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    {{-- @if($booking->status === 'waiting_booker') --}}
                    <div class="d-flex gap-2">
                        <form action="" method="POST"
                            class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bx bx-check me-1"></i> I'm Here
                            </button>
                        </form>
                        <button onclick="toggleDetails('booking_id_nya_disini_nanti')" class="btn btn-label-secondary">
                            <i class="bx bx-info-circle"></i>
                        </button>
                    </div>
                    {{-- @elseif($booking->status === 'in_progress') --}}
                    <div class="d-flex gap-2">
                        <button onclick="openExtensionModal('booking_id_nya_disini_nanti')" class="btn btn-warning flex-fill">
                            <i class="bx bx-time me-1"></i> Request Extension
                        </button>
                        <button onclick="toggleDetails('booking_id_nya_disini_nanti')" class="btn btn-label-secondary">
                            <i class="bx bx-info-circle"></i>
                        </button>
                    </div>
                    {{-- @elseif($booking->status === 'extension_pending') --}}
                    <div class="alert alert-warning mb-0" role="alert">
                        <i class="bx bx-time-five me-1"></i>
                        {{-- Extension request pending admin approval (+{{ $booking->extension_duration }} minutes) --}}
                        Extension request pending admin approval (+30 minutes)
                    </div>
                    {{-- @else --}}
                    <button onclick="toggleDetails('booking_id_nya_disini_nanti')" class="btn btn-label-secondary w-100">
                        <i class="bx bx-info-circle me-1"></i> View Details
                    </button>
                    {{-- @endif --}}

                    {{-- Collapsible Timeline --}}
                    <div id="details-booking_id_nya_disini_nanti" class="collapse">
                        <hr class="my-4">
                        <h6 class="mb-3">Timeline</h6>
                        <ul class="timeline mb-0">
                            {{-- @foreach($booking->activities as $activity)
                            <li class="timeline-item pb-4 {{ $loop->last ? 'timeline-end' : '' }}">
                                <span class="timeline-indicator timeline-indicator-{{ $activity->color }}">
                                    <i class="bx {{ $activity->icon }} bx-xs"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">{{ $activity->description }}</h6>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </li>
                            @endforeach --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- ?Untuk Empty Book --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-car bx-lg text-muted mb-3"></i>
                    <p class="text-muted mb-3">No bookings found</p>
                    <a href="{{ route('booking.driver.index')}} "class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Create your booking
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Extension Modal --}}
<div class="modal fade" id="extensionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Extension</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="extensionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Extension Duration</label>
                        <select class="form-select" name="duration" required>
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="120">2 hours</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea class="form-control" name="reason" rows="3"
                            placeholder="Why do you need more time?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    function toggleDetails(bookingId) {
        const element = document.getElementById('details-' + bookingId);
        const bsCollapse = new bootstrap.Collapse(element, {
            toggle: true
        });
    }

    function openExtensionModal(bookingId) {
        const modal = new bootstrap.Modal(document.getElementById('extensionModal'));
        const form = document.getElementById('extensionForm');
        form.action = `/driver-bookings/${bookingId}/request-extension`;
        modal.show();
    }
</script>
@endsection