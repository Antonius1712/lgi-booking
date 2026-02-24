<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class BookingLog extends Model
{
    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'action',
        'from_status',
        'to_status',
        'payload',
        'performed_by',
        'performed_by_role',
        'notes',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // NOTE: performer() is intentionally omitted.
    // User lives on a separate DB connection (LgiGlobal114).
    // Laravel cannot eager-load relationships across connections —
    // calling ->load('logs.performer') would throw or silently return null.
    // Use BookingLog::resolvePerformers($logs) instead.

    // ── Cross-connection performer resolution ─────────────────────────

    /**
     * Resolve performer names for a collection of logs in one query.
     * Safe across DB connections because it queries User directly
     * (which handles its own connection internally).
     *
     * Controller:
     *   $booking->load('logs');                          // no .performer
     *   $performers = BookingLog::resolvePerformers($booking->logs);
     *
     * Blade:
     *   {{ $performers[$log->performed_by] ?? ($log->performed_by ?? ucfirst($log->performed_by_role ?? 'system')) }}
     *
     * @return array<string, string> NIK => Name
     */
    public static function resolvePerformers(Collection $logs): array
    {
        $niks = $logs->pluck('performed_by')->filter()->unique()->values();

        if ($niks->isEmpty()) {
            return [];
        }

        return User::whereIn('NIK', $niks)
            ->get(['NIK', 'Name'])
            ->pluck('Name', 'NIK')
            ->toArray();
    }

    // ── Static log writer ─────────────────────────────────────────────

    public static function record(
        Model $booking,
        string $action,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        array $payload = [],
        ?string $notes = null,
        ?string $role = null,
    ): self {
        return static::create([
            'loggable_type' => get_class($booking),
            'loggable_id' => $booking->getKey(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'payload' => $payload ?: null,
            'performed_by' => auth()->user()?->NIK,
            'performed_by_role' => $role ?? (auth()->user() ? 'admin' : 'system'),
            'notes' => $notes,
        ]);
    }

    // ── Display helpers ───────────────────────────────────────────────

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'created' => 'Booking Created',
            'confirmed' => 'Departure Confirmed',
            'cancelled' => 'Booking Cancelled',
            'auto_cancelled' => 'Auto-Cancelled',
            'driver_changed' => 'Driver Changed',
            'extended' => 'Duration Extended',
            'rescheduled' => 'Rescheduled',
            'reminder_sent' => 'Reminder Sent',
            'room_changed' => 'Room Changed',
            'time_changed' => 'Time Slot Changed',
            'guests_updated' => 'Guest Emails Updated',
            'completed' => 'Marked Completed',
            'status_changed' => 'Status Updated',
            default => ucfirst(str_replace('_', ' ', $action)),
        };
    }

    public static function actionIcon(string $action): string
    {
        return match ($action) {
            'created' => 'bx-plus-circle',
            'confirmed' => 'bx-check-circle',
            'cancelled', 'auto_cancelled' => 'bx-x-circle',
            'driver_changed' => 'bx-transfer',
            'extended' => 'bx-time-five',
            'rescheduled' => 'bx-calendar',
            'reminder_sent' => 'bx-bell',
            'room_changed' => 'bx-door-open',
            'time_changed' => 'bx-timer',
            'guests_updated' => 'bx-envelope',
            'completed' => 'bx-flag',
            default => 'bx-edit',
        };
    }

    public static function actionColor(string $action): string
    {
        return match ($action) {
            'created' => '#7367f0',
            'confirmed' => '#28c76f',
            'cancelled', 'auto_cancelled' => '#ea5455',
            'driver_changed', 'room_changed',
            'time_changed' => '#00cfe8',
            'extended', 'rescheduled', 'reminder_sent' => '#ff9f43',
            'guests_updated' => '#7367f0',
            'completed' => '#28c76f',
            default => '#82868b',
        };
    }
}
