<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reservas - SAGA</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100" data-laranjeira="{{ auth()->user()->isLaranjeira() ? '1' : '0' }}">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    📅 Reservas de Arranchamento
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
            <!-- Info Box -->
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                <strong>📋 Sistema de Reservas</strong> - Aqui você pode agendar suas refeições (café da manhã e almoço) para os dias úteis.
                @if(auth()->user()->status === 'Laranjeira')
                    <div class="mt-2 p-2 bg-purple-50 border border-purple-300 rounded text-sm text-purple-700">
                        ✅ Status <strong>Laranjeira</strong> ativo: jantar (JN) ficará visível quando reservado futuramente.
                    </div>
                @endif
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Marque as refeições desejadas clicando nas caixas de seleção do calendário</li>
                    <li>Agendamentos podem ser feitos apenas para dias úteis (segunda a sexta)</li>
                    <li>Nas sextas-feiras, apenas café da manhã está disponível</li>
                    <li>Você pode agendar até 30 dias no futuro</li>
                    <li>As alterações são salvas automaticamente</li>
                </ul>
            </div>

            <!-- Calendar View -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Calendário de Reservas</h3>
                    
                    <!-- Month Navigation -->
                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('bookings.index', ['month' => $calendarDate->copy()->subMonth()->format('Y-m')]) }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-300">
                            ← Mês Anterior
                        </a>
                        <h2 class="text-xl font-semibold">{{ $calendarDate->locale('pt_BR')->isoFormat('MMMM [de] YYYY') }}</h2>
                        <a href="{{ route('bookings.index', ['month' => $calendarDate->copy()->addMonth()->format('Y-m')]) }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-300">
                            Próximo Mês →
                        </a>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <!-- Header days -->
                        <div class="p-2 text-center font-semibold bg-gray-200">Dom</div>
                        <div class="p-2 text-center font-semibold bg-gray-200">Seg</div>
                        <div class="p-2 text-center font-semibold bg-gray-200">Ter</div>
                        <div class="p-2 text-center font-semibold bg-gray-200">Qua</div>
                        <div class="p-2 text-center font-semibold bg-gray-200">Qui</div>
                        <div class="p-2 text-center font-semibold bg-gray-200">Sex</div>
                        <div class="p-2 text-center font-semibold bg-gray-200">Sáb</div>

                        @php
                            $startOfMonth = $calendarDate->copy()->startOfMonth();
                            $endOfMonth = $calendarDate->copy()->endOfMonth();
                            $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
                            $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon\Carbon::SATURDAY);
                            $today = Carbon\Carbon::today();
                            $maxBookingDate = Carbon\Carbon::today()->addDays(30);
                        @endphp

                        @for($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay())
                            @php
                                $isCurrentMonth = $date->month === $calendarDate->month;
                                $isToday = $date->isSameDay($today);
                                $isPast = $date->isPast();
                                $isWeekend = $date->isWeekend();
                                $isTooFar = $date->gt($maxBookingDate);
                                
                                // Check if deadline has passed using the same logic as backend
                                $now = Carbon\Carbon::now();
                                $deadlinePassed = false;
                                
                                // Se é hoje ou passou, não pode reservar
                                if ($date->isPast() || $date->isToday()) {
                                    $deadlinePassed = true;
                                }
                                // Se é amanhã e já passou das 13h hoje, bloquear
                                else if ($date->isTomorrow() && $now->hour >= 13) {
                                    $deadlinePassed = true;
                                }
                                
                                // Base bookable rule (weekdays)
                                $isBookable = $isCurrentMonth && !$isPast && !$isTooFar && !$deadlinePassed;
                                $userIsLaranjeira = auth()->user()->status === 'Laranjeira';
                                if (!$userIsLaranjeira) {
                                    $isBookable = $isBookable && !$isWeekend; // non Laranjeira still blocked weekends
                                }
                                $isFriday = $date->isFriday();
                                
                                $dayBookings = $monthBookings->get($date->format('Y-m-d'), collect());
                                $hasBreakfast = $dayBookings->where('meal_type', 'breakfast')->isNotEmpty();
                                $hasLunch = $dayBookings->where('meal_type', 'lunch')->isNotEmpty();
                                $hasDinner = $dayBookings->where('meal_type', 'dinner')->isNotEmpty();
                            @endphp
                            
                            <div class="p-2 text-center relative min-h-[70px] flex flex-col justify-between
                                {{ !$isCurrentMonth ? 'text-gray-400' : '' }}
                                {{ $isToday ? 'bg-blue-100 font-bold' : '' }}
                                {{ !$isBookable ? 'bg-gray-100 text-gray-400' : 'border cursor-pointer hover:bg-blue-50' }}
                                {{ $isBookable ? 'calendar-day' : '' }}"
                                @if($isBookable)
                                    data-date="{{ $date->format('Y-m-d') }}" 
                                    data-is-friday="{{ $isFriday ? 'true' : 'false' }}"
                                    onclick="showBookingModal('{{ $date->format('Y-m-d') }}', '{{ $isFriday ? 'true' : 'false' }}')"
                                @endif>
                                
                                <div class="flex-grow flex flex-col justify-between h-full">
                                    <span class="block text-sm font-medium">{{ $date->day }}</span>
                                    
                                    @if($dayBookings->isNotEmpty())
                                        <div class="flex justify-center space-x-1 mt-auto">
                                            @if($hasBreakfast)
                                                <span class="bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold border border-green-600 shadow-sm">CF</span>
                                            @endif
                                            @if($hasLunch)
                                                <span class="bg-blue-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold border border-blue-600 shadow-sm">AL</span>
                                            @endif
                                            @if($hasDinner)
                                                <span class="bg-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold border border-purple-600 shadow-sm">JN</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                @if($isToday)
                                    <div class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full"></div>
                                @endif
                            </div>
                        @endfor
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap gap-4 text-sm mt-4">
                        <div class="flex items-center">
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold border border-green-600 shadow-sm mr-2">CF</span>
                            <span class="text-gray-700">Café da Manhã</span>
                        </div>
                        <div class="flex items-center">
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-bold border border-blue-600 shadow-sm mr-2">AL</span>
                            <span class="text-gray-700">Almoço</span>
                        </div>
                        @if(auth()->user()->status === 'Laranjeira')
                        <div class="flex items-center">
                            <span class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full font-bold border border-purple-600 shadow-sm mr-2">JN</span>
                            <span class="text-gray-700">Jantar (Laranjeira)</span>
                        </div>
                        @endif
                        @if(!auth()->user()->isLaranjeira())
                        <div class="flex items-center">
                            <span class="bg-gray-300 text-gray-600 px-2 py-1 rounded mr-2">🚫</span>
                            <span class="text-gray-600">Fins de semana indisponíveis</span>
                        </div>
                        @else
                        <div class="flex items-center">
                            <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded mr-2">🌙</span>
                            <span class="text-gray-600">Fins de semana liberados (Laranjeira)</span>
                        </div>
                        @endif
                        <div class="flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2 font-bold">●</span>
                            <span class="text-gray-600">Dia atual</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Bookings Summary -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Meus Próximos Agendamentos</h3>
                    
                    @if($upcomingBookings->count() > 0)
                        @php
                            $bookingsCount = $upcomingBookings->count();
                            $useColumns = $bookingsCount >= 12;
                            $halfCount = $useColumns ? ceil($bookingsCount / 2) : $bookingsCount;
                            $firstColumnBookings = $upcomingBookings->take($halfCount);
                            $secondColumnBookings = $useColumns ? $upcomingBookings->skip($halfCount) : collect();
                        @endphp
                        
                        <div class="{{ $useColumns ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : '' }}">
                            <!-- Primeira coluna ou coluna única -->
                            <div class="space-y-2">
                                @foreach($firstColumnBookings as $booking)
                                    @php
                                        $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                        $isPast = $bookingDate->isPast();
                                        $isToday = $bookingDate->isToday();
                                        
                                        // Check if cancellation deadline has passed using the new rule
                                        $now = \Carbon\Carbon::now();
                                        $canCancel = !$isPast && !$isToday;
                                        
                                        if ($canCancel) {
                                            if ($bookingDate->isTomorrow() && $now->hour >= 13) {
                                                $canCancel = false;
                                            } else {
                                                $deadlineDateTime = $bookingDate->copy()->subDay()->setTime(13, 0, 0);
                                                $canCancel = $now->lt($deadlineDateTime);
                                            }
                                        }
                                        
                                        // Garantir que status seja tratado corretamente
                                        $bookingStatus = $booking->status ?? 'confirmed';
                                        $bookingStatus = trim(strtolower($bookingStatus));
                                    @endphp
                                    <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full {{ $booking->meal_type === 'breakfast' ? 'bg-green-500' : 'bg-blue-500' }}"></div>
                                            <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                                            @php
                                                $mealLabel = match($booking->meal_type) {
                                                    'breakfast' => 'Café da Manhã',
                                                    'lunch' => 'Almoço',
                                                    'dinner' => 'Jantar',
                                                    default => ucfirst($booking->meal_type)
                                                };
                                            @endphp
                                            <span class="text-sm text-gray-600">{{ $mealLabel }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full {{ $bookingStatus === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($bookingStatus) }}
                                            </span>
                                        </div>
                                        @if($canCancel && $bookingStatus === 'confirmed')
                                            <button onclick="cancelBooking('{{ $booking->id }}', this)" class="text-red-600 hover:text-red-800 transition duration-300 p-1" title="Cancelar reserva">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @elseif($bookingStatus === 'cancelled')
                                            <span class="text-red-400 text-sm">Cancelada</span>
                                        @else
                                            <span class="text-gray-400 p-1" title="Não cancelável">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Segunda coluna (apenas quando há 12+ registros) -->
                            @if($useColumns && $secondColumnBookings->count() > 0)
                                <div class="space-y-2">
                                    @foreach($secondColumnBookings as $booking)
                                        @php
                                            $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                            $isPast = $bookingDate->isPast();
                                            $isToday = $bookingDate->isToday();
                                            $deadlineDateTime = $bookingDate->copy()->subDay()->setTime(13, 0, 0);
                                            $canCancel = !$isPast && !$isToday && \Carbon\Carbon::now()->lt($deadlineDateTime);
                                            
                                            // Garantir que status seja tratado corretamente
                                            $bookingStatus = $booking->status ?? 'confirmed';
                                            $bookingStatus = trim(strtolower($bookingStatus));
                                        @endphp
                                        <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 rounded-full {{ $booking->meal_type === 'breakfast' ? 'bg-green-500' : 'bg-blue-500' }}"></div>
                                                <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                                                @php
                                                    $mealLabel = match($booking->meal_type) {
                                                        'breakfast' => 'Café da Manhã',
                                                        'lunch' => 'Almoço',
                                                        'dinner' => 'Jantar',
                                                        default => ucfirst($booking->meal_type)
                                                    };
                                                @endphp
                                                <span class="text-sm text-gray-600">{{ $mealLabel }}</span>
                                                <span class="text-xs px-2 py-1 rounded-full {{ $bookingStatus === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($bookingStatus) }}
                                                </span>
                                            </div>
                                            @if($canCancel && $bookingStatus === 'confirmed')
                                                <button onclick="cancelBooking('{{ $booking->id }}', this)" class="text-red-600 hover:text-red-800 transition duration-300 p-1" title="Cancelar reserva">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @elseif($bookingStatus === 'cancelled')
                                                <span class="text-red-400 text-sm">Cancelada</span>
                                            @else
                                                <span class="text-gray-400 p-1" title="Não cancelável">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a1 1 0 01-1 1H5a1 1 0 01-1-1V8a1 1 0 011-1h3z" />
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 mt-2">Nenhuma reserva encontrada</h4>
                            <p class="text-sm text-gray-600">Você ainda não tem reservas futuras. Use as ações rápidas para fazer suas reservas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>
                        <div class="space-y-3">
                            <button id="btn-reserve-breakfast" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                ☕ Reservar Café da Próxima Semana
                            </button>
                            <button id="btn-reserve-lunch" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                🍽️ Reservar Almoço da Próxima Semana
                            </button>
                            <a href="{{ route('bookings.history') }}" class="block w-full bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded transition duration-300 text-center">
                                📋 Ver Histórico de Reservas
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total este mês:</span>
                                <span class="font-semibold">{{ $totalMeals }} refeições</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cafés:</span>
                                <span class="font-semibold">{{ $breakfastCount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Almoços:</span>
                                <span class="font-semibold">{{ $lunchCount }}</span>
                            </div>
                            @if(auth()->user()->status === 'Laranjeira')
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jantares:</span>
                                <span class="font-semibold">{{ $dinnerCount ?? 0 }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rules -->
            <div class="mt-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                <strong>📝 Regras Importantes:</strong>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><strong>Ações Rápidas:</strong> Os botões reservam automaticamente para a <strong>próxima semana</strong> (segunda a sexta)</li>
                    <li>Almoço não disponível nas sextas-feiras</li>
                    <li><strong>Prazo limite:</strong> Reservas devem ser feitas até às 13h do dia anterior</li>
                    <li><strong>Não é possível:</strong> Reservar para o mesmo dia ou após o prazo</li>
                    <li>Cancelamentos devem ser feitos até às 13h do dia anterior</li>
                    <li>Reservas do dia atual não podem ser canceladas</li>
                    <li>Prazo máximo: 30 dias de antecedência</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden max-w-md">
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg">
            <div id="toast-message" class="text-sm leading-relaxed whitespace-pre-line">Mensagem</div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loading-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Processando...</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Fazendo suas reservas, aguarde...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a1 1 0 01-1 1H5a1 1 0 01-1-1V8a1 1 0 011-1h3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4 text-center" id="modal-title">Fazer Reserva</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center" id="modal-date">Data selecionada</p>
                    <div class="mt-4 space-y-3">
                        <button id="btn-book-breakfast" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                            ☕ Reservar Café da Manhã
                        </button>
                        <button id="btn-book-lunch" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                            🍽️ Reservar Almoço
                        </button>
                        @if(auth()->user()->status === 'Laranjeira')
                        <button id="btn-book-dinner" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                            🌙 Reservar Jantar
                        </button>
                        @endif
                    </div>
                </div>
                <div class="flex justify-center mt-4">
                    <button onclick="closeBookingModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Month bookings data
        const monthBookings = @json($monthBookings);

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastDiv = toast.querySelector('div');
            
            // Convert line breaks to HTML breaks for proper display
            toastMessage.innerHTML = message.replace(/\n/g, '<br>');
            
            // Update toast color based on type
            if (type === 'error') {
                toastDiv.className = 'bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg';
            } else if (type === 'warning') {
                toastDiv.className = 'bg-yellow-500 text-white px-6 py-4 rounded-lg shadow-lg';
            } else {
                toastDiv.className = 'bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg';
            }
            
            toast.classList.remove('hidden');
            
            // Increase timeout for longer messages
            const timeout = message.length > 100 ? 8000 : 7000;
            setTimeout(() => {
                toast.classList.add('hidden');
            }, timeout);
        }

        // Show/hide loading modal
        function showLoading() {
            document.getElementById('loading-modal').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-modal').classList.add('hidden');
        }

        // Reserve breakfast for the week
        document.getElementById('btn-reserve-breakfast').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '⏳ Processando...';
            showLoading();
            
            fetch('/bookings/reserve-breakfast-week', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast(`${data.message} (${data.bookings} reservas feitas para a semana de ${data.week_start})`);
                    // Reload page to update bookings
                    setTimeout(() => location.reload(), 2000);
                } else {
                    const toastType = data.type || 'error';
                    showToast(data.message, toastType);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showToast('Erro ao fazer reservas. Tente novamente.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '☕ Reservar Café da Próxima Semana';
            });
        });

        // Reserve lunch for the week
        document.getElementById('btn-reserve-lunch').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '⏳ Processando...';
            showLoading();
            
            fetch('/bookings/reserve-lunch-week', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast(`${data.message} (${data.bookings} reservas feitas para a semana de ${data.week_start})`);
                    // Reload page to update bookings
                    setTimeout(() => location.reload(), 2000);
                } else {
                    const toastType = data.type || 'error';
                    showToast(data.message, toastType);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showToast('Erro ao fazer reservas. Tente novamente.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '🍽️ Reservar Almoço da Próxima Semana';
            });
        });

        // Cancel booking function
        function cancelBooking(bookingId, element) {
            if (!confirm('Tem certeza que deseja cancelar esta reserva?')) {
                return;
            }
            
            element.disabled = true;
            const originalContent = element.innerHTML;
            element.innerHTML = 'Cancelando...';
            
            fetch(`/bookings/${bookingId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    // Remove the booking element from DOM
                    element.closest('.flex').remove();
                    // Reload page to update calendar icons
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                    element.disabled = false;
                    element.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Erro ao cancelar reserva. Tente novamente.', 'error');
                element.disabled = false;
                element.innerHTML = originalContent;
            });
        }

        // Calendar booking functions
        let selectedDate = '';
        let isFridaySelected = false;

    function showBookingModal(date, isFridayStr) {
            selectedDate = date;
            isFridaySelected = isFridayStr === 'true';
            
            const modal = document.getElementById('booking-modal');
            const modalDate = document.getElementById('modal-date');
            const breakfastButton = document.getElementById('btn-book-breakfast');
            const lunchButton = document.getElementById('btn-book-lunch');
            
            // Format date for display
            const dateObj = new Date(date + 'T00:00:00');
            const formattedDate = dateObj.toLocaleDateString('pt-BR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            modalDate.textContent = formattedDate;
            
            // Check existing bookings for this date
            const dayBookings = monthBookings[date] || [];
            const hasBreakfast = dayBookings.some(booking => booking.meal_type === 'breakfast');
            const hasLunch = dayBookings.some(booking => booking.meal_type === 'lunch');
            const hasDinner = dayBookings.some(booking => booking.meal_type === 'dinner');
            
            // Reset buttons to default state
            breakfastButton.disabled = false;
            breakfastButton.classList.remove('opacity-50', 'cursor-not-allowed');
            breakfastButton.title = '';
            
            lunchButton.disabled = false;
            lunchButton.classList.remove('opacity-50', 'cursor-not-allowed');
            lunchButton.title = '';
            
            // Disable breakfast button if already booked
            if (hasBreakfast) {
                breakfastButton.disabled = true;
                breakfastButton.classList.add('opacity-50', 'cursor-not-allowed');
                breakfastButton.title = 'Você já possui uma reserva de café da manhã para este dia';
                breakfastButton.innerHTML = '☕ Café já Reservado';
            } else {
                breakfastButton.innerHTML = '☕ Reservar Café da Manhã';
            }
            
            // Disable lunch button if Friday or already booked
            if (isFridaySelected) {
                lunchButton.disabled = true;
                lunchButton.classList.add('opacity-50', 'cursor-not-allowed');
                lunchButton.title = 'Almoço não disponível às sextas-feiras';
                lunchButton.innerHTML = '🍽️ Almoço Indisponível';
            } else if (hasLunch) {
                lunchButton.disabled = true;
                lunchButton.classList.add('opacity-50', 'cursor-not-allowed');
                lunchButton.title = 'Você já possui uma reserva de almoço para este dia';
                lunchButton.innerHTML = '🍽️ Almoço já Reservado';
            } else {
                lunchButton.innerHTML = '🍽️ Reservar Almoço';
            }

            // Weekend handling: restrict only if user not Laranjeira
            const dateObjTmp = new Date(date + 'T00:00:00');
            const isWeekend = dateObjTmp.getDay() === 0 || dateObjTmp.getDay() === 6;
            let dinnerButton = document.getElementById('btn-book-dinner');
            const userIsLaranjeira = document.body.dataset.laranjeira === '1';
            if (isWeekend && !userIsLaranjeira) {
                breakfastButton.disabled = true;
                breakfastButton.classList.add('opacity-50', 'cursor-not-allowed');
                breakfastButton.title = 'Indisponível em fins de semana';
                breakfastButton.innerHTML = '☕ Indisponível';
                lunchButton.disabled = true;
                lunchButton.classList.add('opacity-50', 'cursor-not-allowed');
                lunchButton.title = 'Indisponível em fins de semana';
                lunchButton.innerHTML = '🍽️ Indisponível';
                if (dinnerButton) {
                    dinnerButton.disabled = true;
                    dinnerButton.classList.add('opacity-50','cursor-not-allowed');
                    dinnerButton.title = 'Indisponível em fins de semana';
                    dinnerButton.innerHTML = '🌙 Indisponível';
                }
            }
            
            // Dinner button handling (re-use existing variable)
            dinnerButton = document.getElementById('btn-book-dinner');
            if (dinnerButton) {
                dinnerButton.disabled = false;
                dinnerButton.classList.remove('opacity-50', 'cursor-not-allowed');
                dinnerButton.title = '';
                if (hasDinner) {
                    dinnerButton.disabled = true;
                    dinnerButton.classList.add('opacity-50', 'cursor-not-allowed');
                    dinnerButton.title = 'Você já possui uma reserva de jantar para este dia';
                    dinnerButton.innerHTML = '🌙 Jantar já Reservado';
                } else {
                    dinnerButton.innerHTML = '🌙 Reservar Jantar';
                }
            }

            modal.classList.remove('hidden');
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').classList.add('hidden');
        }

        // Book individual meal
        function bookMeal(mealType) {
            if (!selectedDate) return;
            
            let button;
            if (mealType === 'breakfast') button = document.getElementById('btn-book-breakfast');
            else if (mealType === 'lunch') button = document.getElementById('btn-book-lunch');
            else button = document.getElementById('btn-book-dinner');
            
            // Check if button is disabled
            if (button.disabled) {
                showToast('Esta reserva não está disponível', 'warning');
                return;
            }
            
            button.disabled = true;
            button.innerHTML = '⏳ Reservando...';
            
            fetch('/bookings/reserve-single', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    date: selectedDate,
                    meal_type: mealType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeBookingModal();
                    // Reload page to update calendar
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Erro ao fazer reserva. Tente novamente.', 'error');
            })
            .finally(() => {
                button.disabled = false;
                if (mealType === 'breakfast') button.innerHTML = '☕ Reservar Café da Manhã';
                else if (mealType === 'lunch') button.innerHTML = '🍽️ Reservar Almoço';
                else button.innerHTML = '🌙 Reservar Jantar';
            });
        }

    // Expose modal function globally for inline onclick
    window.showBookingModal = showBookingModal;

    // Add event listeners for booking buttons
    document.getElementById('btn-book-breakfast').addEventListener('click', () => bookMeal('breakfast'));
    document.getElementById('btn-book-lunch').addEventListener('click', () => bookMeal('lunch'));
    const dinnerBtn = document.getElementById('btn-book-dinner');
    if (dinnerBtn) dinnerBtn.addEventListener('click', () => bookMeal('dinner'));

        // Close modal when clicking outside
        document.getElementById('booking-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    </script>

    <!-- Footer -->
    <footer class="bg-gray-25 border-t border-gray-100 mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-400 italic">
                © 2025 SAGA - Desenv: Augusto
            </div>
        </div>
    </footer>
</body>
</html>
