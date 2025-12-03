@extends('layouts.auth')
@section('title')
    | Login
@endsection
@section('content')
<div class="card-body">

    <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-6 form-control-validation">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" placeholder="Enter your nik" autofocus
                required />
            @error('nik')
            <span class="text-danger text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-6 form-password-toggle form-control-validation">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" required />
                <span class="input-group-text cursor-pointer">
                    <i class="icon-base bx bx-hide"></i>
                </span>
            </div>
            @error('password')
            <span class="text-danger text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-6">
            <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
        </div>
    </form>

</div>
@endsection