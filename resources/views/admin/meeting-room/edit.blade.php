@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.meeting-rooms.update', $meetingRoom) }}" method="post">
            @csrf
            @method('put')
            <div class="card">
                <div
                    class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                    <h5 class="card-title mb-sm-0 me-2">Setting Meeting Room</h5>
                    <div class="action-btns">
                        <button type="submit" class="btn btn-success">
                            Create
                        </button>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <div class="row">
                        <div class="col-lg-12 mx-auto">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <select name="location" id="location" class="form-control">
                                    <option value=""> -- Choose Location --</option>
                                    @foreach ($locations as $id => $location)
                                        <option {{ $meetingRoom->location_id === $id ? 'selected' : null }} value="{{ $id }}">
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location')
                                <span class="text-danger text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mt-4">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $meetingRoom->name }}">
                                @error('name')
                                <span class="text-danger text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection