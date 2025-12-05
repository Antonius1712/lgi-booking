<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MeetingRoom extends Model
{
    protected $fillable = [
        'location_id',
        'slug',
        'name',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function location(): HasOne
    {
        return $this->HasOne(Location::class, 'id', 'location_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'id', 'meeting_room_id');
    }
}
