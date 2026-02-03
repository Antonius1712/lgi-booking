@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body row justify-content-center g-4">
        @php
            $year = request()->year ?? now()->format('Y');
            $month = request()->month ?? now()->format('m');
            $day = request()->day ?? now()->format('d');

            $slotMinutes = 30;
            $bookedMap = [];
            $skipMap = [];

            foreach ($booked as $b) {
                if (!$b->meetingRoom) continue;

                $roomSlug = $b->meetingRoom->slug;

                $start = $b->start_time->format('H:i');
                $end = $b->end_time->format('H:i');

                $rowspan = $b->start_time->diffInMinutes($b->end_time) / $slotMinutes;

                // booking start cell
                $bookedMap[$roomSlug][$start] = [
                'id' => $b->id,
                'rowspan' => $rowspan,
                'end' => $end,
                'by' => $b->user->Name,
                'description' => $b->description
                ];

                // mark rows to skip
                for ($i = 1; $i < $rowspan; $i++) { 
                    $skipTime=$b->start_time->copy()
                    ->addMinutes($i * $slotMinutes)
                    ->format('H:i');

                    $skipMap[$roomSlug][$skipTime] = true;
                }
            }
        @endphp
        
        <input type="text" class="form-control flatpickr" placeholder="Choose Date" value="{{ date('d M Y', strtotime($year.'-'.$month.'-'.$day)) }}">

        <div class="table-responsive">
            <table class="table table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th width="5%" class=" "></th>
                        @foreach ($rooms as $room)
                        <th class=" room-header-cell">
                            <a href="">
                                {{-- {{ $room->location->name }} <br /> --}}
                                {{-- {{ $room->name }} --}}

                                <span class="d-none d-md-inline">{{ $room->name }}</span>
                                <span class="d-md-none">
                                    {{ Str::limit($room->name, 16, '') }}
                                </span>
                            </a>
                        </th>
                        @endforeach
                    </tr>
                    @foreach ($timeRanges as $range)
                    <tr>
                        <td class="" style="vertical-align: top;">
                            {{ $range }}
                        </td>
                        @if( $range !== '17:00' )
                            @foreach ($rooms as $room)
                                @if (!empty($skipMap[$room->slug][$range]))
                                    @continue
                                @endif
            
                                @if (!empty($bookedMap[$room->slug][$range]))
                                @php $booking = $bookedMap[$room->slug][$range]; @endphp
            
                                <td class="text-center align-middle text-white booked-cell bg-primary"
                                    rowspan="{{ $booking['rowspan'] }}" data-bs-toggle="modal"
                                    data-bs-target="#EditBookingMeetingRoomModal" data-r="{{ $room->slug }}"
                                    data-y="{{ $year }}" data-m="{{ $month }}" data-d="{{ $day }}" data-t="{{ $range }}"
                                    data-sb="{{ $range }}" data-eb="{{ $booking['end'] }}"
                                    data-desc=@json($booking['description']) data-i="{{ $booking['id'] }}"
                                    style="cursor: pointer;">
                                    <strong>BOOKED</strong><br>
                                    {{ $range }} – {{ $booking['end'] }} <br>
                                    {{ $booking['by'] }} <br>
                                    {{ $booking['description'] }}
                                </td>
            
                                {{-- ✅ available slot --}}
                                @else
                                <td class="text-center hoverCell" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#bookingMeetingRoomModal"
                                    data-r="{{ $room->slug }}" data-y="{{ $year }}" data-m="{{ $month }}"
                                    data-d="{{ $day }}" data-t="{{ $range }}">
                                    <div>
                                        &nbsp;
                                    </div>
                                </td>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingMeetingRoomModal" tabindex="-1" aria-labelledby="bookingMeetingRoomModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="bookingMeetingRoomModalLabel">
                    Booking Meeting Room
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('booking.meeting-room.store') }}" method="post">
                @csrf
                <input type="hidden" class="room" name="room">
                <input type="hidden" class="year" name="year">
                <input type="hidden" class="month" name="month">
                <input type="hidden" class="day" name="day">
                <input type="hidden" class="time" name="time">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="stime">Start Time</label>
                        <select name="stime" id="stime" class="form-control">
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="etime">End Time</label>
                        <select name="etime" id="etime" class="form-control">
                            <option value="">-- Select Time --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="participant">Participant</label>
                        <input type="text" name="participant" id="participant" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="EditBookingMeetingRoomModal" tabindex="-1"
    aria-labelledby="EditBookingMeetingRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="EditBookingMeetingRoomModalLabel">
                    Edit Booking
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAction" action="{{ route('booking.meeting-room.store') }}" method="post">
                @csrf
                @method('put')
                <input type="hidden" class="room" name="room">
                <input type="hidden" class="year" name="year">
                <input type="hidden" class="month" name="month">
                <input type="hidden" class="day" name="day">
                <input type="hidden" class="time" name="time">
                <input type="hidden" class="idBooking" name="idBooking">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="e_stime">Start Time</label>
                        <select name="e_stime" id="e_stime" class="form-control">
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="e_etime">End Time</label>
                        <select name="e_etime" id="e_etime" class="form-control">
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="e_description">Description</label>
                        <textarea name="e_description" id="e_description" cols="30" rows="10"
                            class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="e_participant">Participant</label>
                        <input type="text" name="e_participant" id="e_participant" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary modal-footer-button-save">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function () {
        const timeRanges = @json($timeRanges);
        const booked = @json($booked);

        flatpickr(".flatpickr", {
            // inline: true,
            showMonths: 1,
            dateFormat: "d M Y",
            // minDate: "today",
            onChange: function(selectedDates) {
                if (!selectedDates.length) return;
                $('#loading').show();
                const d = selectedDates[0];
                const year  = d.getFullYear();
                const month = d.getMonth() + 1; // JS months are 0-based
                const day   = d.getDate();

                setTimeout(() => {
                    window.location.href =
                    `?year=${year}&month=${month}&day=${day}`;
                }, 50);

            }
        });

        $('#bookingMeetingRoomModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // clicked div
            const room_slug = button.data('r');
            const year   = button.data('y');
            const month  = button.data('m');
            const day    = button.data('d');
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const time   = button.data('t');
            const room   = room_slug.split('-').map(w => w[0].toUpperCase() + w.slice(1)).join(' ')
            const modal = $(this);

            // Change modal title
            modal.find('.modal-title').text('Booking Room ' + room);            
            modal.find('.room').val(room_slug);
            modal.find('.year').val(year);
            modal.find('.month').val(month);
            modal.find('.day').val(day);
            modal.find('.time').val(time);

            generateStartTimes(time, dateStr, room_slug);
            $('#stime').val(time);
            generateEndTimes(time, dateStr, room_slug);
        });

        $('#EditBookingMeetingRoomModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // clicked div
            const room_slug = button.data('r');
            const year   = button.data('y');
            const month  = button.data('m');
            const day    = button.data('d');
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const time   = button.data('t');
            const room   = room_slug.split('-').map(w => w[0].toUpperCase() + w.slice(1)).join(' ')
            const start_booking = button.data('sb');
            const end_booking = button.data('eb');
            const description = button.data('desc');
            const modal = $(this);
            const idBooking = button.data('i');

            $('#e_stime').val(start_booking);
            $('#e_etime').val(end_booking);
            $('#e_description').val(description);

            let routeUpdate = @js(route('booking.meeting-room.update', '__ID__'));
            routeUpdate = routeUpdate.replace('__ID__', idBooking);

            // Change modal title
            modal.find('#formEditAction').attr('action', routeUpdate);
            modal.find('.modal-title').text('Edit Booking Room ' + room);            
            modal.find('.room').val(room_slug);
            modal.find('.year').val(year);
            modal.find('.month').val(month);
            modal.find('.day').val(day);
            modal.find('.time').val(time);
            modal.find('.idBooking').val(idBooking);

            generateStartTimes(time, dateStr, room_slug, idBooking);
            $('#e_stime').val(time);
            generateEndTimes(time, dateStr, room_slug, idBooking);
            $('#e_etime').val(end_booking);


            // TODO: Harusnya yang bukan punya sendiri, tidak bisa edit.
        });

        $('#stime').on('change', function() {
            const stime = $(this).val();
            const year = $('#bookingMeetingRoomModal .year').val();
            const month = $('#bookingMeetingRoomModal .month').val();
            const day = $('#bookingMeetingRoomModal .day').val();
            const room_slug = $('#bookingMeetingRoomModal .room').val();
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;

            generateEndTimes(stime, dateStr, room_slug);
        });

        $('#e_stime').on('change', function() {
            const stime = $(this).val();
            const year = $('#EditBookingMeetingRoomModal .year').val();
            const month = $('#EditBookingMeetingRoomModal .month').val();
            const day = $('#EditBookingMeetingRoomModal .day').val();
            const room_slug = $('#EditBookingMeetingRoomModal .room').val();
            const idBooking = parseInt($('#EditBookingMeetingRoomModal .idBooking').val());
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;

            generateEndTimes(stime, dateStr, room_slug, idBooking);
        });

        function timeToMinutes(time) {
            const [h, m] = time.split(':').map(Number);
            return h * 60 + m;
        }

        function generateStartTimes(time, selectedDate, room_slug, idBooking = null) {
            if( !idBooking ) {
                const stimeSelect = $('#stime');
                stimeSelect.empty();
                stimeSelect.append('<option value="">-- Select Time --</option>');

                timeRanges.forEach(t => {
                    const tMinutes = timeToMinutes(t);
                    let overlap = false;

                    for (let i = 0; i < booked.length; i++) {
                        
                        if (booked[i].meeting_room.slug !== room_slug) continue;

                        const bStart = timeToMinutes(booked[i].start_time);
                        const bEnd   = timeToMinutes(booked[i].end_time);

                        // overlap rule for start time
                        if (tMinutes >= bStart && tMinutes < bEnd) {
                            overlap = true;
                            break;
                        }
                    }

                    // ❗ skip disabled start times
                    if (overlap) return;

                    stimeSelect.append(`<option value="${t}">${t}</option>`);
                });
            } else {
                const stimeSelect = $('#e_stime');
                stimeSelect.empty();
                stimeSelect.append('<option value="">-- Select Time --</option>');

                timeRanges.forEach(t => {
                    const tMinutes = timeToMinutes(t);
                    let overlap = false;

                    for (let i = 0; i < booked.length; i++) {
                        
                        if (booked[i].meeting_room.slug !== room_slug) continue;

                        const bStart = timeToMinutes(booked[i].start_time);
                        const bEnd   = timeToMinutes(booked[i].end_time);

                        // overlap rule for start time
                        if( booked[i].id !== idBooking ){
                            if (tMinutes >= bStart && tMinutes < bEnd) {
                                overlap = true;
                                // break;
                            }
                        }

                        if( t === '17:00' ){
                            overlap = true;
                        }
                    }

                    // ❗ skip disabled start times
                    if (overlap) return;

                    stimeSelect.append(`<option value="${t}">${t}</option>`);
                });
            }
        }
        
        function generateEndTimes(stime, selectedDate, room_slug, idBooking = null) {
            if( !idBooking ) {
                const etimeSelect = $('#etime');
                etimeSelect.empty();
                etimeSelect.append('<option value="">-- Select Time --</option>');

                if (!stime) return;

                const startMinutes = timeToMinutes(stime);

                for (let i = 0; i < timeRanges.length; i++) {
                    const etime = timeRanges[i];
                    const etimeMinutes = timeToMinutes(etime);

                    // End time must be AFTER start time
                    if (etimeMinutes <= startMinutes) continue;

                    let disabled = false;

                    for (let j = 0; j < booked.length; j++) {

                        if (booked[j].meeting_room.slug !== room_slug) continue;

                        const bStart = timeToMinutes(booked[j].start_time);
                        const bEnd   = timeToMinutes(booked[j].end_time);

                        // ❗ CORRECT overlap rule
                        if (etimeMinutes > bStart && startMinutes < bEnd) {
                            disabled = true;
                            break;
                        }
                    }

                    if (disabled) continue;

                    const durationMinutes = etimeMinutes - startMinutes;
                    const hours = Math.floor(durationMinutes / 60);
                    const mins  = durationMinutes % 60;

                    let durationText = '';
                    if (hours) durationText += `${hours} Hour${hours > 1 ? 's' : ''} `;
                    if (mins)  durationText += `${mins} Minutes`;
                    durationText = durationText.trim();

                    etimeSelect.append(`
                        <option value="${etime}" ${disabled ? 'disabled' : ''}>
                            ${etime} (${durationText})
                        </option>
                    `);
                }
            } else {

                console.log('idBooking', idBooking);

                const etimeSelect = $('#e_etime');
                etimeSelect.empty();
                etimeSelect.append('<option value="">-- Select Time --</option>');

                if (!stime) return;

                const startMinutes = timeToMinutes(stime);

                let overlap = [];
                for (let i = 0; i < timeRanges.length; i++) {
                    const currentTimeRange = timeRanges[i];
                    const currentTimeRangeMinutes = timeToMinutes(currentTimeRange);

                    console.log(booked);

                    // End time must be AFTER start time
                    if (currentTimeRangeMinutes <= startMinutes) continue;

                    for (let j = 0; j < booked.length; j++) {
                        if (booked[j].meeting_room.slug !== room_slug) continue;

                        const bookedStartTime = timeToMinutes(booked[j].start_time);
                        const bookedEndTime   = timeToMinutes(booked[j].end_time);

                        if( booked[j].id !== idBooking ){
                            console.log(`currentTimeRangeMinutes = ${currentTimeRangeMinutes} | bookedStartTime = ${bookedStartTime} | bookedEndTime = ${bookedEndTime}`);
                            if( currentTimeRangeMinutes > bookedStartTime && currentTimeRangeMinutes < bookedEndTime ){
                                overlap[i] = true;
                            }
                            
                            if( currentTimeRangeMinutes >= bookedEndTime ){
                                overlap[i] = true;
                            }
                            
                            if( startMinutes >= bookedEndTime ){
                                overlap[i] = false;
                            }
                        }

                        if( booked[j].id === idBooking ){
                            overlap[i] = false;
                        }
                    }

                    // if (overlap) return;
                    if( overlap[i] ) {
                        continue;
                    }

                    const durationMinutes = currentTimeRangeMinutes - startMinutes;
                    const hours = Math.floor(durationMinutes / 60);
                    const mins  = durationMinutes % 60;

                    let durationText = '';
                    if (hours) durationText += `${hours} Hour${hours > 1 ? 's' : ''} `;
                    if (mins)  durationText += `${mins} Minutes`;
                    durationText = durationText.trim();

                    etimeSelect.append(`
                        <option value="${currentTimeRange}">
                            ${currentTimeRange} (${durationText})
                        </option>
                    `);
                }
            }
        }
    });
</script>
@endsection