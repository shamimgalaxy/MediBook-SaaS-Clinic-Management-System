<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;


class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'    => Tenant::count(),
            'active_tenants'   => Tenant::where('is_active', true)->count(),
            'on_trial'         => Tenant::where('on_trial', true)->count(),
            'trials_expiring'  => Tenant::where('on_trial', true)
                                    ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
                                    ->count(),
        ];

        $recentTenants = Tenant::with('subscriptionPlan')
            ->latest()
            ->limit(5)
            ->get();

        $plans = SubscriptionPlan::withCount('tenants')->get();

        return view('superadmin.dashboard', compact('stats', 'recentTenants', 'plans'));
    }
}