@extends('tenant.layouts.app')

@section('title', 'Settings')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Clinic Settings</h1>
        <p class="page-sub">Manage your clinic profile, working hours and preferences</p>
    </div>
</div>

{{-- Tab navigation --}}
<div style="display:flex;gap:4px;margin-bottom:1.25rem;border-bottom:0.5px solid var(--color-border-tertiary);padding-bottom:0;" x-data="{ tab: '{{ session('tab', 'general') }}' }">

    <button @click="tab='general'"
            :style="tab==='general' ? 'border-bottom:2px solid #185FA5;color:#185FA5;' : ''"
            style="padding:8px 16px;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);border-bottom:2px solid transparent;margin-bottom:-0.5px;">
        <i class="ti ti-building-hospital" style="font-size:14px;vertical-align:-2px;margin-right:4px;"></i>
        General
    </button>
    <button @click="tab='hours'"
            :style="tab==='hours' ? 'border-bottom:2px solid #185FA5;color:#185FA5;' : ''"
            style="padding:8px 16px;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);border-bottom:2px solid transparent;margin-bottom:-0.5px;">
        <i class="ti ti-clock" style="font-size:14px;vertical-align:-2px;margin-right:4px;"></i>
        Working Hours
    </button>
    <button @click="tab='notifications'"
            :style="tab==='notifications' ? 'border-bottom:2px solid #185FA5;color:#185FA5;' : ''"
            style="padding:8px 16px;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);border-bottom:2px solid transparent;margin-bottom:-0.5px;">
        <i class="ti ti-bell" style="font-size:14px;vertical-align:-2px;margin-right:4px;"></i>
        Notifications
    </button>
    <button @click="tab='invoice'"
            :style="tab==='invoice' ? 'border-bottom:2px solid #185FA5;color:#185FA5;' : ''"
            style="padding:8px 16px;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);border-bottom:2px solid transparent;margin-bottom:-0.5px;">
        <i class="ti ti-receipt" style="font-size:14px;vertical-align:-2px;margin-right:4px;"></i>
        Invoice
    </button>

{{-- ── General Settings ── --}}
<div x-show="tab==='general'" style="display:grid;grid-template-columns:1.2fr 1fr;gap:1rem;">

    <div class="card" style="padding:1.25rem;">
        <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Clinic Information</p>

        <form method="POST" action="{{ route('settings.general') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label>Clinic Name <span style="color:#A32D2D;">*</span></label>
                <input type="text" name="clinic_name"
                       value="{{ old('clinic_name', $settings->clinic_name) }}"
                       class="field-input" required />
                @error('clinic_name')
                    <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label>Tagline</label>
                <input type="text" name="tagline"
                       value="{{ old('tagline', $settings->tagline) }}"
                       placeholder="e.g. Your health, our priority"
                       class="field-input" />
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $settings->phone) }}"
                           placeholder="01XXXXXXXXX"
                           class="field-input" />
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', $settings->email) }}"
                           placeholder="clinic@example.com"
                           class="field-input" />
                </div>
            </div>

            <div class="field">
                <label>Website</label>
                <input type="url" name="website"
                       value="{{ old('website', $settings->website) }}"
                       placeholder="https://example.com"
                       class="field-input" />
            </div>

            <div class="field">
                <label>Address</label>
                <textarea name="address" rows="2"
                          placeholder="Full clinic address…"
                          class="field-input"
                          style="resize:none;">{{ old('address', $settings->address) }}</textarea>
            </div>

            <button type="submit" class="tb-btn primary"
                    style="width:100%;justify-content:center;padding:9px;">
                <i class="ti ti-device-floppy"></i> Save General Info
            </button>
        </form>
    </div>

    {{-- Logo upload --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="card" style="padding:1.25rem;">
            <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Clinic Logo</p>

            {{-- Current logo --}}
            @if($settings->logo)
            <div style="margin-bottom:1rem;text-align:center;">
                <img src="{{ $settings->logoUrl() }}"
                     alt="Clinic Logo"
                     style="max-height:80px;max-width:200px;object-fit:contain;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:8px;" />
                <div style="margin-top:8px;">
                    <form method="POST" action="{{ route('settings.logo.delete') }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="font-size:12px;color:#A32D2D;background:none;border:none;cursor:pointer;">
                            <i class="ti ti-trash" style="font-size:13px;"></i> Remove logo
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div style="border:2px dashed var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:2rem;text-align:center;margin-bottom:1rem;">
                <i class="ti ti-photo" style="font-size:28px;color:var(--color-text-secondary);display:block;margin-bottom:6px;"></i>
                <p style="font-size:12px;color:var(--color-text-secondary);">No logo uploaded</p>
            </div>
            @endif

            <form method="POST" action="{{ route('settings.general') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label>Upload Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="field-input" style="padding:5px;" />
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:3px;">
                        JPG, PNG, WebP. Max 2MB. Recommended: 200×80px.
                    </p>
                    @error('logo')
                        <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="tb-btn primary"
                        style="width:100%;justify-content:center;padding:8px;">
                    <i class="ti ti-upload"></i> Upload Logo
                </button>
            </form>
        </div>
    </div>

</div>

{{-- ── Working Hours ── --}}
<div x-show="tab==='hours'" style="max-width:640px;">
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Working Hours</p>

        <form method="POST" action="{{ route('settings.hours') }}">
            @csrf

            @php
                $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                $hours = $settings->working_hours ?? \App\Models\ClinicSetting::defaultWorkingHours();
            @endphp

            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach($days as $day)
                @php $h = $hours[$day] ?? ['open'=>true,'start'=>'09:00','end'=>'17:00']; @endphp
                <div x-data="{ open: {{ $h['open'] ? 'true' : 'false' }} }"
                     style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:0.5px solid var(--color-border-tertiary);">

                    {{-- Day toggle --}}
                    <div style="width:110px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox"
                                   name="days[{{ $day }}][open]"
                                   value="1"
                                   {{ $h['open'] ? 'checked' : '' }}
                                   @change="open = $event.target.checked"
                                   style="width:15px;height:15px;" />
                            <span style="font-size:13px;font-weight:500;text-transform:capitalize;">
                                {{ $day }}
                            </span>
                        </label>
                    </div>

                    {{-- Time inputs --}}
                    <div style="display:flex;align-items:center;gap:8px;flex:1;"
                         :style="open ? '' : 'opacity:0.4;pointer-events:none;'">
                        <input type="time"
                               name="days[{{ $day }}][start]"
                               value="{{ $h['start'] }}"
                               :disabled="!open"
                               style="padding:6px 8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);" />
                        <span style="font-size:12px;color:var(--color-text-secondary);">to</span>
                        <input type="time"
                               name="days[{{ $day }}][end]"
                               value="{{ $h['end'] }}"
                               :disabled="!open"
                               style="padding:6px 8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);" />
                    </div>

                    {{-- Closed label --}}
                    <div x-show="!open"
                         style="font-size:12px;color:#A32D2D;font-weight:500;">
                        Closed
                    </div>
                    <div x-show="open"
                         style="font-size:12px;color:#27500A;font-weight:500;">
                        Open
                    </div>

                </div>
                @endforeach
            </div>

            <button type="submit" class="tb-btn primary"
                    style="width:100%;justify-content:center;padding:9px;margin-top:1rem;">
                <i class="ti ti-device-floppy"></i> Save Working Hours
            </button>
        </form>
    </div>
</div>

{{-- ── Notifications ── --}}
<div x-show="tab==='notifications'" style="max-width:560px;">
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Notification Preferences</p>

        <form method="POST" action="{{ route('settings.notifications') }}">
            @csrf

            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach([
                    ['notify_appointment_booked', 'New appointment booked',    'Send notification when a new appointment is created'],
                    ['notify_appointment_status', 'Appointment status changed', 'Notify when appointment status is updated'],
                    ['notify_payment_received',   'Payment received',           'Notify when a payment is recorded'],
                    ['notify_sms_enabled',        'Enable SMS notifications',   'Send SMS to patients (requires SMS provider setup)'],
                ] as [$key, $label, $description])
                <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:12px 0;border-bottom:0.5px solid var(--color-border-tertiary);gap:16px;">
                    <div>
                        <p style="font-size:13px;font-weight:500;">{{ $label }}</p>
                        <p style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">{{ $description }}</p>
                    </div>
                    <label style="position:relative;display:inline-block;width:42px;height:24px;flex-shrink:0;cursor:pointer;">
                        <input type="checkbox"
                               name="{{ $key }}"
                               value="1"
                               {{ $settings->{$key} ? 'checked' : '' }}
                               style="opacity:0;width:0;height:0;" />
                        <span x-data
                              style="position:absolute;inset:0;border-radius:99px;background:{{ $settings->{$key} ? '#185FA5' : '#e5e7eb' }};transition:background 0.2s;">
                            <span style="position:absolute;left:{{ $settings->{$key} ? '20px' : '3px' }};top:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:left 0.2s;"></span>
                        </span>
                    </label>
                </div>
                @endforeach
            </div>

            <button type="submit" class="tb-btn primary"
                    style="width:100%;justify-content:center;padding:9px;margin-top:1rem;">
                <i class="ti ti-device-floppy"></i> Save Preferences
            </button>
        </form>
    </div>
</div>

{{-- ── Invoice Settings ── --}}
<div x-show="tab==='invoice'" style="max-width:560px;">
    <div class="card" style="padding:1.25rem;">
        <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Invoice Settings</p>

        <form method="POST" action="{{ route('settings.general') }}">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="field">
                    <label>Invoice Number Prefix</label>
                    <input type="text" name="invoice_prefix"
                           value="{{ old('invoice_prefix', $settings->invoice_prefix ?? 'INV') }}"
                           placeholder="INV"
                           class="field-input"
                           maxlength="10" />
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:3px;">
                        e.g. INV → INV-2025-00001
                    </p>
                </div>
                <div class="field">
                    <label>Default Tax (%)</label>
                    <input type="number" name="default_tax"
                           value="{{ old('default_tax', $settings->default_tax ?? 0) }}"
                           placeholder="0"
                           class="field-input"
                           min="0" max="100" step="0.01" />
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:3px;">
                        Applied to all new invoices
                    </p>
                </div>
            </div>

            <div class="field">
                <label>Invoice Footer Note</label>
                <textarea name="invoice_footer_note" rows="3"
                          placeholder="e.g. Thank you for choosing our clinic. Payment due within 7 days."
                          class="field-input"
                          style="resize:none;">{{ old('invoice_footer_note', $settings->invoice_footer_note) }}</textarea>
            </div>

            {{-- Preview --}}
            <div style="background:var(--color-background-secondary);border-radius:var(--border-radius-md);padding:12px;margin-bottom:1rem;font-size:12px;">
                <p style="font-weight:500;margin-bottom:6px;color:var(--color-text-secondary);font-size:11px;text-transform:uppercase;letter-spacing:0.4px;">Preview</p>
                <p>Invoice number: <strong>{{ $settings->invoice_prefix ?? 'INV' }}-{{ date('Y') }}-00001</strong></p>
                @if($settings->invoice_footer_note)
                <p style="margin-top:4px;color:var(--color-text-secondary);">{{ $settings->invoice_footer_note }}</p>
                @endif
            </div>

            <button type="submit" class="tb-btn primary"
                    style="width:100%;justify-content:center;padding:9px;">
                <i class="ti ti-device-floppy"></i> Save Invoice Settings
            </button>
        </form>
    </div>
</div>

</div>{{-- end tab wrapper --}}

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.field{margin-bottom:10px;}
.field label{display:block;font-size:12px;color:var(--color-text-secondary);margin-bottom:4px;}
.field-input{width:100%;padding:7px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.field-input:focus{outline:none;border-color:#185FA5;}
</style>

@endsection