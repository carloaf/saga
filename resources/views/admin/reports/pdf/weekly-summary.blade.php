<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo Estatístico - {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</title>
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c5a68;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f8ff;
            font-weight: bold;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            color: #2c5a68;
            border-bottom: 1px solid #2c5a68;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .highlight {
            background-color: #e8f4f8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RELATÓRIO ESTATÍSTICO</h1>
        <p>Sistema de Agendamento e Gestão de Arranchamento</p>
        <p><strong>Período:</strong> {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</p>
        <p><strong>Duração:</strong> {{ $data['period_days'] }} dia{{ $data['period_days'] > 1 ? 's' : '' }}</p>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="section-title">Resumo Geral</div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($data['total_bookings']) }}</div>
            <div class="stat-label">Total de Agendamentos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($data['total_bookings'] / $data['period_days'], 1) }}</div>
            <div class="stat-label">Média Diária</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($data['breakfast_count']) }}</div>
            <div class="stat-label">Café da Manhã</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($data['lunch_count']) }}</div>
            <div class="stat-label">Almoços</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($data['dinner_count'] ?? 0) }}</div>
            <div class="stat-label">Jantares</div>
        </div>
    </div>

    <!-- Distribuição por Tipo de Refeição -->
    <div class="section-title">Distribuição por Refeição</div>
    
    <table>
        <thead>
            <tr>
                <th>Tipo de Refeição</th>
                <th style="text-align: center">Quantidade</th>
                <th style="text-align: center">Percentual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Café da Manhã</td>
                <td style="text-align: center">{{ number_format($data['breakfast_count']) }}</td>
                <td style="text-align: center">{{ $data['total_bookings'] > 0 ? number_format(($data['breakfast_count'] / $data['total_bookings']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Almoço</td>
                <td style="text-align: center">{{ number_format($data['lunch_count']) }}</td>
                <td style="text-align: center">{{ $data['total_bookings'] > 0 ? number_format(($data['lunch_count'] / $data['total_bookings']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Jantar</td>
                <td style="text-align: center">{{ number_format($data['dinner_count'] ?? 0) }}</td>
                <td style="text-align: center">{{ $data['total_bookings'] > 0 ? number_format((($data['dinner_count'] ?? 0) / $data['total_bookings']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="highlight">
                <td><strong>Total</strong></td>
                <td style="text-align: center"><strong>{{ number_format($data['total_bookings']) }}</strong></td>
                <td style="text-align: center"><strong>100.0%</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Estatísticas Diárias -->
    @if($data['daily_stats']->count() > 0)
        <div class="section-title">Estatísticas Diárias</div>
        
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Dia da Semana</th>
                    <th style="text-align: center">Café da Manhã</th>
                    <th style="text-align: center">Almoço</th>
                    <th style="text-align: center">Jantar</th>
                    <th style="text-align: center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['daily_stats'] as $stat)
                    @php
                        $date = \Carbon\Carbon::parse($stat->date);
                        $isWeekend = $date->isWeekend();
                    @endphp
                    <tr class="{{ $isWeekend ? 'highlight' : '' }}">
                        <td>{{ $date->format('d/m/Y') }}</td>
                        <td>{{ $date->translatedFormat('l') }}</td>
                        <td style="text-align: center">{{ $stat->breakfast }}</td>
                        <td style="text-align: center">{{ $stat->lunch }}</td>
                        <td style="text-align: center">{{ $stat->dinner ?? 0 }}</td>
                        <td style="text-align: center"><strong>{{ $stat->total }}</strong></td>
                    </tr>
                @endforeach
                <tr class="highlight">
                    <td colspan="2"><strong>TOTAL DO PERÍODO</strong></td>
                    <td style="text-align: center"><strong>{{ $data['breakfast_count'] }}</strong></td>
                    <td style="text-align: center"><strong>{{ $data['lunch_count'] }}</strong></td>
                    <td style="text-align: center"><strong>{{ $data['dinner_count'] ?? 0 }}</strong></td>
                    <td style="text-align: center"><strong>{{ $data['total_bookings'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- Análise de Tendências -->
    <div class="section-title">Observações</div>
    
    <div style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
        <ul style="margin: 0; padding-left: 20px;">
            <li><strong>Período analisado:</strong> {{ $data['period_days'] }} dia{{ $data['period_days'] > 1 ? 's' : '' }} ({{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }})</li>
            <li><strong>Total de agendamentos:</strong> {{ number_format($data['total_bookings']) }}</li>
            <li><strong>Média diária:</strong> {{ number_format($data['total_bookings'] / $data['period_days'], 1) }} agendamentos por dia</li>
            @if($data['breakfast_count'] > $data['lunch_count'])
                <li><strong>Tendência:</strong> Café da manhã é mais popular ({{ number_format(($data['breakfast_count'] / $data['total_bookings']) * 100, 1) }}% vs {{ number_format(($data['lunch_count'] / $data['total_bookings']) * 100, 1) }}%)</li>
            @elseif($data['lunch_count'] > $data['breakfast_count'])
                <li><strong>Tendência:</strong> Almoço é mais popular ({{ number_format(($data['lunch_count'] / $data['total_bookings']) * 100, 1) }}% vs {{ number_format(($data['breakfast_count'] / $data['total_bookings']) * 100, 1) }}%)</li>
            @else
                <li><strong>Tendência:</strong> Café da manhã e almoço têm popularidade equivalente</li>
            @endif
        </ul>
    </div>

    <div class="footer">
        <p>Relatório gerado em {{ $generated_at->format('d/m/Y H:i:s') }} pelo Sistema SAGA</p>
        <p>Documento gerado automaticamente - Para uso interno</p>
    </div>
</body>
</html>
