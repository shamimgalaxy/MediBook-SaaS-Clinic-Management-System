@extends('superadmin.layouts.app')

@section('title', 'Clinics')
@section('page_title', 'Clinics')

@section('topbar_actions')
    <a href="{{ route('superadmin.tenants.create') }}" class="tb-btn primary">
        <i class="ti ti-plus"></i> Add clinic
    </a>
@endsection

@section('content')

{{-- Filters --}}
<div class="card mb-4" style="padding:0.875rem 1rem;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search clinic name…"
               class="filter-input" style="width:220px;" />

        <select name="status" class="filter-input">
            <option value="">All status</option>
            <option value="active"    @selected(request('status') === 'active')>Active</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            <option value="trial"     @selected(request('status') === 'trial')>On Trial</option>
        </select>

        <select name="plan_id" class="filter-input">
            <option value="">All plans</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" @selected(request('plan_id') == $plan->id)>
                    {{ $plan->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="tb-btn primary">Filter</button>
        @if(request()->hasAny(['search', 'status', 'plan_id']))
            <a href="{{ route('superadmin.tenants.index') }}" class="tb-btn">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card" style="padding:0;">
    <table>
        <thead style="padding:0 1.25rem;">
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                <th style="padding:10px 16px;">Clinic</th>
                <th style="padding:10px 16px;">Domain</th>
                <th style="padding:10px 16px;">Plan</th>
                <th style="padding:10px 16px;">Subscription</th>
                <th style="padding:10px 16px;">Status</th>
                <th style="padding:10px 16px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">

                {{-- Clinic --}}
                <td style="padding:10px 16px;">
                    <div style="font-weight:500;">{{ $tenant->clinic_name }}</div>
                    <div style="font-size:11px;color:var(--color-text-secondary);">{{ $tenant->id }}</div>
                </td>

                {{-- Domain --}}
                <td style="padding:10px 16px;font-size:12px;color:var(--color-text-secondary);">
                    {{ $tenant->domains->first()?->domain ?? '—' }}
                </td>

                {{-- Plan --}}
                <td style="padding:10px 16px;">
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
                        <span style="color:var(--color-text-secondary);font-size:12px;">No plan</span>
                    @endif
                </td>

                {{-- Subscription expiry --}}
                <td style="padding:10px 16px;font-size:12px;">
                    @if($tenant->on_trial && $tenant->trial_ends_at)
                        <span style="color:#D97706;">
                            Trial ends {{ $tenant->trial_ends_at->format('d M Y') }}
                        </span>
                    @elseif($tenant->plan_expires_at)
                        @if(now()->gt($tenant->plan_expires_at))
                            <span style="color:#A32D2D;">Expired {{ $tenant->plan_expires_at->format('d M Y') }}</span>
                        @else
                            <span style="color:#27500A;">Until {{ $tenant->plan_expires_at->format('d M Y') }}</span>
                        @endif
                    @else
                        <span style="color:var(--color-text-secondary);">—</span>
                    @endif
                </td>

                {{-- Status --}}
                <td style="padding:10px 16px;">
                    @if($tenant->is_active)
                        <span class="pill pill-green">Active</span>
                    @else
                        <span class="pill pill-red">Suspended</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;gap:8px;align-items:center;">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}"
                           style="font-size:12px;color:#185FA5;">Manage</a>

                        <form method="POST"
                              action="{{ route('superadmin.tenants.toggle', $tenant) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="background:none;border:none;cursor:pointer;font-size:12px;
                                           color:{{ $tenant->is_active ? '#A32D2D' : '#27500A' }};">
                                {{ $tenant->is_active ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:2.5rem;text-align:center;color:var(--color-text-secondary);">
                    No clinics found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($tenants->hasPages())
    <div style="padding:0.75rem 1rem;border-top:0.5px solid var(--color-border-tertiary);">
        {{ $tenants->links() }}
    </div>
    @endif
</div>

@endsection