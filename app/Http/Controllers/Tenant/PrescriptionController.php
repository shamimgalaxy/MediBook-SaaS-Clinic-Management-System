<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionController extends Controller
{
    public function index()
{
    $user  = Auth::user();
    $query = Prescription::with(['doctor', 'items'])->orderByDesc('created_at');

    if ($user->hasRole('patient')) {
        $query->where('patient_id', $user->id);
    } elseif ($user->hasRole('doctor')) {
        $query->whereHas('doctor', fn($q) => $q->where('user_id', $user->id));
    }

    $prescriptions = $query->paginate(15);

    return view('tenant.prescriptions.index', compact('prescriptions'));
}
    // ── Create form ────────────────────────────────────────────
    public function create(Appointment $appointment)
    {
        // Only the appointment's doctor can write a prescription
        $this->authorizeDoctorAccess($appointment);

        // Prevent duplicate prescription
        if ($appointment->prescription) {
            return redirect()->route('prescriptions.show', $appointment->prescription)
                ->with('error', 'Prescription already exists for this appointment.');
        }

        $appointment->load(['patient', 'doctor']);

        return view('tenant.prescriptions.create', compact('appointment'));
    }

    // ── Store ──────────────────────────────────────────────────
    public function store(Request $request, Appointment $appointment)
    {
        $this->authorizeDoctorAccess($appointment);

        $validated = $request->validate([
            'chief_complaint'        => 'nullable|string|max:500',
            'diagnosis'              => 'nullable|string|max:500',
            'notes'                  => 'nullable|string|max:1000',
            'follow_up_date'         => 'nullable|date|after:today',
            'items'                  => 'nullable|array',
            'items.*.medicine_name'  => 'required_with:items|string|max:255',
            'items.*.dosage'         => 'nullable|string|max:100',
            'items.*.frequency'      => 'nullable|string|max:100',
            'items.*.duration'       => 'nullable|string|max:100',
            'items.*.instructions'   => 'nullable|string|max:255',
        ]);

        $prescription = Prescription::create([
            'appointment_id'  => $appointment->id,
            'doctor_id'       => $appointment->doctor_id,
            'patient_id'      => $appointment->patient_id,
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'diagnosis'       => $validated['diagnosis'] ?? null,
            'notes'           => $validated['notes'] ?? null,
            'follow_up_date'  => $validated['follow_up_date'] ?? null,
        ]);

        // Save medicine items
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['medicine_name'])) {
                    $prescription->items()->create($item);
                }
            }
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Prescription created successfully.');
    }

    // ── Show ───────────────────────────────────────────────────
    public function show(Prescription $prescription)
    {
        $user = Auth::user();

        // Patients can only view their own
        if ($user->hasRole('patient') && $prescription->patient_id !== $user->id) {
            abort(403);
        }

        $prescription->load(['appointment', 'patient', 'doctor', 'items']);

        return view('tenant.prescriptions.show', compact('prescription'));
    }

    // ── Edit form ──────────────────────────────────────────────
    public function edit(Prescription $prescription)
    {
        $this->authorizeDoctorOwnership($prescription);

        $prescription->load(['appointment', 'patient', 'doctor', 'items']);

        return view('tenant.prescriptions.edit', compact('prescription'));
    }

    // ── Update ─────────────────────────────────────────────────
    public function update(Request $request, Prescription $prescription)
    {
        $this->authorizeDoctorOwnership($prescription);

        $validated = $request->validate([
            'chief_complaint'        => 'nullable|string|max:500',
            'diagnosis'              => 'nullable|string|max:500',
            'notes'                  => 'nullable|string|max:1000',
            'follow_up_date'         => 'nullable|date',
            'items'                  => 'nullable|array',
            'items.*.medicine_name'  => 'required_with:items|string|max:255',
            'items.*.dosage'         => 'nullable|string|max:100',
            'items.*.frequency'      => 'nullable|string|max:100',
            'items.*.duration'       => 'nullable|string|max:100',
            'items.*.instructions'   => 'nullable|string|max:255',
        ]);

        $prescription->update([
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'diagnosis'       => $validated['diagnosis'] ?? null,
            'notes'           => $validated['notes'] ?? null,
            'follow_up_date'  => $validated['follow_up_date'] ?? null,
        ]);

        // Replace all items
        $prescription->items()->delete();

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['medicine_name'])) {
                    $prescription->items()->create($item);
                }
            }
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Prescription updated.');
    }

    // ── Helpers ────────────────────────────────────────────────

    private function authorizeDoctorAccess(Appointment $appointment): void
    {
        $user = Auth::user();

        if (!$user->hasRole('doctor')) {
            abort(403);
        }

        // Make sure this doctor owns the appointment
        if ($appointment->doctor->user_id !== $user->id) {
            abort(403);
        }
    }

    private function authorizeDoctorOwnership(Prescription $prescription): void
    {
        $user = Auth::user();

        if (!$user->hasRole('doctor')) {
            abort(403);
        }

        if ($prescription->doctor->user_id !== $user->id) {
            abort(403);
        }
    }
    public function downloadPdf(Prescription $prescription)
{
    $user = Auth::user();

    // Patients only their own
    if ($user->hasRole('patient') && $prescription->patient_id !== $user->id) {
        abort(403);
    }

    $prescription->load(['appointment', 'patient', 'doctor', 'items']);

    $pdf = Pdf::loadView('tenant.prescriptions.pdf', compact('prescription'))
        ->setPaper('a4', 'portrait');

    return $pdf->download('prescription-' . $prescription->id . '.pdf');
}
}