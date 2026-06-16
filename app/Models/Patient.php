<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'gender',
        'blood_group',
        'address',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active'     => 'boolean',
    ];

    const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

    const GENDERS = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'];

    // ─── Relationships ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth
            ? $this->date_of_birth->age
            : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}