<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    /**
     * This model lives in the central database (medibook_central),
     * not per-tenant. Without this, Eloquent uses whichever
     * connection is currently active — which becomes "tenant"
     * once InitializeTenancyByDomain middleware has run, causing
     * "table not found" errors when queried inside a tenant request.
     */
    protected $connection = 'central';

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