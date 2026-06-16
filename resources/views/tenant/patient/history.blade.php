@extends('tenant.layouts.app')

@section('title', 'Appointment History')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Appointment History</h1>
        <p class="page-sub">All your past and upcoming appointments</p>
    </div>
    <a href="{{ route('patient.book') }}" class="tb-btn primary">
        <i class="ti ti-calendar-plus"></i> Book New
    </a>
</div>

{{-- Stats --}}
<div class="stats-row mb-4">
    <div class="stat-card">
        <span class="stat-num">{{ $stats['total'] }}</span>
        <span class="stat-label">Total</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#185FA5;">{{ $stats['upcoming'] }}</span>
        <span class="stat-label">Upcoming</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#27500A;">{{ $stats['completed'] }}</span>
        <span class="stat-label">Completed</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#A32D2D;">{{ $stats['cancelled'] }}</span>
        <span class="stat-label">Cancelled</span>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4" style="padding:0.875rem 1rem;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search doctor name…"
               class="filter-input" style="width:200px;" />

        <select name="status" class="filter-input">
            <option value="">All status</option>
            @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </option>
            @endforeach
        </select>

        <div style="display:flex;align-items:center;gap:6px;">
            <label style="font-size:12px;color:var(--color-text-secondary);">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="filter-input" />
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <label style="font-size:12px;color:var(--color-text-secondary);">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="filter-input" />
        </div>

        <button type="submit" class="tb-btn primary">Filter</button>
        @if(request()->hasAny(['search','status','from','to']))
            <a href="{{ route('patient.history') }}" class="tb-btn">Clear</a>
        @endif
    </form>
</div>

{{-- Appointments list --}}
<div class="card" style="padding:0;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                <th class="th">Doctor</th>
                <th class="th">Date & Time</th>
                <th class="th">Type</th>
                <th class="th">Status</th>
                <th class="th">Fee</th>
                <th class="th">Payment</th>
                <th class="th">Records</th>
                <th class="th"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appt)
            @php
                $statusColors = [
                    'pending'     => ['#FEF3C7','#D97706'],
                    'confirmed'   => ['#E6F1FB','#185FA5'],
                    'in_progress' => ['#EDE9FE','#5B21B6'],
                    'completed'   => ['#EAF3DE','#27500A'],
                    'cancelled'   => ['#FEE2E2','#A32D2D'],
                ];
                $typeColors = [
                    'new'       => ['#E6F1FB','#0C447C'],
                    'follow_up' => ['#F3F4F6','#374151'],
                    'emergency' => ['#FEE2E2','#A32D2D'],
                ];
                [$sbg,$sfg] = $statusColors[$appt->status] ?? ['#F3F4F6','#374151'];
                [$tbg,$tfg] = $typeColors[$appt->visit_type] ?? ['#F3F4F6','#374151'];
            @endphp
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">

                {{-- Doctor --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:30px;height:30px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;color:#0C447C;flex-shrink:0;">
                            {{ $appt->doctor->initials }}
                        </div>
                        <div>
                            <div style="font-weight:500;">Dr. {{ $appt->doctor->name }}</div>
                            <div style="font-size:11px;color:var(--color-text-secondary);">
                                {{ $appt->doctor->specialization ?? '' }}
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Date & Time --}}
                <td style="padding:10px 16px;">
                    <div style="font-weight:500;">{{ $appt->appointment_date->format('d M Y') }}</div>
                    <div style="font-size:11px;color:var(--color-text-secondary);">
                        {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                    </div>
                </td>

                {{-- Visit type --}}
                <td style="padding:10px 16px;">
                    <span style="background:{{ $tbg }};color:{{ $tfg }};padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;">
                        {{ ucfirst(str_replace('_', ' ', $appt->visit_type)) }}
                    </span>
                </td>

                {{-- Status --}}
                <td style="padding:10px 16px;">
                    <span style="background:{{ $sbg }};color:{{ $sfg }};padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;">
                        {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                    </span>
                </td>

                {{-- Fee --}}
                <td style="padding:10px 16px;font-weight:500;">
                    ৳{{ number_format($appt->fee, 0) }}
                </td>

                {{-- Payment --}}
                <td style="padding:10px 16px;">
                    @if($appt->isPaid())
                        <span style="background:#EAF3DE;color:#27500A;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;">Paid</span>
                    @else
                        <span style="background:#F3F4F6;color:#6B7280;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;">Unpaid</span>
                    @endif
                </td>

                {{-- Records --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;gap:8px;align-items:center;">
                        @if($appt->prescription)
                            <a href="{{ route('prescriptions.show', $appt->prescription) }}"
                               title="View Prescription"
                               style="color:#185FA5;font-size:11px;display:flex;align-items:center;gap:3px;">
                                <i class="ti ti-file-text" style="font-size:14px;"></i> Rx
                            </a>
                        @endif
                        @if($appt->invoice)
                            <a href="{{ route('invoices.show', $appt->invoice) }}"
                               title="View Invoice"
                               style="color:#185FA5;font-size:11px;display:flex;align-items:center;gap:3px;">
                                <i class="ti ti-receipt" style="font-size:14px;"></i> Inv
                            </a>
                        @endif
                        @if(!$appt->prescription && !$appt->invoice)
                            <span style="font-size:11px;color:var(--color-text-secondary);">—</span>
                        @endif
                    </div>
                </td>

                {{-- View --}}
                <td style="padding:10px 16px;">
                    <a href="{{ route('appointments.show', $appt) }}"
                       style="font-size:12px;color:#185FA5;">View</a>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:2.5rem;text-align:center;color:var(--color-text-secondary);font-size:13px;">
                    No appointments found.
                    <a href="{{ route('patient.book') }}" style="color:#185FA5;">Book your first appointment</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($appointments->hasPages())
    <div style="padding:0.75rem 1rem;border-top:0.5px solid var(--color-border-tertiary);">
        {{ $appointments->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.mb-4{margin-bottom:1rem;}
.stats-row{display:flex;gap:12px;flex-wrap:wrap;}
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:12px 20px;display:flex;flex-direction:column;align-items:center;min-width:100px;}
.stat-num{font-size:22px;font-weight:600;line-height:1.1;}
.stat-label{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}
.filter-input{padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.th{padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
.table-row-hover:hover{background:var(--color-background-secondary);}
</style>

@endsection