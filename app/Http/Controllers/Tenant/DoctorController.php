<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $user   = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        $today  = Carbon::today();

        // ── Today's appointments ───────────────────────────────
        $todayAppointments = Appointment::with(['patient'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        // ── Stats ──────────────────────────────────────────────
        $totalToday     = $todayAppointments->count();
        $completedToday = $todayAppointments->where('status', 'completed')->count();
        $remainingToday = $todayAppointments->whereNotIn('status', ['completed', 'cancelled'])->count();
        $totalPatients  = Appointment::where('doctor_id', $doctor->id)
                            ->distinct('patient_id')
                            ->count('patient_id');

        // ── Current/next patient ───────────────────────────────
        $now = Carbon::now()->format('H:i:s');

        $currentAppointment = $todayAppointments
            ->where('status', 'in_progress')
            ->first();

        if (!$currentAppointment) {
            $currentAppointment = $todayAppointments
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('appointment_time', '>=', $now)
                ->first();
        }

        // ── Recent prescriptions ───────────────────────────────
        $recentPrescriptions = Prescription::with(['appointment.patient'])
            ->whereHas('appointment', fn($q) => $q->where('doctor_id', $doctor->id))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── This week's appointments ───────────────────────────
        $weekAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('appointment_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();

        return view('tenant.doctor.dashboard', compact(
            'doctor',
            'todayAppointments',
            'totalToday',
            'completedToday',
            'remainingToday',
            'totalPatients',
            'currentAppointment',
            'recentPrescriptions',
            'weekAppointments',
        ));
    }
}