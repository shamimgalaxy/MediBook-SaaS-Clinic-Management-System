@extends('tenant.layouts.app')

@section('title', 'Prescription')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Prescription</h1>
        <p class="page-sub">
            {{ $prescription->patient->name }} &mdash;
            {{ $prescription->created_at->format('d M Y') }}
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        @role('doctor')
        @if(auth()->user()->id === $prescription->doctor->user_id)
        <a href="{{ route('prescriptions.edit', $prescription) }}" class="tb-btn">
            <i class="ti ti-edit"></i> Edit
        </a>
        @endif
        @endrole
        <a href="{{ route('prescriptions.pdf', $prescription) }}" class="tb-btn primary">
            <i class="ti ti-download"></i> Download PDF
        </a>
        <a href="{{ route('appointments.show', $prescription->appointment) }}" class="tb-btn">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr 1fr;gap:1rem;">

    {{-- Left: Clinical details + medicines --}}
    <div>

        {{-- Clinical details --}}
        <div class="card mb-4" style="padding:1.1rem;">
            <div class="card-title">Clinical Details</div>

            @if($prescription->chief_complaint)
            <div class="info-row">
                <span class="info-label">Chief Complaint</span>
                <span class="info-val">{{ $prescription->chief_complaint }}</span>
            </div>
            @endif

            @if($prescription->diagnosis)
            <div class="info-row">
                <span class="info-label">Diagnosis</span>
                <span class="info-val">{{ $prescription->diagnosis }}</span>
            </div>
            @endif

            @if($prescription->follow_up_date)
            <div class="info-row">
                <span class="info-label">Follow-up</span>
                <span class="info-val" style="color:#185FA5;">
                    {{ $prescription->follow_up_date->format('d M Y') }}
                </span>
            </div>
            @endif

            @if($prescription->notes)
            <div style="margin-top:10px;padding-top:10px;border-top:0.5px solid var(--color-border-tertiary);">
                <p style="font-size:11px;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;margin-bottom:6px;">Notes / Advice</p>
                <p style="font-size:13px;">{{ $prescription->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Medicines --}}
        <div class="card" style="padding:1.1rem;">
            <div class="card-title">Medicines</div>

            @if($prescription->items->count())
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                        <th class="th">#</th>
                        <th class="th">Medicine</th>
                        <th class="th">Dosage</th>
                        <th class="th">Frequency</th>
                        <th class="th">Duration</th>
                        <th class="th">Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->items as $i => $item)
                    <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                        <td style="padding:8px 10px;color:var(--color-text-secondary);">{{ $i + 1 }}</td>
                        <td style="padding:8px 10px;font-weight:500;">{{ $item->medicine_name }}</td>
                        <td style="padding:8px 10px;">{{ $item->dosage ?? '—' }}</td>
                        <td style="padding:8px 10px;">{{ $item->frequency ?? '—' }}</td>
                        <td style="padding:8px 10px;">{{ $item->duration ?? '—' }}</td>
                        <td style="padding:8px 10px;color:var(--color-text-secondary);">{{ $item->instructions ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="font-size:13px;color:var(--color-text-secondary);text-align:center;padding:1rem 0;">
                No medicines prescribed.
            </p>
            @endif
        </div>

    </div>

    {{-- Right: Patient & Doctor info --}}
    <div>
        <div class="card" style="padding:1.1rem;">
            <div class="card-title">Patient</div>
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-val">{{ $prescription->patient->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-val">{{ $prescription->patient->email }}</span>
            </div>
        </div>

        <div class="card mt-4" style="padding:1.1rem;">
            <div class="card-title">Prescribing Doctor</div>
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-val">Dr. {{ $prescription->doctor->name }}</span>
            </div>
            @if($prescription->doctor->specialization)
            <div class="info-row">
                <span class="info-label">Specialization</span>
                <span class="info-val">{{ $prescription->doctor->specialization }}</span>
            </div>
            @endif
            @if($prescription->doctor->registration_number)
            <div class="info-row">
                <span class="info-label">Reg. No.</span>
                <span class="info-val">{{ $prescription->doctor->registration_number }}</span>
            </div>
            @endif
        </div>

        <div class="card mt-4" style="padding:1.1rem;">
            <div class="card-title">Appointment</div>
            <div class="info-row">
                <span class="info-label">Date</span>
                <span class="info-val">{{ $prescription->appointment->appointment_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Time</span>
                <span class="info-val">{{ \Carbon\Carbon::parse($prescription->appointment->appointment_time)->format('h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Visit Type</span>
                <span class="info-val">{{ ucfirst(str_replace('_', ' ', $prescription->appointment->visit_type)) }}</span>
            </div>
            <div style="margin-top:10px;">
                <a href="{{ route('appointments.show', $prescription->appointment) }}"
                   style="font-size:12px;color:#185FA5;">
                    View appointment →
                </a>
            </div>
        </div>
    </div>

</div>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.card-title{font-size:13px;font-weight:500;margin-bottom:0.875rem;}
.mb-4{margin-bottom:1rem;}
.mt-4{margin-top:1rem;}
.info-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:0.5px solid var(--color-border-tertiary);font-size:13px;}
.info-row:last-of-type{border-bottom:none;}
.info-label{color:var(--color-text-secondary);}
.info-val{font-weight:500;text-align:right;}
.th{padding:8px 10px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
</style>

@endsection