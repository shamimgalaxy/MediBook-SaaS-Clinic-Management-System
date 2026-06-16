@extends('tenant.layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="page-sub">Manage your personal information and password</p>
    </div>
    <a href="{{ route('patient.dashboard') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Dashboard
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start;">

    {{-- Personal info --}}
    <div class="card" style="padding:1.25rem;">

        {{-- Avatar / name header --}}
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:1.5rem;padding-bottom:1.25rem;border-bottom:0.5px solid var(--color-border-tertiary);">
            <div style="width:56px;height:56px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:600;color:#0C447C;flex-shrink:0;">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <p style="font-size:15px;font-weight:500;">{{ $user->name }}</p>
                <p style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">{{ $user->email }}</p>
                <p style="font-size:11px;color:var(--color-text-secondary);margin-top:2px;">
                    Patient since {{ $user->created_at->format('M Y') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('patient.profile.update') }}">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="field" style="grid-column:1/-1;">
                    <label>Full Name <span style="color:#A32D2D;">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           class="field-input" required />
                    @error('name')
                        <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field" style="grid-column:1/-1;">
                    <label>Email</label>
                    <input type="email" value="{{ $user->email }}"
                           class="field-input" disabled
                           style="background:var(--color-background-secondary);color:var(--color-text-secondary);" />
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:3px;">Email cannot be changed.</p>
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="01XXXXXXXXX"
                           class="field-input" />
                    @error('phone')
                        <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                           class="field-input" />
                    @error('date_of_birth')
                        <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label>Gender</label>
                    <select name="gender" class="field-input">
                        <option value="">Select…</option>
                        <option value="male"   @selected(old('gender', $user->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                        <option value="other"  @selected(old('gender', $user->gender) === 'other')>Other</option>
                    </select>
                </div>

                <div class="field">
                    <label>Blood Group</label>
                    <select name="blood_group" class="field-input">
                        <option value="">Select…</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" @selected(old('blood_group', $user->blood_group) === $bg)>
                                {{ $bg }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="grid-column:1/-1;">
                    <label>Address</label>
                    <textarea name="address" rows="2"
                              placeholder="Your address…"
                              class="field-input"
                              style="resize:none;">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>

            <button type="submit" class="tb-btn primary"
                    style="width:100%;justify-content:center;padding:9px;margin-top:6px;">
                <i class="ti ti-device-floppy"></i> Save Changes
            </button>
        </form>
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Change password --}}
        <div class="card" style="padding:1.25rem;">
            <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Change Password</p>

            <form method="POST" action="{{ route('patient.profile.password') }}">
                @csrf @method('PUT')

                <div class="field">
                    <label>Current Password <span style="color:#A32D2D;">*</span></label>
                    <input type="password" name="current_password"
                           class="field-input" required />
                    @error('current_password')
                        <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label>New Password <span style="color:#A32D2D;">*</span></label>
                    <input type="password" name="password"
                           class="field-input" required />
                    @error('password')
                        <p style="color:#A32D2D;font-size:11px;margin-top:3px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label>Confirm New Password <span style="color:#A32D2D;">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="field-input" required />
                </div>

                <button type="submit" class="tb-btn primary"
                        style="width:100%;justify-content:center;padding:9px;">
                    <i class="ti ti-lock"></i> Change Password
                </button>
            </form>
        </div>

        {{-- Health summary --}}
        <div class="card" style="padding:1.25rem;">
            <p style="font-size:13px;font-weight:500;margin-bottom:1rem;">Health Summary</p>
            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach([
                    ['Blood Group',    $user->blood_group ?? '—'],
                    ['Date of Birth',  $user->date_of_birth ? $user->date_of_birth->format('d M Y') : '—'],
                    ['Gender',         $user->gender ? ucfirst($user->gender) : '—'],
                    ['Phone',          $user->phone ?? '—'],
                ] as [$label, $value])
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:0.5px solid var(--color-border-tertiary);font-size:13px;">
                    <span style="color:var(--color-text-secondary);">{{ $label }}</span>
                    <span style="font-weight:500;">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick links --}}
        <div class="card" style="padding:1.25rem;">
            <p style="font-size:13px;font-weight:500;margin-bottom:0.875rem;">Quick Links</p>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <a href="{{ route('patient.book') }}"
                   style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--color-text-primary);text-decoration:none;padding:7px 0;border-bottom:0.5px solid var(--color-border-tertiary);">
                    <i class="ti ti-calendar-plus" style="color:#185FA5;font-size:15px;"></i> Book Appointment
                </a>
                <a href="{{ route('patient.history') }}"
                   style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--color-text-primary);text-decoration:none;padding:7px 0;border-bottom:0.5px solid var(--color-border-tertiary);">
                    <i class="ti ti-history" style="color:#185FA5;font-size:15px;"></i> Appointment History
                </a>
                <a href="{{ route('invoices.index') }}"
                   style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--color-text-primary);text-decoration:none;padding:7px 0;border-bottom:0.5px solid var(--color-border-tertiary);">
                    <i class="ti ti-receipt" style="color:#185FA5;font-size:15px;"></i> My Invoices
                </a>
                <a href="{{ route('prescriptions.index') }}"
                   style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--color-text-primary);text-decoration:none;padding:7px 0;">
                    <i class="ti ti-file-text" style="color:#185FA5;font-size:15px;"></i> My Prescriptions
                </a>
            </div>
        </div>

    </div>
</div>

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