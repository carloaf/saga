<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio da Semana - SAGA</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-green-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold flex items-center">
                <img src="{{ asset('images/folhaint_transparent.png') }}" alt="11º D Sup Logo" class="w-6 h-6 mr-2 object-contain">
                SAGA - Cardápio da Semana
            </h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm">{{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}" class="bg-green-700 hover:bg-green-600 px-3 py-1 rounded text-sm">
                    🏠 Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                        🚪 Sair
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="container mx-auto py-8 px-4">
        <!-- Título e Botões -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">📅 Cardápio da Semana</h2>
            <p class="text-gray-600 mb-4">
                Semana de {{ $weekStart->format('d/m/Y') }} - {{ $weekStart->copy()->endOfWeek(\Carbon\Carbon::FRIDAY)->format('d/m/Y') }}
            </p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('cardapio.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    ✏️ Editar Cardápio
                </a>
                <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    🖨️ Imprimir
                </button>
            </div>
        </div>

        <!-- Cards do Cardápio -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            @foreach($cardapio as $dia => $refeicoes)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Header do Dia -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-4">
                    <h3 class="text-lg font-bold text-center capitalize">
                        {{ ucfirst($dia) }}-feira
                    </h3>
                    <p class="text-center text-green-200 text-sm mt-1">
                        {{ $weekDates[$dia]->format('d/m') }}
                    </p>
                </div>

                <!-- Conteúdo -->
                <div class="p-4 space-y-4">
                    <!-- Café da Manhã -->
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                            ☕ Café da Manhã
                        </h4>
                        <p class="text-sm text-gray-600 bg-yellow-50 p-2 rounded">
                            {{ $refeicoes['cafe'] }}
                        </p>
                    </div>

                    <!-- Almoço -->
                    @if(isset($refeicoes['almoco']))
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                            🍽️ Almoço
                        </h4>
                        <p class="text-sm text-gray-600 bg-green-50 p-2 rounded">
                            {{ $refeicoes['almoco'] }}
                        </p>
                    </div>
                    @else
                    <div>
                        <h4 class="font-bold text-gray-400 mb-2 flex items-center">
                            🍽️ Almoço
                        </h4>
                        <p class="text-sm text-gray-400 bg-gray-50 p-2 rounded italic">
                            Não disponível às sextas-feiras
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Observações -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informações Importantes</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Este cardápio é referente à semana atual</li>
                            <li>Alterações podem ocorrer conforme disponibilidade de ingredientes</li>
                            <li>Sextas-feiras: apenas café da manhã disponível</li>
                            <li>Para dúvidas, entre em contato com a administração</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
            <button onclick="window.print()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Imprimir Cardápio</span>
            </button>
            
            <a href="{{ route('dashboard') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Voltar ao Dashboard</span>
            </a>
        </div>
    </div>

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
