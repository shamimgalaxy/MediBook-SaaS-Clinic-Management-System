@extends('tenant.layouts.app')

@section('title', 'Edit Prescription')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Edit Prescription</h1>
        <p class="page-sub">
            {{ $prescription->patient->name }} &mdash;
            {{ $prescription->appointment->appointment_date->format('d M Y') }}
        </p>
    </div>
    <a href="{{ route('prescriptions.show', $prescription) }}" class="tb-btn">
        <i class="ti ti-arrow-left"></i> Back
    </a>
</div>

<form method="POST"
      action="{{ route('prescriptions.update', $prescription) }}"
      x-data="prescriptionForm({{ json_encode($prescription->items->map(fn($i) => [
          'medicine_name' => $i->medicine_name,
          'dosage'        => $i->dosage,
          'frequency'     => $i->frequency,
          'duration'      => $i->duration,
          'instructions'  => $i->instructions,
      ])->values()) }})">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:1rem;">

        <div>
            <div class="card mb-4" style="padding:1.1rem;">
                <div class="card-title">Clinical Details</div>

                <div class="field">
                    <label>Chief Complaint</label>
                    <input type="text" name="chief_complaint"
                           value="{{ old('chief_complaint', $prescription->chief_complaint) }}"
                           class="field-input" />
                </div>
                <div class="field">
                    <label>Diagnosis</label>
                    <input type="text" name="diagnosis"
                           value="{{ old('diagnosis', $prescription->diagnosis) }}"
                           class="field-input" />
                </div>
                <div class="field">
                    <label>Notes / Advice</label>
                    <textarea name="notes" rows="3"
                              class="field-input">{{ old('notes', $prescription->notes) }}</textarea>
                </div>
                <div class="field">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date"
                           value="{{ old('follow_up_date', $prescription->follow_up_date?->format('Y-m-d')) }}"
                           class="field-input" style="width:200px;" />
                </div>
            </div>

            <div class="card" style="padding:1.1rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <span class="card-title" style="margin-bottom:0;">Medicines</span>
                    <button type="button" class="tb-btn primary" style="padding:5px 12px;"
                            @click="addRow()">
                        <i class="ti ti-plus"></i> Add medicine
                    </button>
                </div>

                <div style="display:grid;grid-template-columns:2fr 1fr 1.2fr 1fr 1.5fr 32px;gap:6px;margin-bottom:6px;">
                    <span class="col-lbl">Medicine</span>
                    <span class="col-lbl">Dosage</span>
                    <span class="col-lbl">Frequency</span>
                    <span class="col-lbl">Duration</span>
                    <span class="col-lbl">Instructions</span>
                    <span></span>
                </div>

                <template x-for="(row, index) in rows" :key="index">
                    <div style="display:grid;grid-template-columns:2fr 1fr 1.2fr 1fr 1.5fr 32px;gap:6px;margin-bottom:6px;align-items:center;">
                        <input type="text" :name="`items[${index}][medicine_name]`" x-model="row.medicine_name" placeholder="e.g. Paracetamol" class="field-input" />
                        <input type="text" :name="`items[${index}][dosage]`" x-model="row.dosage" placeholder="500mg" class="field-input" />
                        <input type="text" :name="`items[${index}][frequency]`" x-model="row.frequency" placeholder="3x / day" class="field-input" />
                        <input type="text" :name="`items[${index}][duration]`" x-model="row.duration" placeholder="5 days" class="field-input" />
                        <input type="text" :name="`items[${index}][instructions]`" x-model="row.instructions" placeholder="After meal" class="field-input" />
                        <button type="button" @click="removeRow(index)" x-show="rows.length > 1"
                                style="background:none;border:none;cursor:pointer;color:#A32D2D;padding:0;">
                            <i class="ti ti-trash" style="font-size:16px;"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div>
            <div class="card" style="padding:1.1rem;">
                <div class="card-title">Appointment Info</div>
                <div class="info-row">
                    <span class="info-label">Patient</span>
                    <span class="info-val">{{ $prescription->patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-val">{{ $prescription->appointment->appointment_date->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Doctor</span>
                    <span class="info-val">Dr. {{ $prescription->doctor->name }}</span>
                </div>
                <div style="margin-top:1.25rem;padding-top:1rem;border-top:0.5px solid var(--color-border-tertiary);">
                    <button type="submit" class="tb-btn primary" style="width:100%;justify-content:center;">
                        <i class="ti ti-device-floppy"></i> Update Prescription
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
function prescriptionForm(existingItems) {
    return {
        rows: existingItems && existingItems.length
            ? existingItems
            : [{ medicine_name: '', dosage: '', frequency: '', duration: '', instructions: '' }],
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