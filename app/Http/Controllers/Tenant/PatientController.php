<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // ── Upcoming appointments ──────────────────────────────
        $upcoming = Appointment::with(['doctor'])
            ->where('patient_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(3)
            ->get();

        // ── Recent appointments ────────────────────────────────
        $recentAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $user->id)
            ->orderByDesc('appointment_date')
            ->limit(5)
            ->get();

        // ── Stats ──────────────────────────────────────────────
        $stats = [
            'total_appointments' => Appointment::where('patient_id', $user->id)->count(),
            'completed'          => Appointment::where('patient_id', $user->id)
                                        ->where('status', 'completed')->count(),
            'upcoming'           => Appointment::where('patient_id', $user->id)
                                        ->whereIn('status', ['pending', 'confirmed'])
                                        ->whereDate('appointment_date', '>=', today())
                                        ->count(),
            'unpaid_invoices'    => Invoice::where('patient_id', $user->id)
                                        ->where('status', '!=', 'paid')->count(),
        ];

        // ── Recent prescriptions ───────────────────────────────
        $recentPrescriptions = Prescription::with(['doctor'])
            ->where('patient_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // ── Recent invoices ────────────────────────────────────
        $recentInvoices = Invoice::where('patient_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('tenant.patient.dashboard', compact(
            'upcoming',
            'recentAppointments',
            'stats',
            'recentPrescriptions',
            'recentInvoices'
        ));
    }
    public function profile()
{
    $user = Auth::user();
    return view('tenant.patient.profile', compact('user'));
}

public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'phone'         => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date|before:today',
        'gender'        => 'nullable|in:male,female,other',
        'address'       => 'nullable|string|max:500',
        'blood_group'   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
    ]);

    $user->update($validated);

    return back()->with('success', 'Profile updated successfully.');
}


}