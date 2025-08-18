@extends('layouts.app')

@section('title', 'Arranchamento da Cia')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-8">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Arranchamento da Cia</h1>
                        <p class="text-orange-100 text-lg">Gerenciamento das refeições dos Soldados EV</p>
                        <div class="mt-2 text-orange-100">
                            <span class="font-medium">{{ isset($furriel) ? ($furriel->organization->name ?? 'N/A') : 'N/A' }}</span>
                            @if(isset($furriel) && isset($furriel->subunit) && $furriel->subunit)
                                • <span class="font-medium">{{ $furriel->subunit }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aviso sobre regras de arranchamento -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">📋 Regras de Arranchamento</h4>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• <strong>Prazo:</strong> Arranchamento deve ser feito até às 13h do dia anterior</li>
                    <li>• <strong>Restrição:</strong> Não é permitido arranchar para o mesmo dia útil</li>
                    <li>• <strong>Finais de semana:</strong> Não há refeições disponíveis</li>
                    <li>• <strong>Sextas-feiras:</strong> Apenas café da manhã disponível</li>
                    <li>• <strong>Horário atual:</strong> {{ now()->format('H:i') }}h de {{ now()->translatedFormat('l, d/m/Y') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Seletor de Data -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Data das Refeições</h3>
            </div>
            
            <form method="GET" action="{{ route('furriel.arranchamento.index') }}">
                <div class="space-y-3">
                    <label for="booking_date" class="block text-sm font-medium text-gray-700">
                        Selecionar Data:
                    </label>
                    <input type="date" 
                           id="booking_date" 
                           name="date" 
                           value="{{ $selectedDate ?? date('Y-m-d') }}"
                           min="{{ date('Y-m-d') }}"
                           max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Data selecionada:</span>
                        @if(isset($selectedDate))
                            {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d/m/Y') }}
                        @else
                            Nenhuma
                        @endif
                    </div>
                    
                    @php
                        $selectedCarbon = isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate) : \Carbon\Carbon::now();
                        $isWeekend = $selectedCarbon->isWeekend();
                        $isToday = $selectedCarbon->isToday();
                        $isTomorrow = $selectedCarbon->isTomorrow();
                        $currentHour = now()->hour;
                        // Nova regra: não permitir arranchar para o mesmo dia útil E nem para o dia seguinte após 13h
                        $cutoffPassed = ($isToday || ($isTomorrow && $currentHour >= 13));
                    @endphp
                    
                    @if($isWeekend)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-red-700 text-sm font-medium">
                                    ⛔ Final de semana - Sem refeições disponíveis
                                </span>
                            </div>
                        </div>
                    @elseif($cutoffPassed)
                        <div class="p-3 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-orange-700 text-sm font-medium">
                                    @if($isToday)
                                        ⏰ Não é permitido arranchar para o mesmo dia útil
                                    @else
                                        ⏰ Prazo encerrado - Arranchamento deve ser feito até às 13h do dia anterior
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Soldados EV</p>
                    <p class="text-2xl font-bold text-gray-900">{{ isset($soldadosEv) ? $soldadosEv->count() : 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Reservas na Data</p>
                    <p class="text-2xl font-bold text-gray-900">{{ isset($stats['reservasNaData']) ? $stats['reservasNaData'] : 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Soldados EV -->
    @php
        $selectedCarbon = isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate) : \Carbon\Carbon::now();
        $isWeekend = $selectedCarbon->isWeekend();
        $isTomorrow = $selectedCarbon->isTomorrow();
        $isToday = $selectedCarbon->isToday();
        $currentHour = now()->hour;
        // Nova regra: não permitir arranchar para o mesmo dia útil E nem para o dia seguinte após 13h
        $cutoffPassed = ($isToday || ($isTomorrow && $currentHour >= 13));
        $canEditBookings = !$isWeekend && !$cutoffPassed;
    @endphp
    
    @if(isset($soldadosEv) && $soldadosEv->count() > 0)
        @if($canEditBookings)
            <form method="POST" action="{{ route('furriel.arranchamento.store') }}">
                @csrf
                <input type="hidden" name="booking_date" value="{{ $selectedDate ?? date('Y-m-d') }}">
        @endif
        
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header da Tabela -->
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-900">
                        🍽️ Soldados EV - Total: {{ $soldadosEv->count() }}
                    </h3>
                    
                    @if($canEditBookings)
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                    onclick="toggleAllMeals('breakfast')"
                                    class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-200 transition-colors">
                                ☕ Todos Café
                            </button>
                            
                            @if(!$selectedCarbon->isFriday())
                                <button type="button" 
                                        onclick="toggleAllMeals('lunch')"
                                        class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-lg text-sm font-medium hover:bg-orange-200 transition-colors">
                                    🍽️ Todos Almoço
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-sm text-gray-500">
                            @if($isWeekend)
                                📅 Visualização - Final de semana
                            @elseif($isToday)
                                ⏰ Visualização - Mesmo dia útil (não permitido)
                            @elseif($cutoffPassed)
                                ⏰ Visualização - Prazo encerrado
                            @endif
                        </div>
                    @endif
                </div>
            </div>

                <!-- Tabela -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Soldado
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ☕ Café da Manhã
                                </th>
                                @if(isset($selectedDate) && !\Carbon\Carbon::parse($selectedDate)->isFriday())
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        🍽️ Almoço
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($soldadosEv as $soldado)
                                @php
                                    $currentDate = $selectedDate ?? date('Y-m-d');
                                    
                                    // Reservas existentes para o soldado
                                    $existingBookingsForDate = isset($soldado->existingBookings) ? $soldado->existingBookings : collect();
                                    
                                    // Como booking_date é um cast 'date', precisamos comparar corretamente
                                    $hasBreakfast = $existingBookingsForDate
                                        ->filter(function($booking) use ($currentDate) {
                                            // booking_date é um objeto Carbon devido ao cast
                                            return $booking->booking_date->format('Y-m-d') === $currentDate;
                                        })
                                        ->where('meal_type', 'breakfast')
                                        ->isNotEmpty();
                                        
                                    $hasLunch = $existingBookingsForDate
                                        ->filter(function($booking) use ($currentDate) {
                                            return $booking->booking_date->format('Y-m-d') === $currentDate;
                                        })
                                        ->where('meal_type', 'lunch')
                                        ->isNotEmpty();
                                        
                                    $initials = substr($soldado->war_name ?: $soldado->full_name, 0, 2);
                                @endphp
                                
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                                                <span class="text-white font-bold text-sm">{{ strtoupper($initials) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <input type="hidden" name="bookings[{{ $soldado->id }}][user_id]" value="{{ $soldado->id }}">
                                                <div class="font-medium text-gray-900">{{ $soldado->war_name ?: $soldado->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ isset($soldado->rank) ? ($soldado->rank->abbreviation ?? 'N/A') : 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" 
                                               name="bookings[{{ $soldado->id }}][meals][]" 
                                               value="breakfast"
                                               {{ $hasBreakfast ? 'checked' : '' }}
                                               {{ !$canEditBookings ? 'disabled' : '' }}
                                               class="w-4 h-4 text-amber-600 bg-amber-100 border-amber-300 rounded focus:ring-amber-500 {{ !$canEditBookings ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    </td>
                                    
                                    @if(isset($selectedDate) && !\Carbon\Carbon::parse($selectedDate)->isFriday())
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <input type="checkbox" 
                                                   name="bookings[{{ $soldado->id }}][meals][]" 
                                                   value="lunch"
                                                   {{ $hasLunch ? 'checked' : '' }}
                                                   {{ !$canEditBookings ? 'disabled' : '' }}
                                                   class="w-4 h-4 text-orange-600 bg-orange-100 border-orange-300 rounded focus:ring-orange-500 {{ !$canEditBookings ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Total: <strong>{{ $soldadosEv->count() }}</strong> Soldados EV
                            @if(!$canEditBookings)
                                <span class="ml-2 text-orange-600 font-medium">
                                    @if($isWeekend)
                                        • Final de semana
                                    @elseif($isToday)
                                        • Mesmo dia útil (não permitido)
                                    @elseif($cutoffPassed)
                                        • Prazo encerrado (após 13h)
                                    @endif
                                </span>
                            @endif
                        </div>
                        
                        @if($canEditBookings)
                            <button type="submit" 
                                    class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-200">
                                💾 Salvar Arranchamento
                            </button>
                        @else
                            <div class="text-sm text-gray-500 italic">
                                Apenas visualização
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @if($canEditBookings)
            </form>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum Soldado EV encontrado</h3>
            <p class="text-gray-600">Não há soldados eventuais cadastrados na sua companhia ou eles estão inativos.</p>
        </div>
    @endif
    
    <!-- Aviso sobre sexta-feira -->
    @if(isset($selectedDate) && \Carbon\Carbon::parse($selectedDate)->isFriday())
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-yellow-700 font-medium">
                    ℹ️ Sexta-feira: Apenas café da manhã disponível.
                </span>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página do furriel carregada com sucesso!');
    
    var dateInput = document.getElementById('booking_date');
    
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            console.log('Data alterada para:', this.value);
            var url = new URL(window.location.href);
            url.searchParams.set('date', this.value);
            window.location.href = url.toString();
        });
    }
    
    // Função para selecionar/deselecionar todas as refeições de um tipo
    window.toggleAllMeals = function(mealType) {
        var checkboxes = document.querySelectorAll('input[name$="[meals][]"][value="' + mealType + '"]:not([disabled])');
        var checkedCount = document.querySelectorAll('input[name$="[meals][]"][value="' + mealType + '"]:checked:not([disabled])').length;
        var shouldCheck = checkedCount < checkboxes.length;
        
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = shouldCheck;
        }
    };
    
    // Função para selecionar/deselecionar todas as refeições de um soldado
    window.toggleSoldierMeals = function(userId) {
        var checkboxes = document.querySelectorAll('input[name="bookings[' + userId + '][meals][]"]:not([disabled])');
        var checkedCount = document.querySelectorAll('input[name="bookings[' + userId + '][meals][]"]:checked:not([disabled])').length;
        var shouldCheck = checkedCount < checkboxes.length;
        
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = shouldCheck;
        }
    };
});
</script>
@endsection
