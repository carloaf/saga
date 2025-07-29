<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ $monthName }}</h3>
            <div class="flex space-x-2">
                <button wire:click="previousMonth" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    ←
                </button>
                <button wire:click="nextMonth" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    →
                </button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 gap-1 mb-4">
            @foreach(['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'] as $day)
            <div class="py-2 text-center text-sm font-medium text-gray-700">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1">
            @foreach($calendar as $week)
                @foreach($week as $day)
                <div class="min-h-[100px] p-2 border border-gray-200 
                           {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }}
                           {{ $day['isWeekend'] ? 'bg-gray-100' : '' }}">
                    
                    <div class="text-sm text-gray-600 mb-2">{{ $day['date']->format('d') }}</div>
                    
                    @if($day['isBookable'] && !$day['isWeekend'])
                        <div class="space-y-1">
                            <!-- Breakfast -->
                            <label class="flex items-center space-x-1 text-xs">
                                <input type="checkbox" 
                                       wire:click="toggleBooking('{{ $day['date']->format('Y-m-d') }}', 'breakfast')"
                                       {{ isset($selectedBookings[$day['date']->format('Y-m-d')]['breakfast']) ? 'checked' : '' }}
                                       class="rounded text-green-600">
                                <span>Café</span>
                            </label>
                            
                            <!-- Lunch (only if not Friday) -->
                            @if(!$day['date']->isFriday())
                            <label class="flex items-center space-x-1 text-xs">
                                <input type="checkbox" 
                                       wire:click="toggleBooking('{{ $day['date']->format('Y-m-d') }}', 'lunch')"
                                       {{ isset($selectedBookings[$day['date']->format('Y-m-d')]['lunch']) ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span>Almoço</span>
                            </label>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Visual indicator for days with bookings -->
                    @if($day['hasBookings'])
                    <div class="mt-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    </div>
                    @endif
                </div>
                @endforeach
            @endforeach
        </div>

        <!-- Legend -->
        <div class="mt-6 flex flex-wrap gap-4 text-sm text-gray-600">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span>Refeição marcada</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gray-100 border border-gray-300"></div>
                <span>Final de semana</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gray-50 border border-gray-300"></div>
                <span>Fora do mês</span>
            </div>
        </div>
    </div>
</div>
