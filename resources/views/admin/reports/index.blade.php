<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - SAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    📊 Relatórios e Estatísticas
                </h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        ← Voltar ao Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Admin Notice -->
            <div class="bg-purple-100 border border-purple-400 text-purple-700 px-4 py-3 rounded mb-6">
                <strong>🛡️ Área Administrativa</strong> - Relatórios e análises para superusuários.
            </div>

    <div class="space-y-6">
        <!-- Report Generation -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Gerar Relatórios</h3>
                
                <form method="GET" action="{{ route('admin.reports.generate') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo de Relatório</label>
                            <select id="report_type" name="report_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Selecione...</option>
                                <option value="daily_meals">Mapa do Rancho Diário</option>
                                <option value="monthly_summary">Resumo Mensal</option>
                                <option value="organization_breakdown">Quebra por Organização</option>
                                <option value="user_activity">Atividade dos Usuários</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                            <input type="date" id="start_date" name="start_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                            <input type="date" id="end_date" name="end_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" name="format" value="pdf" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Gerar PDF
                        </button>
                        
                        <button type="submit" name="format" value="excel" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Gerar Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Mapa do Rancho - Hoje</h4>
                    <p class="text-sm text-gray-600 mb-4">Relatório das refeições agendadas para hoje</p>
                    
                    @php
                        $todayBookings = \App\Models\Booking::whereDate('booking_date', today())->count();
                        $todayBreakfast = \App\Models\Booking::whereDate('booking_date', today())->where('meal_type', 'breakfast')->count();
                        $todayLunch = \App\Models\Booking::whereDate('booking_date', today())->where('meal_type', 'lunch')->count();
                    @endphp
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Café da Manhã:</span>
                            <span class="text-sm font-medium">{{ $todayBreakfast }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Almoço:</span>
                            <span class="text-sm font-medium">{{ $todayLunch }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium text-gray-900">Total:</span>
                            <span class="text-sm font-bold">{{ $todayBookings }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.reports.generate', ['report_type' => 'daily_meals', 'start_date' => today()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                       class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Gerar Relatório →
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Resumo Semanal</h4>
                    <p class="text-sm text-gray-600 mb-4">Estatísticas da última semana</p>
                    
                    @php
                        $weekBookings = \App\Models\Booking::whereBetween('booking_date', [today()->subWeek(), today()])->count();
                        $avgDaily = round($weekBookings / 7, 1);
                    @endphp
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Agendamentos:</span>
                            <span class="text-sm font-medium">{{ $weekBookings }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Média Diária:</span>
                            <span class="text-sm font-medium">{{ $avgDaily }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.reports.generate', ['report_type' => 'weekly_summary', 'start_date' => today()->subWeek()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                       class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Gerar Relatório →
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Top Organizações</h4>
                    <p class="text-sm text-gray-600 mb-4">Organizações mais ativas este mês</p>
                    
                    @php
                        $topOrgs = \App\Models\Booking::join('users', 'bookings.user_id', '=', 'users.id')
                            ->join('organizations', 'users.organization_id', '=', 'organizations.id')
                            ->whereMonth('booking_date', now()->month)
                            ->select('organizations.name', \DB::raw('count(*) as total'))
                            ->groupBy('organizations.id', 'organizations.name')
                            ->orderBy('total', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    
                    <div class="space-y-2 mb-4">
                        @foreach($topOrgs as $index => $org)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ $index + 1 }}. {{ $org->name }}</span>
                            <span class="text-sm font-medium">{{ $org->total }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('admin.reports.generate', ['report_type' => 'organization_breakdown', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                       class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Gerar Relatório →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Relatórios Recentes</h3>
                
                <div class="space-y-3">
                    <!-- Placeholder for recent reports - would be stored in database -->
                    <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Mapa do Rancho - {{ today()->format('d/m/Y') }}</h4>
                            <p class="text-xs text-gray-500">Gerado hoje às {{ now()->format('H:i') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-700 text-sm">Download PDF</button>
                            <button class="text-green-600 hover:text-green-700 text-sm">Download Excel</button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Resumo Mensal - {{ now()->format('m/Y') }}</h4>
                            <p class="text-xs text-gray-500">Gerado ontem às 15:30</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-700 text-sm">Download PDF</button>
                            <button class="text-green-600 hover:text-green-700 text-sm">Download Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </main>
</body>
</html>
