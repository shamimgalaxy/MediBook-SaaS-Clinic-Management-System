<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = [
        'plan_expires_at' => 'datetime',
        'trial_ends_at'   => 'datetime',
        'on_trial'        => 'boolean',
        'is_active'       => 'boolean',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'clinic_name',
            'is_active',
            'plan_id',
            'plan_expires_at',
            'on_trial',
            'trial_ends_at',
        ];
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isSubscriptionActive(): bool
    {
        if ($this->on_trial && $this->trial_ends_at && now()->lt($this->trial_ends_at)) {
            return true;
        }

        return $this->plan_id &&
               $this->plan_expires_at &&
               now()->lt($this->plan_expires_at);
    }

    public function daysUntilExpiry(): int
    {
        $expiry = $this->on_trial ? $this->trial_ends_at : $this->plan_expires_at;
        return $expiry ? (int) now()->diffInDays($expiry, false) : 0;
    }

    public function isOnTrial(): bool
    {
        return (bool) $this->on_trial && 
               $this->trial_ends_at && 
               now()->lt($this->trial_ends_at);
    }

    public function settings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\ClinicSetting::class);
    }
}