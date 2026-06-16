@extends('superadmin.layouts.app')

@section('title', 'Add Clinic')
@section('page_title', 'Add New Clinic')

@section('topbar_actions')
    <a href="{{ route('superadmin.tenants.index') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
@endsection

@section('content')

<div style="max-width:560px;">
    <div class="card" style="padding:1.5rem;">
        <form method="POST" action="{{ route('superadmin.tenants.store') }}">
            @csrf

            <div class="field">
                <label>Clinic Name <span style="color:#A32D2D;">*</span></label>
                <input type="text" name="clinic_name"
                       value="{{ old('clinic_name') }}"
                       placeholder="e.g. Karim Clinic"
                       class="field-input" required />
                @error('clinic_name')
                    <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label>Domain <span style="color:#A32D2D;">*</span></label>
                <div style="display:flex;align-items:center;gap:0;">
                    <input type="text" name="domain"
                           value="{{ old('domain') }}"
                           placeholder="karimclinic"
                           class="field-input"
                           style="border-radius:6px 0 0 6px;flex:1;" required />
                    <span style="padding:7px 10px;background:var(--color-background-secondary);border:0.5px solid var(--color-border-tertiary);border-left:none;border-radius:0 6px 6px 0;font-size:13px;color:var(--color-text-secondary);white-space:nowrap;">
                        .medibook.com.bd
                    </span>
                </div>
                @error('domain')
                    <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label>Assign Plan</label>
                <select name="plan_id" class="field-input">
                    <option value="">No plan (trial only)</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                            {{ $plan->name }} — ৳{{ number_format($plan->price, 0) }}/mo
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Trial settings --}}
            <div style="border:0.5px solid var(--color-border-tertiary);border-radius:8px;padding:1rem;margin-bottom:1rem;">
                <p style="font-size:12px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;margin-bottom:12px;">Trial Settings</p>

                <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;margin-bottom:10px;">
                    <span>Start on trial</span>
                    <input type="checkbox" name="on_trial" value="1"
                           {{ old('on_trial', true) ? 'checked' : '' }}
                           style="width:16px;height:16px;" />
                </label>

                <div class="field" style="margin-bottom:0;">
                    <label>Trial Duration (days)</label>
                    <input type="number" name="trial_days"
                           value="{{ old('trial_days', 14) }}"
                           class="field-input" style="width:120px;"
                           min="1" max="90" />
                </div>
            </div>

            <button type="submit" class="tb-btn primary"
                    style="width:100%;justify-content:center;padding:9px;">
                <i class="ti ti-building-hospital"></i> Create Clinic
            </button>
        </form>
    </div>
</div>

@endsection