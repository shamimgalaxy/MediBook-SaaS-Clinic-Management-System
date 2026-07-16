@extends('tenant.layouts.app')

@section('title', 'Subscription')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 22px; font-weight: 600; color: var(--color-text-primary);">
            Subscription Plan
        </h1>
        <p style="font-size: 13px; color: var(--color-text-secondary); margin-top: 4px;">
            Manage your clinic's subscription and billing.
        </p>
    </div>

    {{-- ── Current Plan Status ── --}}
    <div style="background: var(--color-background-primary); border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-lg); padding: 1.25rem; margin-bottom: 1.5rem;">
        <p style="font-size: 12px; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Current Status
        </p>

        @if($tenant->on_trial)
            <p style="font-size: 15px; font-weight: 600; color: #185FA5;">
                On Trial
                @if($tenant->trial_ends_at)
                    — ends {{ \Illuminate\Support\Carbon::parse($tenant->trial_ends_at)->format('d M Y') }}
                @endif
            </p>
        @elseif($tenant->plan_id && $tenant->plan_expires_at)
            <p style="font-size: 15px; font-weight: 600; color: #27500A;">
                {{ $tenant->plan?->name ?? 'Active Plan' }}
                — valid until {{ \Illuminate\Support\Carbon::parse($tenant->plan_expires_at)->format('d M Y') }}
            </p>
        @else
            <p style="font-size: 15px; font-weight: 600; color: #A32D2D;">
                No Active Subscription
            </p>
        @endif
    </div>

    {{-- ── Plan Selection Form ── --}}
    <form method="POST" action="{{ route('subscription.pay') }}">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            @forelse ($plans as $plan)
                <label style="display: block; cursor: pointer;">
                    <input type="radio" name="plan_id" value="{{ $plan->id }}" required
                           style="position: absolute; opacity: 0;" class="plan-radio">

                    <div style="background: var(--color-background-primary); border: 1px solid var(--color-border-tertiary); border-radius: var(--border-radius-lg); padding: 1.25rem; height: 100%;">
                        <p style="font-size: 15px; font-weight: 600; color: var(--color-text-primary); margin-bottom: 4px;">
                            {{ $plan->name }}
                        </p>
                        <p style="font-size: 22px; font-weight: 700; color: #185FA5; margin-bottom: 10px;">
                            ৳{{ number_format($plan->price, 0) }}<span style="font-size: 12px; font-weight: 400; color: var(--color-text-secondary);">/mo</span>
                        </p>

                        <ul style="list-style: none; font-size: 12px; color: var(--color-text-secondary); display: flex; flex-direction: column; gap: 6px;">
                            <li>
                                <i class="ti ti-check" style="color: #27500A;"></i>
                                {{ $plan->isUnlimited('max_doctors') ? 'Unlimited doctors' : $plan->max_doctors . ' doctors' }}
                            </li>
                            <li>
                                <i class="ti ti-check" style="color: #27500A;"></i>
                                {{ $plan->isUnlimited('max_appointments') ? 'Unlimited appointments' : $plan->max_appointments . ' appointments/mo' }}
                            </li>
                            @if($plan->sms_notifications)
                                <li><i class="ti ti-check" style="color: #27500A;"></i> SMS notifications</li>
                            @endif
                            @if($plan->custom_domain)
                                <li><i class="ti ti-check" style="color: #27500A;"></i> Custom domain</li>
                            @endif
                            @if($plan->excel_reports)
                                <li><i class="ti ti-check" style="color: #27500A;"></i> Excel reports</li>
                            @endif
                        </ul>
                    </div>
                </label>
            @empty
                <p style="color: var(--color-text-secondary); font-size: 13px;">
                    No plans are currently available. Please contact support.
                </p>
            @endforelse
        </div>

        @if($plans->isNotEmpty())
            <div style="background: var(--color-background-primary); border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-lg); padding: 1.25rem; margin-bottom: 1.5rem;">
                <p style="font-size: 13px; font-weight: 500; margin-bottom: 10px;">Billing Duration</p>
                <select name="duration_days" required
                        style="border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-md); padding: 8px 10px; font-size: 13px; width: 100%; max-width: 260px;">
                    <option value="30">1 Month</option>
                    <option value="90">3 Months</option>
                    <option value="180">6 Months</option>
                    <option value="365">12 Months</option>
                </select>
            </div>

            <button type="submit"
                    style="background: #185FA5; color: #fff; border: none; border-radius: var(--border-radius-md); padding: 10px 20px; font-size: 13px; font-weight: 500; cursor: pointer;">
                Proceed to Payment
            </button>
        @endif
    </form>

</div>

<style>
    .plan-radio:checked + div {
        border-color: #185FA5 !important;
        box-shadow: 0 0 0 2px rgba(24, 95, 165, 0.15);
    }
</style>
@endsection