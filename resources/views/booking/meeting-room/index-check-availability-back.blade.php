@extends('layouts.app')
@section('content')
<div class="row">
    <form action="{{ route('booking.meeting-room.index') }}" method="get">
        <div class="card">
            <div class="card-body row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="sdate">Start Date</label>
                        <input type="date" id="sdate" class="form-control" name="sdate" value="{{ request()->sdate }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="edate">End Date</label>
                        <input type="date" id="edate" class="form-control" name="edate" value="{{ request()->edate }}">
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </form>



    {{-- @foreach ($rooms as $title => $room)
    <div class="col-12">
        <h1>
            {{ $title }}
        </h1>
    </div>

    @foreach ($room as $list)
    <div class="col-4 mb-4">
        <a href="{{ route('booking.meeting-room.show', str($list)->slug()) }}">
            <div class="card">
                <div class="card-header">
                    <i class="icon-base bx bx-building"></i>
                    {{ $list }}
                </div>
                <div class="card-body">
                    <i class="icon-base bx bxs-circle text-success"></i>
                    Available
                </div>
            </div>
        </a>
    </div>
    @endforeach
    @endforeach --}}

    @foreach ($availability as $location => $rooms)
        <div class="col-12">
            <h1>
                {{ $location }}
            </h1>
        </div>

        @foreach ($rooms as $room => $dates)
        <div class="col-4 mb-4">
            <div class="card">
                <div class="card-header">
                        <a href="{{ route('booking.meeting-room.show', [
                            'meeting_room' => str($room)->slug(),
                            'sdate' => request()->sdate,
                            'edate' => request()->edate
                        ]) }}">
                        <h4>
                            <i class="icon-base bx bx-building"></i>
                            {{ $room }}
                        </h4>
                        </a>
                    </div>
                    <div class="card-body">
                        @foreach ($dates as $date => $slots)
                            @if( count($slots) > 0 )
                                <div class="accordion accordion-custom-button mt-3" id="accordionCustom_{{ str($room)->snake() }}_{{ $date }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingCustom_{{ str($room)->snake() }}_{{ $date }}">
                                            <button type="button" class="accordion-button" data-bs-toggle="collapse"
                                                data-bs-target="#accordian_{{ str($room)->snake() }}_{{ $date }}"
                                                aria-controls="accordian_{{ str($room)->snake() }}_{{ $date }}">
                                                {{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                            </button>
                                        </h2>

                                        <div id="accordian_{{ str($room)->snake() }}_{{ $date }}" class="accordion-collapse collapse"
                                            aria-labelledby="headingCustom_{{ str($room)->snake() }}_{{ $date }}" data-bs-parent="#accordionCustom_{{ str($room)->snake() }}_{{ $date }}">
                                            <div class="accordion-body">
                                                @foreach ($slots as $slot)
                                                {{-- {{ $slot }}
                                                <i class="icon-base bx bxs-circle text-success"></i>
                                                Available --}}

                                                <div class="badge bg-success mt-2 min-w-2">
                                                    {{ $slot }}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
        </div>
        @endforeach
    @endforeach
</div>
@endsection