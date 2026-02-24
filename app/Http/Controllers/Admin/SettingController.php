<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $driverDays = Setting::get('driver_booking_days_ahead', 14);
        $meetingRoomDays = Setting::get('meeting_room_booking_days_ahead', 14);

        return view('admin.settings.index', compact('driverDays', 'meetingRoomDays'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'driver_booking_days_ahead' => ['required', 'integer', 'min:1', 'max:365'],
            'meeting_room_booking_days_ahead' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        Setting::set('driver_booking_days_ahead', $request->driver_booking_days_ahead);
        Setting::set('meeting_room_booking_days_ahead', $request->meeting_room_booking_days_ahead);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}
