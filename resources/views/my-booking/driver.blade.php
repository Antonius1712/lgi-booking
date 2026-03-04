@extends('layouts.app')

@section('content')

@php
$pillMap = [
    'booked'               => ['color' => '#7367f0', 'bg' => 'rgba(115,103,240,.12)', 'label' => 'Booked'],
    'waiting_confirmation' => ['color' => '#7367f0', 'bg' => 'rgba(115,103,240,.12)', 'label' => 'Waiting'],
    'reminder_sent_1'      => ['color' => '#ff9f43', 'bg' => 'rgba(255,159,67,.12)',  'label' => 'Reminder 1'],
    'reminder_sent_2'      => ['color' => '#ff9f43', 'bg' => 'rgba(255,159,67,.12)',  'label' => 'Reminder 2'],
    'reminder_sent_3'      => ['color' => '#ff9f43', 'bg' => 'rgba(255,159,67,.12)',  'label' => 'Reminder 3'],
    'departure'            => ['color' => '#28c76f', 'bg' => 'rgba(40,199,111,.12)',  'label' => 'On Trip',    'live' => true],
    'extending'            => ['color' => '#28c76f', 'bg' => 'rgba(40,199,111,.12)',  'label' => 'Extending',  'live' => true],
    'completed'            => ['color' => '#00cfe8', 'bg' => 'rgba(0,207,232,.12)',   'label' => 'Completed'],
    'cancelled'            => ['color' => '#ea5455', 'bg' => 'rgba(234,84,85,.12)',   'label' => 'Cancelled'],
    'auto_cancelled'       => ['color' => '#ea5455', 'bg' => 'rgba(234,84,85,.12)',   'label' => 'Auto Cancelled'],
];
@endphp

{{-- ── HEADER ── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:#2c2c5e">My Driver Bookings</h4>
        <p class="text-muted mb-0" style="font-size:.85rem">Manage your scheduled trips</p>
    </div>
    <a href="{{ route('booking.driver.index') }}" class="btn btn-primary">
        <i class="icon-base bx bx-plus me-1"></i> New Booking
    </a>
</div>

{{-- ── FLASH MESSAGES ── --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="icon-base bx bx-error me-1"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── FILTER TABS ── --}}
<ul class="nav nav-pills mb-4" role="tablist">
    @foreach (['upcoming' => 'Upcoming', 'active' => 'Active', 'completed' => 'History'] as $key => $label)
        <li class="nav-item" role="presentation">
            <a href="?filter={{ $key }}"
               class="nav-link {{ $filter === $key ? 'active' : '' }}"
               role="tab">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

{{-- ── BOOKINGS LIST ── --}}
<div class="row g-4">
@forelse ($bookings as $booking)
    @php
        $pill = $pillMap[$booking->status] ?? ['color' => '#82868b', 'bg' => 'rgba(130,134,139,.12)', 'label' => ucfirst($booking->status)];
        $isActive    = in_array($booking->status, ['departure', 'extending']);
        $canRequest  = $booking->status === 'departure' && $booking->extention_requested_at === null;
        $isPending   = $booking->status === 'departure' && $booking->extention_requested_at !== null;
        $isExtending = $booking->status === 'extending';
    @endphp
    <div class="col-12">
        <div class="card" style="{{ $isActive ? 'border-left: 3px solid #28c76f;' : '' }}">
            <div class="card-body">

                {{-- ── STATUS & HEADER ── --}}
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span style="font-size:.72rem;font-weight:700;color:#b9b9c3;letter-spacing:.5px">
                                {{ $booking->booking_number }}
                            </span>
                            <span style="display:inline-flex;align-items:center;gap:4px;
                                         font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                         text-transform:uppercase;letter-spacing:.4px;
                                         background:{{ $pill['bg'] }};color:{{ $pill['color'] }}">
                                @if (!empty($pill['live']))
                                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $pill['color'] }};
                                                 animation:home-pulse 1.5s infinite;display:inline-block"></span>
                                @endif
                                {{ $pill['label'] }}
                            </span>
                        </div>
                        <p class="mb-0" style="font-size:.82rem;color:#82868b">
                            <i class="icon-base bx bx-calendar me-1"></i>
                            {{ $booking->scheduled_pickup_date?->format('D, d M Y') }}
                            &nbsp;·&nbsp;
                            {{ $booking->scheduled_pickup_time?->format('H:i') }}–{{ $booking->scheduled_end_time?->format('H:i') }} WIB
                        </p>
                    </div>
                    <div class="text-end">
                        <div style="font-size:.88rem;font-weight:600;color:#2c2c5e">
                            {{ $booking->driver?->Name ?? '-' }}
                        </div>
                        <div style="font-size:.73rem;color:#82868b">Driver</div>
                        @if ($booking->driver?->NoTelp)
                            <a href="tel:{{ $booking->driver->NoTelp }}"
                               style="font-size:.75rem;color:#7367f0;text-decoration:none">
                                {{ $booking->driver->NoTelp }}
                            </a>
                        @endif
                    </div>
                </div>

                {{-- ── TRIP DETAILS ── --}}
                <div class="p-3 mb-3 rounded-2" style="background:rgba(var(--bs-body-color-rgb),.03)">
                    <div class="row g-2">
                        <div class="col-6">
                            <div style="font-size:.7rem;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px">Destination</div>
                            <div style="font-size:.82rem;font-weight:500;color:#2c2c5e">{{ $booking->destination }}</div>
                        </div>
                        <div class="col-6">
                            <div style="font-size:.7rem;color:#b9b9c3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px">Purpose</div>
                            <div style="font-size:.82rem;font-weight:500;color:#2c2c5e">{{ $booking->purpose_of_trip }}</div>
                        </div>
                    </div>
                </div>

                {{-- ── ACTION AREA ── --}}
                @if ($canRequest)
                    <button type="button"
                            class="btn btn-warning w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#extensionModal"
                            data-booking-id="{{ $booking->id }}"
                            data-end-time="{{ $booking->scheduled_end_time?->format('H:i') }}">
                        <i class="icon-base bx bx-time-five me-1"></i> Request Extension
                    </button>

                @elseif ($isPending)
                    <div class="alert alert-warning mb-0 py-2" style="font-size:.82rem">
                        <i class="icon-base bx bx-time-five me-1"></i>
                        Permintaan perpanjangan
                        <strong>+{{ $booking->extension_duration >= 60
                            ? floor($booking->extension_duration / 60).'j'.($booking->extension_duration % 60 > 0 ? ' '.($booking->extension_duration % 60).'m' : '')
                            : $booking->extension_duration.'m' }}</strong>
                        sedang menunggu persetujuan admin.
                    </div>

                @elseif ($isExtending)
                    <div class="alert alert-success mb-0 py-2" style="font-size:.82rem">
                        <i class="icon-base bx bx-check-circle me-1"></i>
                        Perpanjangan waktu telah disetujui. Waktu selesai baru:
                        <strong>{{ $booking->scheduled_end_time?->format('H:i') }} WIB</strong>
                    </div>

                @elseif ($booking->cancelation_reason)
                    <div class="alert alert-danger mb-0 py-2" style="font-size:.82rem">
                        <i class="icon-base bx bx-x-circle me-1"></i>
                        Dibatalkan: {{ $booking->cancelation_reason }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base bx bx-car" style="font-size:2.5rem;color:#b9b9c3;display:block;margin-bottom:.75rem"></i>
                <p style="color:#b9b9c3;margin-bottom:1rem">
                    @if ($filter === 'active') Tidak ada perjalanan aktif saat ini.
                    @elseif ($filter === 'completed') Belum ada riwayat perjalanan.
                    @else Tidak ada pemesanan mendatang.
                    @endif
                </p>
                <a href="{{ route('booking.driver.index') }}" class="btn btn-primary">
                    <i class="icon-base bx bx-plus me-1"></i> Buat Pemesanan
                </a>
            </div>
        </div>
    </div>
@endforelse
</div>

{{-- ── PAGINATION ── --}}
@if ($bookings->hasPages())
    <div class="mt-4">{{ $bookings->links() }}</div>
@endif

{{-- ── EXTENSION MODAL ── --}}
<div class="modal fade" id="extensionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base bx bx-time-five me-1" style="color:#ff9f43"></i>
                    Request Extension
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="extensionForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:.85rem">
                        Current end time: <strong id="currentEndTime">-</strong> WIB.
                        Permintaan perpanjangan akan dikirim ke admin untuk disetujui.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Durasi Perpanjangan</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ([15 => '15 menit', 30 => '30 menit', 60 => '1 jam', 120 => '2 jam'] as $val => $label)
                                <label class="ext-duration-btn">
                                    <input type="radio" name="duration" value="{{ $val }}" class="d-none ext-duration-radio"
                                           {{ $val === 30 ? 'checked' : '' }}>
                                    <span class="btn btn-outline-warning btn-sm ext-duration-label {{ $val === 30 ? 'active' : '' }}">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea class="form-control" name="reason" rows="3"
                                  placeholder="Jelaskan mengapa Anda memerlukan waktu tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="icon-base bx bx-send me-1"></i> Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $('#extensionModal').on('show.bs.modal', function (e) {
        const $btn = $(e.relatedTarget);
        const routeTemplate = @js(route('my-booking.driver.extension-request', '__ID__'));

        $('#currentEndTime').text($btn.data('end-time'));
        $('#extensionForm').attr('action', routeTemplate.replace('__ID__', $btn.data('booking-id')));
    });

    $('#extensionModal').on('change', '.ext-duration-radio', function () {
        $('.ext-duration-label').removeClass('active');
        $(this).next('.ext-duration-label').addClass('active');
    });
</script>
@endsection
