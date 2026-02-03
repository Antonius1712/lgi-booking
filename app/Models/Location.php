<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'slug',
        'name',
    ];

    public function meetingRooms(): HasMany
    {
        return $this->hasMany(MeetingRoom::class);
    }
}
