<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'meeting_room_id',
        'nik',
        'booking_date',
        'time_slot',
        'start_time',
        'end_time',
        'status',
        'title',
        'event_url',
        'location',
        'description',
        'calendar_type',
        'guest_emails',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'all_day' => 'boolean',
        'guest_emails' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nik', 'NIK');
    }

    public function meetingRoom(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class);
    }
}
