@extends('tenant.layouts.app')

@section('title', 'Book Appointment')

@section('content')

{{-- Hero --}}
<div style="background:#E6F1FB;border-radius:var(--border-radius-lg);padding:1.5rem;text-align:center;margin-bottom:1.25rem;">
    <h1 style="font-size:20px;font-weight:500;color:#042C53;margin-bottom:6px;">
        Book a doctor appointment
    </h1>
    <p style="font-size:13px;color:#185FA5;margin-bottom:1.25rem;">
        Choose your doctor, pick a time, and confirm — all in minutes.
    </p>

    {{-- Search --}}
    <form method="GET" action="{{ route('patient.book') }}"
          style="display:flex;gap:8px;max-width:520px;margin:0 auto;">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search by doctor name or specialty…"
               style="flex:1;padding:9px 12px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:#fff;" />
        <select name="specialty"
                style="padding:9px 12px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:#fff;">
            <option value="">All specialties</option>
            @foreach($specialties as $s)
                <option value="{{ $s }}" @selected($specialty === $s)>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#185FA5;color:#fff;border:none;padding:9px 18px;border-radius:var(--border-radius-md);font-size:13px;cursor:pointer;font-weight:500;white-space:nowrap;">
            <i class="ti ti-search" style="font-size:13px;vertical-align:-1px;margin-right:4px;"></i>Search
        </button>
    </form>
</div>

{{-- Working hours notice --}}
@php
    $cs   = \App\Models\ClinicSetting::first();
    $today = strtolower(now()->format('l'));
    $todayHours = $cs?->workingHoursForDay($today);
@endphp
@if($cs)
<div style="margin-top:1rem;display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:#185FA5;">
    <i class="ti ti-clock" style="font-size:14px;"></i>
    @if($todayHours['open'] ?? false)
        Open today: {{ \Carbon\Carbon::parse($todayHours['start'])->format('h:i A') }}
        — {{ \Carbon\Carbon::parse($todayHours['end'])->format('h:i A') }}
    @else
        Closed today
    @endif
    @if($cs->phone)
        &nbsp;&bull;&nbsp;<i class="ti ti-phone" style="font-size:13px;"></i> {{ $cs->phone }}
    @endif
</div>
@endif

{{-- Main content --}}
<div x-data="bookingApp()" style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:1rem;">

    {{-- Left: Doctors list --}}
    <div>
        <div class="card" style="padding:1.1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                <span style="font-size:14px;font-weight:500;">Available Doctors</span>
                <span style="font-size:12px;color:var(--color-text-secondary);">{{ $doctors->count() }} found</span>
            </div>

            {{-- Specialty filter chips --}}
            <div style="display:flex;gap:8px;margin-bottom:1rem;flex-wrap:wrap;">
                <a href="{{ route('patient.book', array_merge(request()->except('specialty'), ['specialty' => ''])) }}"
                   style="padding:5px 12px;border-radius:99px;font-size:12px;border:0.5px solid {{ !$specialty ? '#85B7EB' : 'var(--color-border-tertiary)' }};background:{{ !$specialty ? '#E6F1FB' : 'var(--color-background-primary)' }};color:{{ !$specialty ? '#0C447C' : 'var(--color-text-secondary)' }};text-decoration:none;">
                    All
                </a>
                @foreach($specialties as $s)
                <a href="{{ route('patient.book', array_merge(request()->except('specialty'), ['specialty' => $s])) }}"
                   style="padding:5px 12px;border-radius:99px;font-size:12px;border:0.5px solid {{ $specialty === $s ? '#85B7EB' : 'var(--color-border-tertiary)' }};background:{{ $specialty === $s ? '#E6F1FB' : 'var(--color-background-primary)' }};color:{{ $specialty === $s ? '#0C447C' : 'var(--color-text-secondary)' }};text-decoration:none;">
                    {{ $s }}
                </a>
                @endforeach
            </div>

            {{-- Doctor cards --}}
            <div style="display:flex;flex-direction:column;gap:8px;">
                @forelse($doctors as $doctor)
                <div style="border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:10px 12px;cursor:pointer;transition:border-color 0.15s;"
                     :class="selectedDoctor?.id === {{ $doctor->id }} ? 'selected-doc' : ''"
                     @click="selectDoctor({{ json_encode([
                         'id'             => $doctor->id,
                         'name'           => $doctor->name,
                         'specialization' => $doctor->specialization,
                         'fee'            => $doctor->consultation_fee,
                         'initials'       => $doctor->initials,
                     ]) }})">

                    {{-- Doctor info --}}
                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:8px;">
                        @if($doctor->avatar)
                            <img src="{{ Storage::url($doctor->avatar) }}"
                                 style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;" />
                        @else
                            <div style="width:40px;height:40px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;color:#0C447C;flex-shrink:0;">
                                {{ $doctor->initials }}
                            </div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:500;">Dr. {{ $doctor->name }}</p>
                            <span style="font-size:12px;color:var(--color-text-secondary);">
                                {{ $doctor->specialization ?? 'General' }}
                                @if($doctor->experience_years)
                                    &bull; {{ $doctor->experience_years }} yrs experience
                                @endif
                            </span>
                        </div>
                        <div style="font-size:13px;font-weight:500;color:#185FA5;white-space:nowrap;">
                            ৳{{ number_format($doctor->consultation_fee, 0) }}
                        </div>
                    </div>

                    {{-- Time slots --}}
                    @if($doctor->activeSchedules->count())
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach($doctor->activeSchedules->take(6) as $schedule)
                        <span style="padding:4px 10px;border-radius:var(--border-radius-md);font-size:12px;border:0.5px solid #97C459;background:#EAF3DE;color:#27500A;cursor:pointer;"
                              @click.stop="selectDoctor({{ json_encode([
                                  'id'             => $doctor->id,
                                  'name'           => $doctor->name,
                                  'specialization' => $doctor->specialization,
                                  'fee'            => $doctor->consultation_fee,
                                  'initials'       => $doctor->initials,
                                  'time'           => $schedule->start_time,
                              ]) }}, '{{ $schedule->start_time }}')">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size:12px;color:var(--color-text-secondary);">No schedule set</p>
                    @endif

                </div>
                @empty
                <div style="text-align:center;padding:2rem;color:var(--color-text-secondary);font-size:13px;">
                    No doctors found matching your search.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Booking form + history --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Booking form --}}
        <div class="card" style="padding:1.1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                <span style="font-size:14px;font-weight:500;">Confirm Booking</span>
            </div>

            <form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ auth()->id() }}" />
                <input type="hidden" name="visit_type" value="new" />

                {{-- Doctor selection (hidden, set by Alpine) --}}
                <input type="hidden" name="doctor_id" :value="selectedDoctor?.id" />
                <input type="hidden" name="appointment_time" :value="selectedTime" />
                <input type="hidden" name="fee" :value="selectedDoctor?.fee" />

                {{-- Selected doctor display --}}
                <div x-show="selectedDoctor"
                     style="background:var(--color-background-secondary);border-radius:var(--border-radius-md);padding:10px 12px;margin-bottom:0.75rem;display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:#0C447C;flex-shrink:0;"
                         x-text="selectedDoctor?.initials"></div>
                    <div>
                        <p style="font-size:13px;font-weight:500;" x-text="'Dr. ' + (selectedDoctor?.name ?? '')"></p>
                        <p style="font-size:11px;color:var(--color-text-secondary);" x-text="selectedDoctor?.specialization ?? ''"></p>
                    </div>
                </div>
                <div x-show="!selectedDoctor"
                     style="background:var(--color-background-secondary);border-radius:var(--border-radius-md);padding:10px 12px;margin-bottom:0.75rem;text-align:center;font-size:12px;color:var(--color-text-secondary);">
                    ← Select a doctor to continue
                </div>

                {{-- Date --}}
                <div style="margin-bottom:10px;">
                    <label style="display:block;font-size:11px;color:var(--color-text-secondary);margin-bottom:4px;font-weight:500;">
                        Date <span style="color:#A32D2D;">*</span>
                    </label>
                    <input type="date" name="appointment_date"
                           min="{{ today()->toDateString() }}"
                           value="{{ old('appointment_date') }}"
                           style="width:100%;padding:8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);"
                           required />
                </div>

                {{-- Visit type --}}
                <div style="margin-bottom:10px;">
                    <label style="display:block;font-size:11px;color:var(--color-text-secondary);margin-bottom:4px;font-weight:500;">Visit Type</label>
                    <select name="visit_type"
                            style="width:100%;padding:8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);">
                        <option value="new">New Visit</option>
                        <option value="follow_up">Follow-up</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>

                {{-- Reason --}}
                <div style="margin-bottom:10px;">
                    <label style="display:block;font-size:11px;color:var(--color-text-secondary);margin-bottom:4px;font-weight:500;">
                        Reason (optional)
                    </label>
                    <input type="text" name="reason"
                           placeholder="e.g. chest pain, routine checkup"
                           style="width:100%;padding:8px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);" />
                </div>

                {{-- Summary box --}}
                <div x-show="selectedDoctor"
                     style="background:var(--color-background-secondary);border-radius:var(--border-radius-md);padding:10px 12px;margin-bottom:0.75rem;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;">
                        <span style="color:var(--color-text-secondary);">Doctor</span>
                        <span x-text="'Dr. ' + (selectedDoctor?.name ?? '')"></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;">
                        <span style="color:var(--color-text-secondary);">Specialty</span>
                        <span x-text="selectedDoctor?.specialization ?? ''"></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;">
                        <span style="color:var(--color-text-secondary);">Time slot</span>
                        <span x-text="selectedTime ? formatTime(selectedTime) : 'Not selected'"></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;border-top:0.5px solid var(--color-border-tertiary);margin-top:4px;padding-top:8px;font-weight:500;font-size:13px;">
                        <span>Total payable</span>
                        <span style="color:#185FA5;" x-text="'৳' + (selectedDoctor?.fee ?? 0)"></span>
                    </div>
                </div>

                <button type="submit"
                        :disabled="!selectedDoctor"
                        style="width:100%;background:#185FA5;color:#fff;border:none;padding:10px;border-radius:var(--border-radius-md);font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;"
                        :style="!selectedDoctor ? 'opacity:0.5;cursor:not-allowed;' : ''">
                    <i class="ti ti-calendar-check" style="font-size:14px;"></i>
                    Confirm Appointment
                </button>
            </form>
        </div>

        {{-- Past appointments --}}
        <div class="card" style="padding:1.1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                <span style="font-size:14px;font-weight:500;">My Past Appointments</span>
                <a href="{{ route('appointments.index') }}" style="font-size:12px;color:#185FA5;">View all</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                @forelse($recentAppointments as $appt)
                @php
                    $pillStyles = [
                        'completed' => 'background:#EAF3DE;color:#27500A;',
                        'cancelled' => 'background:#FCEBEB;color:#791F1F;',
                        'pending'   => 'background:#FEF3C7;color:#D97706;',
                        'confirmed' => 'background:#E6F1FB;color:#0C447C;',
                    ];
                    $pill = $pillStyles[$appt->status] ?? 'background:#F3F4F6;color:#374151;';
                @endphp
                <div style="padding:9px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-size:12px;font-weight:500;">Dr. {{ $appt->doctor->name }}</span>
                        <span style="{{ $pill }}padding:2px 7px;border-radius:99px;font-size:10px;font-weight:500;">
                            {{ ucfirst($appt->status) }}
                        </span>
                    </div>
                    <div style="font-size:12px;color:var(--color-text-secondary);">
                        {{ $appt->appointment_date->format('d M Y') }}
                        &bull; {{ $appt->doctor->specialization ?? '' }}
                        &bull; ৳{{ number_format($appt->fee, 0) }}
                    </div>
                </div>
                @empty
                <p style="font-size:12px;color:var(--color-text-secondary);text-align:center;padding:1rem 0;">
                    No past appointments.
                </p>
                @endforelse
            </div>
        </div>

    </div>
</div>

<style>
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.selected-doc{border-color:#185FA5 !important;background:#E6F1FB;}
</style>

@push('scripts')
<script>
function bookingApp() {
    return {
        selectedDoctor: null,
        selectedTime: null,

        selectDoctor(doctor, time = null) {
            this.selectedDoctor = doctor;
            if (time) this.selectedTime = time;
        },

        formatTime(time) {
            if (!time) return 'Not selected';
            const [h, m] = time.split(':');
            const hour = parseInt(h);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const h12  = hour % 12 || 12;
            return `${h12}:${m} ${ampm}`;
        }
    }
}
</script>
@endpush

@endsection