<?php

namespace App\Models;

use App\Traits\HasBookingLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DriverBooking extends Model
{
    use HasBookingLogs;

    protected $fillable = [
        'booking_number',
        'user_nik',
        'driver_nik',
        'status',

        'destination',

        'scheduled_pickup_at',
        'scheduled_pickup_date',
        'scheduled_pickup_time',

        'scheduled_end_at',
        'scheduled_end_date',
        'scheduled_end_time',

        'scheduled_time_slot',

        'scheduled_duration',
        'purpose_of_trip',

        'actual_pickup_at',
        'actual_end_at',
        'user_confirmed_presence_at',

        'reminder_count',
        'last_reminder_sent_at',

        'extention_requested_at',
        'extension_duration',
        'extension_request_reason',

        'extension_approved_by',
        'extension_approved_at',

        'extension_rejected_by',
        'extension_rejected_at',
        'extension_rejection_reason',

        'cancelled_by',
        'cancelled_at',
        'cancelation_reason',
    ];

    protected $casts = [
        // Scheduled pickup
        'scheduled_pickup_at' => 'datetime:Y-m-d H:i:s',
        'scheduled_pickup_date' => 'date:Y-m-d',
        'scheduled_pickup_time' => 'datetime:H:i:s',

        // Scheduled end
        'scheduled_end_at' => 'datetime:Y-m-d H:i:s',
        'scheduled_end_date' => 'date:Y-m-d',
        'scheduled_end_time' => 'datetime:H:i:s',

        // Actual times
        'actual_pickup_at' => 'datetime:Y-m-d H:i:s',
        'actual_end_at' => 'datetime:Y-m-d H:i:s',
        'user_confirmed_presence_at' => 'datetime:Y-m-d H:i:s',

        // Reminders & extensions
        'last_reminder_sent_at' => 'datetime:Y-m-d H:i:s',
        'extension_requested_at' => 'datetime:Y-m-d H:i:s',
        'extension_approved_at' => 'datetime:Y-m-d H:i:s',
        'extension_rejected_at' => 'datetime:Y-m-d H:i:s',

        // Cancellation
        'cancelled_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_nik', 'NIK');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_nik', 'NIK');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(DriverBookingFeedback::class);
    }

    public static function generateBookingNumber(): string
    {
        $date = now()->format('Ymd');

        $lastBooking = self::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $sequence = 1;

        if ($lastBooking && preg_match('/(\d+)$/', $lastBooking->booking_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('DRV-%s-%06d', $date, $sequence);
    }
}
