<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['subscriptionPlan', 'domains'])
            ->latest();

        if ($search = $request->get('search')) {
            $query->where('clinic_name', 'like', "%$search%");
        }
        if ($status = $request->get('status')) {
            if ($status === 'active')    $query->where('is_active', true);
            if ($status === 'suspended') $query->where('is_active', false);
            if ($status === 'trial')     $query->where('on_trial', true);
        }
        if ($plan = $request->get('plan_id')) {
            $query->where('plan_id', $plan);
        }

        $tenants = $query->paginate(15)->withQueryString();
        $plans   = SubscriptionPlan::where('is_active', true)->get();

        return view('superadmin.tenants.index', compact('tenants', 'plans'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('superadmin.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clinic_name' => 'required|string|max:255',
            'domain'      => 'required|string|unique:domains,domain|max:100',
            'plan_id'     => 'nullable|exists:subscription_plans,id',
            'on_trial'    => 'boolean',
            'trial_days'  => 'nullable|integer|min:1|max:90',
        ]);

        // Generate tenant ID from clinic name
        $tenantId = Str::slug($validated['clinic_name']) . '-' . Str::random(4);

        $tenant = Tenant::create([
            'id'           => $tenantId,
            'clinic_name'  => $validated['clinic_name'],
            'plan_id'      => $validated['plan_id'] ?? null,
            'is_active'    => true,
            'on_trial'     => $request->boolean('on_trial', true),
            'trial_ends_at'=> $request->boolean('on_trial', true)
                                ? now()->addDays($validated['trial_days'] ?? 14)
                                : null,
        ]);

        $tenant->domains()->create([
            'domain' => $validated['domain'],
        ]);

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Clinic created successfully.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['subscriptionPlan', 'domains']);
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('superadmin.tenants.show', compact('tenant', 'plans'));
    }

    public function assignPlan(Request $request, Tenant $tenant)
    {
        $request->validate([
            'plan_id'      => 'required|exists:subscription_plans,id',
            'duration_days'=> 'required|integer|min:1',
        ]);

        $tenant->update([
            'plan_id'        => $request->plan_id,
            'plan_expires_at'=> now()->addDays($request->duration_days),
            'on_trial'       => false,
        ]);

        return back()->with('success', 'Plan assigned successfully.');
    }

    public function toggle(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);

        $status = $tenant->is_active ? 'activated' : 'suspended';

        return back()->with('success', "Clinic {$status}.");
    }
}