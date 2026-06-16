{{-- resources/views/tenant/patients/create.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Add Patient')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Add Patient</h1>
        <p class="page-sub">Register a new patient and create their account</p>
    </div>
    <a href="{{ route('tenant.patients.index') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('tenant.patients.store') }}">
    @csrf

    <div class="form-grid">

        {{-- Account info --}}
        <div class="card">
            <div class="card-hdr"><span class="card-title">Account information</span></div>
            <div class="field-group">
                <div class="field">
                    <label>Full name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Rahim Uddin" required />
                    @error('name')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Email address <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="patient@email.com" required />
                    @error('email')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Password <span class="req">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 characters" required />
                    @error('password')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field" style="flex-direction:row;align-items:center;gap:10px;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', 1) ? 'checked' : '' }}
                        style="width:16px;height:16px;cursor:pointer;" />
                    <label for="is_active" style="margin:0;font-weight:400;cursor:pointer;">Mark as active</label>
                </div>
            </div>
        </div>

        {{-- Profile info --}}
        <div class="card">
            <div class="card-hdr"><span class="card-title">Profile details</span></div>
            <div class="field-group">
                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="01700-000000" />
                    @error('phone')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field-row">
                    <div class="field">
                        <label>Date of birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" />
                        @error('date_of_birth')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">— Select —</option>
                            @foreach($genders as $val => $label)
                                <option value="{{ $val }}" @selected(old('gender') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender')<p class="err">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="field">
                    <label>Blood group</label>
                    <select name="blood_group">
                        <option value="">— Select —</option>
                        @foreach($bloodGroups as $bg)
                            <option value="{{ $bg }}" @selected(old('blood_group') === $bg)>{{ $bg }}</option>
                        @endforeach
                    </select>
                    @error('blood_group')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Address</label>
                    <textarea name="address" rows="3" placeholder="House, Road, Area, City…" style="resize:vertical;">{{ old('address') }}</textarea>
                    @error('address')<p class="err">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

    </div>

    <div style="margin-top:1rem;display:flex;gap:8px;">
        <button type="submit" class="tb-btn primary" style="padding:8px 20px;">
            <i class="ti ti-check"></i> Save patient
        </button>
        <a href="{{ route('tenant.patients.index') }}" class="tb-btn" style="padding:8px 16px;">Cancel</a>
    </div>

</form>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:1.1rem;}
.card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;}
.card-title{font-size:14px;font-weight:500;}
.field-group{display:flex;flex-direction:column;gap:10px;}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.field{display:flex;flex-direction:column;gap:4px;}
.field label{font-size:12px;color:var(--color-text-secondary);font-weight:500;}
.field input,.field select,.field textarea{padding:7px 9px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);font-family:var(--font-sans);}
.field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:#185FA5;}
.req{color:#A32D2D;}
.err{font-size:11px;color:#A32D2D;margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
</style>
@endsection