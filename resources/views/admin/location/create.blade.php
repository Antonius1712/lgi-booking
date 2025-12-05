@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.locations.store') }}" method="post">
            @csrf
            <div class="card">
                <div
                    class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                    <h5 class="card-title mb-sm-0 me-2">Add Location</h5>
                    <div class="action-btns">
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-success">
                            <span class="align-middle"> Back</span>
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <div class="row">
                        <div class="col-lg-12 mx-auto">
                            <div class="form-group">
                                <label for="name">Location</label>
                                <input type="text" name="name" id="name" class="form-control">
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