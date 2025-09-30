@extends('layouts.app')

@section('title', 'Arranchamento da Cia')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    @php
        $headerDateIso = $selectedDateIso ?? ($selectedDate ?? now()->format('Y-m-d'));
        try {
            $headerCarbon = \Carbon\Carbon::parse($headerDateIso);
        } catch (\Exception $e) {
            $headerCarbon = now();
        }
        $headerDateDisplay = $headerCarbon->translatedFormat('l, d/m/Y');
        $organizationName = optional(optional($furriel)->organization)->name ?? null;
        $subunitName = (isset($furriel) && !empty($furriel->subunit)) ? $furriel->subunit : null;
        $headerBadges = array_filter([
            $organizationName,
            $audience['scope'] ?? null,
            $subunitName,
        ]);
    @endphp

    <header class="mb-8 bg-gradient-to-r from-green-600 via-green-500 to-emerald-600 rounded-3xl shadow-xl border border-emerald-500/30 overflow-hidden">
        <div class="px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-4 text-white">
                    <div class="flex items-start md:items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm shadow-inner">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold leading-tight">Arranchamento da Cia</h1>
                            <p class="text-emerald-100 text-base sm:text-lg">
                                {{ $audience['description'] ?? 'Gerenciamento das refeições da organização' }}
                            </p>
                        </div>
                    </div>

                    @if(!empty($headerBadges))
                        <div class="flex flex-wrap items-center gap-2 text-sm text-white/90">
                            @foreach($headerBadges as $badge)
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm font-semibold">
                                    <svg class="w-3.5 h-3.5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                    </svg>
                                    {{ $badge }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="bg-white/10 border border-white/20 rounded-2xl px-4 py-3 text-white backdrop-blur-sm shadow-lg">
                        <span class="block text-xs uppercase tracking-wider text-emerald-100">Data selecionada</span>
                        <span class="text-lg font-semibold">{{ $headerDateDisplay }}</span>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-3 bg-white text-green-700 font-semibold rounded-xl shadow-lg hover:bg-gray-100 transition-all duration-200">
                        ← Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

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
            
            @php
                $currentDate = \Carbon\Carbon::now();
                $minDateIso = $currentDate->format('Y-m-d');
                $maxDateIso = $currentDate->copy()->addDays(30)->format('Y-m-d');
                $selectedDateIsoValue = $selectedDateIso ?? ($selectedDate ?? $minDateIso);
                $selectedDateDisplayValue = $selectedDateDisplay ?? \Carbon\Carbon::parse($selectedDateIsoValue)->format('d/m/Y');
            @endphp

            <form method="GET" action="{{ route('furriel.arranchamento.index') }}" id="bookingDateForm">
                <div class="space-y-3">
                    <label for="booking_date" class="block text-sm font-medium text-gray-700">
                        Selecionar Data:
                    </label>
                    <input type="hidden"
                           id="booking_date_iso"
                           name="date"
                           value="{{ $selectedDateIsoValue }}">
                    <div class="relative w-full sm:w-1/2">
                        <input type="text" 
                               id="booking_date" 
                               name="date_display"
                               value="{{ $selectedDateDisplayValue }}"
                               data-min-date="{{ $minDateIso }}"
                               data-max-date="{{ $maxDateIso }}"
                               placeholder="DD/MM/AAAA"
                               inputmode="numeric"
                               autocomplete="off"
                               class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="button"
                                id="bookingDatePickerTrigger"
                                class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-orange-600 hover:text-orange-700"
                                aria-label="Abrir calendário">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
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
                    <p class="text-sm font-medium text-gray-600">Total {{ $audience['label_plural'] ?? 'Militares' }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ isset($militares) ? $militares->count() : 0 }}</p>
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

    <!-- Lista de militares elegíveis -->
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
    
    @if(isset($militares) && $militares->count() > 0)
        @if($canEditBookings)
            <form method="POST" action="{{ route('furriel.arranchamento.store') }}">
                @csrf
                <input type="hidden" name="booking_date" value="{{ $selectedDateIsoValue }}">
        @endif
        
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header da Tabela -->
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-900">
                        🍽️ {{ $audience['label_plural'] ?? 'Militares' }} - Total: {{ $militares->count() }}
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
                                    {{ $audience['label_singular'] ?? 'Militar' }}
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
                            @foreach($militares as $militar)
                                @php
                                    $currentDate = $selectedDate ?? date('Y-m-d');
                                    
                                    // Reservas existentes para o militar
                                    $existingBookingsForDate = isset($militar->existingBookings) ? $militar->existingBookings : collect();
                                    
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
                                        
                                    $initials = substr($militar->war_name ?: $militar->full_name, 0, 2);
                                @endphp
                                
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                                                <span class="text-white font-bold text-sm">{{ strtoupper($initials) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <input type="hidden" name="bookings[{{ $militar->id }}][user_id]" value="{{ $militar->id }}">
                                                <div class="font-medium text-gray-900">{{ $militar->war_name ?: $militar->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ isset($militar->rank) ? ($militar->rank->abbreviation ?? 'N/A') : 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" 
                                               name="bookings[{{ $militar->id }}][meals][]" 
                                               value="breakfast"
                                               {{ $hasBreakfast ? 'checked' : '' }}
                                               {{ !$canEditBookings ? 'disabled' : '' }}
                                               class="w-4 h-4 text-amber-600 bg-amber-100 border-amber-300 rounded focus:ring-amber-500 {{ !$canEditBookings ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    </td>
                                    
                                    @if(isset($selectedDate) && !\Carbon\Carbon::parse($selectedDate)->isFriday())
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <input type="checkbox" 
                                                   name="bookings[{{ $militar->id }}][meals][]" 
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
                            Total: <strong>{{ $militares->count() }}</strong> {{ $audience['label_plural'] ?? 'Militares' }}
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
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum {{ strtolower($audience['label_singular'] ?? 'militar') }} encontrado</h3>
            <p class="text-gray-600">Não há {{ strtolower($audience['label_plural'] ?? 'militares') }} elegíveis cadastrados ou eles estão inativos.</p>
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

@endsection

@push('scripts')
<script>
function loadFlatpickrResources() {
    if (window.__flatpickrLoader) {
        return window.__flatpickrLoader;
    }

    const cssId = 'flatpickr-css';
    if (!document.getElementById(cssId)) {
        const link = document.createElement('link');
        link.id = cssId;
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
        document.head.appendChild(link);
    }

    window.__flatpickrLoader = new Promise((resolve, reject) => {
        if (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.pt) {
            resolve();
            return;
        }

        const loadLocale = () => {
            if (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.pt) {
                resolve();
                return;
            }

            const localeScript = document.createElement('script');
            localeScript.src = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js';
            localeScript.onload = resolve;
            localeScript.onerror = reject;
            document.body.appendChild(localeScript);
        };

        if (!window.flatpickr) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
            script.onload = loadLocale;
            script.onerror = reject;
            document.body.appendChild(script);
        } else {
            loadLocale();
        }
    });

    return window.__flatpickrLoader;
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Página do furriel carregada com sucesso!');

    const dateInput = document.getElementById('booking_date');
    const dateHiddenInput = document.getElementById('booking_date_iso');
    const bookingDateForm = document.getElementById('bookingDateForm');
    const calendarButton = document.getElementById('bookingDatePickerTrigger');

    const minDate = dateInput ? dateInput.dataset.minDate : null;
    const maxDate = dateInput ? dateInput.dataset.maxDate : null;

    const isoToDisplay = (isoDate) => {
        if (!isoDate) return '';
        const [year, month, day] = isoDate.split('-');
        if (!year || !month || !day) return '';
        return `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`;
    };

    const displayToIso = (displayDate) => {
        if (!displayDate) return null;
        const digits = displayDate.replace(/\D/g, '').slice(0, 8);
        if (digits.length !== 8) {
            return null;
        }

        const day = digits.slice(0, 2);
        const month = digits.slice(2, 4);
        const year = digits.slice(4, 8);

        const isoDate = `${year}-${month}-${day}`;
        const parsedDate = new Date(`${isoDate}T00:00:00`);

        if (
            Number.isNaN(parsedDate.getTime()) ||
            parsedDate.getUTCFullYear() !== parseInt(year, 10) ||
            parsedDate.getUTCMonth() + 1 !== parseInt(month, 10) ||
            parsedDate.getUTCDate() !== parseInt(day, 10)
        ) {
            return null;
        }

        return isoDate;
    };

    const applyDateMask = (value) => {
        const digits = value.replace(/\D/g, '').slice(0, 8);
        const parts = [];

        if (digits.length > 0) {
            parts.push(digits.slice(0, Math.min(2, digits.length)));
        }
        if (digits.length >= 3) {
            parts.push(digits.slice(2, Math.min(4, digits.length)));
        }
        if (digits.length >= 5) {
            parts.push(digits.slice(4, 8));
        }

        return parts.join('/');
    };

    const isWithinRange = (isoDate) => {
        if (!isoDate) return false;
        if (minDate && isoDate < minDate) return false;
        if (maxDate && isoDate > maxDate) return false;
        return true;
    };

    const restorePreviousValue = () => {
        if (!dateInput) return;

        if (dateHiddenInput && dateHiddenInput.value) {
            dateInput.value = isoToDisplay(dateHiddenInput.value);
        } else {
            dateInput.value = '';
        }
    };

    const applySelectedDate = (isoDate, { shouldNavigate = true, showAlert = true } = {}) => {
        if (!isoDate) {
            if (showAlert) {
                alert('Informe uma data válida no formato DD/MM/AAAA.');
            }
            restorePreviousValue();
            return false;
        }

        if (!isWithinRange(isoDate)) {
            if (showAlert) {
                alert('Data fora do intervalo permitido para arranchamento.');
            }
            restorePreviousValue();
            return false;
        }

        if (dateHiddenInput) {
            dateHiddenInput.value = isoDate;
        }

        if (dateInput) {
            dateInput.value = isoToDisplay(isoDate);
        }

        if (shouldNavigate) {
            const url = new URL(window.location.href);
            url.searchParams.set('date', isoDate);
            window.location.href = url.toString();
        }

        return true;
    };

    if (dateInput) {
        if (dateHiddenInput && dateHiddenInput.value && !dateInput.value) {
            dateInput.value = isoToDisplay(dateHiddenInput.value);
        }

        dateInput.addEventListener('input', function(event) {
            const maskedValue = applyDateMask(event.target.value);
            event.target.value = maskedValue;
        });

        dateInput.addEventListener('blur', function(event) {
            const isoDate = displayToIso(event.target.value);
            if (isoDate) {
                event.target.value = isoToDisplay(isoDate);
            }
        });

        dateInput.addEventListener('change', function(event) {
            const isoDate = displayToIso(event.target.value);
            applySelectedDate(isoDate);
        });
    }

    if (bookingDateForm && dateInput) {
        bookingDateForm.addEventListener('submit', function(event) {
            const isoDate = displayToIso(dateInput.value);
            const isValid = applySelectedDate(isoDate, { shouldNavigate: false });

            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    loadFlatpickrResources()
        .then(() => {
            if (!dateInput || !window.flatpickr) {
                return;
            }

            if (window.bookingDatePicker && typeof window.bookingDatePicker.destroy === 'function') {
                window.bookingDatePicker.destroy();
            }

            window.bookingDatePicker = flatpickr(dateInput, {
                dateFormat: 'd/m/Y',
                defaultDate: dateHiddenInput && dateHiddenInput.value ? dateHiddenInput.value : null,
                allowInput: true,
                locale: (window.flatpickr.l10ns && window.flatpickr.l10ns.pt) ? window.flatpickr.l10ns.pt : 'pt',
                minDate: minDate || null,
                maxDate: maxDate || null,
                disableMobile: true,
                onChange: function(selectedDates) {
                    if (!selectedDates.length) {
                        return;
                    }

                    const isoDate = selectedDates[0].toISOString().split('T')[0];
                    applySelectedDate(isoDate);
                }
            });

            if (calendarButton) {
                calendarButton.addEventListener('click', function() {
                    if (window.bookingDatePicker) {
                        window.bookingDatePicker.open();
                    }
                });
            }
        })
        .catch((error) => {
            console.error('Erro ao carregar o calendário:', error);
        });

    window.toggleAllMeals = function(mealType) {
        const selector = `input[name$="[meals][]"][value="${mealType}"]:not([disabled])`;
        const checkboxes = document.querySelectorAll(selector);
        const checkedCount = document.querySelectorAll(`${selector}:checked`).length;
        const shouldCheck = checkedCount < checkboxes.length;

        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = shouldCheck;
        }
    };

    window.toggleSoldierMeals = function(userId) {
        const selector = `input[name="bookings[${userId}][meals][]"]:not([disabled])`;
        const checkboxes = document.querySelectorAll(selector);
        const checkedCount = document.querySelectorAll(`${selector}:checked`).length;
        const shouldCheck = checkedCount < checkboxes.length;

        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = shouldCheck;
        }
    };
});
</script>
@endpush
