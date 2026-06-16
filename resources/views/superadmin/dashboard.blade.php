@extends('superadmin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Super Admin Dashboard')

@section('topbar_actions')
    <a href="{{ route('superadmin.tenants.create') }}" class="tb-btn primary">
        <i class="ti ti-plus"></i> Add clinic
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-building-hospital"></i> Total clinics</div>
        <div class="stat-val">{{ $stats['total_tenants'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-circle-check"></i> Active</div>
        <div class="stat-val" style="color:#27500A;">{{ $stats['active_tenants'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-clock"></i> On trial</div>
        <div class="stat-val" style="color:#D97706;">{{ $stats['on_trial'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-clock-exclamation"></i> Trials expiring</div>
        <div class="stat-val" style="color:#A32D2D;">{{ $stats['trials_expiring'] }}</div>
        <div class="stat-sub stat-down">In next 7 days</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="ti ti-credit-card"></i> Plans</div>
        <div class="stat-val">{{ $plans->count() }}</div>
    </div>
</div>

{{-- Two column --}}
<div style="display:grid;grid-template-columns:1.6fr 1fr;gap:1rem;">

    {{-- Recent clinics --}}
    <div class="card" style="padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <span style="font-size:14px;font-weight:500;">Recent Clinics</span>
            <a href="{{ route('superadmin.tenants.index') }}" style="font-size:12px;color:#185FA5;">View all</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Clinic</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTenants as $tenant)
                <tr>
                    <td>
                        <div style="font-weight:500;">{{ $tenant->clinic_name }}</div>
                        <div style="font-size:11px;color:var(--color-text-secondary);">
                            {{ $tenant->domains->first()?->domain ?? $tenant->id }}
                        </div>
                    </td>
                    <td>
                        @if($tenant->subscriptionPlan)
                            @php
                                $cls = match($tenant->subscriptionPlan->slug) {
                                    'pro'        => 'plan-pro',
                                    'enterprise' => 'plan-ent',
                                    default      => 'plan-basic',
                                };
                            @endphp
                            <span class="{{ $cls }}">{{ $tenant->subscriptionPlan->name }}</span>
                        @elseif($tenant->on_trial)
                            <span class="pill pill-amber">Trial</span>
                        @else
                            <span style="color:var(--color-text-secondary);font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($tenant->is_active)
                            <span class="pill pill-green">Active</span>
                        @else
                            <span class="pill pill-red">Suspended</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}"
                           style="font-size:12px;color:#185FA5;">Manage</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--color-text-secondary);padding:1.5rem 0;">
                        No clinics yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Plans summary --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:14px;font-weight:500;margin-bottom:1rem;">Plans Overview</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($plans as $plan)
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:13px;">{{ $plan->name }}</span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:12px;color:var(--color-text-secondary);">
                            {{ $plan->tenants_count }} clinic{{ $plan->tenants_count !== 1 ? 's' : '' }}
                        </span>
                        <span style="font-weight:500;font-size:13px;color:#185FA5;">
                            ৳{{ number_format($plan->price, 0) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top:1rem;padding-top:1rem;border-top:0.5px solid var(--color-border-tertiary);">
                <a href="{{ route('superadmin.plans.index') }}" style="font-size:12px;color:#185FA5;">
                    Manage plans →
                </a>
            </div>
        </div>
    </div>

</div>

@endsection