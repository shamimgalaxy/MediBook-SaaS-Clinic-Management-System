@extends('tenant.layouts.app')

@section('title', 'Revenue Report')

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Revenue Report</h1>
        <p class="page-sub">Track clinic income over time</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('reports.appointments') }}" class="tb-btn">Appointments</a>
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
        <select name="period" class="filter-input">
            <option value="daily"   @selected($period === 'daily')>Daily</option>
            <option value="weekly"  @selected($period === 'weekly')>Weekly</option>
            <option value="monthly" @selected($period === 'monthly')>Monthly</option>
        </select>
        <button type="submit" class="tb-btn primary">Apply</button>
        <a href="{{ route('reports.revenue') }}" class="tb-btn">Reset</a>
        <a href="{{ route('reports.revenue.export', ['from' => $from, 'to' => $to]) }}"
           class="tb-btn" style="margin-left:auto;">
            <i class="ti ti-table-export"></i> Export Excel
        </a>
    </form>
</div>

{{-- Summary cards --}}
<div class="stats-row mb-4">
    <div class="stat-card">
        <span class="stat-num" style="color:#185FA5;">৳{{ number_format($summary['total_revenue'], 2) }}</span>
        <span class="stat-label">Total Revenue</span>
    </div>
    <div class="stat-card">
        <span class="stat-num">{{ $summary['total_invoices'] }}</span>
        <span class="stat-label">Paid Invoices</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#27500A;">৳{{ number_format($summary['avg_per_invoice'], 2) }}</span>
        <span class="stat-label">Avg per Invoice</span>
    </div>
    <div class="stat-card">
        <span class="stat-num" style="color:#D97706;">{{ $summary['unpaid_count'] }}</span>
        <span class="stat-label">Unpaid Appointments</span>
    </div>
</div>

{{-- Chart --}}
<div class="card mb-4" style="padding:1.1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <span style="font-size:13px;font-weight:500;">Revenue Trend</span>
        <span style="font-size:12px;color:var(--color-text-secondary);">
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} —
            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </span>
    </div>
    @if($chartData->count())
        <canvas id="revenueChart" height="80"></canvas>
    @else
        <p style="text-align:center;color:var(--color-text-secondary);font-size:13px;padding:2rem 0;">
            No data for selected period.
        </p>
    @endif
</div>

{{-- Data table --}}
<div class="card" style="padding:0;">
    <div style="padding:12px 16px;border-bottom:0.5px solid var(--color-border-tertiary);">
        <span style="font-size:13px;font-weight:500;">Breakdown</span>
    </div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);">
                <th class="th">Period</th>
                <th class="th">Invoices</th>
                <th class="th" style="text-align:right;">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chartData as $row)
            <tr style="border-bottom:0.5px solid var(--color-border-tertiary);" class="table-row-hover">
                <td style="padding:9px 16px;font-weight:500;">{{ $row->period }}</td>
                <td style="padding:9px 16px;">{{ $row->count }}</td>
                <td style="padding:9px 16px;text-align:right;font-weight:600;color:#185FA5;">
                    ৳{{ number_format($row->revenue, 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="padding:2rem;text-align:center;color:var(--color-text-secondary);">
                    No records found.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($chartData->count())
        <tfoot>
            <tr style="border-top:1px solid var(--color-border-tertiary);background:var(--color-background-secondary);">
                <td style="padding:9px 16px;font-weight:600;">Total</td>
                <td style="padding:9px 16px;font-weight:600;">{{ $chartData->sum('count') }}</td>
                <td style="padding:9px 16px;text-align:right;font-weight:700;color:#185FA5;">
                    ৳{{ number_format($chartData->sum('revenue'), 2) }}
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
.stat-card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);padding:12px 20px;display:flex;flex-direction:column;align-items:center;min-width:140px;}
.stat-num{font-size:22px;font-weight:600;line-height:1.1;}
.stat-label{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}
.filter-input{padding:6px 10px;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);font-size:13px;background:var(--color-background-primary);color:var(--color-text-primary);}
.th{padding:10px 16px;text-align:left;font-size:11px;font-weight:500;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.4px;}
.table-row-hover:hover{background:var(--color-background-secondary);}
</style>

@if($chartData->count())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels  = @json($chartData->pluck('period'));
    const revenue = @json($chartData->pluck('revenue')->map(fn($v) => round($v, 2)));

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Revenue (৳)',
                data: revenue,
                borderColor: '#185FA5',
                backgroundColor: 'rgba(24,95,165,0.08)',
                borderWidth: 2,
                pointBackgroundColor: '#185FA5',
                pointRadius: 4,
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '৳' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => '৳' + val.toLocaleString(),
                        font: { size: 11 }
                    },
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