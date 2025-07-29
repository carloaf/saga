<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserActivityExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        return [
            new UserActivitySheet($this->data, $this->startDate, $this->endDate),
            new UserActivityStatsSheet($this->data, $this->startDate, $this->endDate),
        ];
    }
}

class UserActivitySheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
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
            'Nome de Guerra',
            'Nome Completo',
            'Posto/Graduação',
            'Organização',
            'Café da Manhã',
            'Almoço',
            'Total Agendamentos'
        ];
    }

    public function map($user): array
    {
        static $position = 0;
        $position++;
        
        return [
            $position . '°',
            $user->war_name,
            $user->full_name,
            $user->rank_name ?? 'N/A',
            $user->organization_name ?? 'N/A',
            $user->breakfast_count,
            $user->lunch_count,
            $user->total_bookings
        ];
    }

    public function title(): string
    {
        return 'Atividade dos Usuários';
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

class UserActivityStatsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
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
        $totalUsers = $this->data->count();
        
        $stats = collect([
            (object)[
                'category' => 'Muito Alta (≥20)',
                'count' => $this->data->where('total_bookings', '>=', 20)->count(),
                'total_bookings' => $this->data->where('total_bookings', '>=', 20)->sum('total_bookings')
            ],
            (object)[
                'category' => 'Alta (10-19)',
                'count' => $this->data->whereBetween('total_bookings', [10, 19])->count(),
                'total_bookings' => $this->data->whereBetween('total_bookings', [10, 19])->sum('total_bookings')
            ],
            (object)[
                'category' => 'Média (5-9)',
                'count' => $this->data->whereBetween('total_bookings', [5, 9])->count(),
                'total_bookings' => $this->data->whereBetween('total_bookings', [5, 9])->sum('total_bookings')
            ],
            (object)[
                'category' => 'Baixa (1-4)',
                'count' => $this->data->whereBetween('total_bookings', [1, 4])->count(),
                'total_bookings' => $this->data->whereBetween('total_bookings', [1, 4])->sum('total_bookings')
            ],
            (object)[
                'category' => 'Sem Atividade (0)',
                'count' => $this->data->where('total_bookings', 0)->count(),
                'total_bookings' => 0
            ],
        ]);
        
        return $stats;
    }

    public function headings(): array
    {
        return [
            'Nível de Atividade',
            'Quantidade de Usuários',
            'Percentual',
            'Total Agendamentos'
        ];
    }

    public function map($stat): array
    {
        $totalUsers = $this->data->count();
        $percentage = $totalUsers > 0 ? ($stat->count / $totalUsers) * 100 : 0;
        
        return [
            $stat->category,
            $stat->count,
            number_format($percentage, 1) . '%',
            $stat->total_bookings
        ];
    }

    public function title(): string
    {
        return 'Estatísticas de Atividade';
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
