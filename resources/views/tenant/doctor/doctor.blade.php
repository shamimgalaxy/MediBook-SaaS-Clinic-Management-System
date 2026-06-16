@extends('tenant.layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')

<div class="doctor-layout">

    {{-- ── Sidebar ── --}}
    <aside class="doctor-sidebar">
        <div class="sb-doctor">
            <div class="sb-av">{{ $doctor->initials }}</div>
            <div class="sb-doc-info">
                <p>Dr. {{ $doctor->name }}</p>
                <span>{{ $doctor->specialty }}</span>
            </div>
        </div>

        <div class="sb-section">My Work</div>
        <a href="{{ route('doctor.dashboard') }}" class="sb-item active">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
        <a href="{{ route('appointments.index') }}" class="sb-item">
            <i class="ti ti-calendar-event"></i> My Schedule
            @if($remainingToday > 0)
                <span class="sb-badge">{{ $remainingToday }}</span>
            @endif
        </a>
        <a href="{{ route('prescriptions.index') }}" class="sb-item">
            <i class="ti ti-file-text"></i> Prescriptions
        </a>

        <div class="sb-section">History</div>
        <a href="{{ route('appointments.index') }}?status=completed" class="sb-item">
            <i class="ti ti-clock"></i> Past Appointments
        </a>

        <div class="sb-footer">
            <div class="sb-status">
                <div class="status-dot {{ $doctor->is_active ? 'active' : 'inactive' }}"></div>
                {{ $doctor->is_active ? 'Available today' : 'Unavailable' }}
            </div>
        </div>
    </aside>

    {{-- ── Main content ── --}}
    <div class="doctor-main">

        {{-- Topbar --}}
        <div class="doctor-topbar">
            <div>
                <h1>Doctor Dashboard</h1>
                <p>{{ now()->format('l, d F Y') }} — {{ tenant('clinic_name') }}</p>
            </div>
            <div class="topbar-actions">
                @if($currentAppointment)
                    <a href="{{ route('appointments.prescription.create', $currentAppointment) }}"
                       class="tb-btn green">
                        <i class="ti ti-file-plus"></i> New Prescription
                    </a>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <div class="doctor-content">

            {{-- Stats --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-calendar-event"></i> Today</div>
                    <div class="stat-value">{{ $totalToday }}</div>
                    <div class="stat-sub">appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-circle-check"></i> Completed</div>
                    <div class="stat-value" style="color:#166534;">{{ $completedToday }}</div>
                    <div class="stat-sub" style="color:#166534;">done so far</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-clock"></i> Remaining</div>
                    <div class="stat-value" style="color:#D97706;">{{ $remainingToday }}</div>
                    <div class="stat-sub">left today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-users"></i> This Week</div>
                    <div class="stat-value">{{ $weekAppointments }}</div>
                    <div class="stat-sub">appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-heart"></i> Total Patients</div>
                    <div class="stat-value">{{ $totalPatients }}</div>
                    <div class="stat-sub">all time</div>
                </div>
            </div>

            {{-- Two column --}}
            <div class="two-col">

                {{-- Today's schedule --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Today's Schedule</span>
                        <a href="{{ route('appointments.index') }}" class="card-link">Full view</a>
                    </div>

                    @if($todayAppointments->isEmpty())
                        <div class="empty-state">
                            <i class="ti ti-calendar-off"></i>
                            <p>No appointments today</p>
                        </div>
                    @else
                        <div class="slot-list">
                            @foreach($todayAppointments as $appt)
                                @php
                                    $isDone = $appt->status === 'completed';
                                    $isCancelled = $appt->status === 'cancelled';
                                    $isNow = $appt->status === 'in_progress' || $appt->id === $currentAppointment?->id;
                                    $initials = collect(explode(' ', $appt->patient?->name ?? 'U'))
                                        ->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                                @endphp
                                <div class="slot {{ $isDone ? 'done' : '' }} {{ $isCancelled ? 'cancelled' : '' }} {{ $isNow ? 'active' : '' }}">
                                    <span class="slot-time">
                                        {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                                    </span>
                                    <div class="slot-av">{{ $initials }}</div>
                                    <div class="slot-info">
                                        <p>{{ $appt->patient?->name ?? 'Unknown' }}</p>
                                        <span>{{ $appt->reason ?? ucfirst(str_replace('_', ' ', $appt->visit_type)) }}</span>
                                    </div>
                                    @if($isCancelled)
                                        <span class="pill cancelled">Cancelled</span>
                                    @elseif($isDone)
                                        <span class="pill done">Done</span>
                                    @elseif($isNow)
                                        <span class="pill now">Now</span>
                                    @else
                                        <span class="pill waiting">Waiting</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Right column --}}
                <div class="right-col">

                    {{-- Current patient --}}
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">
                                @if($currentAppointment)
                                    Current Patient
                                @else
                                    Next Patient
                                @endif
                            </span>
                            @if($currentAppointment)
                                <a href="{{ route('appointments.show', $currentAppointment) }}"
                                   class="card-link">View details</a>
                            @endif
                        </div>

                        @if($currentAppointment)
                            @php
                                $p = $currentAppointment->patient;
                                $initials = collect(explode(' ', $p?->name ?? 'U'))
                                    ->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                            @endphp
                            <div class="patient-card">
                                <div class="patient-header">
                                    <div class="patient-av">{{ $initials }}</div>
                                    <div class="patient-info">
                                        <p>{{ $p?->name ?? 'Unknown' }}</p>
                                        <span>
                                            {{ $p?->gender ? ucfirst($p->gender) . ' • ' : '' }}
                                            {{ $p?->phone ?? 'No phone' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="patient-meta">
                                    <span><i class="ti ti-clock"></i>
                                        {{ \Carbon\Carbon::parse($currentAppointment->appointment_time)->format('g:i A') }}
                                    </span>
                                    <span><i class="ti ti-stethoscope"></i>
                                        {{ ucfirst(str_replace('_', ' ', $currentAppointment->visit_type)) }}
                                    </span>
                                    @if($currentAppointment->reason)
                                        <span><i class="ti ti-notes"></i> {{ $currentAppointment->reason }}</span>
                                    @endif
                                </div>
                                <div class="patient-actions">
                                    <a href="{{ route('appointments.prescription.create', $currentAppointment) }}"
                                       class="action-btn primary">
                                        <i class="ti ti-file-plus"></i> Write Prescription
                                    </a>
                                    <a href="{{ route('appointments.show', $currentAppointment) }}"
                                       class="action-btn secondary">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="ti ti-user-off"></i>
                                <p>No active patient right now</p>
                            </div>
                        @endif
                    </div>

                    {{-- Recent prescriptions --}}
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Recent Prescriptions</span>
                            <a href="{{ route('prescriptions.index') }}" class="card-link">View all</a>
                        </div>

                        @if($recentPrescriptions->isEmpty())
                            <div class="empty-state">
                                <i class="ti ti-file-off"></i>
                                <p>No prescriptions yet</p>
                            </div>
                        @else
                            <div class="prescription-list">
                                @foreach($recentPrescriptions as $rx)
                                    <div class="rx-item">
                                        <div class="rx-top">
                                            <span class="rx-name">
                                                {{ $rx->appointment?->patient?->name ?? 'Unknown' }}
                                            </span>
                                            <span class="rx-date">
                                                {{ $rx->created_at->format('d M') }}
                                                @if($rx->created_at->isToday()) • Today @endif
                                            </span>
                                        </div>
                                        <div class="rx-diag">
                                            {{ $rx->diagnosis ?? $rx->chief_complaint ?? 'No diagnosis recorded' }}
                                        </div>
                                        <a href="{{ route('prescriptions.show', $rx) }}"
                                           class="rx-link">View →</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
.doctor-layout {
    display: grid;
    grid-template-columns: 210px minmax(0, 1fr);
    min-height: calc(100vh - 52px);
    margin: -1.5rem;
}

/* ── Sidebar ── */
.doctor-sidebar {
    background: #0F6E56;
    display: flex;
    flex-direction: column;
    padding-bottom: 1rem;
}

.sb-doctor {
    padding: 1rem;
    border-bottom: 0.5px solid #085041;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sb-av {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #085041;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    color: #9FE1CB;
    flex-shrink: 0;
}

.sb-doc-info p {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
}

.sb-doc-info span {
    font-size: 11px;
    color: #9FE1CB;
}

.sb-section {
    padding: 0.875rem 1rem 0.4rem;
    font-size: 10px;
    font-weight: 600;
    color: #5DCAA5;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.sb-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 1rem;
    font-size: 13px;
    color: #9FE1CB;
    text-decoration: none;
    border-radius: 6px;
    margin: 1px 6px;
    transition: background 0.15s;
}

.sb-item i { font-size: 15px; }

.sb-item:hover {
    background: #085041;
    color: #fff;
}

.sb-item.active {
    background: #04342C;
    color: #fff;
}

.sb-badge {
    margin-left: auto;
    background: #1D9E75;
    color: #E1F5EE;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 99px;
}

.sb-footer {
    margin-top: auto;
    padding: 1rem;
    border-top: 0.5px solid #085041;
}

.sb-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #9FE1CB;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #9CA3AF;
}

.status-dot.active { background: #5DCAA5; }

/* ── Main ── */
.doctor-main {
    display: flex;
    flex-direction: column;
    background: #F9FAFB;
}

.doctor-topbar {
    background: white;
    border-bottom: 0.5px solid #E5E7EB;
    padding: 0.875rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.doctor-topbar h1 {
    font-size: 17px;
    font-weight: 600;
    color: #111827;
}

.doctor-topbar p {
    font-size: 12px;
    color: #6B7280;
    margin-top: 2px;
}

.topbar-actions { display: flex; gap: 8px; }

.tb-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid #E5E7EB;
    color: #374151;
    background: white;
}

.tb-btn.green {
    background: #0F6E56;
    color: white;
    border-color: #0F6E56;
}

/* ── Content ── */
.doctor-content { padding: 1.25rem; }

/* ── Stats ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 12px;
    margin-bottom: 1.25rem;
}

.stat-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 1rem;
}

.stat-label {
    font-size: 11px;
    color: #6B7280;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 6px;
}

.stat-label i { font-size: 13px; }

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.stat-sub {
    font-size: 11px;
    color: #9CA3AF;
    margin-top: 4px;
}

/* ── Two col ── */
.two-col {
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 1rem;
}

.right-col {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ── Card ── */
.card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    overflow: hidden;
}

.card-header {
    padding: 14px 18px;
    border-bottom: 1px solid #F3F4F6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.card-link {
    font-size: 12px;
    color: #0F6E56;
    text-decoration: none;
    font-weight: 500;
}

/* ── Schedule slots ── */
.slot-list {
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    max-height: 460px;
    overflow-y: auto;
}

.slot {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    transition: background 0.15s;
}

.slot.active {
    border-color: #1D9E75;
    background: #F0FDF4;
}

.slot.done { opacity: 0.5; }
.slot.cancelled { opacity: 0.4; text-decoration: line-through; }

.slot-time {
    font-size: 11px;
    font-weight: 500;
    min-width: 56px;
    color: #6B7280;
}

.slot.active .slot-time { color: #065F46; }

.slot-av {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #E6F1FB;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 600;
    color: #0C447C;
    flex-shrink: 0;
}

.slot.active .slot-av {
    background: #DCFCE7;
    color: #065F46;
}

.slot-info { flex: 1; min-width: 0; }
.slot-info p { font-size: 12px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.slot-info span { font-size: 11px; color: #6B7280; }

.pill {
    padding: 3px 8px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
}

.pill.done { background: #EAF3DE; color: #27500A; }
.pill.now { background: #DCFCE7; color: #065F46; }
.pill.waiting { background: #FEF3C7; color: #92400E; }
.pill.cancelled { background: #FEE2E2; color: #991B1B; }

/* ── Patient card ── */
.patient-card { padding: 14px 18px; }

.patient-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.patient-av {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #E6F1FB;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: #0C447C;
    flex-shrink: 0;
}

.patient-info p { font-size: 14px; font-weight: 600; color: #111827; }
.patient-info span { font-size: 12px; color: #6B7280; }

.patient-meta {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 14px;
    font-size: 12px;
    color: #6B7280;
}

.patient-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.patient-meta i { font-size: 13px; color: #9CA3AF; }

.patient-actions { display: flex; gap: 8px; }

.action-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
}

.action-btn.primary { background: #0F6E56; color: white; }
.action-btn.secondary { background: #F3F4F6; color: #374151; }

/* ── Prescriptions ── */
.prescription-list {
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.rx-item {
    padding: 10px 12px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
}

.rx-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3px;
}

.rx-name { font-size: 13px; font-weight: 500; color: #111827; }
.rx-date { font-size: 11px; color: #9CA3AF; }
.rx-diag { font-size: 12px; color: #6B7280; margin-bottom: 4px; }
.rx-link { font-size: 11px; color: #0F6E56; text-decoration: none; font-weight: 500; }

/* ── Empty state ── */
.empty-state {
    padding: 2rem;
    text-align: center;
    color: #9CA3AF;
}

.empty-state i { font-size: 28px; margin-bottom: 8px; display: block; }
.empty-state p { font-size: 13px; }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .two-col { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .doctor-layout { grid-template-columns: 1fr; }
    .doctor-sidebar { display: none; }
}
</style>

@endsection