@extends('layouts.app')

@section('title', 'Relatórios Administrativos')

@section('content')
<div class="bg-gray-50">
    <!-- Enhanced Header with Gradient -->
    <div class="bg-gradient-to-r from-green-600 via-emerald-700 to-teal-800 shadow-xl">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <div class="flex items-center space-x-4">
                        <!-- Back to Dashboard Button -->
                        <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm hover:bg-opacity-30 transition-all duration-200 group">
                            <svg class="w-6 h-6 text-white group-hover:text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">📊 Relatórios e Estatísticas</h1>
                            <p class="mt-2 text-green-100 font-medium">Gere relatórios detalhados sobre o sistema de agendamento de refeições</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-0 sm:ml-16 flex flex-col sm:flex-row gap-3 header-controls">
                    <!-- Quick Export Button -->
                    <button type="button" onclick="openQuickExportModal()" 
                            class="bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 text-white font-semibold px-6 py-3 rounded-lg border border-white border-opacity-30 transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Exportar Rápido</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-4">

    <!-- Alertas -->
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
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
        <div class="mb-4 rounded-md bg-red-50 p-4">
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

    <div class="space-y-6">
        <!-- Formulário de Geração de Relatórios -->
        <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    Gerar Relatórios
                </h3>
                
                <form method="GET" action="{{ route('admin.reports.generate') }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="field-group">
                            <label for="report_type" class="label-enhanced">
                                <svg class="icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <svg class="icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <svg class="icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Gerar PDF
                        </button>
                        
                        <button type="submit" name="format" value="excel" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    // Função para o botão de exportação rápida
    function openQuickExportModal() {
        // Criar modal dinâmico para exportação rápida
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Exportação Rápida</h3>
                    <div class="mt-4 space-y-3">
                        <a href="${generateReportUrl('daily_meals', 'today')}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition duration-300">
                            📊 Refeições de Hoje
                        </a>
                        <a href="${generateReportUrl('weekly_summary', 'week')}" 
                           class="block w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-300">
                            📈 Resumo Semanal
                        </a>
                        <a href="${generateReportUrl('organization_breakdown', 'month')}" 
                           class="block w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded transition duration-300">
                            🏢 Por Organização (Mês)
                        </a>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button onclick="closeQuickExportModal()" 
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 transition duration-300">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Adicionar evento para fechar ao clicar no backdrop
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeQuickExportModal();
            }
        });
    }
    
    function closeQuickExportModal() {
        const modal = document.querySelector('.fixed.inset-0.bg-gray-600');
        if (modal) {
            modal.remove();
        }
    }
    
    function generateReportUrl(type, period) {
        const today = new Date();
        let startDate, endDate;
        
        switch(period) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'week':
                startDate = new Date(today.setDate(today.getDate() - 7)).toISOString().split('T')[0];
                endDate = new Date().toISOString().split('T')[0];
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                break;
        }
        
        return `/admin/reports/generate?report_type=${type}&start_date=${startDate}&end_date=${endDate}&format=pdf`;
    }

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

<style>
    /* Enhanced Reports Page Styles */
    .header-controls {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .header-controls {
            flex-direction: row;
        }
    }
    
    /* Quick export button hover effects */
    .header-controls button:hover {
        background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.35) 100%);
    }
    
    /* Enhanced form elements */
    .bg-white.shadow-xl {
        transition: all 0.3s ease;
    }
    
    .bg-white.shadow-xl:hover {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: translateY(-2px);
    }
    
    /* Report cards styling */
    .report-card {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .report-card:hover {
        background: linear-gradient(145deg, #f8fafc, #f1f5f9);
        border-color: #cbd5e1;
        transform: translateY(-3px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Green gradient theming for reports */
    .text-green-100 {
        color: rgb(220 252 231);
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        .header-controls {
            width: 100%;
        }
        
        .header-controls button {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

    </div>
</div>
@endsection
