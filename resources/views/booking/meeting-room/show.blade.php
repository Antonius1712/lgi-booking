@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 col-12 px-0 px-md-3">
            <h1 class="venue-name text-left venue-name-other mb-3">
                {{ $meetingRoom->name }}
            </h1>

            {{-- @livewire('booking-calendar') --}}

            <!-- Calendar Section -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Select Date</h5>
                        </div>
                        <div class="card-body">
                            <input type="date" class="form-control"
                                min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">

                            <div class="mt-3">
                                <p class="mb-2"><strong>Selected Date:</strong></p>
                                <p class="text-primary">

                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Available Time Slots</h5>
                            <div>
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bxs-circle"></i> Available
                                </span>
                                <span class="badge bg-label-danger">
                                    <i class="bx bxs-circle"></i> Booked
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($timeRanges as $slot)
                                <div class="col-md-3 col-sm-6">
                                    <label class="position-relative d-block" style="min-height: 80px; cursor: pointer;">
                                        <input type="checkbox" class="position-absolute opacity-0"
                                            style="pointer-events: none;">

                                        <div class="border rounded p-3 h-100 d-flex align-items-center justify-content-center border-dark"
                                            style="min-height: 80px; transition: all 0.2s;">

                                            <div class="text-center w-100">
                                                <div class="mb-1">
                                                    {{ $slot }}
                                                </div>
                                                <div class="mb-1">
                                                    <small class="text-muted">
                                                        60 Menit
                                                    </small>
                                                </div>
                                                <div class="fw-semibold">
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <div class="badge bg-success">
                                                        Available
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                                
                                {{-- @livewire('time-slots') --}}
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="button" wire:click="bookTimeSlot" class="btn btn-primary btn-lg">
                                    <i class="bx bx-check-circle me-2"></i>
                                    Book Selected Time Slot
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection