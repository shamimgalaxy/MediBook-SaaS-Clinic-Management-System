<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\RevenueExport;
use App\Exports\AppointmentsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ── Revenue Report ─────────────────────────────────────────
    public function revenue(Request $request)
    {
        $from   = $request->get('from', now()->startOfMonth()->toDateString());
        $to     = $request->get('to', now()->toDateString());
        $period = $request->get('period', 'daily');

        $baseQuery = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        // Summary cards
        $summary = [
            'total_revenue'  => (clone $baseQuery)->sum('total'),
            'total_invoices' => (clone $baseQuery)->count(),
            'avg_per_invoice'=> (clone $baseQuery)->avg('total') ?? 0,
            'unpaid_count'   => Appointment::where('payment_status', 'unpaid')
                                    ->whereIn('status', ['confirmed', 'completed'])
                                    ->count(),
        ];

        // Chart data grouped by period
        $groupFormat = match($period) {
            'monthly' => '%Y-%m',
            'weekly'  => '%x-W%v',
            default   => '%Y-%m-%d',
        };

        $chartData = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(paid_at, '{$groupFormat}') as period, SUM(total) as revenue, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('tenant.reports.revenue', compact(
            'summary', 'chartData', 'from', 'to', 'period'
        ));
    }

    // ── Appointment Report ─────────────────────────────────────
    public function appointments(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $base = Appointment::whereBetween('appointment_date', [$from, $to]);

        $summary = [
            'total'     => (clone $base)->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'pending'   => (clone $base)->where('status', 'pending')->count(),
        ];

        // By status
        $byStatus = (clone $base)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // By visit type
        $byType = (clone $base)
            ->selectRaw('visit_type, COUNT(*) as count')
            ->groupBy('visit_type')
            ->pluck('count', 'visit_type');

        // Daily trend
        $dailyTrend = (clone $base)
            ->selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('tenant.reports.appointments', compact(
            'summary', 'byStatus', 'byType', 'dailyTrend', 'from', 'to'
        ));
    }

    // ── Doctor Performance Report ──────────────────────────────
    public function doctors(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->get()
            ->map(function ($doctor) use ($from, $to) {
                $base = Appointment::where('doctor_id', $doctor->id)
                    ->whereBetween('appointment_date', [$from, $to]);

                $doctor->total_appointments = (clone $base)->count();
                $doctor->completed          = (clone $base)->where('status', 'completed')->count();
                $doctor->cancelled          = (clone $base)->where('status', 'cancelled')->count();
                $doctor->revenue            = Invoice::where('doctor_id', $doctor->id)
                    ->where('status', 'paid')
                    ->whereBetween('paid_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                    ->sum('total');

                return $doctor;
            })
            ->sortByDesc('total_appointments');

        return view('tenant.reports.doctors', compact('doctors', 'from', 'to'));
    }
    // ── Revenue Excel export ───────────────────────────────────
public function exportRevenue(Request $request)
{
    $from = $request->get('from', now()->startOfMonth()->toDateString());
    $to   = $request->get('to', now()->toDateString());

    $filename = 'revenue-' . $from . '-to-' . $to . '.xlsx';

    return Excel::download(new RevenueExport($from, $to), $filename);
}

// ── Appointments Excel export ──────────────────────────────
public function exportAppointments(Request $request)
{
    $from = $request->get('from', now()->startOfMonth()->toDateString());
    $to   = $request->get('to', now()->toDateString());

    $filename = 'appointments-' . $from . '-to-' . $to . '.xlsx';

    return Excel::download(new AppointmentsExport($from, $to), $filename);
}
}