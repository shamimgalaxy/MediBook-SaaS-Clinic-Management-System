@extends('tenant.layouts.app')

@section('title', 'Doctor Performance Report')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Doctor Performance</h1>
        <p class="page-sub">Compare appointments and revenue per doctor</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('reports.revenue') }}" class="tb-btn">Revenue</a>
        <a href="{{ route('reports.appointments') }}" class="tb-btn">Appointments</a>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4" style="padding:0.875rem 1rem;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:6px;">
            <label style="font-size:12px;color:var(--color-text-secondary);">From</label>
            <input type="date" name="from" value="{{ $from }}" class="filter-input" />
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <label style="font-size:12px;color:var(--color-text-secondary);">To</label>
            <input type="date" name="to" value="{{ $to }}" class="filter-input" />
        </div>
        <button type="submit" class="tb-btn primary">Apply</button>
        <a href="{{ route('reports.doctors') }}" class="tb-btn">Reset</a>
    </form>
</div>

{{-- Summary cards --}}
@php
    $totalAppts    = $doctors->sum('total_appointments');
    $totalRevenue  = $doctors->sum('revenue');
    $totalComplete = $doctors->sum('completed');
@endphp
<div class="stats-row mb-4">
    <div class="stat-card">
        <span class="stat-num">{{ $doctors->count() }}</span>
        <span class="stat-label">Active Doctors</span>
    </div>
    <div class="stat-card">
        <span class="stat-num">{{ $totalAppts }}</span>
        <span class="stat-label">Total Appointments</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#27500A;">{{ $totalComplete }}</span>
        <span class="stat-label">Completed</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#185FA5;">৳{{ number_format($totalRevenue, 2) }}</span>
        <span class="stat-label">Total Revenue</span>
    </div>
</div>

{{-- Bar chart --}}
@if($doctors->count())
<div class="card mb-4" style="padding:1.1rem;">
    <div style="font-size:13px;font-weight:500;margin-bottom:1rem;">Appointments per Doctor</div>
    <canvas id="doctorChart" height="80"></canvas>
</div>
@endif

{{-- Performance table --}}
<div class="card" style="padding:0;">
    <div style="padding:12px 16px;border-bottom:0.5px solid var(--color-border-tertiary);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:13px;font-weight:500;">Detailed Breakdown</span>
        <span style="font-size:12px;color:var(--color-text-secondary);">
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} —
            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </span>
    </div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                <th class="th">Doctor</th>
                <th class="th" style="text-align:center;">Total</th>
                <th class="th" style="text-align:center;">Completed</th>
                <th class="th" style="text-align:center;">Cancelled</th>
                <th class="th" style="text-align:center;">Completion %</th>
                <th class="th" style="text-align:right;">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($doctors as $doctor)
            @php
                $completionRate = $doctor->total_appointments > 0
                    ? round(($doctor->completed / $doctor->total_appointments) * 100)
                    : 0;
                $rateColor = $completionRate >= 75
                    ? '#27500A' : ($completionRate >= 50 ? '#D97706' : '#A32D2D');
            @endphp
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">

                {{-- Doctor --}}
                <td style="padding:10px 16px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($doctor->avatar)
                            <img src="{{ Storage::url($doctor->avatar) }}"
                                 style="width:32px;height:32px;border-radius:50%;object-fit:cover;" />
                        @else
                            <div class="avatar-circle">{{ $doctor->initials }}</div>
                        @endif
                        <div>
                            <div style="font-weight:500;">Dr. {{ $doctor->name }}</div>
                            <div style="font-size:11px;color:var(--color-text-secondary);">
                                {{ $doctor->specialization ?? '—' }}
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Total --}}
                <td style="padding:10px 16px;text-align:center;font-weight:500;">
                    {{ $doctor->total_appointments }}
                </td>

                {{-- Completed --}}
                <td style="padding:10px 16px;text-align:center;">
                    <span class="pill" style="background:#EAF3DE;color:#27500A;">
                        {{ $doctor->completed }}
                    </span>
                </td>

                {{-- Cancelled --}}
                <td style="padding:10px 16px;text-align:center;">
                    <span class="pill" style="background:#FEE2E2;color:#A32D2D;">
                        {{ $doctor->cancelled }}
                    </span>
                </td>

                {{-- Completion rate --}}
                <td style="padding:10px 16px;text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                        <div style="width:60px;height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden;">
                            <div style="width:{{ $completionRate }}%;height:100%;background:{{ $rateColor }};border-radius:99px;"></div>
                        </div>
                        <span style="font-size:12px;font-weight:500;color:{{ $rateColor }};">
                            {{ $completionRate }}%
                        </span>
                    </div>
                </td>

                {{-- Revenue --}}
                <td style="padding:10px 16px;text-align:right;font-weight:600;color:#185FA5;">
                    ৳{{ number_format($doctor->revenue, 2) }}
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:2rem;text-align:center;color:var(--color-text-secondary);">
                    No doctors found.
                </td>
            </tr>
            @endforelse
        </tbody>

        @if($doctors->count())
        <tfoot>
            <tr style="border-top:1px solid var(--color-border-tertiary);background:var(--color-background-secondary);">
                <td style="padding:10px 16px;font-weight:600;">Total</td>
                <td style="padding:10px 16px;text-align:center;font-weight:600;">{{ $totalAppts }}</td>
                <td style="padding:10px 16px;text-align:center;font-weight:600;">{{ $totalComplete }}</td>
                <td style="padding:10px 16px;text-align:center;font-weight:600;">{{ $doctors->sum('cancelled') }}</td>
                <td style="padding:10px 16px;text-align:center;font-weight:600;">
                    @if($totalAppts > 0)
                        {{ round(($totalComplete / $totalAppts) * 100) }}%
                    @else — @endif
                </td>
                <td style="padding:10px 16px;text-align:right;font-weight:700;color:#185FA5;">
                    ৳{{ number_format($totalRevenue, 2) }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.tb-btn.primary{background:#185FA5;color:#fff;border-color:#185FA5;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.mb-4{margin-bottom:1rem;}
.stats-row{display:flex;gap:12px;flex-wrap:wrap;}
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:12px 20px;display:flex;flex-direction:column;align-items:center;min-width:120px;}
.stat-num{font-size:22px;font-weight:600;line-height:1.1;}
.stat-label{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}
.filter-input{padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.th{padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
.table-row-hover:hover{background:var(--color-background-secondary);}
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
.avatar-circle{width:32px;height:32px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:#0C447C;flex-shrink:0;}
</style>

@if($doctors->count())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels      = @json($doctors->pluck('name')->map(fn($n) => 'Dr. ' . $n));
    const appts       = @json($doctors->pluck('total_appointments'));
    const completed   = @json($doctors->pluck('completed'));
    const revenue     = @json($doctors->pluck('revenue'));

    new Chart(document.getElementById('doctorChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Total',
                    data: appts,
                    backgroundColor: 'rgba(24,95,165,0.15)',
                    borderColor: '#185FA5',
                    borderWidth: 1.5,
                    borderRadius: 4,
                },
                {
                    label: 'Completed',
                    data: completed,
                    backgroundColor: 'rgba(39,80,10,0.15)',
                    borderColor: '#27500A',
                    borderWidth: 1.5,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11 }, padding: 12 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endif

@endsection