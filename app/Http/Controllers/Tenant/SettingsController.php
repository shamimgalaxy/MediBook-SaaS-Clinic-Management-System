<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    // ── Show settings page ─────────────────────────────────────
    public function index()
    {
        $settings = ClinicSetting::firstOrCreate(
            [],
            [
                'clinic_name'  => tenant('clinic_name'),
                'working_hours'=> ClinicSetting::defaultWorkingHours(),
            ]
        );

        return view('tenant.settings.index', compact('settings'));
    }

    // ── Update general info ────────────────────────────────────
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'clinic_name'         => 'required|string|max:255',
            'tagline'             => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|url|max:255',
            'address'             => 'nullable|string|max:500',
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'invoice_prefix'      => 'nullable|string|max:10',
            'default_tax'         => 'nullable|numeric|min:0|max:100',
            'invoice_footer_note' => 'nullable|string|max:500',
        ]);

        $settings = ClinicSetting::firstOrCreate([]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($settings->logo) {
                Storage::delete($settings->logo);
            }
            $validated['logo'] = $request->file('logo')
                ->store('clinic/logos', 'public');
        }

        $settings->update($validated);

        return back()->with('success', 'General settings saved.');
    }

    // ── Update working hours ───────────────────────────────────
    public function updateHours(Request $request)
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        $workingHours = [];
        foreach ($days as $day) {
            $workingHours[$day] = [
                'open'  => $request->boolean("days.{$day}.open"),
                'start' => $request->input("days.{$day}.start", '09:00'),
                'end'   => $request->input("days.{$day}.end", '17:00'),
            ];
        }

        $settings = ClinicSetting::firstOrCreate([]);
        $settings->update(['working_hours' => $workingHours]);

        return back()->with('success', 'Working hours saved.');
    }

    // ── Update notification preferences ───────────────────────
    public function updateNotifications(Request $request)
    {
        $settings = ClinicSetting::firstOrCreate([]);

        $settings->update([
            'notify_appointment_booked' => $request->boolean('notify_appointment_booked'),
            'notify_appointment_status' => $request->boolean('notify_appointment_status'),
            'notify_payment_received'   => $request->boolean('notify_payment_received'),
            'notify_sms_enabled'        => $request->boolean('notify_sms_enabled'),
        ]);

        return back()->with('success', 'Notification preferences saved.');
    }

    // ── Delete logo ────────────────────────────────────────────
    public function deleteLogo()
    {
        $settings = ClinicSetting::firstOrCreate([]);

        if ($settings->logo) {
            Storage::delete($settings->logo);
            $settings->update(['logo' => null]);
        }

        return back()->with('success', 'Logo removed.');
    }
}