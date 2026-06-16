@extends('tenant.layouts.app')

@section('title', 'My Dashboard')

@section('content')

{{-- ── Welcome header ── --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:17px;font-weight:500;">
            Welcome, {{ auth()->user()->name }} 👋
        </h1>
        <p style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">
            {{ tenant('clinic_name') }} &bull; {{ now()->format('l, d F Y') }}
        </p>
    </div>
    <a href="{{ route('appointments.create') }}"
       style="background:#185FA5;color:#fff;padding:8px 16px;border-radius:var(--border-radius-md);font-size:13px;font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        <i class="ti ti-calendar-plus"></i> Book Appointment
    </a>
</div>

{{-- ── Stats row ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:1.25rem;">
    <div class="stat-card">
        <span class="stat-num">{{ $stats['total_appointments'] }}</span>
        <span class="stat-label">Total Visits</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#27500A;">{{ $stats['completed'] }}</span>
        <span class="stat-label">Completed</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#185FA5;">{{ $stats['upcoming'] }}</span>
        <span class="stat-label">Upcoming</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#D97706;">{{ $stats['unpaid_invoices'] }}</span>
        <span class="stat-label">Unpaid Invoices</span>
    </div>
</div>

{{-- ── Upcoming appointments ── --}}
@if($upcoming->count())
<div class="card mb-4" style="padding:1.1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <span style="font-size:13px;font-weight:500;">Upcoming Appointments</span>
        <a href="{{ route('appointments.index') }}" style="font-size:12px;color:#185FA5;">View all</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($upcoming as $appt)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--color-background-secondary);border-radius:var(--border-radius-md);border:0.5px solid var(--color-border-tertiary);">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:500;color:#0C447C;flex-shrink:0;">
                    {{ $appt->doctor->initials }}
                </div>
                <div>
                    <p style="font-size:13px;font-weight:500;">Dr. {{ $appt->doctor->name }}</p>
                    <p style="font-size:11px;color:var(--color-text-secondary);">
                        {{ $appt->doctor->specialization ?? '' }}
                    </p>
                </div>
            </div>
            <div style="text-align:center;">
                <p style="font-size:13px;font-weight:500;">{{ $appt->appointment_date->format('d M Y') }}</p>
                <p style="font-size:11px;color:var(--color-text-secondary);">
                    {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                </p>
            </div>
            <div>
                @php
                    $statusColors = [
                        'pending'   => ['#FEF3C7','#D97706'],
                        'confirmed' => ['#E6F1FB','#185FA5'],
                    ];
                    [$sbg,$sfg] = $statusColors[$appt->status] ?? ['#F3F4F6','#374151'];
                @endphp
                <span style="background:{{ $sbg }};color:{{ $sfg }};padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;">
                    {{ ucfirst($appt->status) }}
                </span>
            </div>
            <a href="{{ route('appointments.show', $appt) }}"
               style="font-size:12px;color:#185FA5;">View</a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── Two column: Recent appointments + Quick links ── --}}
<div style="display:grid;grid-template-columns:1.2fr 1fr;gap:1rem;margin-bottom:1rem;">

    {{-- Recent appointments --}}
    <div class="card" style="padding:0;">
        <div style="padding:12px 16px;border-bottom:0.5px solid var(--color-border-tertiary);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;font-weight:500;">Recent Appointments</span>
            <a href="{{ route('appointments.index') }}" style="font-size:12px;color:#185FA5;">View all</a>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                    <th class="th">Doctor</th>
                    <th class="th">Date</th>
                    <th class="th">Status</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments as $appt)
                @php
                    $statusColors = [
                        'pending'     => ['#FEF3C7','#D97706'],
                        'confirmed'   => ['#E6F1FB','#185FA5'],
                        'in_progress' => ['#EDE9FE','#5B21B6'],
                        'completed'   => ['#EAF3DE','#27500A'],
                        'cancelled'   => ['#FEE2E2','#A32D2D'],
                    ];
                    [$sbg,$sfg] = $statusColors[$appt->status] ?? ['#F3F4F6','#374151'];
                @endphp
                <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                    <td style="padding:9px 16px;">
                        <div style="font-weight:500;font-size:12px;">Dr. {{ $appt->doctor->name }}</div>
                        <div style="font-size:11px;color:var(--color-text-secondary);">
                            {{ $appt->doctor->specialization ?? '' }}
                        </div>
                    </td>
                    <td style="padding:9px 16px;font-size:12px;">
                        {{ $appt->appointment_date->format('d M Y') }}
                    </td>
                    <td style="padding:9px 16px;">
                        <span style="background:{{ $sbg }};color:{{ $sfg }};padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;">
                            {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                        </span>
                    </td>
                    <td style="padding:9px 16px;">
                        <a href="{{ route('appointments.show', $appt) }}"
                           style="font-size:11px;color:#185FA5;">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:1.5rem;text-align:center;color:var(--color-text-secondary);font-size:13px;">
                        No appointments yet.
                        <a href="{{ route('appointments.create') }}" style="color:#185FA5;">Book now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Quick links + recent prescriptions + recent invoices --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Quick links --}}
        <div class="card" style="padding:1.1rem;">
            <p style="font-size:13px;font-weight:500;margin-bottom:0.875rem;">Quick Links</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <a href="{{ route('appointments.create') }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);text-decoration:none;color:var(--color-text-primary);">
                    <i class="ti ti-calendar-plus" style="font-size:20px;color:#185FA5;"></i>
                    <span style="font-size:12px;text-align:center;">Book Appointment</span>
                </a>
                <a href="{{ route('invoices.index') }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);text-decoration:none;color:var(--color-text-primary);">
                    <i class="ti ti-receipt" style="font-size:20px;color:#185FA5;"></i>
                    <span style="font-size:12px;text-align:center;">My Invoices</span>
                </a>
                <a href="{{ route('prescriptions.index') }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);text-decoration:none;color:var(--color-text-primary);">
                    <i class="ti ti-file-text" style="font-size:20px;color:#185FA5;"></i>
                    <span style="font-size:12px;text-align:center;">Prescriptions</span>
                </a>
                <a href="{{ route('appointments.index') }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);text-decoration:none;color:var(--color-text-primary);">
                    <i class="ti ti-history" style="font-size:20px;color:#185FA5;"></i>
                    <span style="font-size:12px;text-align:center;">History</span>
                </a>
            </div>
        </div>

        {{-- Recent prescriptions --}}
        @if($recentPrescriptions->count())
        <div class="card" style="padding:1.1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.875rem;">
                <span style="font-size:13px;font-weight:500;">Recent Prescriptions</span>
                <a href="{{ route('prescriptions.index') }}" style="font-size:12px;color:#185FA5;">View all</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($recentPrescriptions as $rx)
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:6px 0;border-bottom:0.5px solid var(--color-border-tertiary);">
                    <div>
                        <p style="font-weight:500;">Dr. {{ $rx->doctor->name }}</p>
                        <p style="color:var(--color-text-secondary);font-size:11px;">
                            {{ $rx->created_at->format('d M Y') }}
                            &bull; {{ $rx->items->count() ?? 0 }} medicine(s)
                        </p>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <a href="{{ route('prescriptions.show', $rx) }}"
                           style="color:#185FA5;font-size:11px;">View</a>
                        <a href="{{ route('prescriptions.pdf', $rx) }}"
                           style="color:#185FA5;font-size:11px;">PDF</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</div>

<style>
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.mb-4{margin-bottom:1rem;}
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:12px 20px;display:flex;flex-direction:column;align-items:center;}
.stat-num{font-size:22px;font-weight:600;line-height:1.1;}
.stat-label{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}
.th{padding:8px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
</style>

@endsection