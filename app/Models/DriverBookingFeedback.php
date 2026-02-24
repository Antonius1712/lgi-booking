<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverBookingFeedback extends Model
{
    protected $fillable = [
        'driver_booking_id',
        'user_nik',
        'rating',
        'feedback_tag_ids',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'feedback_tag_ids' => 'array',
            'rating' => 'integer',
        ];
    }

    public function driverBooking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DriverBooking::class);
    }
}
