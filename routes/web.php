<?php

use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MeetingRoomController;
use App\Http\Controllers\BookingDriverController;
use App\Http\Controllers\BookingMeetingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::view('/', 'index')->name('home');

    Route::prefix('admin')->as('admin.')->group(function () {
        Route::resource('/locations', LocationController::class);
        Route::resource('/meeting-rooms', MeetingRoomController::class);
        Route::resource('/drivers', DriverController::class);
    });

    Route::prefix('booking')->as('booking.')->group(function () {
        Route::get('/fetch-data', [BookingMeetingController::class, 'fetchData'])->name('fetchData');
        Route::resource('/meeting-room', BookingMeetingController::class);
        Route::resource('/driver', BookingDriverController::class);
    });
});

// require __DIR__.'/settings.php';
