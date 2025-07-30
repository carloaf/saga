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
use Carbon\Carbon;

class WeeklySummaryExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
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
            'Semana',
            'Período',
            'Café da Manhã',
            'Almoço',
            'Total Semanal',
            'Total Usuários',
            'Percentual Utilização'
        ];
    }

    public function map($week): array
    {
        $startOfWeek = Carbon::parse($week->week_start)->format('d/m/Y');
        $endOfWeek = Carbon::parse($week->week_start)->addDays(6)->format('d/m/Y');
        $weekNumber = Carbon::parse($week->week_start)->weekOfYear;
        
        $totalPossibleMeals = ($week->unique_users * 7 * 2); // 7 dias, 2 refeições por dia
        $utilizationPercentage = $totalPossibleMeals > 0 ? 
            ($week->total_bookings / $totalPossibleMeals) * 100 : 0;
        
        return [
            'Semana ' . $weekNumber,
            $startOfWeek . ' - ' . $endOfWeek,
            $week->breakfast_count,
            $week->lunch_count,
            $week->total_bookings,
            $week->unique_users,
            number_format($utilizationPercentage, 1) . '%'
        ];
    }

    public function title(): string
    {
        return 'Resumo Semanal';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '2c5a68']]
            ],
        ];
    }
}
