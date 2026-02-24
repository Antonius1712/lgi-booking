@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h4 class="mb-1 fw-bold" style="color:#2c2c5e">Configuration</h4>
    <p class="text-muted mb-0" style="font-size:.85rem">
        Configure booking time range and other system settings.
    </p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-calendar-check" style="color:#7367f0;font-size:1.1rem"></i>
                    <h6 class="mb-0 fw-semibold">Booking Time Range</h6>
                </div>
                <p class="text-muted mb-0 mt-1" style="font-size:.8rem">
                    Set how many days ahead users are allowed to create bookings.
                </p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.83rem">
                            Driver Booking — Days Ahead
                        </label>
                        <div class="input-group" style="max-width:250px">
                            <input type="number" name="driver_booking_days_ahead"
                                   class="form-control @error('driver_booking_days_ahead') is-invalid @enderror"
                                   value="{{ old('driver_booking_days_ahead', $driverDays) }}"
                                   min="1" max="365" required>
                            <span class="input-group-text" style="font-size:.82rem">days</span>
                        </div>
                        @error('driver_booking_days_ahead')
                            <div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>
                        @enderror
                        <div class="text-muted mt-1" style="font-size:.75rem">
                            Current setting: users can book up to <strong>{{ $driverDays }} days</strong> from today.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.83rem">
                            Meeting Room Booking — Days Ahead
                        </label>
                        <div class="input-group" style="max-width:250px">
                            <input type="number" name="meeting_room_booking_days_ahead"
                                   class="form-control @error('meeting_room_booking_days_ahead') is-invalid @enderror"
                                   value="{{ old('meeting_room_booking_days_ahead', $meetingRoomDays) }}"
                                   min="1" max="365" required>
                            <span class="input-group-text" style="font-size:.82rem">days</span>
                        </div>
                        @error('meeting_room_booking_days_ahead')
                            <div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>
                        @enderror
                        <div class="text-muted mt-1" style="font-size:.75rem">
                            Current setting: users can book up to <strong>{{ $meetingRoomDays }} days</strong> from today.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base bx bx-save me-1"></i>Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
