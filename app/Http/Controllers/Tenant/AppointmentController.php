<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\AppointmentBooked;
use App\Notifications\AppointmentStatusChanged;
use App\Notifications\PaymentReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\InvoiceService;

class AppointmentController extends Controller
{
    public function index()
    {
        $user  = Auth::user();

        $query = Appointment::with(['doctor', 'patient'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time');

        if ($user->hasRole('doctor')) {
            $query->whereHas('doctor', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->hasRole('patient')) {
            $query->where('patient_id', $user->id);
        }

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('doctor',  fn($q) => $q->where('name', 'like', "%$search%"));
            });
        }
        if ($date = request('date')) {
            $query->whereDate('appointment_date', $date);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        if ($doctorId = request('doctor_id')) {
            $query->where('doctor_id', $doctorId);
        }

        $appointments = $query->paginate(15);

        $stats = [
            'total'     => Appointment::count(),
            'pending'   => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        $doctors = Doctor::where('is_active', true)->get();

        return view('tenant.appointments.index', compact('appointments', 'stats', 'doctors'));
    }

    public function create()
    {
        $doctors  = Doctor::with(['user', 'activeSchedules'])->where('is_active', true)->get();
        $patients = User::on('central')->where('tenant_id', tenant('id'))->whereHas('roles', fn($q) => $q->where('name', 'patient'))->get();

        return view('tenant.appointments.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id'        => 'required|exists:doctors,id',
            'patient_id'       => 'required',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'visit_type'       => 'required|in:new,follow_up,emergency',
            'reason'           => 'nullable|string|max:500',
            'fee'              => 'nullable|numeric|min:0',
        ]);

        if (Auth::user()->hasRole('patient')) {
            $validated['patient_id'] = Auth::id();
        }

        if (empty($validated['fee'])) {
            $doctor = Doctor::find($validated['doctor_id']);
            $validated['fee'] = $doctor->consultation_fee ?? 0;
        }

        $appointment = Appointment::create($validated);
        $appointment->load(['doctor', 'patient']);

        $appointment->patient->notify(new AppointmentBooked($appointment));
        $appointment->doctor->user->notify(new AppointmentBooked($appointment));

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['doctor', 'patient', 'prescription.items']);
        return view('tenant.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $doctors  = Doctor::with(['user', 'activeSchedules'])->where('is_active', true)->get();
        $patients = User::on('central')->where('tenant_id', tenant('id'))->whereHas('roles', fn($q) => $q->where('name', 'patient'))->get();

        return view('tenant.appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'doctor_id'        => 'required|exists:doctors,id',
            'patient_id'       => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'visit_type'       => 'required|in:new,follow_up,emergency',
            'reason'           => 'nullable|string|max:500',
            'notes'            => 'nullable|string|max:1000',
            'fee'              => 'nullable|numeric|min:0',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $oldStatus = $appointment->status;
        $appointment->update(['status' => $request->status]);
        $appointment->load(['doctor', 'patient']);

        $appointment->patient->notify(new AppointmentStatusChanged($appointment, $oldStatus));

        if (!Auth::user()->hasRole('doctor')) {
            $appointment->doctor->user->notify(new AppointmentStatusChanged($appointment, $oldStatus));
        }

        return back()->with('success', 'Status updated to ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }

    public function updatePayment(Request $request, Appointment $appointment, InvoiceService $invoiceService)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bkash,card',
        ]);

        $appointment->update([
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
        ]);
        $appointment->load(['doctor', 'patient']);

        if (!$appointment->invoice) {
            $invoiceService->createFromAppointment($appointment);
        }

        $appointment->patient->notify(new PaymentReceived($appointment));

        return back()->with('success', 'Payment recorded.');
    }

    public function destroy(Appointment $appointment)
    {
        if (in_array($appointment->status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Cannot delete an active or completed appointment.');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted.');
    }
}