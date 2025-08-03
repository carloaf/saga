<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Histórico de Reservas - SAGA</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-green-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold flex items-center">
                <img src="{{ asset('images/folhaint_transparent.png') }}" alt="11º D Sup Logo" class="w-6 h-6 mr-2 object-contain">
                SAGA - Histórico de Reservas
            </h1>
            <div class="space-x-4">
                <a href="{{ route('dashboard') }}" class="hover:text-green-200">Dashboard</a>
                <a href="{{ route('bookings.index') }}" class="hover:text-green-200">Reservas</a>
                <a href="{{ route('profile.edit') }}" class="hover:text-green-200">Perfil</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-green-200">Sair</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-8 px-4 pb-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Histórico de Reservas</h2>
                            <p class="text-gray-600 mt-1">Visualize todas as suas reservas passadas e futuras</p>
                        </div>
                        <a href="{{ route('bookings.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                            ← Voltar às Reservas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Monthly Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📊 Estatísticas dos Últimos 6 Meses</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach($monthlyStats as $stat)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-800">{{ $stat['month'] }}</h4>
                                <div class="mt-2 space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total:</span>
                                        <span class="font-medium">{{ $stat['total'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-600">☕ Cafés:</span>
                                        <span class="font-medium">{{ $stat['breakfast'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600">🍽️ Almoços:</span>
                                        <span class="font-medium">{{ $stat['lunch'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Booking History Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Todas as Reservas</h3>
                    
                    @if($allBookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Refeição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dia da Semana</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservado em</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($allBookings as $booking)
                                        @php
                                            $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                            $isPast = $bookingDate->isPast();
                                            $isToday = $bookingDate->isToday();
                                            $deadlineDateTime = $bookingDate->copy()->subDay()->setTime(13, 0, 0);
                                            $canCancel = !$isPast && !$isToday && \Carbon\Carbon::now()->lt($deadlineDateTime);
                                        @endphp
                                        <tr class="{{ $isPast ? 'bg-gray-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $bookingDate->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="flex items-center">
                                                    <span class="w-3 h-3 rounded-full {{ $booking->meal_type === 'breakfast' ? 'bg-green-500' : 'bg-blue-500' }} mr-2"></span>
                                                    {{ $booking->meal_type === 'breakfast' ? 'Café da Manhã' : 'Almoço' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                       ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $bookingDate->locale('pt_BR')->isoFormat('dddd') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($canCancel && $booking->status === 'confirmed')
                                                    <button onclick="cancelBooking('{{ $booking->id }}', this)" 
                                                            class="text-red-600 hover:text-red-900 transition duration-300 p-1"
                                                            title="Cancelar reserva">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @elseif($isPast)
                                                    <span class="text-gray-400">Concluída</span>
                                                @elseif($booking->status === 'cancelled')
                                                    <span class="text-red-400">Cancelada</span>
                                                @else
                                                    <span class="text-gray-400 p-1" title="Não cancelável">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $allBookings->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 mt-2">Nenhuma reserva encontrada</h4>
                            <p class="text-sm text-gray-600">Você ainda não fez nenhuma reserva.</p>
                            <a href="{{ route('bookings.index') }}" class="mt-4 inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                Fazer Primeira Reserva
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <strong>📝 Legenda:</strong>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 text-sm">
                    <div class="flex items-center">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded mr-2 text-xs">Confirmed</span>
                        <span>Reserva confirmada</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded mr-2 text-xs">Cancelled</span>
                        <span>Reserva cancelada</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-green-200 text-green-800 px-2 py-1 rounded mr-2">☕</span>
                        <span>Café da Manhã</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded mr-2">🍽️</span>
                        <span>Almoço</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <span id="toast-message">Mensagem</span>
        </div>
    </div>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastDiv = toast.querySelector('div');
            
            toastMessage.textContent = message;
            
            // Update toast color based on type
            if (type === 'error') {
                toastDiv.className = 'bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg';
            } else {
                toastDiv.className = 'bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg';
            }
            
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000);
        }

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
                    // Reload page to update status
                    setTimeout(() => location.reload(), 2000);
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
    </script>
</body>
</html>
