@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('booking.meeting-room.store') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="location">Meeting Room Location</label>
                            <select name="location" id="location" class="form-control">
                                <option value="">-- Choose Location --</option>
                                @foreach ($locations as $id => $location)
                                    <option value="{{ $id }}">
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-2">
                            <label for="name">Meeting Room Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection