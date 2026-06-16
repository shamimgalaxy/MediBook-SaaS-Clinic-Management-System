@extends('tenant.layouts.app')

@section('title', 'Appointment Report')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Appointment Report</h1>
        <p class="page-sub">Analyse appointment trends and patterns</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('reports.revenue') }}" class="tb-btn">Revenue</a>
        <a href="{{ route('reports.doctors') }}" class="tb-btn">Doctors</a>
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
       <a href="{{ route('reports.appointments.export', ['from' => $from, 'to' => $to]) }}"
   class="tb-btn" style="margin-left:auto;">
    <i class="ti ti-table-export"></i> Export Excel
</a>
    </form>
</div>

{{-- Summary cards --}}
<div class="stats-row mb-4">
    <div class="stat-card">
        <span class="stat-num">{{ $summary['total'] }}</span>
        <span class="stat-label">Total</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#27500A;">{{ $summary['completed'] }}</span>
        <span class="stat-label">Completed</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#D97706;">{{ $summary['pending'] }}</span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#A32D2D;">{{ $summary['cancelled'] }}</span>
        <span class="stat-label">Cancelled</span>
    </div>
    @if($summary['total'] > 0)
    <div class="stat-card">
        <span class="stat-num" style="color:#185FA5;">
            {{ round(($summary['completed'] / $summary['total']) * 100) }}%
        </span>
        <span class="stat-label">Completion Rate</span>
    </div>
    @endif
</div>

{{-- Charts row --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">

    {{-- By Status --}}
    <div class="card" style="padding:1.1rem;">
        <div style="font-size:13px;font-weight:500;margin-bottom:1rem;">By Status</div>
        @if($byStatus->count())
            <canvas id="statusChart" height="160"></canvas>
        @else
            <p style="text-align:center;color:var(--color-text-secondary);font-size:13px;padding:2rem 0;">No data.</p>
        @endif
    </div>

    {{-- By Visit Type --}}
    <div class="card" style="padding:1.1rem;">
        <div style="font-size:13px;font-weight:500;margin-bottom:1rem;">By Visit Type</div>
        @if($byType->count())
            <canvas id="typeChart" height="160"></canvas>
        @else
            <p style="text-align:center;color:var(--color-text-secondary);font-size:13px;padding:2rem 0;">No data.</p>
        @endif
    </div>

</div>

{{-- Daily trend chart --}}
<div class="card mb-4" style="padding:1.1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <span style="font-size:13px;font-weight:500;">Daily Trend</span>
        <span style="font-size:12px;color:var(--color-text-secondary);">
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} —
            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </span>
    </div>
    @if($dailyTrend->count())
        <canvas id="trendChart" height="70"></canvas>
    @else
        <p style="text-align:center;color:var(--color-text-secondary);font-size:13px;padding:2rem 0;">
            No data for selected period.
        </p>
    @endif
</div>

{{-- Status & Type breakdown tables --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

    {{-- By Status table --}}
    <div class="card" style="padding:0;">
        <div style="padding:12px 16px;border-bottom:0.5px solid var(--color-border-tertiary);">
            <span style="font-size:13px;font-weight:500;">Status Breakdown</span>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                    <th class="th">Status</th>
                    <th class="th" style="text-align:right;">Count</th>
                    <th class="th" style="text-align:right;">Share</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byStatus as $status => $count)
                @php
                    $statusColors = [
                        'pending'     => ['#FEF3C7','#D97706'],
                        'confirmed'   => ['#E6F1FB','#185FA5'],
                        'in_progress' => ['#EDE9FE','#5B21B6'],
                        'completed'   => ['#EAF3DE','#27500A'],
                        'cancelled'   => ['#FEE2E2','#A32D2D'],
                    ];
                    [$sbg,$sfg] = $statusColors[$status] ?? ['#F3F4F6','#374151'];
                    $share = $summary['total'] > 0 ? round(($count / $summary['total']) * 100) : 0;
                @endphp
                <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">
                    <td style="padding:9px 16px;">
                        <span class="pill" style="background:{{ $sbg }};color:{{ $sfg }};">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>
                    <td style="padding:9px 16px;text-align:right;font-weight:500;">{{ $count }}</td>
                    <td style="padding:9px 16px;text-align:right;color:var(--color-text-secondary);">{{ $share }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding:1.5rem;text-align:center;color:var(--color-text-secondary);">No data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- By Visit Type table --}}
    <div class="card" style="padding:0;">
        <div style="padding:12px 16px;border-bottom:0.5px solid var(--color-border-tertiary);">
            <span style="font-size:13px;font-weight:500;">Visit Type Breakdown</span>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                    <th class="th">Type</th>
                    <th class="th" style="text-align:right;">Count</th>
                    <th class="th" style="text-align:right;">Share</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byType as $type => $count)
                @php
                    $typeColors = [
                        'new'        => ['#E6F1FB','#0C447C'],
                        'follow_up'  => ['#F3F4F6','#374151'],
                        'emergency'  => ['#FEE2E2','#A32D2D'],
                    ];
                    [$tbg,$tfg] = $typeColors[$type] ?? ['#F3F4F6','#374151'];
                    $share = $summary['total'] > 0 ? round(($count / $summary['total']) * 100) : 0;
                @endphp
                <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">
                    <td style="padding:9px 16px;">
                        <span class="pill" style="background:{{ $tbg }};color:{{ $tfg }};">
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </span>
                    </td>
                    <td style="padding:9px 16px;text-align:right;font-weight:500;">{{ $count }}</td>
                    <td style="padding:9px 16px;text-align:right;color:var(--color-text-secondary);">{{ $share }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding:1.5rem;text-align:center;color:var(--color-text-secondary);">No data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:12px 20px;display:flex;flex-direction:column;align-items:center;min-width:100px;}
.stat-num{font-size:22px;font-weight:600;line-height:1.1;}
.stat-label{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}
.filter-input{padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.th{padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
.table-row-hover:hover{background:var(--color-background-secondary);}
.pill{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500;}
</style>

@if($byStatus->count() || $byType->count() || $dailyTrend->count())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Status doughnut ──
    @if($byStatus->count())
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byStatus->keys()->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))),
            datasets: [{
                data: @json($byStatus->values()),
                backgroundColor: ['#FEF3C7','#E6F1FB','#EDE9FE','#EAF3DE','#FEE2E2'],
                borderColor:     ['#D97706','#185FA5','#5B21B6','#27500A','#A32D2D'],
                borderWidth: 1.5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
            }
        }
    });
    @endif

    // ── Visit type doughnut ──
    @if($byType->count())
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byType->keys()->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))),
            datasets: [{
                data: @json($byType->values()),
                backgroundColor: ['#E6F1FB','#F3F4F6','#FEE2E2'],
                borderColor:     ['#185FA5','#374151','#A32D2D'],
                borderWidth: 1.5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
            }
        }
    });
    @endif

    // ── Daily trend bar ──
    @if($dailyTrend->count())
    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: @json($dailyTrend->pluck('date')),
            datasets: [{
                label: 'Appointments',
                data: @json($dailyTrend->pluck('count')),
                backgroundColor: 'rgba(24,95,165,0.15)',
                borderColor: '#185FA5',
                borderWidth: 1.5,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
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
    @endif

});
</script>
@endif

@endsection