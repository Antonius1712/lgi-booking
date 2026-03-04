<?php

namespace App\Http\Controllers;

use App\Models\DriverBooking;
use App\Models\DriverBookingFeedback;
use App\Models\FeedbackTag;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverBookingFeedbackController extends Controller
{
    public function create(DriverBooking $driverBooking, #[CurrentUser] User $user): View
    {
        abort_if($driverBooking->user_nik !== $user->NIK, 403);
        abort_if($driverBooking->status !== 'completed', 403);
        abort_if($driverBooking->feedback !== null, 403);

        $tags = FeedbackTag::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('feedback.driver', compact('driverBooking', 'tags'));
    }

    public function store(Request $request, DriverBooking $driverBooking, #[CurrentUser] User $user): RedirectResponse
    {
        abort_if($driverBooking->user_nik !== $user->NIK, 403);
        abort_if($driverBooking->status !== 'completed', 403);
        abort_if($driverBooking->feedback !== null, 403);

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback_tag_ids' => ['nullable', 'array'],
            'feedback_tag_ids.*' => ['integer', 'exists:feedback_tags,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DriverBookingFeedback::create([
            'driver_booking_id' => $driverBooking->id,
            'user_nik' => $user->NIK,
            'rating' => $request->rating,
            'feedback_tag_ids' => $request->feedback_tag_ids ?? [],
            'notes' => $request->notes,
        ]);

        return redirect()->route('my-booking.driver')
            ->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
