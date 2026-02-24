<?php

use App\Http\Controllers\Admin\AdminDriverBookingController;
use App\Http\Controllers\Admin\AdminMeetingRoomBookingController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FeedbackTagController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MeetingRoomController;
use App\Http\Controllers\Admin\SettingController;
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

        // ── Booking configuration ──────────────────────────────────────────────
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // ── Email templates ────────────────────────────────────────────────────
        Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('/email-templates/{trigger}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('/email-templates/{trigger}', [EmailTemplateController::class, 'update'])->name('email-templates.update');

        // ── Feedback tags ──────────────────────────────────────────────────────
        Route::get('/feedback-tags', [FeedbackTagController::class, 'index'])->name('feedback-tags.index');
        Route::post('/feedback-tags', [FeedbackTagController::class, 'store'])->name('feedback-tags.store');
        Route::patch('/feedback-tags/{feedbackTag}', [FeedbackTagController::class, 'update'])->name('feedback-tags.update');
        Route::delete('/feedback-tags/{feedbackTag}', [FeedbackTagController::class, 'destroy'])->name('feedback-tags.destroy');
        Route::post('/feedback-tags/reorder', [FeedbackTagController::class, 'reorder'])->name('feedback-tags.reorder');

        // ── Export ─────────────────────────────────────────────────────────────
        Route::get('/export', [ExportController::class, 'index'])->name('export.index');
        Route::post('/export/download', [ExportController::class, 'export'])->name('export.download');

        // ── Driver Booking Management (admin controls all actions) ─────────────
        Route::prefix('driver-bookings')->name('driver-bookings.')->group(function () {
            Route::get('/', [AdminDriverBookingController::class, 'index'])->name('index');
            Route::get('/{driverBooking}', [AdminDriverBookingController::class, 'show'])->name('show');
            Route::patch('/{driverBooking}/confirm', [AdminDriverBookingController::class, 'confirm'])->name('confirm');
            Route::patch('/{driverBooking}/cancel', [AdminDriverBookingController::class, 'cancel'])->name('cancel');
            Route::patch('/{driverBooking}/change-driver', [AdminDriverBookingController::class, 'changeDriver'])->name('change-driver');
            Route::patch('/{driverBooking}/extend', [AdminDriverBookingController::class, 'extend'])->name('extend');
            Route::patch('/{driverBooking}/reschedule', [AdminDriverBookingController::class, 'reschedule'])->name('reschedule');
            Route::get('/available-drivers', [AdminDriverBookingController::class, 'availableDrivers'])->name('available-drivers');
        });

        // ── Meeting Room Bookings ─────────────────────────────────────────────
        Route::prefix('meeting-room-bookings')->name('meeting-room-bookings.')->group(function () {
            Route::get('/', [AdminMeetingRoomBookingController::class, 'index'])->name('index');
            Route::get('/{meetingRoomBooking}', [AdminMeetingRoomBookingController::class, 'show'])->name('show');
            Route::patch('/{meetingRoomBooking}/cancel', [AdminMeetingRoomBookingController::class, 'cancel'])->name('cancel');
            Route::patch('/{meetingRoomBooking}/extend', [AdminMeetingRoomBookingController::class, 'extend'])->name('extend');
            Route::patch('/{meetingRoomBooking}/reschedule', [AdminMeetingRoomBookingController::class, 'reschedule'])->name('reschedule');
            Route::patch('/{meetingRoomBooking}/change-room', [AdminMeetingRoomBookingController::class, 'changeRoom'])->name('change-room');
            Route::patch('/{meetingRoomBooking}/change-time', [AdminMeetingRoomBookingController::class, 'changeTime'])->name('change-time');
            Route::patch('/{meetingRoomBooking}/change-room-and-time', [AdminMeetingRoomBookingController::class, 'changeRoomAndTime'])->name('change-room-and-time');
            Route::patch('/{meetingRoomBooking}/update-guests', [AdminMeetingRoomBookingController::class, 'updateGuests'])->name('update-guests');
            Route::get('/available-rooms', [AdminMeetingRoomBookingController::class, 'availableRooms'])->name('available-rooms');
        });
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
