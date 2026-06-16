<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'amount',
        'duration_days',
        'transaction_id',
        'val_id',
        'status',
        'gateway_response',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'amount'           => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}