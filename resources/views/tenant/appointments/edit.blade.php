{{-- resources/views/tenant/appointments/edit.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Edit Appointment #' . $appointment->id)

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Edit Appointment #{{ $appointment->id }}</h1>
        <p class="page-sub">{{ $appointment->patient->name }} — {{ $appointment->appointment_date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('appointments.show', $appointment) }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('appointments.update', $appointment) }}" x-data="appointmentForm()">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">

    {{-- Left --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Patient & Doctor --}}
        <div class="card section-card">
            <div class="section-title">Patient & Doctor</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Patient <span class="req">*</span></label>
                    <select name="patient_id" class="form-control" required>
                        <option value="">Select patient…</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}"
                                @selected(old('patient_id', $appointment->patient_id) == $p->id)>
                                {{ $p->name }} — {{ $p->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Doctor <span class="req">*</span></label>
                    <select name="doctor_id" class="form-control" required
                        x-model="selectedDoctor" @change="onDoctorChange()">
                        <option value="">Select doctor…</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}"
                                data-fee="{{ $doc->consultation_fee }}"
                                data-schedules="{{ json_encode($doc->activeSchedules->pluck('day_of_week')->toArray()) }}"
                                @selected(old('doctor_id', $appointment->doctor_id) == $doc->id)>
                                Dr. {{ $doc->name }} — {{ $doc->specialty }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        {{-- Date & Time --}}
        <div class="card section-card">
            <div class="section-title">Date & Time</div>
            <div class="form-row two-col">
                <div class="form-group">
                    <label class="form-label">Appointment Date <span class="req">*</span></label>
                    <input type="date" name="appointment_date" class="form-control"
                        value="{{ old('appointment_date', $appointment->appointment_date->toDateString()) }}"
                        required x-model="selectedDate" @change="checkSchedule()" />
                    @error('appointment_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Appointment Time <span class="req">*</span></label>
                    <input type="time" name="appointment_time" class="form-control"
                        value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i')) }}"
                        required />
                    @error('appointment_time')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div x-show="scheduleWarning" x-cloak
                style="background:#FEF3C7;color:#92400E;border-radius:6px;padding:8px 12px;font-size:12px;display:flex;gap:6px;align-items:center;margin-top:4px;">
                <i class="ti ti-alert-triangle"></i>
                <span>This doctor may not be available on the selected day. Please confirm.</span>
            </div>
        </div>

        {{-- Visit details --}}
        <div class="card section-card">
            <div class="section-title">Visit Details</div>
            <div class="form-row two-col">
                <div class="form-group">
                    <label class="form-label">Visit Type <span class="req">*</span></label>
                    <select name="visit_type" class="form-control" required>
                        <option value="new"       @selected(old('visit_type',$appointment->visit_type) === 'new')>New Visit</option>
                        <option value="follow_up" @selected(old('visit_type',$appointment->visit_type) === 'follow_up')>Follow Up</option>
                        <option value="emergency" @selected(old('visit_type',$appointment->visit_type) === 'emergency')>Emergency</option>
                    </select>
                    @error('visit_type')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Consultation Fee (৳)</label>
                    <input type="number" name="fee" class="form-control"
                        value="{{ old('fee', $appointment->fee) }}"
                        min="0" step="0.01" x-model="fee" />
                    @error('fee')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label class="form-label">Reason for Visit</label>
                <textarea name="reason" class="form-control" rows="3" style="resize:vertical;">{{ old('reason', $appointment->reason) }}</textarea>
                @error('reason')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Notes <span style="font-size:11px;color:var(--color-text-secondary);">(internal)</span></label>
                <textarea name="notes" class="form-control" rows="3" style="resize:vertical;">{{ old('notes', $appointment->notes) }}</textarea>
                @error('notes')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

    </div>

    {{-- Right --}}
    <div>
        <div class="card section-card" style="position:sticky;top:20px;">
            <div class="section-title">Current Status</div>
            @php
                $statusColors = [
                    'pending'     => ['#FEF3C7','#D97706'],
                    'confirmed'   => ['#E6F1FB','#185FA5'],
                    'in_progress' => ['#EDE9FE','#5B21B6'],
                    'completed'   => ['#EAF3DE','#27500A'],
                    'cancelled'   => ['#FEE2E2','#A32D2D'],
                ];
                [$sbg,$sfg] = $statusColors[$appointment->status] ?? ['#F3F4F6','#374151'];
            @endphp
            <span class="pill" style="background:{{ $sbg }};color:{{ $sfg }};font-size:13px;padding:4px 12px;">
                {{ ucfirst(str_replace('_',' ',$appointment->status)) }}
            </span>

            <div style="border-top:0.5px solid var(--color-border-tertiary);margin:14px 0;"></div>
            <div class="summary-row">
                <span class="summary-label">Fee</span>
                <span class="summary-val" style="color:#185FA5;font-weight:600;">৳<span x-text="fee"></span></span>
            </div>
            <div style="border-top:0.5px solid var(--color-border-tertiary);margin:14px 0;"></div>

            <button type="submit" class="tb-btn primary" style="width:100%;justify-content:center;padding:9px 0;">
                <i class="ti ti-device-floppy"></i> Save Changes
            </button>
            <a href="{{ route('appointments.show', $appointment) }}"
                class="tb-btn" style="width:100%;justify-content:center;padding:9px 0;margin-top:8px;">
                Cancel
            </a>
        </div>
    </div>

</div>
</form>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.section-card{padding:1rem 1.25rem;}
.section-title{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--color-text-secondary);margin-bottom:14px;}
.form-row{margin-bottom:12px;}
.form-row.two-col{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.form-group{display:flex;flex-direction:column;gap:5px;}
.form-label{font-size:12px;font-weight:500;color:var(--color-text-primary);}
.req{color:#A32D2D;}
.form-control{padding:7px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);width:100%;box-sizing:border-box;}
.form-control:focus{outline:none;border-color:#185FA5;}
.form-error{font-size:11px;color:#A32D2D;}
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
.summary-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;}
.summary-label{font-size:12px;color:var(--color-text-secondary);}
.summary-val{font-size:13px;font-weight:500;}
[x-cloak]{display:none!important;}
</style>

<script>
function appointmentForm() {
    return {
        selectedDoctor: '{{ old('doctor_id', $appointment->doctor_id) }}',
        selectedDate: '{{ old('appointment_date', $appointment->appointment_date->toDateString()) }}',
        fee: '{{ old('fee', $appointment->fee) }}',
        doctorSchedules: [],
        scheduleWarning: false,

        init() {
            // Pre-load schedules for current doctor
            const sel = document.querySelector('select[name="doctor_id"]');
            if (sel) {
                const opt = sel.options[sel.selectedIndex];
                this.doctorSchedules = opt.dataset.schedules ? JSON.parse(opt.dataset.schedules) : [];
                this.checkSchedule();
            }
        },

        onDoctorChange() {
            const sel = document.querySelector('select[name="doctor_id"]');
            const opt = sel.options[sel.selectedIndex];
            this.fee = opt.dataset.fee || this.fee;
            this.doctorSchedules = opt.dataset.schedules ? JSON.parse(opt.dataset.schedules) : [];
            this.checkSchedule();
        },

        checkSchedule() {
            if (!this.selectedDate || this.doctorSchedules.length === 0) {
                this.scheduleWarning = false;
                return;
            }
            const dayOfWeek = new Date(this.selectedDate).getDay();
            this.scheduleWarning = !this.doctorSchedules.includes(dayOfWeek);
        }
    }
}
</script>
@endsection