@component('mail::message')
# Booking Confirmation

@if($recipientRole === 'booker')
Your driver booking has been successfully created.
@else
You have been assigned a new booking.
@endif

@component('mail::table')
| Detail | Info |
|:-------|:-----|
| Booking Number | {{ $booking->booking_number }} |
| Date | {{ $booking->scheduled_pickup_date }} |
| Time | {{ $booking->scheduled_time_slot }} |
| Destination | {{ $booking->destination }} |
| Purpose | {{ $booking->purpose_of_trip }} |
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent