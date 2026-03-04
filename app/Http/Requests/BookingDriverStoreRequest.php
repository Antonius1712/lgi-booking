<?php

namespace App\Http\Requests;

use App\Models\DriverBooking;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BookingDriverStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driver_name' => ['required'],
            'year' => ['required'],
            'month' => ['required'],
            'day' => ['required'],
            'time' => ['required'],
            'stime' => ['required', function ($attribute, $value, $fail) {
                $bookingDateTime = Carbon::parse(
                    $this->input('year').'-'.$this->input('month').'-'.$this->input('day').' '.$value
                );
                if ($bookingDateTime->isPast()) {
                    $fail('The scheduled pickup time cannot be in the past.');
                }
            }],
            'etime' => ['required'],
            'destination' => ['required'],
            'purpose_of_trip' => ['required'],
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

            $hasOverlap = DriverBooking::query()
                ->where('user_nik', auth()->user()->NIK)
                ->where('scheduled_pickup_date', $date)
                ->whereNotIn('status', ['cancelled', 'auto_cancelled', 'completed'])
                ->where('scheduled_pickup_time', '<', $etime)
                ->where('scheduled_end_time', '>', $stime)
                ->exists();

            if ($hasOverlap) {
                $validator->errors()->add(
                    'stime',
                    'Anda sudah memiliki pemesanan driver pada rentang waktu yang sama.'
                );
            }
        });
    }
}
