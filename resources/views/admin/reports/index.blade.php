@extends('layouts.app')

@section('title', 'Relatórios Administrativos')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">📊 Relatórios e Estatísticas</h1>
            <p class="mt-2 text-sm text-gray-700">Gere relatórios detalhados sobre o sistema de agendamento de refeições</p>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-8 space-y-6">
        <!-- Formulário de Geração de Relatórios -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Gerar Relatórios</h3>
                
                <form method="GET" action="{{ route('admin.reports.generate') }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="field-group">
                            <label for="report_type" class="label-enhanced">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 5H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Tipo de Relatório</span>
                            </label>
                            <select id="report_type" name="report_type" required class="select-enhanced">
                                <option value="">Selecione o tipo de relatório</option>
                                <option value="daily_meals">📋 Mapa do Rancho Diário</option>
                                <option value="weekly_summary">📊 Resumo Semanal</option>
                                <option value="monthly_summary">📈 Resumo Mensal</option>
                                <option value="organization_breakdown">🏢 Quebra por Organização</option>
                                <option value="user_activity">👤 Atividade dos Usuários</option>
                            </select>
                        </div>
                        
                        <div class="field-group">
                            <label for="start_date" class="label-enhanced">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Data Inicial</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="{{ request('start_date', today()->format('Y-m-d')) }}"
                                   class="input-enhanced">
                        </div>
                        
                        <div class="field-group">
                            <label for="end_date" class="label-enhanced">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Data Final</span>
                            </label>
                            <input type="date" id="end_date" name="end_date" required
                                   value="{{ request('end_date', today()->format('Y-m-d')) }}"
                                   class="input-enhanced">
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

        <!-- Cards de Estatísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Estatísticas de Hoje -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Refeições Hoje</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $todayBookings }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Café:</span>
                            <span class="font-medium">{{ $todayBreakfast }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Almoço:</span>
                            <span class="font-medium">{{ $todayLunch }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.reports.generate', ['report_type' => 'daily_meals', 'start_date' => today()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                           class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Gerar Relatório →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas da Semana -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Última Semana</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $weekBookings }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-600">
                            Média diária: <span class="font-medium">{{ $avgDaily }}</span>
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.reports.generate', ['report_type' => 'weekly_summary', 'start_date' => today()->subWeek()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                           class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Gerar Relatório →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do Mês -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Este Mês</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $monthlyBookings }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-600">
                            {{ now()->format('F Y') }}
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.reports.generate', ['report_type' => 'monthly_summary', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                           class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Gerar Relatório →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Top Organizações -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Top Organizações</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $topOrgs->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        @if($topOrgs->count() > 0)
                            <div class="space-y-1">
                                @foreach($topOrgs->take(2) as $index => $org)
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600 truncate">{{ $index + 1 }}. {{ Str::limit($org->name, 15) }}</span>
                                    <span class="font-medium text-gray-900">{{ $org->total }}</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">Sem dados</p>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.reports.generate', ['report_type' => 'organization_breakdown', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                           class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Gerar Relatório →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relatórios Predefinidos -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Relatórios Predefinidos</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Mapa do Rancho Hoje -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Mapa do Rancho - Hoje</h4>
                        <p class="text-xs text-gray-600 mb-3">Relatório detalhado das refeições agendadas para hoje</p>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'daily_meals', 'start_date' => today()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                               class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded text-center">
                                PDF
                            </a>
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'daily_meals', 'start_date' => today()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'excel']) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded text-center">
                                Excel
                            </a>
                        </div>
                    </div>

                    <!-- Resumo da Semana -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Resumo Semanal</h4>
                        <p class="text-xs text-gray-600 mb-3">Estatísticas dos últimos 7 dias</p>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'weekly_summary', 'start_date' => today()->subWeek()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                               class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded text-center">
                                PDF
                            </a>
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'weekly_summary', 'start_date' => today()->subWeek()->format('Y-m-d'), 'end_date' => today()->format('Y-m-d'), 'format' => 'excel']) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded text-center">
                                Excel
                            </a>
                        </div>
                    </div>

                    <!-- Resumo Mensal -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Resumo Mensal</h4>
                        <p class="text-xs text-gray-600 mb-3">Estatísticas do mês atual</p>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'monthly_summary', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                               class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded text-center">
                                PDF
                            </a>
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'monthly_summary', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'excel']) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded text-center">
                                Excel
                            </a>
                        </div>
                    </div>

                    <!-- Quebra por Organização -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Por Organização</h4>
                        <p class="text-xs text-gray-600 mb-3">Análise por unidade militar</p>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'organization_breakdown', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                               class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded text-center">
                                PDF
                            </a>
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'organization_breakdown', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'excel']) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded text-center">
                                Excel
                            </a>
                        </div>
                    </div>

                    <!-- Atividade dos Usuários -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Atividade dos Usuários</h4>
                        <p class="text-xs text-gray-600 mb-3">Relatório de uso por militar</p>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'user_activity', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'pdf']) }}" 
                               class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded text-center">
                                PDF
                            </a>
                            <a href="{{ route('admin.reports.generate', ['report_type' => 'user_activity', 'start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d'), 'format' => 'excel']) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded text-center">
                                Excel
                            </a>
                        </div>
                    </div>

                    <!-- Personalizado -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Relatório Personalizado</h4>
                        <p class="text-xs text-gray-600 mb-3">Use o formulário acima para gerar relatórios customizados</p>
                        
                        <div class="text-center">
                            <span class="text-xs text-gray-500 font-medium">Configure acima ↑</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Validação do formulário
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        
        function validateDates() {
            if (startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                
                if (start > end) {
                    endDate.setCustomValidity('A data final deve ser maior ou igual à data inicial');
                } else {
                    endDate.setCustomValidity('');
                }
            }
        }
        
        startDate.addEventListener('change', validateDates);
        endDate.addEventListener('change', validateDates);
        
        // Configuração automática de datas baseada no tipo de relatório
        const reportType = document.getElementById('report_type');
        reportType.addEventListener('change', function() {
            const today = new Date();
            let start, end;
            
            switch(this.value) {
                case 'daily_meals':
                    start = end = today;
                    break;
                case 'weekly_summary':
                    start = new Date(today);
                    start.setDate(start.getDate() - 7);
                    end = today;
                    break;
                case 'monthly_summary':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                default:
                    return; // Não alterar as datas para outros tipos
            }
            
            startDate.value = start.toISOString().split('T')[0];
            endDate.value = end.toISOString().split('T')[0];
        });
    });
</script>
@endsection
@endsection
