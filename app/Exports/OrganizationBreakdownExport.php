<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrganizationBreakdownExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Posição',
            'Organização',
            'Usuários Únicos',
            'Café da Manhã',
            'Almoço',
            'Total Agendamentos',
            'Percentual',
            'Agendamentos por Usuário'
        ];
    }

    public function map($organization): array
    {
        static $position = 0;
        $position++;
        
        $totalBookings = $this->data->sum('total_bookings');
        $percentage = $totalBookings > 0 ? ($organization->total_bookings / $totalBookings) * 100 : 0;
        $avgPerUser = $organization->unique_users > 0 ? $organization->total_bookings / $organization->unique_users : 0;
        
        return [
            $position . '°',
            $organization->organization_name,
            $organization->unique_users,
            $organization->breakfast_count,
            $organization->lunch_count,
            $organization->total_bookings,
            number_format($percentage, 1) . '%',
            number_format($avgPerUser, 1)
        ];
    }

    public function title(): string
    {
        return 'Relatório por Organização';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2c5a68'],
                ]
            ],
        ];
    }
}
