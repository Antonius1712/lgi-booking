@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header bg-secondary">
                    My Booking
                </div>
                <div class="card-body mt-4">
                    <div class="pull-left">
                        Meeting Room
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <a href="{{ route('my-booking.driver', ['filter' => 'active']) }}">
                <div class="card">
                    <div class="card-header bg-secondary">
                        My Booking
                    </div>
                    <div class="card-body mt-4">
                        Driver
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection