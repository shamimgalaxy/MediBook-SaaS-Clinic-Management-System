{{-- resources/views/tenant/doctors/show.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Dr. ' . $doctor->name)

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Dr. {{ $doctor->name }}</h1>
        <p class="page-sub">{{ $doctor->specialty }} · {{ $doctor->experience_years }} yr{{ $doctor->experience_years != 1 ? 's' : '' }} experience</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('tenant.doctors.edit', $doctor) }}" class="tb-btn primary">
            <i class="ti ti-edit"></i> Edit
        </a>
        <a href="{{ route('tenant.doctors.index') }}" class="tb-btn">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="show-grid">

    {{-- ── Left column ── --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Profile card --}}
        <div class="card">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:1rem;padding-bottom:1rem;border-bottom:0.5px solid var(--color-border-tertiary);">
                @if($doctor->avatar)
                    <img src="{{ Storage::url($doctor->avatar) }}" alt=""
                        style="width:60px;height:60px;border-radius:50%;object-fit:cover;flex-shrink:0;" />
                @else
                    <div style="width:60px;height:60px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:500;color:#0C447C;flex-shrink:0;">
                        {{ $doctor->initials }}
                    </div>
                @endif
                <div style="flex:1;min-width:0;">
                    <div style="font-size:16px;font-weight:500;">Dr. {{ $doctor->name }}</div>
                    <div style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">{{ $doctor->user->email }}</div>
                    <div style="margin-top:6px;">
                        @if($doctor->is_active)
                            <span class="pill active-pill">Active</span>
                        @else
                            <span class="pill inactive-pill">Inactive</span>
                        @endif
                    </div>
                </div>
                {{-- Quick toggle --}}
                <form method="POST" action="{{ route('tenant.doctors.toggle', $doctor) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="tb-btn" style="font-size:12px;"
                        title="{{ $doctor->is_active ? 'Deactivate' : 'Activate' }}">
                        <i class="ti ti-power"></i>
                        {{ $doctor->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label"><i class="ti ti-stethoscope"></i> Specialty</span>
                    <span class="info-value">{{ $doctor->specialty }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="ti ti-clock"></i> Experience</span>
                    <span class="info-value">{{ $doctor->experience_years }} year{{ $doctor->experience_years != 1 ? 's' : '' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="ti ti-receipt"></i> Consultation fee</span>
                    <span class="info-value">৳{{ number_format($doctor->consultation_fee) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="ti ti-phone"></i> Phone</span>
                    <span class="info-value">{{ $doctor->phone ?? '—' }}</span>
                </div>
            </div>

            @if($doctor->bio)
                <div style="margin-top:1rem;padding-top:1rem;border-top:0.5px solid var(--color-border-tertiary);">
                    <p style="font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Bio</p>
                    <p style="font-size:13px;line-height:1.6;color:var(--color-text-primary);">{{ $doctor->bio }}</p>
                </div>
            @endif
        </div>

        {{-- Weekly schedule --}}
        <div class="card">
            <div class="card-hdr">
                <span class="card-title">Weekly schedule</span>
                <a href="{{ route('tenant.doctors.edit', $doctor) }}" class="card-link">Edit</a>
            </div>

            @forelse($doctor->schedules->sortBy('day_of_week') as $schedule)
                <div class="schedule-row {{ $loop->last ? '' : 'schedule-row-border' }}">
                    <div style="display:flex;align-items:center;gap:8px;min-width:110px;">
                        <span class="day-pill">{{ $schedule->day_name }}</span>
                    </div>
                    <div style="font-size:13px;color:var(--color-text-primary);">
                        {{ $schedule->time_range }}
                    </div>
                    <div style="font-size:12px;color:var(--color-text-secondary);margin-left:auto;">
                        {{ $schedule->slot_duration }} min slots
                    </div>
                    @if(!$schedule->is_active)
                        <span class="pill inactive-pill" style="font-size:10px;">Off</span>
                    @endif
                </div>
            @empty
                <p style="font-size:13px;color:var(--color-text-secondary);text-align:center;padding:1rem 0;">
                    No schedule configured yet.
                    <a href="{{ route('tenant.doctors.edit', $doctor) }}" style="color:#185FA5;">Add one</a>.
                </p>
            @endforelse
        </div>

    </div>

    {{-- ── Right column ── --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="stat-card">
                <div class="stat-lbl"><i class="ti ti-calendar-event"></i> Total appointments</div>
                <div class="stat-val">{{ $doctor->appointments->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-lbl"><i class="ti ti-check"></i> Completed</div>
                <div class="stat-val" style="color:#27500A;">
                    {{ $doctor->appointments->where('status', 'completed')->count() }}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-lbl"><i class="ti ti-clock"></i> Pending</div>
                <div class="stat-val" style="color:#633806;">
                    {{ $doctor->appointments->where('status', 'pending')->count() }}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-lbl"><i class="ti ti-x"></i> Cancelled</div>
                <div class="stat-val" style="color:#791F1F;">
                    {{ $doctor->appointments->where('status', 'cancelled')->count() }}
                </div>
            </div>
        </div>

        {{-- Recent appointments --}}
        <div class="card">
            <div class="card-hdr">
                <span class="card-title">Recent appointments</span>
            </div>

            @forelse($doctor->appointments as $appt)
                <div class="appt-row {{ $loop->last ? '' : 'appt-row-border' }}">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $appt->patient->user->name ?? 'Patient' }}
                        </div>
                        <div style="font-size:11px;color:var(--color-text-secondary);margin-top:2px;">
                            {{ $appt->appointment_date ? \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y, h:i A') : '—' }}
                        </div>
                    </div>
                    <div>
                        @php
                            $pillMap = [
                                'pending'    => ['bg'=>'#FAEEDA','color'=>'#633806'],
                                'completed'  => ['bg'=>'#EAF3DE','color'=>'#27500A'],
                                'cancelled'  => ['bg'=>'#FCEBEB','color'=>'#791F1F'],
                                'in_progress'=> ['bg'=>'#E6F1FB','color'=>'#0C447C'],
                            ];
                            $p = $pillMap[$appt->status] ?? ['bg'=>'#F3F4F6','color'=>'#6B7280'];
                        @endphp
                        <span class="pill" style="background:{{ $p['bg'] }};color:{{ $p['color'] }};">
                            {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                        </span>
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:var(--color-text-secondary);text-align:center;padding:1rem 0;">
                    No appointments yet.
                </p>
            @endforelse
        </div>

        {{-- Danger zone --}}
        <div class="card" style="border-color:#FCEBEB;">
            <div class="card-hdr">
                <span class="card-title" style="color:#791F1F;">Danger zone</span>
            </div>
            <p style="font-size:12px;color:var(--color-text-secondary);margin-bottom:10px;">
                Removing this doctor will permanently delete their account, schedules, and all associated data.
            </p>
            <form method="POST" action="{{ route('tenant.doctors.destroy', $doctor) }}"
                onsubmit="return confirm('Permanently remove Dr. {{ $doctor->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="tb-btn" style="border-color:#A32D2D;color:#A32D2D;">
                    <i class="ti ti-trash"></i> Remove doctor
                </button>
            </form>
        </div>

    </div>

</div>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.show-grid{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);gap:1rem;align-items:start;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:1.1rem;}
.card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;}
.card-title{font-size:14px;font-weight:500;}
.card-link{font-size:12px;color:#185FA5;cursor:pointer;text-decoration:none;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
.active-pill{background:#EAF3DE;color:#27500A;}
.inactive-pill{background:#F3F4F6;color:#6B7280;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.info-item{display:flex;flex-direction:column;gap:3px;}
.info-label{font-size:11px;color:var(--color-text-secondary);display:flex;align-items:center;gap:4px;}
.info-label i{font-size:13px;}
.info-value{font-size:13px;font-weight:500;}
.schedule-row{display:flex;align-items:center;gap:12px;padding:8px 0;}
.schedule-row-border{border-bottom:0.5px solid var(--color-border-tertiary);}
.day-pill{background:#E6F1FB;color:#0C447C;font-size:11px;font-weight:500;padding:3px 8px;border-radius:99px;}
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:0.875rem;}
.stat-lbl{font-size:11px;color:var(--color-text-secondary);display:flex;align-items:center;gap:5px;margin-bottom:5px;}
.stat-lbl i{font-size:13px;}
.stat-val{font-size:22px;font-weight:500;}
.appt-row{display:flex;align-items:center;gap:10px;padding:8px 0;}
.appt-row-border{border-bottom:0.5px solid var(--color-border-tertiary);}
</style>
@endsection