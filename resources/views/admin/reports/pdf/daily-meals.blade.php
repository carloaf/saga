<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa do Rancho - {{ $start_date->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .date-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .date-header {
            background-color: #f5f5f5;
            padding: 8px;
            border: 1px solid #ddd;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .meal-section {
            margin-bottom: 20px;
        }
        .meal-header {
            background-color: #e8f4f8;
            padding: 6px;
            border: 1px solid #b8d4e0;
            font-weight: bold;
            color: #2c5a68;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .summary {
            background-color: #f0f8ff;
            padding: 10px;
            border: 1px solid #b8d4e0;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MAPA DO RANCHO</h1>
        <p>Sistema de Agendamento e Gestão de Arranchamento</p>
        @if($start_date->eq($end_date))
            <p><strong>Data:</strong> {{ $start_date->format('d/m/Y') }} ({{ $start_date->translatedFormat('l') }})</p>
        @else
            <p><strong>Período:</strong> {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</p>
        @endif
    </div>

    @forelse($data as $date => $dayBookings)
        <div class="date-section">
            <div class="date-header">
                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}
            </div>

            @php
                $mealTypes = $dayBookings->groupBy('meal_type');
                $dayTotal = $dayBookings->count();
            @endphp

            @foreach(['breakfast' => 'Café da Manhã', 'lunch' => 'Almoço'] as $mealType => $mealLabel)
                @if(isset($mealTypes[$mealType]))
                    <div class="meal-section">
                        <div class="meal-header">
                            {{ $mealLabel }} ({{ $mealTypes[$mealType]->count() }} militar{{ $mealTypes[$mealType]->count() > 1 ? 'es' : '' }})
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 30%">Nome Completo</th>
                                    <th style="width: 20%">Nome de Guerra</th>
                                    <th style="width: 25%">Posto/Graduação</th>
                                    <th style="width: 25%">Organização</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mealTypes[$mealType]->sortBy('user.full_name') as $booking)
                                    <tr>
                                        <td>{{ $booking->user->full_name }}</td>
                                        <td><strong>{{ $booking->user->war_name }}</strong></td>
                                        <td>{{ $booking->user->rank ? $booking->user->rank->name : 'N/A' }}</td>
                                        <td>{{ $booking->user->organization ? $booking->user->organization->name : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach

            <div class="summary">
                <strong>Resumo do Dia:</strong>
                Café da Manhã: {{ $mealTypes->get('breakfast', collect())->count() }} | 
                Almoço: {{ $mealTypes->get('lunch', collect())->count() }} | 
                <strong>Total: {{ $dayTotal }}</strong>
            </div>
        </div>
    @empty
        <div class="no-data">
            <p>Nenhum agendamento encontrado para o período selecionado.</p>
        </div>
    @endforelse

    <div class="footer">
        <p>Relatório gerado em {{ $generated_at->format('d/m/Y H:i:s') }} pelo Sistema SAGA</p>
        <p>Documento gerado automaticamente - Não requer assinatura</p>
    </div>
</body>
</html>
