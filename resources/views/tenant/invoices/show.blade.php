<x-tenant-layout>
    <x-slot name="header">
        Invoice {{ $invoice->invoice_number }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow p-8">

            {{-- Header --}}
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ tenant('clinic_name') }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ tenant('domain') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold font-mono text-blue-600">{{ $invoice->invoice_number }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $invoice->created_at->format('d M Y') }}</p>
                    @if ($invoice->status === 'paid')
                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full mt-2 inline-block">
                            PAID
                        </span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full mt-2 inline-block">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Bill To / Doctor --}}
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase mb-1">Bill To</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $invoice->patient->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase mb-1">Doctor</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->doctor->name }}</p>
                    <p class="text-sm text-gray-500">{{ $invoice->doctor->specialization ?? '' }}</p>
                </div>
            </div>

            {{-- Appointment Info --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-gray-400 text-xs uppercase">Date</p>
                        <p class="font-medium">{{ $invoice->appointment->appointment_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase">Time</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($invoice->appointment->appointment_time)->format('h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase">Visit Type</p>
                        <p class="font-medium capitalize">{{ str_replace('_', ' ', $invoice->appointment->visit_type) }}</p>
                    </div>
                </div>
            </div>

            {{-- Line Items --}}
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b text-gray-500 text-xs uppercase">
                        <th class="py-2 text-left">Description</th>
                        <th class="py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-3">Consultation Fee
                            <span class="text-gray-400 text-xs ml-1">({{ ucfirst(str_replace('_', ' ', $invoice->appointment->visit_type)) }})</span>
                        </td>
                        <td class="py-3 text-right">৳{{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if ($invoice->tax > 0)
                    <tr class="border-b">
                        <td class="py-3 text-gray-500">Tax</td>
                        <td class="py-3 text-right">৳{{ number_format($invoice->tax, 2) }}</td>
                    </tr>
                    @endif
                    @if ($invoice->discount > 0)
                    <tr class="border-b">
                        <td class="py-3 text-gray-500">Discount</td>
                        <td class="py-3 text-right text-red-500">- ৳{{ number_format($invoice->discount, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td class="py-3 font-bold text-gray-800">Total</td>
                        <td class="py-3 text-right font-bold text-lg text-gray-800">
                            ৳{{ number_format($invoice->total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- Payment Info --}}
            @if ($invoice->isPaid())
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm mb-6">
                <p class="text-green-700">
                    <span class="font-semibold">Paid on</span>
                    {{ $invoice->paid_at->format('d M Y, h:i A') }}
                    via <span class="capitalize font-semibold">{{ $invoice->appointment->payment_method }}</span>
                </p>
            </div>
            @endif

            {{-- Notes --}}
            @if ($invoice->notes)
            <div class="text-sm text-gray-500 mb-6">
                <p class="font-semibold text-gray-700 mb-1">Notes</p>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-3 justify-end border-t pt-4">
                <a href="{{ route('invoices.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">
                    ← Back
                </a>
                {{-- PDF download comes in Step 4 --}}
                <button disabled
                        class="px-4 py-2 text-sm bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                    Download PDF (coming soon)
                </button>
            </div>

        </div>
    </div>
</x-tenant-layout>