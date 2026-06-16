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
            padding-bottom: 16px;
            border-bottom: 2px solid #185FA5;
            margin-bottom: 24px;
        }
        .clinic-name {
            font-size: 20px;
            font-weight: bold;
            color: #185FA5;
        }
        .clinic-domain {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }
        .rx-badge {
            font-size: 36px;
            font-weight: bold;
            color: #185FA5;
            line-height: 1;
        }
        .rx-label {
            font-size: 10px;
            color: #6b7280;
            text-align: right;
            margin-top: 2px;
        }

        /* ── Doctor & Patient row ── */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        .party-block {
            width: 48%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .party-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.05em;
            margin-bottom: 5px;
        }
        .party-name {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }
        .party-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Appointment info ── */
        .appt-row {
            display: flex;
            gap: 32px;
            background: #eff6ff;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .appt-item-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
        }
        .appt-item-val {
            font-weight: 600;
            margin-top: 2px;
        }

        /* ── Clinical details ── */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        .clinical-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .clinical-item {
            flex: 1;
        }
        .clinical-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 3px;
        }
        .clinical-val {
            font-size: 13px;
            font-weight: 500;
        }

        /* ── Medicines table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead tr {
            background: #185FA5;
            color: #fff;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        tbody td {
            padding: 8px 10px;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .med-name {
            font-weight: 600;
            color: #111827;
        }

        /* ── Notes ── */
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .notes-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        /* ── Follow-up ── */
        .followup-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            color: #15803d;
            margin-bottom: 20px;
        }

        /* ── Signature ── */
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #374151;
            width: 180px;
            margin-left: auto;
            margin-bottom: 6px;
        }
        .signature-name {
            font-size: 13px;
            font-weight: bold;
        }
        .signature-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 32px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
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
             style="max-height:50px;max-width:100px;object-fit:contain;" />
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

    {{-- Doctor & Patient --}}
    <div class="parties">
        <div class="party-block">
            <div class="party-label">Prescribing Doctor</div>
            <div class="party-name">Dr. {{ $prescription->doctor->name }}</div>
            @if($prescription->doctor->specialization)
            <div class="party-sub">{{ $prescription->doctor->specialization }}</div>
            @endif
            @if($prescription->doctor->registration_number)
            <div class="party-sub">Reg. No: {{ $prescription->doctor->registration_number }}</div>
            @endif
        </div>
        <div class="party-block">
            <div class="party-label">Patient</div>
            <div class="party-name">{{ $prescription->patient->name }}</div>
            <div class="party-sub">{{ $prescription->patient->email }}</div>
        </div>
    </div>

    {{-- Appointment info --}}
    <div class="appt-row">
        <div>
            <div class="appt-item-label">Date</div>
            <div class="appt-item-val">{{ $prescription->appointment->appointment_date->format('d M Y') }}</div>
        </div>
        <div>
            <div class="appt-item-label">Time</div>
            <div class="appt-item-val">{{ \Carbon\Carbon::parse($prescription->appointment->appointment_time)->format('h:i A') }}</div>
        </div>
        <div>
            <div class="appt-item-label">Visit Type</div>
            <div class="appt-item-val">{{ ucfirst(str_replace('_', ' ', $prescription->appointment->visit_type)) }}</div>
        </div>
        <div>
            <div class="appt-item-label">Issued On</div>
            <div class="appt-item-val">{{ $prescription->created_at->format('d M Y') }}</div>
        </div>
    </div>

    {{-- Clinical details --}}
    @if($prescription->chief_complaint || $prescription->diagnosis)
    <div class="section-title">Clinical Details</div>
    <div class="clinical-grid">
        @if($prescription->chief_complaint)
        <div class="clinical-item">
            <div class="clinical-label">Chief Complaint</div>
            <div class="clinical-val">{{ $prescription->chief_complaint }}</div>
        </div>
        @endif
        @if($prescription->diagnosis)
        <div class="clinical-item">
            <div class="clinical-label">Diagnosis</div>
            <div class="clinical-val">{{ $prescription->diagnosis }}</div>
        </div>
        @endif
    </div>
    @endif

    {{-- Medicines --}}
    <div class="section-title">Prescribed Medicines</div>
    @if($prescription->items->count())
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Instructions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prescription->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="med-name">{{ $item->medicine_name }}</td>
                <td>{{ $item->dosage ?? '—' }}</td>
                <td>{{ $item->frequency ?? '—' }}</td>
                <td>{{ $item->duration ?? '—' }}</td>
                <td>{{ $item->instructions ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="font-size:12px;color:#6b7280;margin-bottom:20px;">No medicines prescribed.</p>
    @endif

    {{-- Notes --}}
    @if($prescription->notes)
    <div class="notes-box">
        <div class="notes-label">Doctor's Notes / Advice</div>
        <p>{{ $prescription->notes }}</p>
    </div>
    @endif

    {{-- Follow-up --}}
    @if($prescription->follow_up_date)
    <div class="followup-box">
        &#10003; Follow-up appointment recommended on
        <strong>{{ $prescription->follow_up_date->format('d M Y') }}</strong>
    </div>
    @endif

    {{-- Signature --}}
    <div class="signature">
        <div class="signature-line"></div>
        <div class="signature-name">Dr. {{ $prescription->doctor->name }}</div>
        @if($prescription->doctor->specialization)
        <div class="signature-sub">{{ $prescription->doctor->specialization }}</div>
        @endif
        @if($prescription->doctor->registration_number)
        <div class="signature-sub">Reg. No: {{ $prescription->doctor->registration_number }}</div>
        @endif
        <div class="signature-sub">{{ $prescription->created_at->format('d M Y') }}</div>
    </div>

   <div class="footer">
    {{ $cs?->clinic_name ?? tenant('clinic_name') }}
    @if($cs?->phone) &bull; {{ $cs->phone }} @endif
    @if($cs?->address) &bull; {{ $cs->address }} @endif
    &bull; Generated on {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>