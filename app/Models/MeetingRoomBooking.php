<?php

namespace App\Models;

use App\Traits\HasBookingLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingRoomBooking extends Model
{
    use HasBookingLogs;

    protected $fillable = [
        'meeting_room_id',
        'nik',
        'booking_date',
        'time_slot',
        'start_time',
        'end_time',
        'status',
        'location',
        'description',
        'usage_type',
        'guest_emails',
        'cancelled_by',
        'cancelled_at',
        'cancelation_reason',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'all_day' => 'boolean',
        'guest_emails' => 'array',
        'cancelled_at' => 'datetime',
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
