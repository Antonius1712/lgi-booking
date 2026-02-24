<?php

namespace App\Traits;

use App\Models\BookingLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBookingLogs
{
    public function logs(): MorphMany
    {
        return $this->morphMany(BookingLog::class, 'loggable')->orderByDesc('created_at');
    }

    public function log(
        string $action,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        array $payload = [],
        ?string $notes = null,
        ?string $role = null,
    ): BookingLog {
        return BookingLog::record($this, $action, $fromStatus, $toStatus, $payload, $notes, $role);
    }
}
