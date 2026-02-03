@extends('layouts.auth')

@section('title', '| Two Factor Authentication')

@section('content')
<div class="card-body">

    <h5 class="mb-4">Two Factor Authentication</h5>

    <form method="POST" action="{{ url('/two-factor-challenge') }}">
        @csrf

        <div class="mb-4">
            <label class="form-label">Authentication Code</label>
            <input type="text" name="code" class="form-control" autofocus>
            @error('code')
                <div class="text-danger text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-center mb-3">or</div>

        <div class="mb-4">
            <label class="form-label">Recovery Code</label>
            <input type="text" name="recovery_code" class="form-control">
        </div>

        <button class="btn btn-primary w-100">Verify</button>
    </form>

</div>
@endsection