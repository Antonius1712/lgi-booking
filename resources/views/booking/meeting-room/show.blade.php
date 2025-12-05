@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-12 px-0 px-md-3">
        <h1 class="venue-name text-left venue-name-other mb-3">
            {{ $meetingRoom->name }}
        </h1>

        @livewire('time-slots', ['meetingRoom' => $meetingRoom])
    </div>
</div>
@endsection