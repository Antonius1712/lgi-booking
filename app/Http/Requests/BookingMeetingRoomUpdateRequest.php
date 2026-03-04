<?php

namespace App\Http\Requests;

use App\Models\MeetingRoomBooking;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BookingMeetingRoomUpdateRequest extends FormRequest
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
            'e_stime' => ['required'],
            'e_etime' => ['required'],
            'e_description' => ['required'],
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

            $stime = Carbon::parse($this->input('e_stime'))->format('H:i:s');
            $etime = Carbon::parse($this->input('e_etime'))->format('H:i:s');

            /** @var MeetingRoomBooking $currentBooking */
            $currentBooking = $this->route('meetingRoomBooking');

            $hasOverlap = MeetingRoomBooking::query()
                ->where('nik', auth()->user()->NIK)
                ->where('booking_date', $date)
                ->where('id', '!=', $currentBooking->id)
                ->whereNotIn('status', ['cancelled', 'Cancelled'])
                ->where('start_time', '<', $etime)
                ->where('end_time', '>', $stime)
                ->exists();

            if ($hasOverlap) {
                $validator->errors()->add(
                    'e_stime',
                    'Anda sudah memiliki pemesanan ruang meeting pada rentang waktu yang sama.'
                );
            }
        });
    }
}
