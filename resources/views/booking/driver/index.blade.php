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

            foreach ($booked as $book) {
                if (!$book->driver) continue;

                $driver = str()->slug($book->driver->Name);

                $start = $book->scheduled_pickup_time->format('H:i');
                $end = $book->scheduled_end_time->format('H:i');

                $rowspan = $book->scheduled_pickup_time->diffInMinutes($book->scheduled_end_time) / $slotMinutes;

                // booking start cell
                $bookedMap[$driver][$start] = [
                    'booking_id' => $book->id,
                    'user_nik' => $book->user_nik,
                    'name' => $book->user->Name,
                    'rowspan' => $rowspan,
                    'scheduled_pickup_time' => $start,
                    'scheduled_end_time' => $end,
                    'purpose_of_trip' => $book->purpose_of_trip,
                    'destination' => $book->destination
                ];

                // dd($bookedMap);

                // mark rows to skip
                for ($i = 1; $i < $rowspan; $i++) {
                    $skipTime=$book->scheduled_pickup_time->copy()
                    ->addMinutes($i * $slotMinutes)
                    ->format('H:i');

                    $skipMap[$driver][$skipTime] = true;
                }
            }

            // For today: re-anchor bookings that started in the past but end in the future
            if ($isToday) {
                foreach ($bookedMap as $slug => $timesMap) {
                    foreach ($timesMap as $start => $booking) {
                        if ($start >= $currentTime) continue;

                        $end = $booking['scheduled_end_time'];
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
                            'scheduled_pickup_time' => $firstVisible,
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

        <input type="text" class="form-control flatpickr" placeholder="Choose Date" value="{{ date('d M Y', strtotime($year.'-'.$month.'-'.$day)) }}">

        <div class="table-responsive">
            <table class="table table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th width="5%" class=" "></th>
                        @foreach ($drivers as $driver)
                        <th class=" room-header-cell">
                            <a href="">
                                <span class="d-none d-md-inline">{{ $driver->Name }}</span>
                                <span class="d-md-none">
                                    {{ Str::limit($driver->Name, 16, '') }}
                                </span>
                            </a>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timeRanges as $range)
                    @if ($isToday && $range < $currentTime)
                        @continue
                    @endif
                    <tr>
                        <td class="" style="vertical-align: top;">
                            {{ $range }}
                        </td>
                        @if( $range !== '17:00' )
                            @foreach ($drivers as $driver)
                                @php
                                    $driverSlug = str()->slug($driver->Name);
                                @endphp
                                @if (!empty($skipMap[$driverSlug][$range]))
                                @continue
                                @endif

                                @if (!empty($bookedMap[$driverSlug][$range]))
                                    @php $booking = $bookedMap[$driverSlug][$range]; @endphp

                                    <td class="text-center align-middle text-white booked-cell bg-primary"
                                        rowspan="{{ $booking['rowspan'] }}" data-bs-toggle="modal"
                                        data-bs-target="#EditDriverBookingModal"
                                        data-driver_nik="{{ $driver->NIK }}"
                                        data-driver_name="{{ $driver->Name }}"
                                        data-slug="{{ $driverSlug }}"
                                        data-year="{{ $year }}"
                                        data-month="{{ $month }}"
                                        data-day="{{ $day }}"
                                        data-time_range="{{ $range }}"

                                        data-scheduled_pickup_time="{{ $booking['scheduled_pickup_time'] }}"
                                        data-scheduled_end_time="{{ $booking['scheduled_end_time'] }}"
                                        data-purpose_of_trip="{{ $booking['purpose_of_trip'] }}"
                                        data-booking_id="{{ $booking['booking_id'] }}"
                                        data-user_nik="{{ $booking['user_nik'] }}"
                                        data-name="{{ $booking['name'] }}"
                                        data-destination="{{ $booking['destination'] }}"
                                    >
                                        <strong>BOOKED</strong><br>
                                        {{ $booking['scheduled_pickup_time'] }} – {{ $booking['scheduled_end_time'] }} <br>
                                        {{ $booking['name'] }} <br>
                                        {{ $booking['purpose_of_trip'] }}
                                    </td>

                                    {{-- ✅ available slot --}}
                                @else
                                    <td class="text-center hoverCell" style="cursor: pointer;" data-bs-toggle="modal"
                                        data-bs-target="#DriverBookingModal"
                                        data-slug="{{ str()->slug($driver->Name) }}"
                                        data-driver_name="{{ $driver->Name }}"
                                        data-driver_nik="{{ $driver->NIK }}"
                                        data-year="{{ $year }}"
                                        data-month="{{ $month }}"
                                        data-day="{{ $day }}"
                                        data-time_range="{{ $range }}"
                                    >
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

<div class="modal fade" id="DriverBookingModal" tabindex="-1" aria-labelledby="DriverBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="DriverBookingModalLabel">
                    Booking Driver
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('booking.driver.store') }}" method="post">
                @csrf
                <input type="hidden" class="driver_slug" name="driver_slug">
                <input type="hidden" class="driver_name" name="driver_name">
                <input type="hidden" class="driver_nik" name="driver_nik">
                <input type="hidden" class="year" name="year">
                <input type="hidden" class="month" name="month">
                <input type="hidden" class="day" name="day">
                <input type="hidden" class="time" name="time">

                <div class="modal-body position-relative">

                    <div class="form-group mt-2">
                        <label for="stime">Scheduled Pickup At</label>
                        <select name="stime" id="stime" class="form-control" required>
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="etime">Scheduled End At</label>
                        <select name="etime" id="etime" class="form-control" required>
                            <option value="">-- Select Time --</option>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="destination">Destination</label>
                        <input type="text" name="destination" id="destination" class="form-control" placeholder="Cari Nama Gedung / Jalan" required>
                        <ul class="dropdown-menu autocomplete-dropdown w-100" id="destinationDropdown">
                            <!-- Results will be dynamically inserted here -->
                        </ul>
                    </div>

                    <div class="form-group mt-2">
                        <label for="purpose_of_trip">Purpose of Trip</label>
                        {{-- <input type="text" name="purpose_of_trip" id="purpose_of_trip" class="form-control" placeholder="Cari Nama Gedung / Jalan"> --}}
                        <textarea name="purpose_of_trip" id="purpose_of_trip" cols="30" rows="10" class="form-control" placeholder="Enter Purpose of Trip" required></textarea>
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

<div class="modal fade" id="EditDriverBookingModal" tabindex="-1" aria-labelledby="EditDriverBookingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="EditDriverBookingModalLabel">
                    Edit Booking
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAction" action="{{ route('booking.driver.store') }}" method="post">
                @csrf
                @method('put')
                <input type="hidden" class="edit_driver_slug" name="driver_slug">
                <input type="hidden" class="edit_driver_name" name="driver_name">
                <input type="hidden" class="edit_driver_nik" name="driver_nik">
                <input type="hidden" class="edit_year" name="year">
                <input type="hidden" class="edit_month" name="month">
                <input type="hidden" class="edit_day" name="day">
                <input type="hidden" class="edit_time" name="time">
                <input type="hidden" class="edit_booking_id">

                <div class="modal-body position-relative">
                    <div class="form-group">
                        <label for="edit_stime">Scheduled Pickup At</label>
                        <select name="stime" id="edit_stime" class="form-control">
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_etime">Scheduled End At</label>
                        <select name="etime" id="edit_etime" class="form-control" required>
                            <option value="">-- Select Time --</option>
                            @foreach ($timeRanges as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_destination">Destination</label>
                        <input type="text" name="destination" id="edit_destination" class="form-control" value="" placeholder="Cari Nama Gedung / Jalan" required>
                        <ul class="dropdown-menu autocomplete-dropdown w-100" id="destinationDropdown">
                            <!-- Results will be dynamically inserted here -->
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="edit_purpose_of_trip">Purpose of Trip</label>
                        <textarea name="purpose_of_trip" id="edit_purpose_of_trip" cols="30" rows="10" class="form-control" placeholder="Enter Purpose of Trip" required></textarea>
                    </div>
                </div>
            </form>

            <form id="formCancelAction" method="post">
                @csrf
                @method('delete')
            </form>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="formCancelAction" class="btn btn-danger modal-footer-button-cancel">Cancel Booking</button>
                <button type="submit" form="formEditAction" class="btn btn-primary modal-footer-button-save">Save changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Minimal styles for autocomplete behavior */
    .autocomplete-dropdown {
        max-height: 300px;
        overflow-y: auto;
    }

    .autocomplete-dropdown.show {
        display: block !important;
        margin-top: 0.25rem;
    }

    .autocomplete-loading::before {
        content: '';
        display: inline-block;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        vertical-align: middle;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

@endsection
@section('script')
<script>
    const timeRanges = @json($timeRanges);
    const booked = @json($booked);
    const searchMapUrl = @js(config('map.nominatim.url'));
    const isToday = @json(now()->format('Y-m-d') === "$year-$month-$day");
    const currentTimeStr = @json(now()->format('H:i'));

    let highlightedIndex = -1;
    let filteredResults = [];
    let debounceTimer = null;

    flatpickr(".flatpickr", {
        // inline: true,
        showMonths: 1,
        dateFormat: "d M Y",
        minDate: "today",
        maxDate: new Date(new Date().setDate(new Date().getDate() + {{ $driverDays }})),
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

    // Handle click on result
    $(document).on('click', '.destination-item', function(e) {
        e.preventDefault();
        selectDestination($(this));
    });

    // Keyboard navigation
    $(document).on('keydown', '#destination', function(e) {
        const $items = $('#destinationDropdown').find('.dropdown-item:not(.disabled)');
        const itemCount = $items.length;

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if ($('#destinationDropdown').hasClass('show') && itemCount > 0) {
                    highlightedIndex = Math.min(highlightedIndex + 1, itemCount - 1);
                    updateHighlight($items);
                    $items.eq(highlightedIndex)[0].scrollIntoView({ block: 'nearest' });
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if ($('#destinationDropdown').hasClass('show') && itemCount > 0) {
                    highlightedIndex = Math.max(highlightedIndex - 1, -1);
                    updateHighlight($items);
                    if (highlightedIndex >= 0) {
                        $items.eq(highlightedIndex)[0].scrollIntoView({ block: 'nearest' });
                    }
                }
                break;

            case 'Enter':
                e.preventDefault();
                if (highlightedIndex >= 0 && $items.eq(highlightedIndex).length) {
                    selectDestination($items.eq(highlightedIndex));
                }
                break;

            case 'Escape':
                $('#destinationDropdown').removeClass('show');
                highlightedIndex = -1;
                break;
        }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('#destinationDropdown').removeClass('show');
            highlightedIndex = -1;
        }
    });

    $('#DriverBookingModal').on('show.bs.modal', function (event) {
        const button                = $(event.relatedTarget); // clicked div
        const slug                  = button.data('slug');
        const year                  = button.data('year');
        const month                 = button.data('month');
        const day                   = button.data('day');
        const dateStr               = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
        const selected_time_range   = button.data('time_range');
        const driver_name           = button.data('driver_name');
        const driver_nik            = button.data('driver_nik')
        const modal                 = $(this);

        $('.modal-footer').show();
        $('#edit_stime').attr('disabled', false);
        $('#edit_etime').attr('disabled', false);
        $('#edit_description').attr('disabled', false);
        $('#edit_participant').attr('disabled', false);

        // Change modal title
        modal.find('.modal-title').text('Booking Driver ' + driver_name);            
        modal.find('.driver_nik').val(driver_nik);
        modal.find('.driver_slug').val(slug);
        modal.find('.driver_name').val(driver_name);
        modal.find('.year').val(year);
        modal.find('.month').val(month);
        modal.find('.day').val(day);
        modal.find('.time').val(selected_time_range);

        generateStartTimes(slug);
        // generateStartTimes(time_range, dateStr, slug);
        $('#stime').val(selected_time_range);
        generateEndTimes(selected_time_range, slug);
        // generateEndTimes(time_range, dateStr, slug);
    });

    $('#EditDriverBookingModal').on('show.bs.modal', function (event) {
        const button                    = $(event.relatedTarget); // clicked div
        const modal                     = $(this);
        const driver_nik                = button.data('driver_nik');
        const driver_name               = button.data('driver_name');
        const slug                      = button.data('slug');
        const year                      = button.data('year');
        const month                     = button.data('month');
        const day                       = button.data('day');
        const dateStr                   = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
        const selected_time_range       = button.data('time_range');
        const scheduled_pickup_time     = button.data('scheduled_pickup_time');
        const scheduled_end_time        = button.data('scheduled_end_time');
        const purpose_of_trip           = button.data('purpose_of_trip');
        const booking_id                = button.data('booking_id');
        const user_nik                  = button.data('user_nik');
        const name                      = button.data('name');
        const nik_login                 = parseInt(@js(auth()->user()->NIK));
        const destination               = button.data('destination');

        $('.modal-footer').show();
        $('#edit_stime').attr('disabled', false);
        $('#edit_etime').attr('disabled', false);
        $('#edit_destination').attr('disabled', false);
        $('#edit_purpose_of_trip').attr('disabled', false);
        modal.find('.modal-title').text('Edit Booking Driver ' + slug);

        if( user_nik !== nik_login ){
            $('.modal-footer').hide();
            $('#edit_stime').attr('disabled', true);
            $('#edit_etime').attr('disabled', true);
            $('#edit_destination').attr('disabled', true);
            $('#edit_purpose_of_trip').attr('disabled', true);
            modal.find('.modal-title').text(`Booked By : ${name}`);
        }

        $('#edit_stime').val(scheduled_pickup_time);
        $('#edit_etime').val(scheduled_end_time);
        $('#edit_description').val(purpose_of_trip);

        let routeUpdate = @js(route('booking.driver.update', '__ID__'));
        routeUpdate = routeUpdate.replace('__ID__', booking_id);

        let routeCancel = @js(route('booking.driver.destroy', '__ID__'));
        routeCancel = routeCancel.replace('__ID__', booking_id);

        // Change modal title
        modal.find('#formEditAction').attr('action', routeUpdate);    
        modal.find('#formCancelAction').attr('action', routeCancel);    
        modal.find('.edit_driver_name').val(driver_name);
        modal.find('.edit_driver_slug').val(slug);
        modal.find('.edit_driver_nik').val(slug);
        modal.find('.edit_booking_id').val(booking_id);
        modal.find('.edit_year').val(year);
        modal.find('.edit_month').val(month);
        modal.find('.edit_day').val(day);
        modal.find('.edit_time').val(scheduled_pickup_time);
        modal.find('#edit_destination').val(destination);
        modal.find('#edit_purpose_of_trip').html(purpose_of_trip);

        generateStartTimes(slug, booking_id);
        // generateStartTimes(time_range, dateStr, slug, booking_id, driver_nik);
        $('#edit_stime').val(selected_time_range);
        generateEndTimes(selected_time_range, slug, booking_id)
        // generateEndTimes(time_range, dateStr, slug, booking_id, driver_nik);
        $('#edit_etime').val(scheduled_end_time);


        // TODO: Harusnya yang bukan punya sendiri, tidak bisa edit.
    });

    $('body').on('keyup', '#destination', function(){
        const $input = $(this);
        const searchTerm = $.trim($input.val());
        const $dropdown = $('#destinationDropdown');

        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        if (searchTerm.length < 1) {
            $dropdown.removeClass('show').empty();
            highlightedIndex = -1;
            return;
        }

        // Show loading state
        $dropdown.addClass('show').html('<li class="dropdown-item disabled autocomplete-loading">Loading...</li>');

        debounceTimer = setTimeout(function() {
            // AJAX call to search map
            $.ajax({
                url: searchMapUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    q: searchTerm,
                    format: 'json',
                    limit: 20
                },
                success: function(response) {
                    filteredResults = response;
                    renderResults(response);
                    highlightedIndex = -1;
                },
                error: function() {
                    $dropdown.html('<li class="dropdown-item disabled text-center text-body-secondary">Error loading results</li>');
                }
            });
        }, 500); // Wait 500ms after user stops typing
    });

    $('#stime').on('change', function() {
        const stime = $(this).val();
        const slug = $('#DriverBookingModal .driver_slug').val();
        generateEndTimes(stime, slug);
    });

    $('#edit_stime').on('change', function() {
        const stime = $(this).val();
        const slug = $('#EditDriverBookingModal .edit_driver_slug').val();
        const idBooking = parseInt($('#EditDriverBookingModal .edit_booking_id').val());
        generateEndTimes(stime, slug, idBooking);
    });

    // Render results using Bootstrap dropdown-item classes
    function renderResults(results) {
        const $dropdown = $('#destinationDropdown');
        
        if (results.length === 0) {
            $dropdown.html('<li class="dropdown-item disabled text-center text-body-secondary">No locations found</li>');
            return;
        }

        let html = '';
        $.each(results, function(index, item) {
            html += '<li>';
            html += '<a href="javascript:void(0);" class="dropdown-item destination-item" data-index="' + index + '" data-display-name="' + item.display_name + '" data-lat="' + item.lat + '" data-lon="' + item.lon + '">';
            html += '<div class="fw-semibold">' + item.name + '</div>';
            html += '<small class="text-body-secondary">' + item.display_name + '</small>';
            html += '</a>';
            html += '</li>';
        });

        $dropdown.html(html);
    }

    // Select destination
    function selectDestination($element) {
        const displayName = $element.data('display-name');
        $('#destination').val(displayName);
        $('#destinationDropdown').removeClass('show').empty();
        highlightedIndex = -1;
        $('#destination').trigger('change');
    }

    // Update highlight on keyboard navigation
    function updateHighlight($items) {
        $items.each(function(index) {
            if (index === highlightedIndex) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    function toMinutes(time) {
        const [h, m] = time.split(':').map(Number);
        return h * 60 + m;
    }

    function fromMinutes(minutes) {
        const h = String(Math.floor(minutes / 60)).padStart(2, '0');
        const m = String(minutes % 60).padStart(2, '0');
        return `${h}:${m}`;
    }

    function generateStartTimes(slug, idBooking = null) {
        if( !idBooking ) {
            const stimeSelect = $('#stime');
            stimeSelect.empty();
            stimeSelect.append(`<option value=''>-- Select Time --</option>`);
            timeRanges.forEach(time => {
                if (isToday && toMinutes(time) < toMinutes(currentTimeStr)) return;

                const timeToMinutes = toMinutes(time);
                let overlap = false;

                slug = slug.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                for( const book of booked ) {
                    if( book.driver.Name !== slug ) continue;

                    const bookedTimeStart = toMinutes(book.scheduled_pickup_time);
                    const bookedTimeEnd = toMinutes(book.scheduled_end_time);

                    if( timeToMinutes >= bookedTimeStart && timeToMinutes < bookedTimeEnd ) {
                        overlap = true;
                        break;
                    }
                }

                if( overlap ) return;
                stimeSelect.append(`<option value="${time}">${time}</option>`);
            });
        } else {
            const stimeSelect = $('#edit_stime');
            stimeSelect.empty();
            stimeSelect.append(`<option value="">-- Select Time --</option>`);

            timeRanges.forEach(time => {
                if (isToday && toMinutes(time) < toMinutes(currentTimeStr)) return;

                const timeToMinutes = toMinutes(time);
                let overlap = false;

                for( const book of booked ) {
                    if( book.driver.Name.split(' ').join('-').toLowerCase() !== slug ) continue;
                    const bookedTimeStart = toMinutes(book.scheduled_pickup_time);
                    const bookedTimeEnd = toMinutes(book.scheduled_end_time);

                    if( book.id !== idBooking ) {
                        if( timeToMinutes >= bookedTimeStart && timeToMinutes < bookedTimeEnd ) {
                            overlap = true;
                        }
                    }

                    if( time === '17:00' ) {
                        overlap = true;
                    }
                }

                if( overlap ) return;
                stimeSelect.append(`<option value="${time}">${time}</option>`);
            });
        }
    }

    function generateEndTimes(selectedStartTime, slug, idBooking = null) {
        if( !idBooking ) {
            const etimeSelect = $('#etime');
            const selectedStartTimeToMinutes = toMinutes(selectedStartTime);

            etimeSelect.empty();
            etimeSelect.append(`<option value="">-- Select Time --</option>`);

            if(!selectedStartTime) return;

            for( const time of timeRanges ) {
                const timeToMinutes = toMinutes(time);
                if (timeToMinutes <= selectedStartTimeToMinutes) continue;
                let disabled = false;

                let bookedTimeStart = '';
                let bookedTimeEnd = '';
                
                for (const book of booked) {
                    if( book.driver.Name.split(' ').join('-').toLowerCase() !== slug ) continue;
                    bookedTimeStart = toMinutes(book.scheduled_pickup_time);
                    bookedTimeEnd = toMinutes(book.scheduled_end_time);

                    if( timeToMinutes > bookedTimeStart && timeToMinutes < bookedTimeEnd ) {
                        disabled = true;
                        break;
                    }
                }

                if ( disabled ) continue;

                const durationInMinute = timeToMinutes - selectedStartTimeToMinutes;
                const hours = Math.floor(durationInMinute / 60);
                const minutes = durationInMinute % 60;
                
                let durationText = '';
                if( hours ) durationText += `${hours} Hour${hours > 1 ? 's' : ''}`;
                if( minutes ) durationText += ` ${minutes} Minutes`;
                durationText = durationText.trim();

                etimeSelect.append(`
                    <option value="${time}" ${disabled ? 'disabled' : ''}>
                        ${time} (${durationText})    
                    </option>
                `);
            }
        } else {
            const etimeSelect = $('#edit_etime');
            etimeSelect.empty();
            etimeSelect.append('<option value="">-- Select Time --</option>');

            if (!selectedStartTime) return;

            const selectedStartTimeInMinutes = toMinutes(selectedStartTime);

            let bookedTime = [];

            booked.forEach(book => {
                if( book.id === idBooking ) return;

                if( book.driver.Name.split(' ').join('-').toLowerCase() === slug ){
                    let bookedStartTime = toMinutes(book.scheduled_pickup_time);
                    let bookedEndTime = toMinutes(book.scheduled_end_time);                    
                    let end   = toMinutes('17:00');

                    // kalo waktu yang di pilih >= waktu booking yang lain. misal waktu book : 09:00 - 10:00 waktu yang di pilih 10:00.
                    // maka endtime nya endtime dari waktu booking (10:00), karna waktu yang lain masih bisa di pilih.
                    // kalo waktu yang di pilih < waktu booking yang lain. misal waktu book : 09:00 - 10:00 waktu yang di pilih 08:00.
                    // maka endtime nya 17:00, karna tidak mungkin book melewati jam 09:00. maka select box hanya menampilkan 08:30, 09:00
                    if( selectedStartTimeInMinutes >= bookedEndTime ) {
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
            availableTime = availableTime.filter(time => time > selectedStartTime);

            availableTime.forEach(time => {
                const timeToMinutes = toMinutes(time);
                const durationInMinutes = timeToMinutes - selectedStartTimeInMinutes;
                const hours = Math.floor(durationInMinutes / 60);
                const minutes = durationInMinutes % 60;

                let durationText = '';

                if( hours ) durationText += `${hours} Hour${hours > 1 ? '%' : ''}`;
                if( minutes ) durationText += `${minutes} Minutes`;

                durationText = durationText.trim();

                etimeSelect.append(`
                    <option value="${time}">
                        ${time} (${durationText})
                    </option>
                `);
            });
        }
    }
</script>
@endsection