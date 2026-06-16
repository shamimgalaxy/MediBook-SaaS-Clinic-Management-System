<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'experience_years',
        'consultation_fee',
        'phone',
        'bio',
        'avatar',
        'is_active',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? 'Unknown';
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        return strtoupper(
            collect($words)->take(2)->map(fn($w) => $w[0] ?? '')->join('')
        );
    }

    public function activeSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}