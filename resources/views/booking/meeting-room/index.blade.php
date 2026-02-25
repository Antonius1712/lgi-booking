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
        $isToday = now()->format('Y-m-d') === "$year-$month-$day";
        $currentTime = now()->format('H:i');

        foreach ($booked as $b) {
        if (!$b->meetingRoom) continue;

        $roomSlug = $b->meetingRoom->slug;

        $start = $b->start_time->format('H:i');
        $end = $b->end_time->format('H:i');

        $rowspan = $b->start_time->diffInMinutes($b->end_time) / $slotMinutes;

        // booking start cell
        $bookedMap[$roomSlug][$start] = [
        'id' => $b->id,
        'nik' => $b->nik,
        'name' => $b->user->Name,
        'rowspan' => $rowspan,
        'end' => $end,
        'by' => $b->user->Name,
        'description' => $b->description
        ];

        // mark rows to skip
        for ($i = 1; $i < $rowspan; $i++) { $skipTime=$b->start_time->copy()
            ->addMinutes($i * $slotMinutes)
            ->format('H:i');

            $skipMap[$roomSlug][$skipTime] = true;
            }
            }

            // For today: re-anchor bookings that started in the past but end in the future
            if ($isToday) {
                foreach ($bookedMap as $slug => $timesMap) {
                    foreach ($timesMap as $start => $booking) {
                        if ($start >= $currentTime) continue;

                        $end = $booking['end'];
                        unset($bookedMap[$slug][$start]);

                        // Clear skip entries caused by this past booking
                        foreach (array_keys($skipMap[$slug] ?? []) as $skipTime) {
                            if ($skipTime < $end) {
                                unset($skipMap[$slug][$skipTime]);
                            }
                        }

                        if ($end <= $currentTime) continue;

                        // Find the first visible slot within this booking's range
                        $firstVisible = null;
                        foreach ($timeRanges as $slot) {
                            if ($slot >= $currentTime && $slot < $end) {
                                $firstVisible = $slot;
                                break;
                            }
                        }

                        if (!$firstVisible) continue;

                        [$fh, $fm] = explode(':', $firstVisible);
                        [$eh, $em] = explode(':', $end);
                        $newRowspan = ((int)$eh * 60 + (int)$em - ((int)$fh * 60 + (int)$fm)) / $slotMinutes;

                        $bookedMap[$slug][$firstVisible] = array_merge($booking, [
                            'rowspan' => $newRowspan,
                            'end' => $end,
                        ]);

                        $prevSlot = $firstVisible;
                        for ($i = 1; $i < $newRowspan; $i++) {
                            [$ph, $pm] = explode(':', $prevSlot);
                            $nextMinutes = (int)$ph * 60 + (int)$pm + 30;
                            $nextSlot = sprintf('%02d:%02d', intdiv($nextMinutes, 60), $nextMinutes % 60);
                            $skipMap[$slug][$nextSlot] = true;
                            $prevSlot = $nextSlot;
                        }
                    }
                }
            }
            @endphp

            <input type="text" class="form-control flatpickr" placeholder="Choose Date"
                value="{{ date('d M Y', strtotime($year.'-'.$month.'-'.$day)) }}">

            <div class="table-responsive">
                <table class="table table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th width="5%" class=" "></th>
                            @foreach ($rooms as $room)
                            <th class=" room-header-cell">
                                <a href="">
                                    <span class="d-none d-md-inline">{{ $room->name }}</span>
                                    <span class="d-md-none">
                                        {{ Str::limit($room->name, 16, '') }}
                                    </span>
                                </a>
                            </th>
                            @endforeach
                        </tr>
                        @foreach ($timeRanges as $range)
                        @if ($isToday && $range < $currentTime)
                            @continue
                        @endif
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
                                        data-bs-target="#EditBookingMeetingRoomModal" 
                                        data-r="{{ $room->slug }}"
                                        data-room_id="{{ $room->id }}" data-y="{{ $year }}" data-m="{{ $month }}"
                                        data-d="{{ $day }}" data-t="{{ $range }}" data-sb="{{ $range }}"
                                        data-eb="{{ $booking['end'] }}" data-desc=@json($booking['description'])
                                        data-i="{{ $booking['id'] }}" data-nik_booking="{{ $booking['nik'] }}"
                                        data-username_booking="{{ $booking['name'] }}" style="cursor: pointer;"
                                    >
                                        <strong>BOOKED</strong><br>
                                        {{ $range }} – {{ $booking['end'] }} <br>
                                        {{ $booking['by'] }} <br>
                                        {{ $booking['description'] }}
                                    </td>

                                    {{-- ✅ available slot --}}
                                    @else
                                    <td class="text-center hoverCell" style="cursor: pointer;" data-bs-toggle="modal"
                                        data-bs-target="#bookingMeetingRoomModal" data-r="{{ $room->slug }}"
                                        data-room_id="{{ $room->id }}" data-y="{{ $year }}" data-m="{{ $month }}"
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

                    <div class="form-group mt-2">
                        <label for="stime">Start Time</label>
                        <select name="stime" id="stime" class="form-control" required>
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="etime">End Time</label>
                        <select name="etime" id="etime" class="form-control" required>
                            <option value="">-- Select Time --</option>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="description">Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="form-control" required></textarea>
                    </div>

                    <div class="form-group mt-2">
                        <label for="description">Usage Type :</label>
                        <br />
                        <div class="form-check form-check-inline mt-4">
                            <input class="form-check-input" type="radio" name="usage_type" id="usage_type_meeting"
                                value="Meeting" required>
                            <label class="form-check-label" for="usage_type_meeting">Meeting</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="usage_type" id="usage_type_interview"
                                value="Interview">
                            <label class="form-check-label" for="usage_type_interview">Interview</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="usage_type" id="usage_type_other"
                                value="Other">
                            <label class="form-check-label" for="usage_type_other">Other</label>
                        </div>
                    </div>

                    {{-- <div class="form-group mt-2">
                        <label for="participant">
                            Participant
                            <span data-bs-toggle="tooltip" data-bs-offset="0,6" data-bs-placement="top"
                                data-bs-html="true"
                                data-bs-original-title="&lt;i class='icon-base bx bx-info icon-xs' &gt;&lt;/i&gt; &lt;span&gt;Email yang ditambahkan akan menerima notifikasi undangan meeting ini.&lt;/span&gt;">
                                (Optional)
                            </span>
                        </label>
                        <div class="col-md-6 select2-primary">
                            <label class="form-label" for="multicol-language">Language</label>
                            <select id="multicol-language" class="select2 form-select" multiple>
                                <option value="en" selected>English</option>
                                <option value="fr" selected>French</option>
                                <option value="de">German</option>
                                <option value="pt">Portuguese</option>
                            </select>
                        </div>
                    </div> --}}
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
        const isToday = @json(now()->format('Y-m-d') === "$year-$month-$day");
        const currentTimeStr = @json(now()->format('H:i'));

        $('.select2').select2();

        flatpickr(".flatpickr", {
            // inline: true,
            showMonths: 1,
            dateFormat: "d M Y",
            minDate: "today",
            maxDate: new Date(new Date().setDate(new Date().getDate() + {{ $meetingRoomDays }})),
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
            const button    = $(event.relatedTarget); // clicked div
            const room_slug = button.data('r');
            const year      = button.data('y');
            const month     = button.data('m');
            const day       = button.data('d');
            const dateStr   = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const time      = button.data('t');
            const room      = room_slug.split('-').map(w => w[0].toUpperCase() + w.slice(1)).join(' ')
            const modal     = $(this);

            $('.modal-footer').show();
            $('#e_stime').attr('disabled', false);
            $('#e_etime').attr('disabled', false);
            $('#e_description').attr('disabled', false);
            $('#e_participant').attr('disabled', false);

            // Change modal title
            modal.find('.modal-title').text('Booking Room ' + room);            
            modal.find('.room').val(room_slug);
            modal.find('.year').val(year);
            modal.find('.month').val(month);
            modal.find('.day').val(day);
            modal.find('.time').val(time);

            generateStartTimes(room_slug);
            $('#stime').val(time);
            generateEndTimes(time, dateStr, room_slug);
        });

        $('#EditBookingMeetingRoomModal').on('show.bs.modal', function (event) {
            const button            = $(event.relatedTarget); // clicked div
            const room_id           = button.data('room_id');
            const room_slug         = button.data('r');
            const year              = button.data('y');
            const month             = button.data('m');
            const day               = button.data('d');
            const dateStr           = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const time              = button.data('t');
            const room              = room_slug.split('-').map(w => w[0].toUpperCase() + w.slice(1)).join(' ')
            const start_booking     = button.data('sb');
            const end_booking       = button.data('eb');
            const description       = button.data('desc');
            const modal             = $(this);
            const idBooking         = button.data('i');
            const nik_booking       = button.data('nik_booking');
            const username_booking  = button.data('username_booking');
            const nik_login         = parseInt(@js(auth()->user()->NIK));

            $('.modal-footer').show();
            $('#e_stime').attr('disabled', false);
            $('#e_etime').attr('disabled', false);
            $('#e_description').attr('disabled', false);
            $('#e_participant').attr('disabled', false);
            modal.find('.modal-title').text('Edit Booking Room ' + room);

            if( nik_booking !== nik_login ){
                $('.modal-footer').hide();
                $('#e_stime').attr('disabled', true);
                $('#e_etime').attr('disabled', true);
                $('#e_description').attr('disabled', true);
                $('#e_participant').attr('disabled', true);
                modal.find('.modal-title').text(`Booked By : ${username_booking}`);
            }

            $('#e_stime').val(start_booking);
            $('#e_etime').val(end_booking);
            $('#e_description').val(description);

            let routeUpdate = @js(route('booking.meeting-room.update', '__ID__'));
            routeUpdate = routeUpdate.replace('__ID__', idBooking);

            // Change modal title
            modal.find('#formEditAction').attr('action', routeUpdate);            
            modal.find('.room').val(room_slug);
            modal.find('.year').val(year);
            modal.find('.month').val(month);
            modal.find('.day').val(day);
            modal.find('.time').val(time);
            modal.find('.idBooking').val(idBooking);

            generateStartTimes(room_slug, idBooking, room_id);
            $('#e_stime').val(time);
            generateEndTimes(time, dateStr, room_slug, idBooking, room_id);
            $('#e_etime').val(end_booking);
        });

        $('#stime').on('change', function() {
            const stime     = $(this).val();
            const year      = $('#bookingMeetingRoomModal .year').val();
            const month     = $('#bookingMeetingRoomModal .month').val();
            const day       = $('#bookingMeetingRoomModal .day').val();
            const room_slug = $('#bookingMeetingRoomModal .room').val();
            const dateStr   = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;

            generateEndTimes(stime, dateStr, room_slug);
        });

        $('#e_stime').on('change', function() {
            const stime     = $(this).val();
            const year      = $('#EditBookingMeetingRoomModal .year').val();
            const month     = $('#EditBookingMeetingRoomModal .month').val();
            const day       = $('#EditBookingMeetingRoomModal .day').val();
            const room_slug = $('#EditBookingMeetingRoomModal .room').val();
            const idBooking = parseInt($('#EditBookingMeetingRoomModal .idBooking').val());
            const dateStr   = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;

            generateEndTimes(stime, dateStr, room_slug, idBooking);
        });

        function timeToMinutes(time) {
            const [h, m] = time.split(':').map(Number);
            return h * 60 + m;
        }

        function generateStartTimes(room_slug, idBooking = null, room_id = null) {
            if( !idBooking ) {
                const stimeSelect = $('#stime');
                stimeSelect.empty();
                stimeSelect.append('<option value="">-- Select Time --</option>');

                timeRanges.forEach(t => {
                    if (isToday && timeToMinutes(t) < timeToMinutes(currentTimeStr)) return;

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
                    if (isToday && timeToMinutes(t) < timeToMinutes(currentTimeStr)) return;

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
        
        function generateEndTimes(stime, selectedDate, room_slug, idBooking = null, room_id = null) {
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

                        const bookedStart = timeToMinutes(booked[j].start_time);
                        const bookedEnd   = timeToMinutes(booked[j].end_time);

                        // ❗ CORRECT overlap rule
                        if (etimeMinutes > bookedStart && startMinutes < bookedEnd) {
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
                const etimeSelect = $('#e_etime');
                etimeSelect.empty();
                etimeSelect.append('<option value="">-- Select Time --</option>');

                if (!stime) return;

                const startMinutes = timeToMinutes(stime);

                let bookedTime = [];
                booked.forEach(item => {
                    if( item.id === idBooking ) return;

                    if( room_id === item.meeting_room_id ){
                        let bookedStartTime = toMinutes(item.start_time);
                        let bookedEndTime = toMinutes(item.end_time);                    
                        
                        let selectedStartTime = toMinutes(stime);
                        let end   = toMinutes('17:00');

                        // kalo waktu yang di pilih >= waktu booking yang lain. misal waktu book : 09:00 - 10:00 waktu yang di pilih 10:00.
                        // maka endtime nya endtime dari waktu booking (10:00), karna waktu yang lain masih bisa di pilih.
                        // kalo waktu yang di pilih < waktu booking yang lain. misal waktu book : 09:00 - 10:00 waktu yang di pilih 08:00.
                        // maka endtime nya 17:00, karna tidak mungkin book melewati jam 09:00. maka select box hanya menampilkan 08:30, 09:00
                        if( selectedStartTime >= bookedEndTime ) {
                            end = (bookedEndTime - 30);
                        }

                        while (bookedStartTime <= end) {
                            bookedStartTime += 30;
                            bookedTime.push(fromMinutes(bookedStartTime));
                        }
                    }                    
                });

                bookedTime = [...new Set(bookedTime)];

                let availableTime = timeRanges.filter(time => !bookedTime.includes(time));
                availableTime = availableTime.filter(time => time > stime);

                for (let index = 0; index < availableTime.length; index++) {
                    const currentTimeRange = availableTime[index];
                    const currentTimeRangeMinutes = timeToMinutes(currentTimeRange);

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


                // let overlap = [];
                // for (let i = 0; i < timeRanges.length; i++) {
                //     const currentTimeRange = timeRanges[i];
                //     const currentTimeRangeMinutes = timeToMinutes(currentTimeRange);
                        
                //     if (currentTimeRangeMinutes <= startMinutes) continue;

                //     for (let j = 0; j < booked.length; j++) {
                //         if (booked[j].meeting_room.slug !== room_slug) continue;

                //         const bookedStartTime = timeToMinutes(booked[j].start_time);
                //         const bookedEndTime   = timeToMinutes(booked[j].end_time);

                //         if( booked[j].id !== idBooking ){
                //             console.log(`currentTimeRangeMinutes = ${currentTimeRangeMinutes} | bookedStartTime = ${bookedStartTime} | bookedEndTime = ${bookedEndTime}`);
                //             if( currentTimeRangeMinutes > bookedStartTime && currentTimeRangeMinutes < bookedEndTime ){
                //                 overlap[i] = true;
                //             }
                            
                //             if( currentTimeRangeMinutes >= bookedEndTime ){
                //                 overlap[i] = true;
                //             }
                            
                //             if( startMinutes >= bookedEndTime ){
                //                 overlap[i] = false;
                //             }
                //         }

                //         if( booked[j].id === idBooking ){
                //             overlap[i] = false;
                //         }
                //     }

                //     const durationMinutes = currentTimeRangeMinutes - startMinutes;
                //     const hours = Math.floor(durationMinutes / 60);
                //     const mins  = durationMinutes % 60;

                //     let durationText = '';
                //     if (hours) durationText += `${hours} Hour${hours > 1 ? 's' : ''} `;
                //     if (mins)  durationText += `${mins} Minutes`;
                //     durationText = durationText.trim();

                //     etimeSelect.append(`
                //         <option value="${currentTimeRange}">
                //             ${currentTimeRange} (${durationText})
                //         </option>
                //     `);
                // }
            }
        }

        function toMinutes(time) {
            const [h, m] = time.split(':');
            return parseInt(h) * 60 + parseInt(m);
        }

        function fromMinutes(minutes) {
            const h = String(Math.floor(minutes / 60)).padStart(2, '0');
            const m = String(minutes % 60).padStart(2, '0');
            return `${h}:${m}`;
        }

    });
</script>
@endsection