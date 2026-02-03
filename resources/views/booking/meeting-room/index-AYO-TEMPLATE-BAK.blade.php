@extends('layouts.app')
@section('content')
<div class="row" x-data="pickTime">
    @livewire('booking-in-table-view')
</div>
@endsection
@section('script')
<script src="{{ asset('alpine/index.js') }}"></script>
@endsection