<?php

// app/Models/Appointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_date',
        'appointment_time',
        'visit_type',
        'reason',
        'notes',
        'fee',
        'status',
        'payment_status',
        'payment_method',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'fee'              => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    // app/Models/Appointment.php — add inside the class

public function prescription(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(Prescription::class);
}
}