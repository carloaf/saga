<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório por Organização - {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</title>
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
        .rank-1 { background-color: #fff2cc; }
        .rank-2 { background-color: #f0f0f0; }
        .rank-3 { background-color: #ffeaa7; }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RELATÓRIO POR ORGANIZAÇÃO</h1>
        <p>Sistema de Agendamento e Gestão de Arranchamento</p>
        <p><strong>Período:</strong> {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</p>
    </div>

    @if($data->count() > 0)
        <!-- Resumo Executivo -->
        <div class="section-title">Resumo Executivo</div>
        
        <div class="summary-box">
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Total de organizações ativas:</strong> {{ $data->count() }}</li>
                <li><strong>Total de agendamentos:</strong> {{ number_format($data->sum('total_bookings')) }}</li>
                <li><strong>Total de usuários únicos:</strong> {{ number_format($data->sum('unique_users')) }}</li>
                <li><strong>Organização mais ativa:</strong> {{ $data->first()->organization_name }} ({{ number_format($data->first()->total_bookings) }} agendamentos)</li>
                <li><strong>Média por organização:</strong> {{ number_format($data->avg('total_bookings'), 1) }} agendamentos</li>
            </ul>
        </div>

        <!-- Ranking de Organizações -->
        <div class="section-title">Ranking de Organizações</div>
        
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; width: 8%">Pos.</th>
                    <th style="width: 35%">Organização</th>
                    <th style="text-align: center; width: 12%">Usuários Únicos</th>
                    <th style="text-align: center; width: 12%">Café da Manhã</th>
                    <th style="text-align: center; width: 12%">Almoço</th>
                    <th style="text-align: center; width: 12%">Total</th>
                    <th style="text-align: center; width: 9%">%</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalBookings = $data->sum('total_bookings');
                @endphp
                @foreach($data as $index => $org)
                    @php
                        $percentage = $totalBookings > 0 ? ($org->total_bookings / $totalBookings) * 100 : 0;
                        $rowClass = '';
                        if ($index === 0) $rowClass = 'rank-1';
                        elseif ($index === 1) $rowClass = 'rank-2';
                        elseif ($index === 2) $rowClass = 'rank-3';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td style="text-align: center">
                            <strong>{{ $index + 1 }}°</strong>
                            @if($index < 3)
                                @if($index === 0) 1º
                                @elseif($index === 1) 2º
                                @else 3º
                                @endif
                            @endif
                        </td>
                        <td><strong>{{ $org->organization_name }}</strong></td>
                        <td style="text-align: center">{{ number_format($org->unique_users) }}</td>
                        <td style="text-align: center">{{ number_format($org->breakfast_count) }}</td>
                        <td style="text-align: center">{{ number_format($org->lunch_count) }}</td>
                        <td style="text-align: center"><strong>{{ number_format($org->total_bookings) }}</strong></td>
                        <td style="text-align: center">{{ number_format($percentage, 1) }}%</td>
                    </tr>
                @endforeach
                <tr class="highlight">
                    <td style="text-align: center"><strong>-</strong></td>
                    <td><strong>TOTAL GERAL</strong></td>
                    <td style="text-align: center"><strong>{{ number_format($data->sum('unique_users')) }}</strong></td>
                    <td style="text-align: center"><strong>{{ number_format($data->sum('breakfast_count')) }}</strong></td>
                    <td style="text-align: center"><strong>{{ number_format($data->sum('lunch_count')) }}</strong></td>
                    <td style="text-align: center"><strong>{{ number_format($data->sum('total_bookings')) }}</strong></td>
                    <td style="text-align: center"><strong>100.0%</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Análise Detalhada -->
        <div class="section-title">Análise Detalhada</div>
        
        <table>
            <thead>
                <tr>
                    <th>Organização</th>
                    <th style="text-align: center">Usuários</th>
                    <th style="text-align: center">Agendamentos por Usuário</th>
                    <th style="text-align: center">Preferência Café (%)</th>
                    <th style="text-align: center">Preferência Almoço (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $org)
                    @php
                        $avgPerUser = $org->unique_users > 0 ? $org->total_bookings / $org->unique_users : 0;
                        $breakfastPct = $org->total_bookings > 0 ? ($org->breakfast_count / $org->total_bookings) * 100 : 0;
                        $lunchPct = $org->total_bookings > 0 ? ($org->lunch_count / $org->total_bookings) * 100 : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $org->organization_name }}</strong></td>
                        <td style="text-align: center">{{ number_format($org->unique_users) }}</td>
                        <td style="text-align: center">{{ number_format($avgPerUser, 1) }}</td>
                        <td style="text-align: center">{{ number_format($breakfastPct, 1) }}%</td>
                        <td style="text-align: center">{{ number_format($lunchPct, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Insights e Observações -->
        <div class="section-title">Insights e Observações</div>
        
        <div class="summary-box">
            @php
                $topOrg = $data->first();
                $leastActiveOrg = $data->last();
                $avgUsersPerOrg = $data->avg('unique_users');
                $totalOrgs = $data->count();
            @endphp
            
            <h4 style="margin-top: 0;">Destaques do Período:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Organização líder:</strong> {{ $topOrg->organization_name }} com {{ number_format($topOrg->total_bookings) }} agendamentos</li>
                <li><strong>Maior engajamento por usuário:</strong> {{ $data->sortByDesc(function($org) { return $org->unique_users > 0 ? $org->total_bookings / $org->unique_users : 0; })->first()->organization_name }} ({{ number_format($data->sortByDesc(function($org) { return $org->unique_users > 0 ? $org->total_bookings / $org->unique_users : 0; })->first()->total_bookings / max($data->sortByDesc(function($org) { return $org->unique_users > 0 ? $org->total_bookings / $org->unique_users : 0; })->first()->unique_users, 1), 1) }} agendamentos por usuário)</li>
                <li><strong>Média de usuários por organização:</strong> {{ number_format($avgUsersPerOrg, 1) }} usuários</li>
                
                @php
                    $breakfastDominant = $data->where('breakfast_count', '>', DB::raw('lunch_count'))->count();
                    $lunchDominant = $data->where('lunch_count', '>', DB::raw('breakfast_count'))->count();
                @endphp
                
                @if($breakfastDominant > $lunchDominant)
                    <li><strong>Tendência geral:</strong> Maioria das organizações prefere café da manhã ({{ $breakfastDominant }} de {{ $totalOrgs }} organizações)</li>
                @elseif($lunchDominant > $breakfastDominant)
                    <li><strong>Tendência geral:</strong> Maioria das organizações prefere almoço ({{ $lunchDominant }} de {{ $totalOrgs }} organizações)</li>
                @else
                    <li><strong>Tendência geral:</strong> Preferências equilibradas entre café da manhã e almoço</li>
                @endif
            </ul>
        </div>
    @else
        <div class="no-data">
            <p>Nenhum dado de organização encontrado para o período selecionado.</p>
            <p>Verifique se existem agendamentos no período ou se os usuários possuem organizações associadas.</p>
        </div>
    @endif

    <div class="footer">
        <p>Relatório gerado em {{ $generated_at->format('d/m/Y H:i:s') }} pelo Sistema SAGA</p>
        <p>Documento gerado automaticamente - Para análise administrativa</p>
    </div>
</body>
</html>
