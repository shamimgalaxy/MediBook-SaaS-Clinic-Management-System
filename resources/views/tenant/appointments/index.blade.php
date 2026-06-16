{{-- resources/views/tenant/appointments/index.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Appointments</h1>
        <p class="page-sub">Track and manage all clinic appointments</p>
    </div>
    @hasanyrole('clinic_admin|receptionist|patient')
    <a href="{{ route('appointments.create') }}" class="tb-btn primary">
        <i class="ti ti-plus"></i> Book appointment
    </a>
    @endhasanyrole
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert-item mb-4">
        <i class="ti ti-circle-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert-item error mb-4">
        <i class="ti ti-alert-circle"></i> {{ session('error') }}
    </div>
@endif

{{-- Stats row --}}
<div class="stats-row mb-4">
    <div class="stat-card">
        <span class="stat-num">{{ $stats['total'] ?? 0 }}</span>
        <span class="stat-label">Total</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#D97706;">{{ $stats['pending'] ?? 0 }}</span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#185FA5;">{{ $stats['confirmed'] ?? 0 }}</span>
        <span class="stat-label">Confirmed</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#27500A;">{{ $stats['completed'] ?? 0 }}</span>
        <span class="stat-label">Completed</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#A32D2D;">{{ $stats['cancelled'] ?? 0 }}</span>
        <span class="stat-label">Cancelled</span>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4" style="padding:0.75rem 1rem;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search patient or doctor…"
            class="filter-input" style="flex:1;min-width:180px;" />

        <input type="date" name="date" value="{{ request('date') }}"
            class="filter-input" style="width:150px;" />

        <select name="status" class="filter-input" style="width:140px;">
            <option value="">All status</option>
            @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>

        @hasanyrole('clinic_admin|receptionist')
        <select name="doctor_id" class="filter-input" style="width:160px;">
            <option value="">All doctors</option>
            @foreach($doctors as $doc)
                <option value="{{ $doc->id }}" @selected(request('doctor_id') == $doc->id)>Dr. {{ $doc->name }}</option>
            @endforeach
        </select>
        @endhasanyrole

        <button type="submit" class="tb-btn primary" style="padding:6px 14px;">Filter</button>
        @if(request()->hasAny(['search','date','status','doctor_id']))
            <a href="{{ route('appointments.index') }}" class="tb-btn" style="padding:6px 14px;">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card" style="padding:0;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                <th class="th">Patient</th>
                <th class="th">Doctor</th>
                <th class="th">Date & Time</th>
                <th class="th">Type</th>
                <th class="th">Status</th>
                <th class="th">Fee</th>
                <th class="th">Payment</th>
                <th class="th"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appt)
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">

                {{-- Patient --}}
                <td style="padding:10px 16px;">
                    <div style="font-weight:500;">{{ $appt->patient->name }}</div>
                    <div style="font-size:11px;color:var(--color-text-secondary);">{{ $appt->patient->email }}</div>
                </td>

                {{-- Doctor --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($appt->doctor->avatar)
                            <img src="{{ Storage::url($appt->doctor->avatar) }}"
                                style="width:26px;height:26px;border-radius:50%;object-fit:cover;" />
                        @else
                            <div style="width:26px;height:26px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;color:#0C447C;">
                                {{ $appt->doctor->initials }}
                            </div>
                        @endif
                        <span>Dr. {{ $appt->doctor->name }}</span>
                    </div>
                </td>

                {{-- Date & Time --}}
                <td style="padding:10px 16px;">
                    <div style="font-weight:500;">{{ $appt->appointment_date->format('d M Y') }}</div>
                    <div style="font-size:11px;color:var(--color-text-secondary);">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</div>
                </td>

                {{-- Visit type --}}
                <td style="padding:10px 16px;">
                    @php
                        $typeColors = ['new'=>['#E6F1FB','#0C447C'],'follow_up'=>['#F3F4F6','#374151'],'emergency'=>['#FEE2E2','#A32D2D']];
                        [$bg,$fg] = $typeColors[$appt->visit_type] ?? ['#F3F4F6','#374151'];
                    @endphp
                    <span class="pill" style="background:{{ $bg }};color:{{ $fg }};">
                        {{ ucfirst(str_replace('_',' ',$appt->visit_type)) }}
                    </span>
                </td>

                {{-- Status --}}
                <td style="padding:10px 16px;">
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
                    <span class="pill" style="background:{{ $sbg }};color:{{ $sfg }};">
                        {{ ucfirst(str_replace('_',' ',$appt->status)) }}
                    </span>
                </td>

                {{-- Fee --}}
                <td style="padding:10px 16px;">৳{{ number_format($appt->fee) }}</td>

                {{-- Payment --}}
                <td style="padding:10px 16px;">
                    @if($appt->isPaid())
                        <span class="pill" style="background:#EAF3DE;color:#27500A;">Paid</span>
                    @else
                        <span class="pill" style="background:#F3F4F6;color:#6B7280;">Unpaid</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <a href="{{ route('appointments.show', $appt) }}"
                            title="View" style="color:#185FA5;"><i class="ti ti-eye" style="font-size:16px;"></i></a>

                        @hasanyrole('clinic_admin|receptionist')
                        <a href="{{ route('appointments.edit', $appt) }}"
                            title="Edit" style="color:#185FA5;"><i class="ti ti-edit" style="font-size:16px;"></i></a>

                        @if(!in_array($appt->status, ['in_progress','completed']))
                        <form method="POST" action="{{ route('appointments.destroy', $appt) }}"
                            onsubmit="return confirm('Delete this appointment?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;cursor:pointer;color:#A32D2D;" title="Delete">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </button>
                        </form>
                        @endif
                        @endhasanyrole
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:2.5rem;text-align:center;color:var(--color-text-secondary);font-size:13px;">
                    No appointments found.
                    @hasanyrole('clinic_admin|receptionist|patient')
                    <a href="{{ route('appointments.create') }}" style="color:#185FA5;">Book the first one</a>.
                    @endhasanyrole
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
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
.table-row-hover:hover{background:var(--color-background-secondary);}
.th{padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
.filter-input{padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.alert-item{background:#E6F1FB;color:#0C447C;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:13px;}
.alert-item.error{background:#FEE2E2;color:#A32D2D;}
.stats-row{display:flex;gap:12px;flex-wrap:wrap;}
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:12px 20px;display:flex;flex-direction:column;align-items:center;min-width:80px;}
.stat-num{font-size:22px;font-weight:600;line-height:1.1;}
.stat-label{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}
</style>
@endsection