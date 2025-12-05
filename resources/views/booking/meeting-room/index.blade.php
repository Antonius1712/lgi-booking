@extends('layouts.app')
@section('content')
    <div class="row">
        {{-- @if( auth()->user()->NIK === '2018113907' )
        <div class="col-12 p-2">
            <a href="{{ route('booking.meeting-room.create') }}" class="btn btn-primary" style="float: right;">
                Add Meeting Room
            </a>
        </div>
        @endif --}}
        @foreach ($rooms as $title => $room)
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
        @endforeach
    </div>
@endsection