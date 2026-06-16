<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'clinic_name',
        'tagline',
        'phone',
        'email',
        'website',
        'address',
        'logo',
        'working_hours',
        'notify_appointment_booked',
        'notify_appointment_status',
        'notify_payment_received',
        'notify_sms_enabled',
        'invoice_prefix',
        'default_tax',
        'invoice_footer_note',
    ];

    protected $casts = [
        'working_hours'               => 'array',
        'notify_appointment_booked'   => 'boolean',
        'notify_appointment_status'   => 'boolean',
        'notify_payment_received'     => 'boolean',
        'notify_sms_enabled'          => 'boolean',
        'default_tax'                 => 'decimal:2',
    ];

    // ── Default working hours ──────────────────────────────────

    public static function defaultWorkingHours(): array
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $defaults = [];

        foreach ($days as $day) {
            $defaults[$day] = [
                'open'  => in_array($day, ['saturday', 'sunday']) ? false : true,
                'start' => '09:00',
                'end'   => '17:00',
            ];
        }

        return $defaults;
    }

    // ── Helpers ────────────────────────────────────────────────

    public function logoUrl(): ?string
    {
        return $this->logo
            ? \Illuminate\Support\Facades\Storage::url($this->logo)
            : null;
    }

    public function workingHoursForDay(string $day): array
    {
        $hours = $this->working_hours ?? self::defaultWorkingHours();
        return $hours[strtolower($day)] ?? ['open' => false, 'start' => '09:00', 'end' => '17:00'];
    }

    public function isOpenOn(string $day): bool
    {
        return $this->workingHoursForDay($day)['open'] ?? false;
    }
}