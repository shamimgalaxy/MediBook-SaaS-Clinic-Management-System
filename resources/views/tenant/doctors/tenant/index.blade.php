{{-- resources/views/tenant/doctors/index.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Doctors')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Doctors</h1>
        <p class="page-sub">Manage your clinic's doctors and their schedules</p>
    </div>
    <a href="{{ route('tenant.doctors.create') }}" class="tb-btn primary">
        <i class="ti ti-plus"></i> Add doctor
    </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert-item info mb-4" style="background:#E6F1FB;color:#0C447C;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:13px;">
        <i class="ti ti-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Filters --}}
<div class="card mb-4" style="padding:0.75rem 1rem;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name or specialty…"
            style="flex:1;min-width:180px;padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);" />
        <select name="status"
            style="padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);">
            <option value="">All status</option>
            <option value="1" @selected(request('status')==='1')>Active</option>
            <option value="0" @selected(request('status')==='0')>Inactive</option>
        </select>
        <button type="submit" class="tb-btn primary" style="padding:6px 14px;">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('tenant.doctors.index') }}" class="tb-btn" style="padding:6px 14px;">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card" style="padding:0;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;">Doctor</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;">Specialty</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;">Experience</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;">Fee</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;">Schedule</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;">Status</th>
                <th style="padding:10px 16px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($doctors as $doctor)
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">
                {{-- Doctor name + avatar --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($doctor->avatar)
                            <img src="{{ Storage::url($doctor->avatar) }}" alt=""
                                style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;" />
                        @else
                            <div style="width:34px;height:34px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:#0C447C;flex-shrink:0;">
                                {{ $doctor->initials }}
                            </div>
                        @endif
                        <div>
                            <div style="font-weight:500;">Dr. {{ $doctor->name }}</div>
                            <div style="font-size:11px;color:var(--color-text-secondary);">{{ $doctor->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding:10px 16px;">{{ $doctor->specialty }}</td>
                <td style="padding:10px 16px;">{{ $doctor->experience_years }} yr{{ $doctor->experience_years != 1 ? 's' : '' }}</td>
                <td style="padding:10px 16px;">৳{{ number_format($doctor->consultation_fee) }}</td>
                {{-- Schedule days --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        @forelse($doctor->activeSchedules as $sch)
                            <span style="background:#E6F1FB;color:#0C447C;font-size:10px;font-weight:500;padding:2px 6px;border-radius:99px;">
                                {{ substr(\App\Models\DoctorSchedule::DAYS[$sch->day_of_week], 0, 3) }}
                            </span>
                        @empty
                            <span style="font-size:12px;color:var(--color-text-secondary);">—</span>
                        @endforelse
                    </div>
                </td>
                {{-- Status toggle --}}
                <td style="padding:10px 16px;">
                    <form method="POST" action="{{ route('tenant.doctors.toggle', $doctor) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            style="background:none;border:none;cursor:pointer;padding:0;"
                            title="Click to toggle">
                            @if($doctor->is_active)
                                <span class="pill" style="background:#EAF3DE;color:#27500A;">Active</span>
                            @else
                                <span class="pill" style="background:#F3F4F6;color:#6B7280;">Inactive</span>
                            @endif
                        </button>
                    </form>
                </td>
                {{-- Actions --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <a href="{{ route('tenant.doctors.show', $doctor) }}"
                            title="View" style="color:#185FA5;"><i class="ti ti-eye" style="font-size:16px;"></i></a>
                        <a href="{{ route('tenant.doctors.edit', $doctor) }}"
                            title="Edit" style="color:#185FA5;"><i class="ti ti-edit" style="font-size:16px;"></i></a>
                        <form method="POST" action="{{ route('tenant.doctors.destroy', $doctor) }}"
                            onsubmit="return confirm('Remove Dr. {{ $doctor->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;cursor:pointer;color:#A32D2D;" title="Delete">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding:2rem;text-align:center;color:var(--color-text-secondary);font-size:13px;">
                    No doctors yet. <a href="{{ route('tenant.doctors.create') }}" style="color:#185FA5;">Add the first one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($doctors->hasPages())
        <div style="padding:0.75rem 1rem;border-top:0.5px solid var(--color-border-tertiary);">
            {{ $doctors->links() }}
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
</style>
@endsection