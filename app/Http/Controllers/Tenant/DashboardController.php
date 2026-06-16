<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Appointment stats ──────────────────────────────────
        $appointmentStats = [
            'total'     => Appointment::count(),
            'pending'   => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        // ── Billing stats ──────────────────────────────────────
        $billingStats = [
            'total_revenue'  => Invoice::where('status', 'paid')->sum('total'),
            'this_month'     => Invoice::where('status', 'paid')
                                    ->whereMonth('paid_at', now()->month)
                                    ->whereYear('paid_at', now()->year)
                                    ->sum('total'),
            'today'          => Invoice::where('status', 'paid')
                                    ->whereDate('paid_at', today())
                                    ->sum('total'),
            'unpaid_count'   => Appointment::where('payment_status', 'unpaid')
                                    ->whereIn('status', ['confirmed', 'completed'])
                                    ->count(),
            'total_invoices' => Invoice::count(),
        ];

        // ── Recent invoices (last 5) ───────────────────────────
        $recentInvoices = Invoice::with(['patient', 'doctor'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Recent appointments (last 5) ──────────────────────
        $recentAppointments = Appointment::with(['doctor', 'patient'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Quick counts ──────────────────────────────────────
        $totalDoctors = Doctor::where('is_active', true)->count();

        // Force central DB for user/role queries
        $totalPatients = User::on('central')
            ->where('tenant_id', tenant('id'))
            ->whereHas('roles', fn($q) => $q->where('name', 'patient'))
            ->count();

        // ── Subscription status ────────────────────────────────
        $tenant = tenant();
        $rawExpiry = $tenant->on_trial ? $tenant->trial_ends_at : $tenant->plan_expires_at;

        $subscriptionStatus = [
            'is_active'      => $tenant->isSubscriptionActive(),
            'is_on_trial'    => $tenant->isOnTrial(),
            'days_remaining' => $tenant->daysUntilExpiry(),
            'plan_name'      => $tenant->subscriptionPlan?->name ?? ($tenant->on_trial ? 'Trial' : 'No Plan'),
            'expires_at'     => $rawExpiry ? Carbon::parse($rawExpiry) : null,
        ];

        return view('tenant.admin.dashboard', compact(
            'appointmentStats',
            'billingStats',
            'recentInvoices',
            'recentAppointments',
            'totalDoctors',
            'totalPatients',
            'subscriptionStatus',
        ));
    }
}