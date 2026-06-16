{{-- resources/views/tenant/appointments/show.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Appointment #' . $appointment->id)

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Appointment #{{ $appointment->id }}</h1>
        <p class="page-sub">{{ $appointment->appointment_date->format('l, d F Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
    </div>
    <div style="display:flex;gap:8px;">
        {{-- Prescription button for doctor --}}
        @role('doctor')
        @if(auth()->user()->id === $appointment->doctor->user_id)
            @if(!$appointment->prescription)
                <a href="{{ route('appointments.prescription.create', $appointment) }}" class="tb-btn primary">
                    <i class="ti ti-file-plus"></i> Write Prescription
                </a>
            @else
                <a href="{{ route('prescriptions.show', $appointment->prescription) }}" class="tb-btn">
                    <i class="ti ti-file-text"></i> View Prescription
                </a>
            @endif
        @endif
        @endrole

        @hasanyrole('clinic_admin|receptionist')
        <a href="{{ route('appointments.edit', $appointment) }}" class="tb-btn">
            <i class="ti ti-edit"></i> Edit
        </a>
        @endhasanyrole
        <a href="{{ route('appointments.index') }}" class="tb-btn">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-item mb-4"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">

    {{-- Left --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Patient & Doctor --}}
        <div class="card section-card">
            <div class="section-title">Patient & Doctor</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="info-block">
                    <div class="info-block-label">Patient</div>
                    <div style="display:flex;align-items:center;gap:10px;margin-top:6px;">
                        <div class="avatar-circle">{{ substr($appointment->patient->name,0,1) }}</div>
                        <div>
                            <div style="font-weight:500;font-size:13px;">{{ $appointment->patient->name }}</div>
                            <div style="font-size:11px;color:var(--color-text-secondary);">{{ $appointment->patient->email }}</div>
                        </div>
                    </div>
                </div>
                <div class="info-block">
                    <div class="info-block-label">Doctor</div>
                    <div style="display:flex;align-items:center;gap:10px;margin-top:6px;">
                        @if($appointment->doctor->avatar)
                            <img src="{{ Storage::url($appointment->doctor->avatar) }}"
                                style="width:34px;height:34px;border-radius:50%;object-fit:cover;" />
                        @else
                            <div class="avatar-circle">{{ $appointment->doctor->initials }}</div>
                        @endif
                        <div>
                            <div style="font-weight:500;font-size:13px;">Dr. {{ $appointment->doctor->name }}</div>
                            <div style="font-size:11px;color:var(--color-text-secondary);">{{ $appointment->doctor->specialty }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Appointment details --}}
        <div class="card section-card">
            <div class="section-title">Appointment Details</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="detail-item">
                    <span class="detail-label">Date</span>
                    <span class="detail-val">{{ $appointment->appointment_date->format('d M Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Time</span>
                    <span class="detail-val">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Visit Type</span>
                    @php
                        $typeColors = ['new'=>['#E6F1FB','#0C447C'],'follow_up'=>['#F3F4F6','#374151'],'emergency'=>['#FEE2E2','#A32D2D']];
                        [$bg,$fg] = $typeColors[$appointment->visit_type] ?? ['#F3F4F6','#374151'];
                    @endphp
                    <span class="pill" style="background:{{ $bg }};color:{{ $fg }};">
                        {{ ucfirst(str_replace('_',' ',$appointment->visit_type)) }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    @php
                        $statusColors = [
                            'pending'     => ['#FEF3C7','#D97706'],
                            'confirmed'   => ['#E6F1FB','#185FA5'],
                            'in_progress' => ['#EDE9FE','#5B21B6'],
                            'completed'   => ['#EAF3DE','#27500A'],
                            'cancelled'   => ['#FEE2E2','#A32D2D'],
                        ];
                        [$sbg,$sfg] = $statusColors[$appointment->status] ?? ['#F3F4F6','#374151'];
                    @endphp
                    <span class="pill" style="background:{{ $sbg }};color:{{ $sfg }};">
                        {{ ucfirst(str_replace('_',' ',$appointment->status)) }}
                    </span>
                </div>
            </div>

            @if($appointment->reason)
            <div style="margin-top:14px;padding-top:14px;border-top:0.5px solid var(--color-border-tertiary);">
                <div class="detail-label" style="margin-bottom:4px;">Reason for Visit</div>
                <p style="font-size:13px;margin:0;line-height:1.5;">{{ $appointment->reason }}</p>
            </div>
            @endif

            @if($appointment->notes)
            <div style="margin-top:12px;padding-top:12px;border-top:0.5px solid var(--color-border-tertiary);">
                <div class="detail-label" style="margin-bottom:4px;">Notes</div>
                <p style="font-size:13px;margin:0;line-height:1.5;">{{ $appointment->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Payment --}}
        <div class="card section-card">
            <div class="section-title">Payment</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="detail-item">
                    <span class="detail-label">Fee</span>
                    <span class="detail-val" style="font-weight:600;">৳{{ number_format($appointment->fee, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    @if($appointment->isPaid())
                        <span class="pill" style="background:#EAF3DE;color:#27500A;">Paid</span>
                    @else
                        <span class="pill" style="background:#F3F4F6;color:#6B7280;">Unpaid</span>
                    @endif
                </div>
                <div class="detail-item">
                    <span class="detail-label">Method</span>
                    <span class="detail-val">{{ $appointment->payment_method ? ucfirst($appointment->payment_method) : '—' }}</span>
                </div>
            </div>

            @if(!$appointment->isPaid() && $appointment->status !== 'cancelled')
            @hasanyrole('clinic_admin|receptionist')
            <form method="POST" action="{{ route('appointments.updatePayment', $appointment) }}"
                style="margin-top:14px;padding-top:14px;border-top:0.5px solid var(--color-border-tertiary);display:flex;gap:10px;align-items:center;">
                @csrf @method('PATCH')
                <select name="payment_method" class="form-control" style="width:150px;" required>
                    <option value="">Select method…</option>
                    <option value="cash">Cash</option>
                    <option value="bkash">bKash</option>
                    <option value="card">Card</option>
                </select>
                <button type="submit" class="tb-btn primary" style="padding:6px 14px;">
                    <i class="ti ti-credit-card"></i> Mark Paid
                </button>
            </form>
            @endhasanyrole
            @endif
        </div>

        {{-- Prescription summary (visible to all roles if exists) --}}
        @if($appointment->prescription)
        <div class="card section-card">
            <div class="section-title">Prescription</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @if($appointment->prescription->diagnosis)
                <div class="detail-item">
                    <span class="detail-label">Diagnosis</span>
                    <span class="detail-val">{{ $appointment->prescription->diagnosis }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Medicines</span>
                    <span class="detail-val">{{ $appointment->prescription->items->count() }} prescribed</span>
                </div>
                @if($appointment->prescription->follow_up_date)
                <div class="detail-item">
                    <span class="detail-label">Follow-up</span>
                    <span class="detail-val" style="color:#185FA5;">
                        {{ $appointment->prescription->follow_up_date->format('d M Y') }}
                    </span>
                </div>
                @endif
            </div>
            <div style="margin-top:10px;">
                <a href="{{ route('prescriptions.show', $appointment->prescription) }}"
                   style="font-size:12px;color:#185FA5;">
                    View full prescription →
                </a>
            </div>
        </div>
        @endif

    </div>

    {{-- Right — status actions --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        @hasanyrole('clinic_admin|receptionist|doctor')
        @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
        <div class="card section-card">
            <div class="section-title">Update Status</div>
            @php
                $transitions = [
                    'pending'     => ['confirmed' => 'Confirm','cancelled' => 'Cancel'],
                    'confirmed'   => ['in_progress' => 'Start Visit','cancelled' => 'Cancel'],
                    'in_progress' => ['completed' => 'Complete'],
                ];
                $available = $transitions[$appointment->status] ?? [];
            @endphp
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($available as $newStatus => $label)
                <form method="POST" action="{{ route('appointments.updateStatus', $appointment) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $newStatus }}" />
                    @php
                        $btnStyle = match($newStatus) {
                            'confirmed'   => 'background:#185FA5;color:#fff;border-color:#185FA5;',
                            'in_progress' => 'background:#5B21B6;color:#fff;border-color:#5B21B6;',
                            'completed'   => 'background:#27500A;color:#fff;border-color:#27500A;',
                            'cancelled'   => 'background:#A32D2D;color:#fff;border-color:#A32D2D;',
                            default       => '',
                        };
                    @endphp
                    <button type="submit" class="tb-btn"
                        style="width:100%;justify-content:center;padding:8px 0;{{ $btnStyle }}">
                        {{ $label }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
        @endif
        @endhasanyrole

        <div class="card section-card">
            <div class="section-title">Record Info</div>
            <div class="summary-row">
                <span class="summary-label">Created</span>
                <span class="summary-val">{{ $appointment->created_at->format('d M Y') }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Updated</span>
                <span class="summary-val">{{ $appointment->updated_at->diffForHumans() }}</span>
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
.section-card{padding:1rem 1.25rem;}
.section-title{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--color-text-secondary);margin-bottom:14px;}
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
.mb-4{margin-bottom:1rem;}
.alert-item{background:#E6F1FB;color:#0C447C;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:13px;}
.info-block{padding:12px;background:var(--color-background-secondary);border-radius:var(--border-radius-md);}
.info-block-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.4px;color:var(--color-text-secondary);}
.avatar-circle{width:34px;height:34px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:#0C447C;flex-shrink:0;}
.detail-item{display:flex;flex-direction:column;gap:4px;padding:8px 0;border-bottom:0.5px solid var(--color-border-tertiary);}
.detail-label{font-size:11px;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.3px;}
.detail-val{font-size:13px;font-weight:500;}
.summary-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:0.5px solid var(--color-border-tertiary);}
.summary-row:last-of-type{border-bottom:none;}
.summary-label{font-size:12px;color:var(--color-text-secondary);}
.summary-val{font-size:13px;font-weight:500;}
.form-control{padding:7px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.form-control:focus{outline:none;border-color:#185FA5;}
</style>
@endsection