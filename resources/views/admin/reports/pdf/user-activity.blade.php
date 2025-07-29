<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Atividade dos Usuários - {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f8ff;
            font-weight: bold;
            font-size: 10px;
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
        .high-activity { background-color: #d1f2eb; }
        .medium-activity { background-color: #fdf2e9; }
        .low-activity { background-color: #fadbd8; }
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
            font-size: 12px;
        }
        .war-name {
            font-weight: bold;
            color: #2c5a68;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👥 RELATÓRIO DE ATIVIDADE DOS USUÁRIOS</h1>
        <p>Sistema de Agendamento e Gestão de Arranchamento</p>
        <p><strong>Período:</strong> {{ $start_date->format('d/m/Y') }} a {{ $end_date->format('d/m/Y') }}</p>
    </div>

    @if($data->count() > 0)
        <!-- Resumo Executivo -->
        <div class="section-title">📊 Resumo Executivo</div>
        
        <div class="summary-box">
            @php
                $totalUsers = $data->count();
                $totalBookings = $data->sum('total_bookings');
                $avgBookingsPerUser = $totalUsers > 0 ? $totalBookings / $totalUsers : 0;
                $activeUsers = $data->where('total_bookings', '>', 0)->count();
                $highActivityUsers = $data->where('total_bookings', '>=', 10)->count();
                $topUser = $data->first();
            @endphp
            
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Total de usuários analisados:</strong> {{ number_format($totalUsers) }}</li>
                <li><strong>Usuários ativos no período:</strong> {{ number_format($activeUsers) }} ({{ number_format(($activeUsers / $totalUsers) * 100, 1) }}%)</li>
                <li><strong>Total de agendamentos:</strong> {{ number_format($totalBookings) }}</li>
                <li><strong>Média por usuário:</strong> {{ number_format($avgBookingsPerUser, 1) }} agendamentos</li>
                <li><strong>Usuário mais ativo:</strong> {{ $topUser->war_name }} ({{ $topUser->full_name }}) - {{ number_format($topUser->total_bookings) }} agendamentos</li>
                <li><strong>Usuários com alta atividade (≥10):</strong> {{ number_format($highActivityUsers) }}</li>
            </ul>
        </div>

        <!-- Ranking de Usuários -->
        <div class="section-title">🏆 Ranking de Usuários Mais Ativos</div>
        
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; width: 6%">Pos.</th>
                    <th style="width: 22%">Nome de Guerra</th>
                    <th style="width: 25%">Nome Completo</th>
                    <th style="width: 15%">Posto/Graduação</th>
                    <th style="width: 15%">Organização</th>
                    <th style="text-align: center; width: 6%">Café</th>
                    <th style="text-align: center; width: 6%">Almoço</th>
                    <th style="text-align: center; width: 5%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->take(50) as $index => $user)
                    @php
                        $activityLevel = '';
                        if ($user->total_bookings >= 15) $activityLevel = 'high-activity';
                        elseif ($user->total_bookings >= 5) $activityLevel = 'medium-activity';
                        elseif ($user->total_bookings > 0) $activityLevel = 'low-activity';
                    @endphp
                    <tr class="{{ $activityLevel }}">
                        <td style="text-align: center">
                            <strong>{{ $index + 1 }}°</strong>
                            @if($index < 3)
                                @if($index === 0) 🥇
                                @elseif($index === 1) 🥈
                                @else 🥉
                                @endif
                            @endif
                        </td>
                        <td class="war-name">{{ $user->war_name }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->rank_name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($user->organization_name ?? 'N/A', 20) }}</td>
                        <td style="text-align: center">{{ number_format($user->breakfast_count) }}</td>
                        <td style="text-align: center">{{ number_format($user->lunch_count) }}</td>
                        <td style="text-align: center"><strong>{{ number_format($user->total_bookings) }}</strong></td>
                    </tr>
                @endforeach
                
                @if($data->count() > 50)
                    <tr class="highlight">
                        <td colspan="8" style="text-align: center; font-style: italic;">
                            ... e mais {{ number_format($data->count() - 50) }} usuários (mostrando apenas os 50 primeiros)
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Estatísticas por Faixa de Atividade -->
        <div class="section-title">📈 Distribuição por Nível de Atividade</div>
        
        @php
            $veryHighActivity = $data->where('total_bookings', '>=', 20)->count();
            $highActivity = $data->whereBetween('total_bookings', [10, 19])->count();
            $mediumActivity = $data->whereBetween('total_bookings', [5, 9])->count();
            $lowActivity = $data->whereBetween('total_bookings', [1, 4])->count();
            $noActivity = $data->where('total_bookings', 0)->count();
        @endphp
        
        <table>
            <thead>
                <tr>
                    <th>Nível de Atividade</th>
                    <th style="text-align: center">Faixa</th>
                    <th style="text-align: center">Usuários</th>
                    <th style="text-align: center">% do Total</th>
                    <th style="text-align: center">Total Agendamentos</th>
                </tr>
            </thead>
            <tbody>
                <tr class="high-activity">
                    <td><strong>🔥 Muito Alta</strong></td>
                    <td style="text-align: center">≥ 20</td>
                    <td style="text-align: center">{{ number_format($veryHighActivity) }}</td>
                    <td style="text-align: center">{{ number_format(($veryHighActivity / $totalUsers) * 100, 1) }}%</td>
                    <td style="text-align: center">{{ number_format($data->where('total_bookings', '>=', 20)->sum('total_bookings')) }}</td>
                </tr>
                <tr class="high-activity">
                    <td><strong>🚀 Alta</strong></td>
                    <td style="text-align: center">10 - 19</td>
                    <td style="text-align: center">{{ number_format($highActivity) }}</td>
                    <td style="text-align: center">{{ number_format(($highActivity / $totalUsers) * 100, 1) }}%</td>
                    <td style="text-align: center">{{ number_format($data->whereBetween('total_bookings', [10, 19])->sum('total_bookings')) }}</td>
                </tr>
                <tr class="medium-activity">
                    <td><strong>📊 Média</strong></td>
                    <td style="text-align: center">5 - 9</td>
                    <td style="text-align: center">{{ number_format($mediumActivity) }}</td>
                    <td style="text-align: center">{{ number_format(($mediumActivity / $totalUsers) * 100, 1) }}%</td>
                    <td style="text-align: center">{{ number_format($data->whereBetween('total_bookings', [5, 9])->sum('total_bookings')) }}</td>
                </tr>
                <tr class="low-activity">
                    <td><strong>📉 Baixa</strong></td>
                    <td style="text-align: center">1 - 4</td>
                    <td style="text-align: center">{{ number_format($lowActivity) }}</td>
                    <td style="text-align: center">{{ number_format(($lowActivity / $totalUsers) * 100, 1) }}%</td>
                    <td style="text-align: center">{{ number_format($data->whereBetween('total_bookings', [1, 4])->sum('total_bookings')) }}</td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td><strong>😴 Sem Atividade</strong></td>
                    <td style="text-align: center">0</td>
                    <td style="text-align: center">{{ number_format($noActivity) }}</td>
                    <td style="text-align: center">{{ number_format(($noActivity / $totalUsers) * 100, 1) }}%</td>
                    <td style="text-align: center">0</td>
                </tr>
                <tr class="highlight">
                    <td><strong>TOTAL</strong></td>
                    <td style="text-align: center"><strong>-</strong></td>
                    <td style="text-align: center"><strong>{{ number_format($totalUsers) }}</strong></td>
                    <td style="text-align: center"><strong>100.0%</strong></td>
                    <td style="text-align: center"><strong>{{ number_format($totalBookings) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Top 10 por Organização -->
        <div class="section-title">🏢 Usuários Mais Ativos por Organização</div>
        
        @php
            $orgStats = $data->groupBy('organization_name')->map(function($users, $orgName) {
                return [
                    'name' => $orgName ?? 'Sem Organização',
                    'total_users' => $users->count(),
                    'total_bookings' => $users->sum('total_bookings'),
                    'avg_per_user' => $users->avg('total_bookings'),
                    'top_user' => $users->first()
                ];
            })->sortByDesc('total_bookings');
        @endphp
        
        <table>
            <thead>
                <tr>
                    <th style="width: 35%">Organização</th>
                    <th style="text-align: center; width: 15%">Total Usuários</th>
                    <th style="text-align: center; width: 15%">Total Agendamentos</th>
                    <th style="text-align: center; width: 15%">Média por Usuário</th>
                    <th style="width: 20%">Usuário Mais Ativo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orgStats->take(10) as $orgStat)
                    <tr>
                        <td><strong>{{ $orgStat['name'] }}</strong></td>
                        <td style="text-align: center">{{ number_format($orgStat['total_users']) }}</td>
                        <td style="text-align: center">{{ number_format($orgStat['total_bookings']) }}</td>
                        <td style="text-align: center">{{ number_format($orgStat['avg_per_user'], 1) }}</td>
                        <td>{{ $orgStat['top_user']->war_name }} ({{ $orgStat['top_user']->total_bookings }})</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Análise e Insights -->
        <div class="section-title">💡 Análise e Insights</div>
        
        <div class="summary-box">
            <h4 style="margin-top: 0;">Principais Observações:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Taxa de engajamento:</strong> {{ number_format(($activeUsers / $totalUsers) * 100, 1) }}% dos usuários fizeram pelo menos um agendamento</li>
                <li><strong>Concentração de atividade:</strong> Os top 10 usuários representam {{ number_format(($data->take(10)->sum('total_bookings') / $totalBookings) * 100, 1) }}% de todos os agendamentos</li>
                <li><strong>Usuários altamente ativos:</strong> {{ number_format(($highActivityUsers / $totalUsers) * 100, 1) }}% dos usuários têm ≥10 agendamentos</li>
                
                @php
                    $breakfastUsers = $data->where('breakfast_count', '>', 0)->count();
                    $lunchUsers = $data->where('lunch_count', '>', 0)->count();
                @endphp
                
                <li><strong>Preferência por café da manhã:</strong> {{ number_format($breakfastUsers) }} usuários ({{ number_format(($breakfastUsers / $totalUsers) * 100, 1) }}%)</li>
                <li><strong>Preferência por almoço:</strong> {{ number_format($lunchUsers) }} usuários ({{ number_format(($lunchUsers / $totalUsers) * 100, 1) }}%)</li>
                
                @if($noActivity > 0)
                    <li><strong>⚠️ Usuários sem atividade:</strong> {{ number_format($noActivity) }} usuários ({{ number_format(($noActivity / $totalUsers) * 100, 1) }}%) não fizeram agendamentos no período</li>
                @endif
            </ul>
        </div>
    @else
        <div class="no-data">
            <p>🚫 Nenhum usuário encontrado para o período selecionado.</p>
            <p>Verifique se existem usuários cadastrados no sistema.</p>
        </div>
    @endif

    <div class="footer">
        <p>Relatório gerado em {{ $generated_at->format('d/m/Y H:i:s') }} pelo Sistema SAGA</p>
        <p>Documento gerado automaticamente - Para análise de uso do sistema</p>
    </div>
</body>
</html>
