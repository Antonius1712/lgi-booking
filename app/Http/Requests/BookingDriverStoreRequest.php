<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingDriverStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'driver_name' => ['required'],
            'year' => ['required'],
            'month' => ['required'],
            'day' => ['required'],
            'time' => ['required'],
            'stime' => ['required', function ($attribute, $value, $fail) {
                $bookingDateTime = \Carbon\Carbon::parse(
                    $this->input('year') . '-' . $this->input('month') . '-' . $this->input('day') . ' ' . $value
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
}
