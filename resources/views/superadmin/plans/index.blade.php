@extends('superadmin.layouts.app')

@section('title', 'Plans')
@section('page_title', 'Subscription Plans')

@section('topbar_actions')
    <a href="{{ route('superadmin.plans.create') }}" class="tb-btn primary">
        <i class="ti ti-plus"></i> New plan
    </a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
    @forelse($plans as $plan)
    <div class="card" style="padding:1.25rem;">

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
            <div>
                <div style="font-size:16px;font-weight:600;">{{ $plan->name }}</div>
                <div style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">
                    slug: {{ $plan->slug }}
                </div>
            </div>
            @if($plan->is_active)
                <span class="pill pill-green">Active</span>
            @else
                <span class="pill pill-red">Inactive</span>
            @endif
        </div>

        {{-- Price --}}
        <div style="font-size:28px;font-weight:700;color:#185FA5;margin-bottom:1rem;">
            ৳{{ number_format($plan->price, 0) }}
            <span style="font-size:13px;font-weight:400;color:var(--color-text-secondary);">/ month</span>
        </div>

        {{-- Features --}}
        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:1rem;font-size:13px;">
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--color-text-secondary);">Doctors</span>
                <span style="font-weight:500;">
                    {{ $plan->max_doctors === -1 ? 'Unlimited' : $plan->max_doctors }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--color-text-secondary);">Appointments</span>
                <span style="font-weight:500;">
                    {{ $plan->max_appointments === -1 ? 'Unlimited' : $plan->max_appointments }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--color-text-secondary);">SMS Notifications</span>
                <span style="font-weight:500;">
                    {!! $plan->sms_notifications ? '<span style="color:#27500A;">✓</span>' : '<span style="color:#A32D2D;">✗</span>' !!}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--color-text-secondary);">Custom Domain</span>
                <span style="font-weight:500;">
                    {!! $plan->custom_domain ? '<span style="color:#27500A;">✓</span>' : '<span style="color:#A32D2D;">✗</span>' !!}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--color-text-secondary);">Excel Reports</span>
                <span style="font-weight:500;">
                    {!! $plan->excel_reports ? '<span style="color:#27500A;">✓</span>' : '<span style="color:#A32D2D;">✗</span>' !!}
                </span>
            </div>
        </div>

        {{-- Clinics using --}}
        <div style="background:var(--color-background-secondary);border-radius:6px;padding:8px 12px;font-size:12px;margin-bottom:1rem;">
            <span style="color:var(--color-text-secondary);">Clinics on this plan:</span>
            <span style="font-weight:600;margin-left:4px;">{{ $plan->tenants_count }}</span>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:6px;">
            <a href="{{ route('superadmin.plans.edit', $plan) }}"
               class="tb-btn" style="flex:1;justify-content:center;">
                <i class="ti ti-edit"></i> Edit
            </a>

            <form method="POST" action="{{ route('superadmin.plans.toggle', $plan) }}">
                @csrf @method('PATCH')
                <button type="submit" class="tb-btn"
                        style="{{ $plan->is_active ? 'color:#A32D2D;' : 'color:#27500A;' }}">
                    <i class="ti ti-{{ $plan->is_active ? 'ban' : 'circle-check' }}"></i>
                </button>
            </form>

            @if($plan->tenants_count === 0)
            <form method="POST" action="{{ route('superadmin.plans.destroy', $plan) }}"
                  onsubmit="return confirm('Delete {{ $plan->name }} plan?')">
                @csrf @method('DELETE')
                <button type="submit" class="tb-btn" style="color:#A32D2D;">
                    <i class="ti ti-trash"></i>
                </button>
            </form>
            @endif
        </div>

    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--color-text-secondary);">
        No plans yet.
        <a href="{{ route('superadmin.plans.create') }}" style="color:#185FA5;">Create the first one</a>.
    </div>
    @endforelse
</div>

@endsection