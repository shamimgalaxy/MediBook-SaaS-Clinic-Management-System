<?php

// app/Services/InvoiceService.php

namespace App\Services;

use App\Models\Invoice;

class InvoiceService
{
  public function generateInvoiceNumber(): string
{
    $settings = \App\Models\ClinicSetting::first();
    $prefix   = $settings?->invoice_prefix ?? 'INV';
    $year     = now()->year;
    $full     = "{$prefix}-{$year}-";

    $last = \App\Models\Invoice::where('invoice_number', 'like', "{$full}%")
        ->orderByDesc('id')
        ->first();

    $next = $last
        ? (int) substr($last->invoice_number, strlen($full)) + 1
        : 1;

    return $full . str_pad($next, 5, '0', STR_PAD_LEFT);
}

    public function createFromAppointment(\App\Models\Appointment $appointment): Invoice
    {
        $subtotal = $appointment->fee ?? 0;

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'doctor_id'      => $appointment->doctor_id,
            'subtotal'       => $subtotal,
            'tax'            => 0,
            'discount'       => 0,
            'total'          => $subtotal,
            'status'         => 'paid',
            'due_date'       => now()->toDateString(),
            'paid_at'        => now(),
        ]);
    }
}