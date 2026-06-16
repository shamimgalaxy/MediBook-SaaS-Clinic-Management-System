{{-- resources/views/tenant/doctors/edit.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Edit Doctor')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Edit Doctor</h1>
        <p class="page-sub">Update Dr. {{ $doctor->name }}'s profile and schedule</p>
    </div>
    <a href="{{ route('tenant.doctors.index') }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('tenant.doctors.update', $doctor) }}" enctype="multipart/form-data"
    x-data="doctorEditForm({{ json_encode($doctor->schedules->map(fn($s) => [
        'day_of_week'   => $s->day_of_week,
        'start_time'    => substr($s->start_time, 0, 5),
        'end_time'      => substr($s->end_time, 0, 5),
        'slot_duration' => $s->slot_duration,
    ])) }})">
    @csrf
    @method('PUT')

    <div class="form-grid">

        {{-- ── Left column ── --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Account info --}}
            <div class="card">
                <div class="card-hdr"><span class="card-title">Account information</span></div>
                <div class="field-group">
                    <div class="field">
                        <label>Full name <span class="req">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $doctor->user->name) }}" required />
                        @error('name')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label>Email address <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $doctor->user->email) }}" required />
                        @error('email')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label>New password <span style="color:var(--color-text-secondary);font-weight:400;">(leave blank to keep current)</span></label>
                        <input type="password" name="password" placeholder="Min. 8 characters" />
                        @error('password')<p class="err">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Profile --}}
            <div class="card">
                <div class="card-hdr"><span class="card-title">Profile details</span></div>
                <div class="field-group">
                    <div class="field-row">
                        <div class="field">
                            <label>Specialty <span class="req">*</span></label>
                            <input type="text" name="specialty" value="{{ old('specialty', $doctor->specialty) }}" required />
                            @error('specialty')<p class="err">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label>Experience (years) <span class="req">*</span></label>
                            <input type="number" name="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" min="0" required />
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Consultation fee (৳) <span class="req">*</span></label>
                            <input type="number" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" min="0" step="0.01" required />
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $doctor->phone) }}" />
                        </div>
                    </div>
                    <div class="field">
                        <label>Bio</label>
                        <textarea name="bio" rows="3" style="resize:vertical;">{{ old('bio', $doctor->bio) }}</textarea>
                    </div>
                    <div class="field">
                        <label>Avatar</label>
                        @if($doctor->avatar)
                            <div style="margin-bottom:8px;display:flex;align-items:center;gap:10px;">
                                <img src="{{ Storage::url($doctor->avatar) }}" alt=""
                                    style="width:48px;height:48px;border-radius:50%;object-fit:cover;" />
                                <span style="font-size:12px;color:var(--color-text-secondary);">Current avatar</span>
                            </div>
                        @endif
                        <input type="file" name="avatar" accept="image/*" />
                        <p style="font-size:11px;color:var(--color-text-secondary);margin-top:4px;">Upload new to replace current</p>
                    </div>
                    <div class="field" style="flex-direction:row;align-items:center;gap:10px;">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $doctor->is_active) ? 'checked' : '' }}
                            style="width:16px;height:16px;cursor:pointer;" />
                        <label for="is_active" style="margin:0;font-weight:400;cursor:pointer;">Mark as active</label>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right column — Schedule builder ── --}}
        <div class="card" style="align-self:start;">
            <div class="card-hdr">
                <span class="card-title">Weekly schedule</span>
                <button type="button" class="tb-btn primary" @click="addRow()" style="font-size:12px;padding:5px 10px;">
                    <i class="ti ti-plus"></i> Add day
                </button>
            </div>

            <template x-if="schedules.length === 0">
                <p style="font-size:13px;color:var(--color-text-secondary);text-align:center;padding:1.5rem 0;">
                    No schedule. Add a day above.
                </p>
            </template>

            <div style="display:flex;flex-direction:column;gap:10px;">
                <template x-for="(row, i) in schedules" :key="i">
                    <div style="background:var(--color-background-secondary);border-radius:var(--border-radius-md);padding:10px 12px;display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:12px;font-weight:500;color:var(--color-text-secondary);" x-text="'Slot ' + (i+1)"></span>
                            <button type="button" @click="removeRow(i)"
                                style="background:none;border:none;cursor:pointer;color:#A32D2D;font-size:14px;">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div class="field">
                                <label>Day</label>
                                <select :name="'schedules[' + i + '][day_of_week]'" x-model="row.day_of_week">
                                    @foreach($days as $num => $name)
                                        <option value="{{ $num }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Slot duration (min)</label>
                                <select :name="'schedules[' + i + '][slot_duration]'" x-model="row.slot_duration">
                                    <option value="15">15 min</option>
                                    <option value="20">20 min</option>
                                    <option value="30">30 min</option>
                                    <option value="45">45 min</option>
                                    <option value="60">60 min</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Start time</label>
                                <input type="time" :name="'schedules[' + i + '][start_time]'" x-model="row.start_time" />
                            </div>
                            <div class="field">
                                <label>End time</label>
                                <input type="time" :name="'schedules[' + i + '][end_time]'" x-model="row.end_time" />
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div style="margin-top:1rem;padding-top:1rem;border-top:0.5px solid var(--color-border-tertiary);display:flex;gap:8px;">
                <button type="submit" class="tb-btn primary" style="flex:1;justify-content:center;padding:8px;">
                    <i class="ti ti-check"></i> Update doctor
                </button>
                <a href="{{ route('tenant.doctors.index') }}" class="tb-btn" style="padding:8px 16px;">Cancel</a>
            </div>
        </div>

    </div>
</form>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.form-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:1rem;align-items:start;}
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

<script>
function doctorEditForm(existing) {
    return {
        schedules: existing || [],
        addRow() {
            this.schedules.push({ day_of_week: 0, start_time: '09:00', end_time: '17:00', slot_duration: 30 });
        },
        removeRow(i) {
            this.schedules.splice(i, 1);
        }
    }
}
</script>
@endsection