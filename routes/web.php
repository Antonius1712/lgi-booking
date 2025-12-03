<?php

use App\Http\Controllers\BookingDriverController;
use App\Http\Controllers\BookingMeetingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::view('/', 'index')->name('home');
    Route::prefix('booking')->as('booking.')->group(function () {
        Route::resource('/meeting-room', BookingMeetingController::class);
        Route::resource('/driver', BookingDriverController::class);
    });
});
