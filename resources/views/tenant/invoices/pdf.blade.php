<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            padding: 40px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        .clinic-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .clinic-domain {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-number {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            font-family: monospace;
        }
        .invoice-date {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .badge {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid    { background: #dcfce7; color: #15803d; }
        .badge-draft   { background: #fef9c3; color: #a16207; }
        .badge-sent    { background: #dbeafe; color: #1d4ed8; }

        /* ── Bill To / Doctor ── */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .party-block { width: 48%; }
        .party-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 4px;
            letter-spacing: 0.05em;
        }
        .party-name {
            font-weight: bold;
            font-size: 14px;
            color: #111827;
        }
        .party-sub {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Appointment Info ── */
        .appt-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
        }
        .appt-grid {
            display: flex;
            gap: 32px;
        }
        .appt-item-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
        }
        .appt-item-value {
            font-size: 13px;
            font-weight: 600;
            margin-top: 2px;
        }

        /* ── Line Items ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        thead tr {
            border-bottom: 1px solid #e5e7eb;
        }
        thead th {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            padding: 8px 0;
            text-align: left;
            letter-spacing: 0.05em;
        }
        thead th.right { text-align: right; }
        tbody td {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }
        tbody td.right { text-align: right; }
        tbody td.muted { color: #6b7280; }
        tfoot td {
            padding: 10px 0;
            font-weight: bold;
            font-size: 15px;
        }
        tfoot td.right { text-align: right; }

        /* ── Payment Info ── */
        .paid-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            color: #15803d;
            margin-bottom: 16px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
        }
    </style>
</head>
<body>

    
    
   
    {{-- Header --}}
@php $cs = \App\Models\ClinicSetting::first(); @endphp
<div class="header">
    <div style="display:flex;align-items:center;gap:14px;">
        @if($cs?->logo)
        <img src="{{ storage_path('app/public/' . $cs->logo) }}"
             style="max-height:50px;max-width:120px;object-fit:contain;" />
        @endif
        <div>
            <div class="clinic-name">{{ $cs?->clinic_name ?? tenant('clinic_name') }}</div>
            @if($cs?->tagline)
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">{{ $cs->tagline }}</div>
            @endif
            @if($cs?->phone || $cs?->email)
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                {{ $cs?->phone }} {{ $cs?->phone && $cs?->email ? '•' : '' }} {{ $cs?->email }}
            </div>
            @endif
            @if($cs?->address)
            <div style="font-size:11px;color:#6b7280;margin-top:1px;">{{ $cs->address }}</div>
            @endif
        </div>
    </div>

    {{-- Bill To / Doctor --}}
    <div class="parties">
        <div class="party-block">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $invoice->patient->name }}</div>
            <div class="party-sub">{{ $invoice->patient->email }}</div>
        </div>
        <div class="party-block">
            <div class="party-label">Doctor</div>
            <div class="party-name">{{ $invoice->doctor->name }}</div>
            <div class="party-sub">{{ $invoice->doctor->specialization ?? '' }}</div>
        </div>
    </div>

    {{-- Appointment Info --}}
    <div class="appt-box">
        <div class="appt-grid">
            <div>
                <div class="appt-item-label">Date</div>
                <div class="appt-item-value">{{ $invoice->appointment->appointment_date->format('d M Y') }}</div>
            </div>
            <div>
                <div class="appt-item-label">Time</div>
                <div class="appt-item-value">{{ \Carbon\Carbon::parse($invoice->appointment->appointment_time)->format('h:i A') }}</div>
            </div>
            <div>
                <div class="appt-item-label">Visit Type</div>
                <div class="appt-item-value">{{ ucfirst(str_replace('_', ' ', $invoice->appointment->visit_type)) }}</div>
            </div>
            <div>
                <div class="appt-item-label">Payment Method</div>
                <div class="appt-item-value">{{ ucfirst($invoice->appointment->payment_method ?? 'N/A') }}</div>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consultation Fee
                    <span class="muted" style="font-size:11px;">
                        ({{ ucfirst(str_replace('_', ' ', $invoice->appointment->visit_type)) }})
                    </span>
                </td>
                <td class="right">৳{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if ($invoice->tax > 0)
            <tr>
                <td class="muted">Tax</td>
                <td class="right">৳{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            @if ($invoice->discount > 0)
            <tr>
                <td class="muted">Discount</td>
                <td class="right" style="color:#dc2626;">- ৳{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="right" style="color:#1e3a8a;">৳{{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Payment Info --}}
    @if ($invoice->isPaid())
    <div class="paid-box">
        ✓ Paid on {{ $invoice->paid_at->format('d M Y, h:i A') }}
        via {{ ucfirst($invoice->appointment->payment_method) }}
    </div>
    @endif

    {{-- Notes --}}
    @if ($invoice->notes)
    <p style="font-size:12px; color:#6b7280; margin-bottom:16px;">
        <strong style="color:#374151;">Notes:</strong> {{ $invoice->notes }}
    </p>
    @endif

   <div class="footer">
    @if($cs?->invoice_footer_note)
        {{ $cs->invoice_footer_note }} &bull;
    @else
        Thank you for choosing {{ $cs?->clinic_name ?? tenant('clinic_name') }} &bull;
    @endif
    Generated on {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>