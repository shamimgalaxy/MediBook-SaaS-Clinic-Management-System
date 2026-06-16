<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RevenueExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function collection()
    {
        return Invoice::with(['patient', 'doctor', 'appointment'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [
                $this->from . ' 00:00:00',
                $this->to   . ' 23:59:59',
            ])
            ->orderBy('paid_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Patient',
            'Doctor',
            'Visit Type',
            'Subtotal (৳)',
            'Tax (৳)',
            'Discount (৳)',
            'Total (৳)',
            'Payment Method',
            'Paid At',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->patient->name,
            'Dr. ' . $invoice->doctor->name,
            ucfirst(str_replace('_', ' ', $invoice->appointment->visit_type ?? '')),
            number_format($invoice->subtotal, 2),
            number_format($invoice->tax, 2),
            number_format($invoice->discount, 2),
            number_format($invoice->total, 2),
            ucfirst($invoice->appointment->payment_method ?? ''),
            $invoice->paid_at?->format('d M Y, h:i A'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '185FA5']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Revenue ' . $this->from . ' to ' . $this->to;
    }
}