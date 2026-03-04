<?php

namespace App\Http\Requests;

use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BookingMeetingRoomStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room' => ['required'],
            'year' => ['required'],
            'month' => ['required'],
            'day' => ['required'],
            'stime' => ['required', function ($attribute, $value, $fail) {
                $bookingDateTime = Carbon::parse(
                    $this->input('year').'-'.$this->input('month').'-'.$this->input('day').' '.$value
                );
                if ($bookingDateTime->isPast()) {
                    $fail('The start time cannot be in the past.');
                }
            }],
            'etime' => ['required'],
            'description' => ['required'],
            'usage_type' => ['required'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $date = Carbon::parse(
                $this->input('year').'-'.$this->input('month').'-'.$this->input('day')
            )->format('Y-m-d');

            $stime = Carbon::parse($this->input('stime'))->format('H:i:s');
            $etime = Carbon::parse($this->input('etime'))->format('H:i:s');

            $hasOverlap = MeetingRoomBooking::query()
                ->where('nik', auth()->user()->NIK)
                ->where('booking_date', $date)
                ->whereNotIn('status', ['cancelled', 'Cancelled'])
                ->where('start_time', '<', $etime)
                ->where('end_time', '>', $stime)
                ->exists();

            if ($hasOverlap) {
                $validator->errors()->add(
                    'stime',
                    'Anda sudah memiliki pemesanan ruang meeting pada rentang waktu yang sama.'
                );
            }
        });
    }
}
