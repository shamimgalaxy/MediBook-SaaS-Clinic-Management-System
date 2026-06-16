{{-- resources/views/tenant/patients/edit.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Edit Patient</h1>
        <p class="page-sub">Update {{ $patient->name }}'s profile</p>
    </div>
    <a href="{{ route('tenant.patients.index') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('tenant.patients.update', $patient) }}">
    @csrf @method('PUT')

    <div class="form-grid">

        {{-- Account info --}}
        <div class="card">
            <div class="card-hdr"><span class="card-title">Account information</span></div>
            <div class="field-group">
                <div class="field">
                    <label>Full name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $patient->user->name) }}" required />
                    @error('name')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Email address <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $patient->user->email) }}" required />
                    @error('email')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>New password <span style="color:var(--color-text-secondary);font-weight:400;">(leave blank to keep current)</span></label>
                    <input type="password" name="password" placeholder="Min. 8 characters" />
                    @error('password')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div class="field" style="flex-direction:row;align-items:center;gap:10px;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $patient->is_active) ? 'checked' : '' }}
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
                    <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" />
                </div>
                <div class="field-row">
                    <div class="field">
                        <label>Date of birth</label>
                        <input type="date" name="date_of_birth"
                            value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" />
                    </div>
                    <div class="field">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">— Select —</option>
                            @foreach($genders as $val => $label)
                                <option value="{{ $val }}" @selected(old('gender', $patient->gender) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label>Blood group</label>
                    <select name="blood_group">
                        <option value="">— Select —</option>
                        @foreach($bloodGroups as $bg)
                            <option value="{{ $bg }}" @selected(old('blood_group', $patient->blood_group) === $bg)>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Address</label>
                    <textarea name="address" rows="3" style="resize:vertical;">{{ old('address', $patient->address) }}</textarea>
                </div>
            </div>
        </div>

    </div>

    <div style="margin-top:1rem;display:flex;gap:8px;">
        <button type="submit" class="tb-btn primary" style="padding:8px 20px;">
            <i class="ti ti-check"></i> Update patient
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