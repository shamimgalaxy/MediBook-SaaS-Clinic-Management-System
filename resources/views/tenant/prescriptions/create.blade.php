@extends('tenant.layouts.app')

@section('title', 'New Prescription')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">New Prescription</h1>
        <p class="page-sub">
            {{ $appointment->patient->name }} &mdash;
            {{ $appointment->appointment_date->format('d M Y') }}
        </p>
    </div>
    <a href="{{ route('appointments.show', $appointment) }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back to appointment
    </a>
</div>

<form method="POST"
      action="{{ route('appointments.prescription.store', $appointment) }}"
      x-data="prescriptionForm()">
    @csrf

    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:1rem;">

        {{-- Left: Main fields + medicines --}}
        <div>

            {{-- Chief complaint & diagnosis --}}
            <div class="card mb-4" style="padding:1.1rem;">
                <div class="card-title">Clinical Details</div>

                <div class="field">
                    <label>Chief Complaint</label>
                    <input type="text" name="chief_complaint"
                           value="{{ old('chief_complaint') }}"
                           placeholder="e.g. Fever, headache for 3 days"
                           class="field-input" />
                </div>

                <div class="field">
                    <label>Diagnosis</label>
                    <input type="text" name="diagnosis"
                           value="{{ old('diagnosis') }}"
                           placeholder="e.g. Viral fever, Hypertension"
                           class="field-input" />
                </div>

                <div class="field">
                    <label>Notes / Advice</label>
                    <textarea name="notes" rows="3"
                              placeholder="Additional advice, lifestyle changes…"
                              class="field-input">{{ old('notes') }}</textarea>
                </div>

                <div class="field">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date"
                           value="{{ old('follow_up_date') }}"
                           class="field-input" style="width:200px;" />
                </div>
            </div>

            {{-- Medicine rows --}}
            <div class="card" style="padding:1.1rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <span class="card-title" style="margin-bottom:0;">Medicines</span>
                    <button type="button" class="tb-btn primary" style="padding:5px 12px;"
                            @click="addRow()">
                        <i class="ti ti-plus"></i> Add medicine
                    </button>
                </div>

                {{-- Header --}}
                <div style="display:grid;grid-template-columns:2fr 1fr 1.2fr 1fr 1.5fr 32px;gap:6px;margin-bottom:6px;">
                    <span class="col-lbl">Medicine</span>
                    <span class="col-lbl">Dosage</span>
                    <span class="col-lbl">Frequency</span>
                    <span class="col-lbl">Duration</span>
                    <span class="col-lbl">Instructions</span>
                    <span></span>
                </div>

                {{-- Rows --}}
                <template x-for="(row, index) in rows" :key="index">
                    <div style="display:grid;grid-template-columns:2fr 1fr 1.2fr 1fr 1.5fr 32px;gap:6px;margin-bottom:6px;align-items:center;">
                        <input type="text"
                               :name="`items[${index}][medicine_name]`"
                               x-model="row.medicine_name"
                               placeholder="e.g. Paracetamol"
                               class="field-input" />
                        <input type="text"
                               :name="`items[${index}][dosage]`"
                               x-model="row.dosage"
                               placeholder="500mg"
                               class="field-input" />
                        <input type="text"
                               :name="`items[${index}][frequency]`"
                               x-model="row.frequency"
                               placeholder="3x / day"
                               class="field-input" />
                        <input type="text"
                               :name="`items[${index}][duration]`"
                               x-model="row.duration"
                               placeholder="5 days"
                               class="field-input" />
                        <input type="text"
                               :name="`items[${index}][instructions]`"
                               x-model="row.instructions"
                               placeholder="After meal"
                               class="field-input" />
                        <button type="button"
                                @click="removeRow(index)"
                                x-show="rows.length > 1"
                                style="background:none;border:none;cursor:pointer;color:#A32D2D;padding:0;">
                            <i class="ti ti-trash" style="font-size:16px;"></i>
                        </button>
                    </div>
                </template>

                <p x-show="rows.length === 0"
                   style="font-size:12px;color:var(--color-text-secondary);text-align:center;padding:1rem 0;">
                    No medicines added yet.
                </p>
            </div>

        </div>

        {{-- Right: Appointment summary --}}
        <div>
            <div class="card" style="padding:1.1rem;">
                <div class="card-title">Appointment Info</div>

                <div class="info-row">
                    <span class="info-label">Patient</span>
                    <span class="info-val">{{ $appointment->patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-val">{{ $appointment->appointment_date->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time</span>
                    <span class="info-val">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Visit Type</span>
                    <span class="info-val">{{ ucfirst(str_replace('_', ' ', $appointment->visit_type)) }}</span>
                </div>
                @if($appointment->reason)
                <div class="info-row" style="align-items:flex-start;">
                    <span class="info-label">Reason</span>
                    <span class="info-val">{{ $appointment->reason }}</span>
                </div>
                @endif

                <div style="margin-top:1.25rem;padding-top:1rem;border-top:0.5px solid var(--color-border-tertiary);">
                    <button type="submit" class="tb-btn primary" style="width:100%;justify-content:center;">
                        <i class="ti ti-file-plus"></i> Save Prescription
                    </button>
                </div>
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
.card-title{font-size:13px;font-weight:500;margin-bottom:0.875rem;}
.mb-4{margin-bottom:1rem;}
.field{margin-bottom:10px;}
.field label{display:block;font-size:12px;color:var(--color-text-secondary);margin-bottom:4px;}
.field-input{width:100%;padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.field-input:focus{outline:none;border-color:#185FA5;}
.col-lbl{font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
.info-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:0.5px solid var(--color-border-tertiary);font-size:13px;}
.info-row:last-of-type{border-bottom:none;}
.info-label{color:var(--color-text-secondary);}
.info-val{font-weight:500;text-align:right;}
</style>

<script>
function prescriptionForm() {
    return {
        rows: [
            { medicine_name: '', dosage: '', frequency: '', duration: '', instructions: '' }
        ],
        addRow() {
            this.rows.push({ medicine_name: '', dosage: '', frequency: '', duration: '', instructions: '' });
        },
        removeRow(index) {
            this.rows.splice(index, 1);
        }
    }
}
</script>

@endsection