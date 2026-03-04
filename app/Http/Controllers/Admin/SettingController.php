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
        $extensionMaxHours = Setting::get('driver_extension_max_hours', 3);

        return view('admin.settings.index', compact('driverDays', 'meetingRoomDays', 'extensionMaxHours'));
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];

        if ($request->has('driver_booking_days_ahead')) {
            $rules['driver_booking_days_ahead'] = ['required', 'integer', 'min:1', 'max:365'];
        }

        if ($request->has('meeting_room_booking_days_ahead')) {
            $rules['meeting_room_booking_days_ahead'] = ['required', 'integer', 'min:1', 'max:365'];
        }

        if ($request->has('driver_extension_max_hours')) {
            $rules['driver_extension_max_hours'] = ['required', 'integer', 'min:1', 'max:24'];
        }

        $request->validate($rules);

        if ($request->has('driver_booking_days_ahead')) {
            Setting::set('driver_booking_days_ahead', $request->driver_booking_days_ahead);
        }

        if ($request->has('meeting_room_booking_days_ahead')) {
            Setting::set('meeting_room_booking_days_ahead', $request->meeting_room_booking_days_ahead);
        }

        if ($request->has('driver_extension_max_hours')) {
            Setting::set('driver_extension_max_hours', $request->driver_extension_max_hours);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}
