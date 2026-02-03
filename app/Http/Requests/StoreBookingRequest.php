<?php

namespace App\Http\Requests;

use App\CalendarType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBookingRequest extends FormRequest
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
            'meeting_room_id' => ['required', 'exists:meeting_rooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'all_day' => ['boolean'],
            'participant' => ['string'],

            // Will replace this with enum ↓
            'calendar_type' => ['required', new Enum(CalendarType::class)],

            'event_url' => ['nullable', 'url'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'guest_emails' => ['nullable', 'array'],
            'guest_emails.*' => ['email'],
        ];
    }

    public function messages(): array
    {
        return [
            'guest_emails.*.email' => 'Each guest must have a valid email address.',
        ];
    }
}
