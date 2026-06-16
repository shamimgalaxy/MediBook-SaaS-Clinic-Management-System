<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'max_doctors',
        'max_appointments',
        'sms_notifications',
        'custom_domain',
        'excel_reports',
        'is_active',
    ];

    protected $casts = [
        'sms_notifications' => 'boolean',
        'custom_domain'     => 'boolean',
        'excel_reports'     => 'boolean',
        'is_active'         => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'plan_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isUnlimited(string $feature): bool
    {
        return $this->{$feature} === -1;
    }
}