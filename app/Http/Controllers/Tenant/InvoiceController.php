<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Invoice::with(['appointment', 'patient', 'doctor'])
            ->orderByDesc('created_at');

        // Patients only see their own invoices
        if ($user->hasRole('patient')) {
            $query->where('patient_id', $user->id);
        }

        // Filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('patient', fn($q) => $q->where('name', 'like', "%$search%"));
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'total'   => Invoice::count(),
            'paid'    => Invoice::where('status', 'paid')->count(),
            'draft'   => Invoice::where('status', 'draft')->count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total'),
        ];

        return view('tenant.invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        // Patients can only view their own
        if ($user->hasRole('patient') && $invoice->patient_id !== $user->id) {
            abort(403);
        }

        $invoice->load(['appointment', 'patient', 'doctor']);

        return view('tenant.invoices.show', compact('invoice'));
    }
    public function downloadPdf(Invoice $invoice)
{
    $user = Auth::user();

    // Patients can only download their own
    if ($user->hasRole('patient') && $invoice->patient_id !== $user->id) {
        abort(403);
    }

    $invoice->load(['appointment', 'patient', 'doctor']);

    $pdf = Pdf::loadView('tenant.invoices.pdf', compact('invoice'))
        ->setPaper('a4', 'portrait');

    return $pdf->download($invoice->invoice_number . '.pdf');
}
}