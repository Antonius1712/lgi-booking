<?php

namespace App\Http\Controllers;

class MyBookingDriverController extends Controller
{
    public function index()
    {
        $bookings = [];

        return view('my-booking.driver', compact('bookings'));
    }
}
