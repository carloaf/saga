<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SummaryExport implements WithMultipleSheets
{
    protected $data;
    protected $startDate;
    protected $endDate;
    protected $type;

    public function __construct($data, $startDate, $endDate, $type = 'weekly')
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
    }

    public function sheets(): array
    {
        return [
            new SummaryOverviewSheet($this->data, $this->startDate, $this->endDate, $this->type),
            new SummaryDailySheet($this->data, $this->startDate, $this->endDate, $this->type),
        ];
    }
}

class SummaryOverviewSheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $startDate;
    protected $endDate;
    protected $type;

    public function __construct($data, $startDate, $endDate, $type)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
    }

    public function array(): array
    {
        return [
            ['Período', $this->startDate->format('d/m/Y') . ' a ' . $this->endDate->format('d/m/Y')],
            ['Total de Dias', $this->data['period_days']],
            ['Total de Agendamentos', $this->data['total_bookings']],
            ['Café da Manhã', $this->data['breakfast_count']],
            ['Almoço', $this->data['lunch_count']],
            ['Jantar', $this->data['dinner_count'] ?? 0],
            ['Média Diária', round($this->data['total_bookings'] / $this->data['period_days'], 1)],
            ['% Café da Manhã', $this->data['total_bookings'] > 0 ? round(($this->data['breakfast_count'] / $this->data['total_bookings']) * 100, 1) . '%' : '0%'],
            ['% Almoço', $this->data['total_bookings'] > 0 ? round(($this->data['lunch_count'] / $this->data['total_bookings']) * 100, 1) . '%' : '0%'],
            ['% Jantar', $this->data['total_bookings'] > 0 ? round((($this->data['dinner_count'] ?? 0) / $this->data['total_bookings']) * 100, 1) . '%' : '0%'],
        ];
    }

    public function headings(): array
    {
        return ['Métrica', 'Valor'];
    }

    public function title(): string
    {
        return $this->type === 'weekly' ? 'Resumo Semanal' : 'Resumo Mensal';
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

class SummaryDailySheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $startDate;
    protected $endDate;
    protected $type;

    public function __construct($data, $startDate, $endDate, $type)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
    }

    public function array(): array
    {
        $result = [];
        
        foreach ($this->data['daily_stats'] as $stat) {
            $date = Carbon::parse($stat->date);
            $result[] = [
                $date->format('d/m/Y'),
                $date->translatedFormat('l'),
                $stat->breakfast,
                $stat->lunch,
                property_exists($stat, 'dinner') ? $stat->dinner : ($stat->dinner ?? 0),
                $stat->total
            ];
        }
        
        return $result;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Dia da Semana',
            'Café da Manhã',
            'Almoço',
            'Jantar',
            'Total'
        ];
    }

    public function title(): string
    {
        return 'Estatísticas Diárias';
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
