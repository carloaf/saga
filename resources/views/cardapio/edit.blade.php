<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cardápio da Semana - SAGA</title>
    
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
                SAGA - Editar Cardápio
            </h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm">{{ auth()->user()->war_name ?? auth()->user()->full_name }}</span>
                <a href="{{ route('cardapio.index') }}" class="bg-gray-600 hover:bg-gray-700 px-3 py-1 rounded text-sm">
                    ↩️ Voltar
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
        <!-- Título -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">✏️ Editar Cardápio da Semana</h2>
            
            <!-- Seletor de Semana -->
            <div class="mb-6 max-w-lg mx-auto">
                <label for="week_selector" class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Selecionar Semana para Editar:
                </label>
                <select id="week_selector" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center">
                    @foreach($availableWeeks as $week)
                        <option value="{{ $week['value'] }}" {{ $week['is_current'] ? 'selected' : '' }}>
                            {{ $week['label'] }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">
                    Semana atual: {{ $weekStart->format('d/m/Y') }} - {{ $weekStart->copy()->endOfWeek(\Carbon\Carbon::FRIDAY)->format('d/m/Y') }}
                </p>
            </div>

            <!-- Botão para carregar sugestões -->
            @if($cardapioAnterior)
                <div class="mb-4">
                    <button type="button" id="load_suggestions" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        💡 Carregar Sugestões da Semana Anterior ({{ $previousWeekStart->format('d/m') }} - {{ $previousWeekStart->copy()->endOfWeek(\Carbon\Carbon::FRIDAY)->format('d/m') }})
                    </button>
                </div>
            @endif
            
            @if(now()->dayOfWeek >= \Carbon\Carbon::FRIDAY && $weekStart->isCurrentWeek())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 mx-auto max-w-2xl">
                    <p class="font-medium">📋 Você está editando o cardápio da próxima semana</p>
                    <p class="text-sm">Como hoje é {{ now()->dayName }}, você pode editar o cardápio da semana seguinte.</p>
                </div>
            @endif
        </div>

        <!-- Mensagens de Sucesso/Erro -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 mx-auto max-w-4xl">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 mx-auto max-w-4xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário de Edição -->
        <form method="POST" action="{{ route('cardapio.update') }}" class="max-w-6xl mx-auto" onsubmit="return validateForm()">
            @csrf
            @method('PUT')
            <input type="hidden" id="week_start_input" name="week_start" value="{{ $weekStart->toDateString() }}">

            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <!-- Segunda-feira -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
                    <h3 class="text-xl font-bold text-blue-800 mb-2 flex items-center justify-between">
                        <span>📅 Segunda-feira</span>
                        @if($cardapioAnterior && isset($cardapioAnterior['segunda']))
                            <button type="button" 
                                    class="load-day-suggestion text-xs bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-2 py-1 rounded"
                                    data-day="segunda"
                                    title="Carregar sugestões da semana anterior">
                                💡 Sugestão
                            </button>
                        @endif
                    </h3>
                    <p class="text-sm text-blue-600 mb-4">{{ $weekDates['segunda']->format('d/m/Y') }}</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">☕ Café da Manhã</label>
                            <textarea name="menu[segunda][cafe]" 
                                      data-day="segunda" 
                                      data-meal="cafe"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Ex: Café, Pão Francês, Manteiga, Leite"
                                      required>{{ old('menu.segunda.cafe', $cardapio['segunda']['cafe'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['segunda']['cafe']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['segunda']['cafe'] }}
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🍽️ Almoço</label>
                            <textarea name="menu[segunda][almoco]" 
                                      data-day="segunda" 
                                      data-meal="almoco"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Ex: Arroz, Feijão, Carne Assada, Salada Verde"
                                      required>{{ old('menu.segunda.almoco', $cardapio['segunda']['almoco'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['segunda']['almoco']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['segunda']['almoco'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Terça-feira -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-xl font-bold text-green-800 mb-2 flex items-center justify-between">
                        <span>📅 Terça-feira</span>
                        @if($cardapioAnterior && isset($cardapioAnterior['terca']))
                            <button type="button" 
                                    class="load-day-suggestion text-xs bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-2 py-1 rounded"
                                    data-day="terca"
                                    title="Carregar sugestões da semana anterior">
                                💡 Sugestão
                            </button>
                        @endif
                    </h3>
                    <p class="text-sm text-green-600 mb-4">{{ $weekDates['terca']->format('d/m/Y') }}</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">☕ Café da Manhã</label>
                            <textarea name="menu[terca][cafe]" 
                                      data-day="terca" 
                                      data-meal="cafe"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Ex: Café, Pão de Forma, Requeijão, Leite"
                                      required>{{ old('menu.terca.cafe', $cardapio['terca']['cafe'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['terca']['cafe']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['terca']['cafe'] }}
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🍽️ Almoço</label>
                            <textarea name="menu[terca][almoco]" 
                                      data-day="terca" 
                                      data-meal="almoco"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Ex: Arroz, Feijão, Frango Grelhado, Legumes"
                                      required>{{ old('menu.terca.almoco', $cardapio['terca']['almoco'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['terca']['almoco']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['terca']['almoco'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quarta-feira -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                    <h3 class="text-xl font-bold text-purple-800 mb-2 flex items-center justify-between">
                        <span>📅 Quarta-feira</span>
                        @if($cardapioAnterior && isset($cardapioAnterior['quarta']))
                            <button type="button" 
                                    class="load-day-suggestion text-xs bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-2 py-1 rounded"
                                    data-day="quarta"
                                    title="Carregar sugestões da semana anterior">
                                💡 Sugestão
                            </button>
                        @endif
                    </h3>
                    <p class="text-sm text-purple-600 mb-4">{{ $weekDates['quarta']->format('d/m/Y') }}</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">☕ Café da Manhã</label>
                            <textarea name="menu[quarta][cafe]" 
                                      data-day="quarta" 
                                      data-meal="cafe"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Ex: Café, Pão Francês, Presunto e Queijo, Leite"
                                      required>{{ old('menu.quarta.cafe', $cardapio['quarta']['cafe'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['quarta']['cafe']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['quarta']['cafe'] }}
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🍽️ Almoço</label>
                            <textarea name="menu[quarta][almoco]" 
                                      data-day="quarta" 
                                      data-meal="almoco"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Ex: Arroz, Feijão, Peixe Assado, Salada Mista"
                                      required>{{ old('menu.quarta.almoco', $cardapio['quarta']['almoco'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['quarta']['almoco']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['quarta']['almoco'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quinta-feira -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500">
                    <h3 class="text-xl font-bold text-orange-800 mb-2 flex items-center justify-between">
                        <span>📅 Quinta-feira</span>
                        @if($cardapioAnterior && isset($cardapioAnterior['quinta']))
                            <button type="button" 
                                    class="load-day-suggestion text-xs bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-2 py-1 rounded"
                                    data-day="quinta"
                                    title="Carregar sugestões da semana anterior">
                                💡 Sugestão
                            </button>
                        @endif
                    </h3>
                    <p class="text-sm text-orange-600 mb-4">{{ $weekDates['quinta']->format('d/m/Y') }}</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">☕ Café da Manhã</label>
                            <textarea name="menu[quinta][cafe]" 
                                      data-day="quinta" 
                                      data-meal="cafe"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                      placeholder="Ex: Café, Pão de Forma, Manteiga, Leite"
                                      required>{{ old('menu.quinta.cafe', $cardapio['quinta']['cafe'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['quinta']['cafe']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['quinta']['cafe'] }}
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🍽️ Almoço</label>
                            <textarea name="menu[quinta][almoco]" 
                                      data-day="quinta" 
                                      data-meal="almoco"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                      placeholder="Ex: Arroz, Feijão, Carne de Porco, Purê de Batata"
                                      required>{{ old('menu.quinta.almoco', $cardapio['quinta']['almoco'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['quinta']['almoco']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['quinta']['almoco'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sexta-feira -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500">
                    <h3 class="text-xl font-bold text-red-800 mb-2 flex items-center justify-between">
                        <span>📅 Sexta-feira</span>
                        @if($cardapioAnterior && isset($cardapioAnterior['sexta']))
                            <button type="button" 
                                    class="load-day-suggestion text-xs bg-yellow-400 hover:bg-yellow-500 text-yellow-900 px-2 py-1 rounded"
                                    data-day="sexta"
                                    title="Carregar sugestões da semana anterior">
                                💡 Sugestão
                            </button>
                        @endif
                    </h3>
                    <p class="text-sm text-red-600 mb-4">{{ $weekDates['sexta']->format('d/m/Y') }}</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">☕ Café da Manhã</label>
                            <textarea name="menu[sexta][cafe]" 
                                      data-day="sexta" 
                                      data-meal="cafe"
                                      rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Ex: Café, Pão Francês, Geleia, Leite"
                                      required>{{ old('menu.sexta.cafe', $cardapio['sexta']['cafe'] ?? '') }}</textarea>
                            @if($cardapioAnterior && isset($cardapioAnterior['sexta']['cafe']))
                                <div class="mt-1 text-xs text-gray-500 bg-yellow-50 p-2 rounded">
                                    <strong>💡 Semana anterior:</strong> {{ $cardapioAnterior['sexta']['cafe'] }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-gray-100 rounded-lg p-4 text-center">
                            <p class="text-gray-600 text-sm">
                                🚫 Não há almoço às sextas-feiras
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-8 flex justify-center space-x-4">
                <a href="{{ route('cardapio.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    ❌ Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    💾 Salvar Cardápio
                </button>
            </div>
        </form>

        <!-- Rodapé Informativo -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>💡 Dica: Seja específico nos itens do cardápio para facilitar a logística da cozinha.</p>
        </div>
    </div>

    <!-- JavaScript para funcionalidades interativas -->
    <script>
        // Variáveis globais
        let cardapioAnterior = @json($cardapioAnterior ?? []);
        
        document.addEventListener('DOMContentLoaded', function() {
            // Seletor de semana
            const weekSelector = document.getElementById('week_selector');
            
            weekSelector.addEventListener('change', function() {
                const selectedWeek = this.value;
                
                // Atualizar campo hidden do formulário
                const weekStartInput = document.getElementById('week_start_input');
                if (weekStartInput) {
                    weekStartInput.value = selectedWeek;
                }
                
                // Redirecionar para carregar nova semana
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('week_start', selectedWeek);
                window.location.href = currentUrl.toString();
            });

            // Botão para carregar todas as sugestões
            const loadSuggestionsBtn = document.getElementById('load_suggestions');
            if (loadSuggestionsBtn) {
                loadSuggestionsBtn.addEventListener('click', function() {
                    if (confirm('Deseja carregar as sugestões da semana anterior para todos os dias? Isso irá sobrescrever o conteúdo atual.')) {
                        loadAllSuggestions();
                    }
                });
            }

            // Botões para carregar sugestões por dia
            const loadDayBtns = document.querySelectorAll('.load-day-suggestion');
            loadDayBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const day = this.getAttribute('data-day');
                    if (confirm(`Deseja carregar as sugestões da semana anterior para ${day}-feira? Isso irá sobrescrever o conteúdo atual.`)) {
                        loadDaySuggestion(day);
                    }
                });
            });
        });

        // Função para carregar todas as sugestões
        function loadAllSuggestions() {
            if (!cardapioAnterior) {
                alert('Não há sugestões disponíveis da semana anterior.');
                return;
            }

            const days = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];
            
            days.forEach(day => {
                if (cardapioAnterior[day]) {
                    // Café da manhã
                    if (cardapioAnterior[day].cafe) {
                        const cafeTextarea = document.querySelector(`textarea[name="menu[${day}][cafe]"]`);
                        if (cafeTextarea) {
                            cafeTextarea.value = cardapioAnterior[day].cafe;
                            // Animação visual
                            cafeTextarea.style.backgroundColor = '#fef3c7';
                            setTimeout(() => {
                                cafeTextarea.style.backgroundColor = '';
                            }, 1000);
                        }
                    }
                    
                    // Almoço (exceto sexta-feira)
                    if (day !== 'sexta' && cardapioAnterior[day].almoco) {
                        const almocoTextarea = document.querySelector(`textarea[name="menu[${day}][almoco]"]`);
                        if (almocoTextarea) {
                            almocoTextarea.value = cardapioAnterior[day].almoco;
                            // Animação visual
                            almocoTextarea.style.backgroundColor = '#fef3c7';
                            setTimeout(() => {
                                almocoTextarea.style.backgroundColor = '';
                            }, 1000);
                        }
                    }
                }
            });

            // Mostrar mensagem de sucesso
            showNotification('✅ Sugestões carregadas com sucesso para todos os dias!', 'success');
        }

        // Função para carregar sugestão de um dia específico
        function loadDaySuggestion(day) {
            if (!cardapioAnterior || !cardapioAnterior[day]) {
                alert(`Não há sugestões disponíveis para ${day}-feira.`);
                return;
            }

            // Café da manhã
            if (cardapioAnterior[day].cafe) {
                const cafeTextarea = document.querySelector(`textarea[name="menu[${day}][cafe]"]`);
                if (cafeTextarea) {
                    cafeTextarea.value = cardapioAnterior[day].cafe;
                    cafeTextarea.style.backgroundColor = '#fef3c7';
                    setTimeout(() => {
                        cafeTextarea.style.backgroundColor = '';
                    }, 1000);
                }
            }
            
            // Almoço (exceto sexta-feira)
            if (day !== 'sexta' && cardapioAnterior[day].almoco) {
                const almocoTextarea = document.querySelector(`textarea[name="menu[${day}][almoco]"]`);
                if (almocoTextarea) {
                    almocoTextarea.value = cardapioAnterior[day].almoco;
                    almocoTextarea.style.backgroundColor = '#fef3c7';
                    setTimeout(() => {
                        almocoTextarea.style.backgroundColor = '';
                    }, 1000);
                }
            }

            // Mostrar mensagem de sucesso
            showNotification(`✅ Sugestões carregadas para ${day}-feira!`, 'success');
        }

        // Função para mostrar notificações
        function showNotification(message, type = 'info') {
            // Criar elemento de notificação
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            // Adicionar ao DOM
            document.body.appendChild(notification);
            
            // Remover após 3 segundos
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Função para validar formulário antes do envio
        function validateForm() {
            const requiredFields = document.querySelectorAll('textarea[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                showNotification('❌ Preencha todos os campos obrigatórios!', 'error');
                return false;
            }
            
            return true;
        }
    </script>

    <!-- Estilos para impressão -->
    <style>
        @media print {
            header, .no-print, button, input[type="submit"] {
                display: none !important;
            }
            
            body {
                background: white !important;
            }
            
            .container {
                margin: 0 !important;
                padding: 20px !important;
            }
        }

        /* Animações */
        .load-day-suggestion {
            transition: all 0.2s ease;
        }
        
        .load-day-suggestion:hover {
            transform: scale(1.05);
        }
        
        textarea {
            transition: background-color 0.3s ease;
        }
    </style>

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
