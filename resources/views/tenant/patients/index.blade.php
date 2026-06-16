{{-- resources/views/tenant/patients/index.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Patients')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Patients</h1>
        <p class="page-sub">Manage all registered patients in your clinic</p>
    </div>
    <a href="{{ route('tenant.patients.create') }}" class="tb-btn primary">
        <i class="ti ti-plus"></i> Add patient
    </a>
</div>

@if(session('success'))
    <div class="flash-success">
        <i class="ti ti-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Filters --}}
<div class="card mb-4" style="padding:0.75rem 1rem;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search name, email or phone…" class="filter-input" style="flex:1;min-width:180px;" />

        <select name="gender" class="filter-select">
            <option value="">All genders</option>
            @foreach($genders as $val => $label)
                <option value="{{ $val }}" @selected(request('gender') === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="blood_group" class="filter-select">
            <option value="">All blood groups</option>
            @foreach($bloodGroups as $bg)
                <option value="{{ $bg }}" @selected(request('blood_group') === $bg)>{{ $bg }}</option>
            @endforeach
        </select>

        <select name="status" class="filter-select">
            <option value="">All status</option>
            <option value="1" @selected(request('status') === '1')>Active</option>
            <option value="0" @selected(request('status') === '0')>Inactive</option>
        </select>

        <button type="submit" class="tb-btn primary" style="padding:6px 14px;">Filter</button>
        @if(request()->hasAny(['search','gender','blood_group','status']))
            <a href="{{ route('tenant.patients.index') }}" class="tb-btn" style="padding:6px 14px;">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card" style="padding:0;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Phone</th>
                <th>Age / Gender</th>
                <th>Blood group</th>
                <th>Appointments</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($patients as $patient)
            <tr class="table-row-hover">
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="avatar-initials">{{ $patient->initials }}</div>
                        <div>
                            <div style="font-weight:500;">{{ $patient->name }}</div>
                            <div style="font-size:11px;color:var(--color-text-secondary);">{{ $patient->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $patient->phone ?? '—' }}</td>
                <td>
                    {{ $patient->age ? $patient->age . ' yrs' : '—' }}
                    @if($patient->gender)
                        · {{ ucfirst($patient->gender) }}
                    @endif
                </td>
                <td>
                    @if($patient->blood_group)
                        <span class="blood-pill">{{ $patient->blood_group }}</span>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $patient->appointments_count ?? $patient->appointments->count() }}</td>
                <td>
                    <form method="POST" action="{{ route('tenant.patients.toggle', $patient) }}">
                        @csrf @method('PATCH')
                        <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;">
                            @if($patient->is_active)
                                <span class="pill active-pill">Active</span>
                            @else
                                <span class="pill inactive-pill">Inactive</span>
                            @endif
                        </button>
                    </form>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <a href="{{ route('tenant.patients.show', $patient) }}" title="View" style="color:#185FA5;">
                            <i class="ti ti-eye" style="font-size:16px;"></i>
                        </a>
                        <a href="{{ route('tenant.patients.edit', $patient) }}" title="Edit" style="color:#185FA5;">
                            <i class="ti ti-edit" style="font-size:16px;"></i>
                        </a>
                        <form method="POST" action="{{ route('tenant.patients.destroy', $patient) }}"
                            onsubmit="return confirm('Remove {{ $patient->name }}? This cannot be undone.')">
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
                    No patients yet. <a href="{{ route('tenant.patients.create') }}" style="color:#185FA5;">Add the first one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($patients->hasPages())
        <div style="padding:0.75rem 1rem;border-top:0.5px solid var(--color-border-tertiary);">
            {{ $patients->links() }}
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
.flash-success{background:#E6F1FB;color:#0C447C;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:13px;margin-bottom:1rem;}
.filter-input,.filter-select{padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.data-table{width:100%;border-collapse:collapse;font-size:13px;}
.data-table th{text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);padding:10px 16px;border-bottom:0.5px solid var(--color-border-tertiary);text-transform:uppercase;letter-spacing:0.4px;}
.data-table td{padding:10px 16px;border-bottom:0.5px solid var(--color-border-tertiary);vertical-align:middle;}
.data-table tr:last-child td{border-bottom:none;}
.table-row-hover:hover{background:var(--color-background-secondary);}
.avatar-initials{width:34px;height:34px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:#0C447C;flex-shrink:0;}
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
.active-pill{background:#EAF3DE;color:#27500A;}
.inactive-pill{background:#F3F4F6;color:#6B7280;}
.blood-pill{background:#FAEEDA;color:#633806;font-size:11px;font-weight:500;padding:2px 8px;border-radius:99px;}
</style>
@endsection