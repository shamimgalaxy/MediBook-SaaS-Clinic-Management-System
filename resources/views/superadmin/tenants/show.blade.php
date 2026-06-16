@extends('superadmin.layouts.app')

@section('title', $tenant->clinic_name)
@section('page_title', $tenant->clinic_name)

@section('topbar_actions')
    <a href="{{ route('superadmin.tenants.index') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
    <form method="POST" action="{{ route('superadmin.tenants.toggle', $tenant) }}">
        @csrf @method('PATCH')
        <button type="submit" class="tb-btn"
                style="{{ $tenant->is_active ? 'color:#A32D2D;' : 'color:#27500A;' }}">
            <i class="ti ti-{{ $tenant->is_active ? 'ban' : 'circle-check' }}"></i>
            {{ $tenant->is_active ? 'Suspend' : 'Activate' }}
        </button>
    </form>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

    {{-- Clinic info --}}
    <div class="card" style="padding:1.25rem;">
        <div style="font-size:13px;font-weight:500;text-transform:uppercase;letter-spacing:0.4px;color:var(--color-text-secondary);margin-bottom:1rem;">Clinic Info</div>

        <div style="display:flex;flex-direction:column;gap:0;">
            @foreach([
                ['Clinic Name',  $tenant->clinic_name],
                ['Tenant ID',    $tenant->id],
                ['Domain',       $tenant->domains->first()?->domain ?? '—'],
                ['Status',       $tenant->is_active ? 'Active' : 'Suspended'],
                ['Created',      $tenant->created_at->format('d M Y')],
            ] as [$label, $value])
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:0.5px solid var(--color-border-tertiary);font-size:13px;">
                <span style="color:var(--color-text-secondary);">{{ $label }}</span>
                <span style="font-weight:500;">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Subscription info --}}
    <div class="card" style="padding:1.25rem;">
        <div style="font-size:13px;font-weight:500;text-transform:uppercase;letter-spacing:0.4px;color:var(--color-text-secondary);margin-bottom:1rem;">Subscription</div>

        @if($tenant->on_trial)
        <div style="background:#FAEEDA;border-radius:6px;padding:10px 14px;font-size:13px;color:#633806;margin-bottom:1rem;">
            <i class="ti ti-clock"></i>
            On trial — ends {{ $tenant->trial_ends_at?->format('d M Y') ?? 'N/A' }}
            ({{ $tenant->daysUntilExpiry() }} days left)
        </div>
        @endif

        @if($tenant->subscriptionPlan)
        <div style="display:flex;flex-direction:column;gap:0;margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:0.5px solid var(--color-border-tertiary);font-size:13px;">
                <span style="color:var(--color-text-secondary);">Current Plan</span>
                <span style="font-weight:500;">{{ $tenant->subscriptionPlan->name }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:0.5px solid var(--color-border-tertiary);font-size:13px;">
                <span style="color:var(--color-text-secondary);">Price</span>
                <span style="font-weight:500;">৳{{ number_format($tenant->subscriptionPlan->price, 0) }}/mo</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;">
                <span style="color:var(--color-text-secondary);">Expires</span>
                <span style="font-weight:500;
                    {{ $tenant->plan_expires_at && now()->gt($tenant->plan_expires_at) ? 'color:#A32D2D;' : 'color:#27500A;' }}">
                    {{ $tenant->plan_expires_at?->format('d M Y') ?? '—' }}
                </span>
            </div>
        </div>
        @else
        <p style="font-size:13px;color:var(--color-text-secondary);margin-bottom:1rem;">No plan assigned.</p>
        @endif

        {{-- Assign / renew plan --}}
        <div style="border-top:0.5px solid var(--color-border-tertiary);padding-top:1rem;">
            <p style="font-size:12px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;margin-bottom:10px;">
                {{ $tenant->subscriptionPlan ? 'Renew / Change Plan' : 'Assign Plan' }}
            </p>
            <form method="POST" action="{{ route('superadmin.tenants.assignPlan', $tenant) }}">
                @csrf @method('PATCH')

                <div class="field">
                    <label>Plan</label>
                    <select name="plan_id" class="field-input" required>
                        <option value="">Select plan…</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                    @selected($tenant->plan_id == $plan->id)>
                                {{ $plan->name }} — ৳{{ number_format($plan->price, 0) }}/mo
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Duration (days)</label>
                    <select name="duration_days" class="field-input">
                        <option value="30">30 days (1 month)</option>
                        <option value="90">90 days (3 months)</option>
                        <option value="180">180 days (6 months)</option>
                        <option value="365">365 days (1 year)</option>
                    </select>
                </div>

                <button type="submit" class="tb-btn primary"
                        style="width:100%;justify-content:center;padding:8px;">
                    <i class="ti ti-credit-card"></i>
                    {{ $tenant->subscriptionPlan ? 'Update Subscription' : 'Assign Plan' }}
                </button>
            </form>
        </div>
    </div>

</div>

@endsection