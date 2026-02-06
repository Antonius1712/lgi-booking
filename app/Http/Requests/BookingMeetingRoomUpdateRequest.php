<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingMeetingRoomUpdateRequest extends FormRequest
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
            'room' => ['required'],
            'year' => ['required'],
            'month' => ['required'],
            'day' => ['required'],
            'stime' => ['required'],
            'etime' => ['required'],
            'description' => ['required'],
            'usage_type' => ['required'],
        ];
    }
}
