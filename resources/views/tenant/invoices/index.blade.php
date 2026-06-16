<x-tenant-layout>
    <x-slot name="header">Invoices</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-sm text-gray-500">Paid</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-sm text-gray-500">Draft</p>
            <p class="text-2xl font-bold text-yellow-500">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-blue-600">৳{{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('invoices.index') }}"
              class="flex flex-wrap gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Invoice # or patient name"
                   class="border rounded-lg px-3 py-2 text-sm w-56 focus:ring focus:ring-blue-200">

            <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-200">
                <option value="">All Status</option>
                <option value="draft"  @selected(request('status') === 'draft')>Draft</option>
                <option value="sent"   @selected(request('status') === 'sent')>Sent</option>
                <option value="paid"   @selected(request('status') === 'paid')>Paid</option>
            </select>

            <input type="date" name="from" value="{{ request('from') }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-200">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-200">

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Filter
            </button>
            <a href="{{ route('invoices.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 py-2">Clear</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Invoice #</th>
                    <th class="px-4 py-3 text-left">Patient</th>
                    <th class="px-4 py-3 text-left">Doctor</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-medium text-blue-600">
                        {{ $invoice->invoice_number }}
                    </td>
                    <td class="px-4 py-3">{{ $invoice->patient->name }}</td>
                    <td class="px-4 py-3">{{ $invoice->doctor->name }}</td>
                    <td class="px-4 py-3 text-gray-500">
                        {{ $invoice->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">
                        ৳{{ number_format($invoice->total, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if ($invoice->status === 'paid')
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Paid</span>
                        @elseif ($invoice->status === 'sent')
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">Sent</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('invoices.show', $invoice) }}"
                           class="text-blue-600 hover:underline text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($invoices->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</x-tenant-layout>