@extends('tenant.layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ── Subscription warning banner ── --}}
@if(!$subscriptionStatus['is_active'])
<div class="premium-alert error">
    <div class="alert-content">
        <i class="ti ti-alert-circle"></i>
        <div>
            <p class="alert-title">Subscription Expired</p>
            <p class="alert-subtitle">Your subscription has expired. Renew now to continue using MediBook.</p>
        </div>
    </div>
    <a href="{{ route('subscription.index') }}" class="alert-action">Renew Now</a>
</div>

@elseif($subscriptionStatus['days_remaining'] <= 7)
<div class="premium-alert warning">
    <div class="alert-content">
        <i class="ti ti-clock-exclamation"></i>
        <div>
            <p class="alert-title">
                {{ $subscriptionStatus['is_on_trial'] ? 'Trial' : 'Subscription' }} Expiring Soon
            </p>
            <p class="alert-subtitle">
                Your {{ $subscriptionStatus['is_on_trial'] ? 'free trial' : $subscriptionStatus['plan_name'] . ' plan' }}
                expires in <strong>{{ $subscriptionStatus['days_remaining'] }} day{{ $subscriptionStatus['days_remaining'] !== 1 ? 's' : '' }}</strong>
                on {{ $subscriptionStatus['expires_at'] ? \Carbon\Carbon::parse($subscriptionStatus['expires_at'])->format('d M Y') : '' }}.
            </p>
        </div>
    </div>
    <a href="{{ route('subscription.index') }}" class="alert-action">Renew Plan</a>
</div>
@endif

{{-- ── Subscription info card ── --}}
<div class="subscription-card">
    <div class="subscription-info">
        <div class="icon-circle">
            <i class="ti ti-credit-card"></i>
        </div>
        <div>
            <p class="plan-name">
                {{ $subscriptionStatus['plan_name'] }} Plan
                @if($subscriptionStatus['is_on_trial'])
                    <span class="trial-badge">Trial</span>
                @endif
            </p>
            <p class="plan-validity">
                @if($subscriptionStatus['expires_at'])
                    {{ $subscriptionStatus['is_on_trial'] ? 'Trial ends' : 'Valid until' }}
                    {{ $subscriptionStatus['expires_at']->format('d M Y') }}
                    • {{ max(0, $subscriptionStatus['days_remaining']) }} days remaining
                @else
                    No active subscription
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('subscription.index') }}" class="manage-btn">
        <i class="ti ti-credit-card"></i> Manage Subscription
    </a>
</div>

{{-- ── Page header ── --}}
@php $cs = \App\Models\ClinicSetting::first(); @endphp
<div class="content-header">
    <div class="clinic-header">
        @if($cs?->logo)
        <img src="{{ $cs->logoUrl() }}" class="clinic-logo" alt="Clinic Logo">
        @endif
        <div>
            <h1 class="clinic-name">{{ $cs?->clinic_name ?? tenant('clinic_name') }}</h1>
            <p class="clinic-tagline">
                {{ $cs?->tagline ?? 'Welcome back' }}
                @if($cs?->address)
                    • {{ $cs->address }}
                @endif
            </p>
        </div>
    </div>
</div>

{{-- ── Top stats ── --}}
<div class="stats-grid">
    <div class="stat-card premium">
        <div class="stat-value">{{ $totalDoctors }}</div>
        <div class="stat-label">Active Doctors</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value">{{ $totalPatients }}</div>
        <div class="stat-label">Patients</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value">{{ $appointmentStats['total'] }}</div>
        <div class="stat-label">Total Appointments</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value pending">{{ $appointmentStats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value completed">{{ $appointmentStats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
</div>

{{-- ── Billing summary ── --}}
<div class="section-title">Billing Summary</div>
<div class="stats-grid">
    <div class="stat-card premium">
        <div class="stat-value revenue">৳{{ number_format($billingStats['total_revenue'], 2) }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value revenue">৳{{ number_format($billingStats['this_month'], 2) }}</div>
        <div class="stat-label">This Month</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value revenue">৳{{ number_format($billingStats['today'], 2) }}</div>
        <div class="stat-label">Today</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value">{{ $billingStats['total_invoices'] }}</div>
        <div class="stat-label">Total Invoices</div>
    </div>
    <div class="stat-card premium">
        <div class="stat-value unpaid">{{ $billingStats['unpaid_count'] }}</div>
        <div class="stat-label">Unpaid Appointments</div>
    </div>
</div>

{{-- ── Two column layout ── --}}
<div class="dashboard-grid">
    {{-- Recent Invoices --}}
    <div class="card premium">
        <div class="card-header">
            <span>Recent Invoices</span>
            <a href="{{ route('invoices.index') }}" class="view-all">View all</a>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Patient</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentInvoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $invoice) }}" class="invoice-link">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td>{{ $invoice->patient->name }}</td>
                        <td class="amount">৳{{ number_format($invoice->total, 2) }}</td>
                        <td>
                            @if($invoice->status === 'paid')
                                <span class="status-pill paid">Paid</span>
                            @elseif($invoice->status === 'sent')
                                <span class="status-pill sent">Sent</span>
                            @else
                                <span class="status-pill draft">Draft</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state">No invoices yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Appointments --}}
    <div class="card premium">
        <div class="card-header">
            <span>Recent Appointments</span>
            <a href="{{ route('appointments.index') }}" class="view-all">View all</a>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAppointments as $appt)
                    <tr>
                        <td>{{ $appt->patient->name }}</td>
                        <td>Dr. {{ $appt->doctor->name }}</td>
                        <td>{{ $appt->appointment_date->format('d M') }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending'     => ['#FEF3C7','#D97706'],
                                    'confirmed'   => ['#E6F1FB','#185FA5'],
                                    'in_progress' => ['#EDE9FE','#5B21B6'],
                                    'completed'   => ['#EAF3DE','#27500A'],
                                    'cancelled'   => ['#FEE2E2','#A32D2D'],
                                ];
                                [$sbg,$sfg] = $statusColors[$appt->status] ?? ['#F3F4F6','#374151'];
                            @endphp
                            <span class="status-pill" style="background:{{ $sbg }};color:{{ $sfg }};">
                                {{ ucfirst(str_replace('_',' ',$appt->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state">No appointments yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Premium Modern Styles */
.premium-alert {
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.premium-alert.error {
    background: linear-gradient(135deg, #FEE2E2, #FCEBEB);
    border: 1px solid #F5C6C6;
    color: #991B1B;
}

.premium-alert.warning {
    background: linear-gradient(135deg, #FEF3C7, #FAEEDA);
    border: 1px solid #F5D9A8;
    color: #92400E;
}

.alert-content { display: flex; align-items: center; gap: 12px; }
.alert-content i { font-size: 24px; flex-shrink: 0; }
.alert-title { font-weight: 600; margin: 0; font-size: 14px; }
.alert-subtitle { margin: 2px 0 0; font-size: 13px; opacity: 0.9; }

.alert-action {
    background: #1E40AF;
    color: white;
    padding: 8px 16px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
}

.subscription-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 20px;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.icon-circle {
    width: 52px;
    height: 52px;
    background: #EFF6FF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #1E40AF;
}

.plan-name {
    font-size: 15px;
    font-weight: 600;
    margin: 0;
}

.trial-badge {
    font-size: 11px;
    background: #ECFDF5;
    color: #065F46;
    padding: 1px 8px;
    border-radius: 9999px;
    margin-left: 8px;
}

.manage-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #1E40AF;
    color: white;
    padding: 10px 18px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
}

.content-header {
    margin-bottom: 2rem;
}

.clinic-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.clinic-logo {
    height: 48px;
    object-fit: contain;
    border-radius: 8px;
}

.clinic-name {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    color: #111827;
}

.clinic-tagline {
    margin: 4px 0 0;
    font-size: 14px;
    color: #6B7280;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 2rem;
}

.stat-card.premium {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 20px;
    padding: 20px 24px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card.premium:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
}

.stat-value.revenue { color: #1E40AF; }
.stat-value.pending { color: #D97706; }
.stat-value.completed { color: #166534; }
.stat-value.unpaid { color: #B45309; }

.stat-label {
    font-size: 12px;
    color: #6B7280;
    margin-top: 8px;
    font-weight: 500;
}

.section-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6B7280;
    margin-bottom: 12px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.card.premium {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #E5E7EB;
}

.card-header {
    padding: 16px 24px;
    border-bottom: 1px solid #F3F4F6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    font-size: 14px;
}

.view-all {
    font-size: 12px;
    color: #1E40AF;
    text-decoration: none;
    font-weight: 500;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

.modern-table th {
    text-align: left;
    padding: 14px 24px;
    font-size: 11px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    background: #F9FAFB;
}

.modern-table td {
    padding: 16px 24px;
    border-top: 1px solid #F3F4F6;
}

.invoice-link {
    color: #1E40AF;
    font-family: ui-monospace, monospace;
    font-weight: 500;
}

.amount {
    font-weight: 600;
    color: #111827;
}

.status-pill {
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 500;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
    color: #9CA3AF;
    font-style: italic;
}

.table-responsive {
    overflow-x: auto;
}

/* Responsive */
@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection