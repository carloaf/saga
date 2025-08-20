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

class DailyMealsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
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
        $flattenedData = collect();
        
        foreach ($this->data as $date => $dayBookings) {
            foreach ($dayBookings as $booking) {
                $flattenedData->push($booking);
            }
        }
        
        return $flattenedData;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Dia da Semana',
            'Tipo de Refeição',
            'Nome Completo',
            'Nome de Guerra',
            'Posto/Graduação',
            'Organização',
            'Email',
            'Status'
        ];
    }

    public function map($booking): array
    {
        $date = Carbon::parse($booking->booking_date);
        $mealLabel = match($booking->meal_type) {
            'breakfast' => 'Café da Manhã',
            'lunch' => 'Almoço',
            'dinner' => 'Jantar',
            default => ucfirst($booking->meal_type)
        };

        return [
            $date->format('d/m/Y'),
            $date->translatedFormat('l'),
            $mealLabel,
            $booking->user->full_name,
            $booking->user->war_name,
            $booking->user->rank ? $booking->user->rank->name : 'N/A',
            $booking->user->organization ? $booking->user->organization->name : 'N/A',
            $booking->user->email,
            $booking->user->is_active ? 'Ativo' : 'Inativo'
        ];
    }

    public function title(): string
    {
        if ($this->startDate->eq($this->endDate)) {
            return 'Mapa Rancho ' . $this->startDate->format('d-m-Y');
        }
        
        return 'Mapa Rancho ' . $this->startDate->format('d-m-Y') . ' a ' . $this->endDate->format('d-m-Y');
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
