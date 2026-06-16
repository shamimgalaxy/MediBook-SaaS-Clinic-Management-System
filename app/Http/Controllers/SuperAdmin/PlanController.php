<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('tenants')->orderBy('price')->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'slug'              => 'required|string|unique:subscription_plans,slug|max:50',
            'price'             => 'required|numeric|min:0',
            'max_doctors'       => 'required|integer|min:-1',
            'max_appointments'  => 'required|integer|min:-1',
            'sms_notifications' => 'boolean',
            'custom_domain'     => 'boolean',
            'excel_reports'     => 'boolean',
            'is_active'         => 'boolean',
        ]);

        $validated['sms_notifications'] = $request->boolean('sms_notifications');
        $validated['custom_domain']     = $request->boolean('custom_domain');
        $validated['excel_reports']     = $request->boolean('excel_reports');
        $validated['is_active']         = $request->boolean('is_active', true);

        SubscriptionPlan::create($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'slug'              => 'required|string|unique:subscription_plans,slug,' . $plan->id . '|max:50',
            'price'             => 'required|numeric|min:0',
            'max_doctors'       => 'required|integer|min:-1',
            'max_appointments'  => 'required|integer|min:-1',
            'sms_notifications' => 'boolean',
            'custom_domain'     => 'boolean',
            'excel_reports'     => 'boolean',
            'is_active'         => 'boolean',
        ]);

        $validated['sms_notifications'] = $request->boolean('sms_notifications');
        $validated['custom_domain']     = $request->boolean('custom_domain');
        $validated['excel_reports']     = $request->boolean('excel_reports');
        $validated['is_active']         = $request->boolean('is_active', true);

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan updated.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->tenants()->count() > 0) {
            return back()->with('error', 'Cannot delete a plan with active clinics.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan deleted.');
    }

    public function toggle(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return back()->with('success', 'Plan ' . ($plan->is_active ? 'activated' : 'deactivated') . '.');
    }
}