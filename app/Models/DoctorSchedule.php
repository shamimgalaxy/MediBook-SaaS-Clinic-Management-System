<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'day_of_week' => 'integer',
    ];

    const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }

    public function getTimeRangeAttribute(): string
    {
        return date('h:i A', strtotime($this->start_time))
            . ' – '
            . date('h:i A', strtotime($this->end_time));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}