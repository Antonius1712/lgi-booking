<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'id', 'meeting_room_id');
    }
}
