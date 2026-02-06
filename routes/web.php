<?php

use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MeetingRoomController;
use App\Http\Controllers\BookingDriverController;
use App\Http\Controllers\BookingMeetingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyBookingDriverController;
use App\Http\Controllers\MyBookingMeetingRoomController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

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

    Route::prefix('my-booking')->as('my-booking.')->group(function () {
        Route::get('/driver', [MyBookingDriverController::class, 'index'])->name('driver');
        Route::get('/meeting-room', [MyBookingMeetingRoomController::class, 'index'])->name('meeting-room');
    });

    Route::prefix('services')->as('services.')->group(function () {
        Route::post('/search-user-by-nik', [ServiceController::class, 'searchUserByNik'])->name('search-user-by-nik');
    });
});

// require __DIR__.'/settings.php';
