@extends('superadmin.layouts.app')

@section('title', 'Edit Plan')
@section('page_title', 'Edit Plan — ' . $plan->name)

@section('topbar_actions')
    <a href="{{ route('superadmin.plans.index') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
@endsection

@section('content')

<div style="max-width:600px;">
    <div class="card" style="padding:1.5rem;">

        <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="field">
                    <label>Plan Name <span style="color:#A32D2D;">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $plan->name) }}"
                           class="field-input" required />
                    @error('name')<p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label>Slug <span style="color:#A32D2D;">*</span></label>
                    <input type="text" name="slug"
                           value="{{ old('slug', $plan->slug) }}"
                           class="field-input" required />
                    @error('slug')<p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="field">
                <label>Monthly Price (৳) <span style="color:#A32D2D;">*</span></label>
                <input type="number" name="price"
                       value="{{ old('price', $plan->price) }}"
                       class="field-input" min="0" step="0.01" required />
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="field">
                    <label>Max Doctors</label>
                    <input type="number" name="max_doctors"
                           value="{{ old('max_doctors', $plan->max_doctors) }}"
                           class="field-input" min="-1" required />
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:3px;">-1 = unlimited</p>
                </div>
                <div class="field">
                    <label>Max Appointments</label>
                    <input type="number" name="max_appointments"
                           value="{{ old('max_appointments', $plan->max_appointments) }}"
                           class="field-input" min="-1" required />
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:3px;">-1 = unlimited</p>
                </div>
            </div>

            <div style="border:0.5px solid var(--color-border-tertiary);border-radius:8px;padding:1rem;margin-bottom:1rem;">
                <p style="font-size:12px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;margin-bottom:12px;">Features</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        ['sms_notifications', 'SMS Notifications'],
                        ['custom_domain',     'Custom Domain'],
                        ['excel_reports',     'Excel Reports'],
                    ] as [$key, $label])
                    <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;">
                        <span>{{ $label }}</span>
                        <input type="checkbox" name="{{ $key }}" value="1"
                               {{ old($key, $plan->{$key}) ? 'checked' : '' }}
                               style="width:16px;height:16px;" />
                    </label>
                    @endforeach

                    <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;">
                        <span>Active</span>
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                               style="width:16px;height:16px;" />
                    </label>
                </div>
            </div>

            <button type="submit" class="tb-btn primary" style="width:100%;justify-content:center;padding:9px;">
                <i class="ti ti-device-floppy"></i> Update Plan
            </button>
        </form>

    </div>
</div>

@endsection