<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements
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
        return Appointment::with(['doctor', 'patient'])
            ->whereBetween('appointment_date', [$this->from, $this->to])
            ->orderBy('appointment_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient',
            'Doctor',
            'Date',
            'Time',
            'Visit Type',
            'Status',
            'Fee (৳)',
            'Payment',
            'Payment Method',
        ];
    }

    public function map($appt): array
    {
        return [
            $appt->id,
            $appt->patient->name,
            'Dr. ' . $appt->doctor->name,
            $appt->appointment_date->format('d M Y'),
            \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A'),
            ucfirst(str_replace('_', ' ', $appt->visit_type)),
            ucfirst(str_replace('_', ' ', $appt->status)),
            number_format($appt->fee, 2),
            ucfirst($appt->payment_status),
            ucfirst($appt->payment_method ?? ''),
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
        return 'Appointments ' . $this->from . ' to ' . $this->to;
    }
}