@php
function getRankSymbol($rankName) {
    $rank = strtolower($rankName);
    
    // Subtenente - Losango
    if (str_contains($rank, 'subtenente')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L6 12l6 10 6-10L12 2z"/>
        </svg>';
    }
    
    // Segundo Tenente - 1 Estrela
    else if (str_contains($rank, '2º tenente') || str_contains($rank, 'segundo tenente')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>';
    }
    
    // Primeiro Tenente - 2 Estrelas
    else if (str_contains($rank, '1º tenente') || str_contains($rank, 'primeiro tenente')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <g transform="scale(0.8)">
                <path d="M8 2l2.32 4.7L16 7.9l-3.75 3.65.88 5.17L8 14.34l-5.13 2.38.88-5.17L0 7.9l5.68-.8L8 2z"/>
                <path d="M16 2l2.32 4.7L24 7.9l-3.75 3.65.88 5.17L16 14.34l-5.13 2.38.88-5.17L8 7.9l5.68-.8L16 2z"/>
            </g>
        </svg>';
    }
    
    // Capitão - 3 Estrelas
    else if (str_contains($rank, 'capitão')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <g transform="scale(0.6)">
                <path d="M6 2l2.32 4.7L14 7.9l-3.75 3.65.88 5.17L6 14.34l-5.13 2.38.88-5.17L-2 7.9l5.68-.8L6 2z"/>
                <path d="M20 2l2.32 4.7L28 7.9l-3.75 3.65.88 5.17L20 14.34l-5.13 2.38.88-5.17L12 7.9l5.68-.8L20 2z"/>
                <path d="M34 2l2.32 4.7L42 7.9l-3.75 3.65.88 5.17L34 14.34l-5.13 2.38.88-5.17L26 7.9l5.68-.8L34 2z"/>
            </g>
        </svg>';
    }
    
    // Major - 1 Estrela + Losango
    else if (str_contains($rank, 'major')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            <path d="M12 18L9 21l3 3 3-3-3-3z" opacity="0.7"/>
        </svg>';
    }
    
    // Tenente Coronel - 2 Estrelas + Losango
    else if (str_contains($rank, 'tenente coronel')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <g transform="scale(0.8)">
                <path d="M8 2l2.32 4.7L16 7.9l-3.75 3.65.88 5.17L8 14.34l-5.13 2.38.88-5.17L0 7.9l5.68-.8L8 2z"/>
                <path d="M16 2l2.32 4.7L24 7.9l-3.75 3.65.88 5.17L16 14.34l-5.13 2.38.88-5.17L8 7.9l5.68-.8L16 2z"/>
                <path d="M12 18L9 21l3 3 3-3-3-3z" opacity="0.7"/>
            </g>
        </svg>';
    }
    
    // Coronel - 3 Estrelas + Losango
    else if (str_contains($rank, 'coronel') && !str_contains($rank, 'tenente')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <g transform="scale(0.7)">
                <path d="M6 2l2.32 4.7L14 7.9l-3.75 3.65.88 5.17L6 14.34l-5.13 2.38.88-5.17L-2 7.9l5.68-.8L6 2z"/>
                <path d="M20 2l2.32 4.7L28 7.9l-3.75 3.65.88 5.17L20 14.34l-5.13 2.38.88-5.17L12 7.9l5.68-.8L20 2z"/>
                <path d="M34 2l2.32 4.7L42 7.9l-3.75 3.65.88 5.17L34 14.34l-5.13 2.38.88-5.17L26 7.9l5.68-.8L34 2z"/>
                <path d="M20 20L17 23l3 3 3-3-3-3z" opacity="0.7"/>
            </g>
        </svg>';
    }
    
    // Generais
    else if (str_contains($rank, 'general')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <g transform="scale(0.6)">
                <path d="M6 2l2.32 4.7L14 7.9l-3.75 3.65.88 5.17L6 14.34l-5.13 2.38.88-5.17L-2 7.9l5.68-.8L6 2z"/>
                <path d="M20 2l2.32 4.7L28 7.9l-3.75 3.65.88 5.17L20 14.34l-5.13 2.38.88-5.17L12 7.9l5.68-.8L20 2z"/>
                <path d="M34 2l2.32 4.7L42 7.9l-3.75 3.65.88 5.17L34 14.34l-5.13 2.38.88-5.17L26 7.9l5.68-.8L34 2z"/>
                <path d="M48 2l2.32 4.7L56 7.9l-3.75 3.65.88 5.17L48 14.34l-5.13 2.38.88-5.17L40 7.9l5.68-.8L48 2z"/>
                <path d="M27 20L24 23l3 3 3-3-3-3z" opacity="0.7"/>
            </g>
        </svg>';
    }
    
    // 3º Sargento - 1 Chevron
    else if (str_contains($rank, '3º sargento') || str_contains($rank, 'terceiro sargento')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 8L8 16h8L12 8z"/>
        </svg>';
    }
    
    // 2º Sargento - 2 Chevrons
    else if (str_contains($rank, '2º sargento') || str_contains($rank, 'segundo sargento')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 6L8 12h8L12 6z"/>
            <path d="M12 12L8 18h8L12 12z"/>
        </svg>';
    }
    
    // 1º Sargento - 3 Chevrons
    else if (str_contains($rank, '1º sargento') || str_contains($rank, 'primeiro sargento') || str_contains($rank, 'sargento')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 4L8 8h8L12 4z"/>
            <path d="M12 8L8 12h8L12 8z"/>
            <path d="M12 12L8 16h8L12 12z"/>
        </svg>';
    }
    
    // Cabo - 2 Chevrons menores
    else if (str_contains($rank, 'cabo')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 6L9 12h6L12 6z"/>
            <path d="M12 12L9 18h6L12 12z"/>
        </svg>';
    }
    
    // Soldado - 1 Chevron pequeno
    else if (str_contains($rank, 'soldado')) {
        return '<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 8L10 14h4L12 8z"/>
        </svg>';
    }
    
    // Símbolo genérico militar: Escudo
    else {
        return '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>';
    }
}
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meu Perfil - SAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for enhanced form elements */
        .form-group {
            transition: all 0.3s ease;
        }
        
        .form-group:hover {
            transform: translateY(-2px);
        }
        
        .form-input {
            transition: all 0.2s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        }
        
        .form-input:focus {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        
        .form-select {
            background-image: none;
            transition: all 0.2s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        }
        
        .form-select:focus {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        
        .form-select:hover {
            background: linear-gradient(145deg, #f8fafc 0%, #ffffff 100%);
        }
        
        .gradient-border {
            background: linear-gradient(145deg, #e5e7eb 0%, #d1d5db 100%);
            padding: 2px;
            border-radius: 0.75rem;
        }
        
        .gradient-border:focus-within {
            background: linear-gradient(145deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .card-enhanced {
            background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .icon-bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -8px, 0);
            }
            70% {
                transform: translate3d(0, -4px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }
        
        .label-enhanced {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    👤 Meu Perfil
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
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Profile Info -->
            <div class="card-enhanced overflow-hidden shadow-2xl sm:rounded-2xl mb-8 border border-gray-100 relative">
                <!-- Background gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-100 opacity-60"></div>
                
                <div class="relative p-8">
                    <div class="flex items-center space-x-8">
                        <div class="flex-shrink-0 relative">
                            <!-- Avatar with gradient border and glow effect -->
                            <div class="relative">
                                <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse"></div>
                                <div class="relative h-24 w-24 rounded-full bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 flex items-center justify-center shadow-2xl ring-4 ring-white">
                                    <span class="text-3xl font-bold bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 bg-clip-text text-transparent">
                                        {{ strtoupper(substr(auth()->user()->war_name ?? auth()->user()->full_name, 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Online status indicator -->
                            <div class="absolute bottom-1 right-1 w-6 h-6 bg-green-400 border-2 border-white rounded-full shadow-lg">
                                <div class="w-full h-full bg-green-500 rounded-full animate-ping"></div>
                            </div>
                        </div>
                        
                        <div class="flex-1 space-y-3">
                            <!-- Name with gradient text -->
                            <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent">
                                {{ auth()->user()->full_name }}
                            </h2>
                            
                            <!-- War name with icon -->
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <p class="text-xl font-semibold text-gray-700">{{ auth()->user()->war_name }}</p>
                            </div>
                            
                            <!-- Email with icon -->
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                <p class="text-sm text-gray-600 font-medium">{{ auth()->user()->email }}</p>
                            </div>
                            
                            <!-- Role badge with enhanced styling -->
                            <div class="mt-4">
                                @if(auth()->user()->role === 'superuser')
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        Superusuário
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Usuário Padrão
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Side decorative elements -->
                        <div class="hidden lg:flex flex-col space-y-4">
                            <div id="rank-symbol" class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center shadow-lg transform rotate-6 hover:rotate-12 transition-transform duration-300">
                                @if(auth()->user()->rank)
                                    @php
                                        $rankName = strtolower(auth()->user()->rank->name);
                                    @endphp
                                    {!! getRankSymbol($rankName) !!}
                                @else
                                    <!-- Fallback para usuário sem posto definido -->
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center shadow-lg transform -rotate-3 hover:-rotate-6 transition-transform duration-300">
                                <!-- Símbolo da organização militar -->
                                @if(auth()->user()->organization && str_contains(strtolower(auth()->user()->organization->name), 'depósito'))
                                    <!-- Símbolo de Depósito/Suprimento: Caixas empilhadas -->
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @else
                                    <!-- Símbolo genérico de organização militar -->
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bottom info bar -->
                    <div class="mt-6 pt-6 border-t border-gray-200 bg-white bg-opacity-50 rounded-xl p-4">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2 text-gray-600">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Conta Ativa</span>
                                </div>
                                <div class="flex items-center space-x-2 text-gray-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span class="font-medium">Dados Protegidos</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-medium">Última atualização: hoje</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="card-enhanced overflow-hidden shadow-xl sm:rounded-xl mb-6 border border-gray-100">
                <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center shadow-lg icon-bounce">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                Informações Pessoais
                            </h3>
                            <p class="text-sm text-gray-600 font-medium">Mantenha seus dados sempre atualizados para um melhor atendimento</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-8 bg-gradient-to-br from-white to-gray-50">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-8">
                        @csrf
                        @method('patch')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Nome Completo -->
                            <div class="form-group">
                                <label for="full_name" class="flex items-center text-sm font-semibold text-gray-700 mb-3 label-enhanced">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Nome Completo
                                </label>
                                <div class="gradient-border">
                                    <input type="text" name="full_name" id="full_name" 
                                           value="{{ auth()->user()->full_name }}"
                                           class="form-input block w-full px-4 py-4 border-0 rounded-lg shadow-sm text-gray-900 placeholder-gray-500 font-medium"
                                           placeholder="Digite seu nome completo">
                                </div>
                            </div>
                            
                            <!-- Nome de Guerra -->
                            <div class="form-group">
                                <label for="war_name" class="flex items-center text-sm font-semibold text-gray-700 mb-3 label-enhanced">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Nome de Guerra
                                </label>
                                <div class="gradient-border">
                                    <input type="text" name="war_name" id="war_name" 
                                           value="{{ auth()->user()->war_name }}"
                                           class="form-input block w-full px-4 py-4 border-0 rounded-lg shadow-sm text-gray-900 placeholder-gray-500 font-medium"
                                           placeholder="Digite seu nome de guerra">
                                </div>
                            </div>
                            
                            <!-- E-mail -->
                            <div class="form-group lg:col-span-2">
                                <label for="email" class="flex items-center text-sm font-semibold text-gray-700 mb-3 label-enhanced">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                    E-mail Institucional
                                </label>
                                <div class="relative">
                                    <input type="email" name="email" id="email" 
                                           value="{{ auth()->user()->email }}" readonly
                                           class="block w-full px-4 py-4 border border-gray-300 rounded-lg shadow-sm bg-gradient-to-r from-gray-50 to-gray-100 text-gray-600 cursor-not-allowed font-medium">
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center bg-yellow-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    E-mail vinculado ao Google OAuth não pode ser alterado
                                </p>
                            </div>
                            
                            <!-- Posto/Graduação -->
                            <div class="form-group">
                                <label for="rank_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3 label-enhanced">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                    Posto/Graduação Militar
                                </label>
                                <div class="gradient-border">
                                    <div class="relative">
                                        <select name="rank_id" id="rank_id" 
                                                class="form-select block w-full px-4 py-4 border-0 rounded-lg shadow-sm appearance-none cursor-pointer font-medium text-gray-900">
                                            @foreach($ranks as $rank)
                                                <option value="{{ $rank->id }}" {{ auth()->user()->rank_id == $rank->id ? 'selected' : '' }}>
                                                    {{ $rank->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Organização -->
                            <div class="form-group">
                                <label for="organization_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3 label-enhanced">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Organização Militar
                                </label>
                                <div class="gradient-border">
                                    <div class="relative">
                                        <select name="organization_id" id="organization_id" 
                                                class="form-select block w-full px-4 py-4 border-0 rounded-lg shadow-sm appearance-none cursor-pointer font-medium text-gray-900">
                                            @foreach($organizations as $organization)
                                                @php
                                                    $isSelected = false;
                                                    if (auth()->user()->organization_id) {
                                                        // Se o usuário já tem uma organização, selecionar ela
                                                        $isSelected = auth()->user()->organization_id == $organization->id;
                                                    } else {
                                                        // Se não tem organização, selecionar "11º Depósito de Suprimento" como padrão
                                                        $isSelected = $organization->name === '11º Depósito de Suprimento';
                                                    }
                                                @endphp
                                                <option value="{{ $organization->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $organization->name }}
                                                    @if($organization->name === '11º Depósito de Suprimento')
                                                        ⭐
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center bg-blue-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                    </svg>
                                    <span class="font-medium">11º Depósito de Suprimento</span> é a organização padrão do sistema
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-500 bg-green-50 px-4 py-2 rounded-lg">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">Dados protegidos e salvos com segurança</span>
                            </div>
                            <button type="submit" class="inline-flex items-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:from-blue-600 hover:via-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:scale-105 shadow-xl hover:shadow-2xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Salvar Alterações
                            </button>
                        </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Booking Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Minhas Estatísticas</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['breakfast_this_month'] }}</div>
                            <div class="text-sm text-gray-600">Cafés este mês</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['lunch_this_month'] }}</div>
                            <div class="text-sm text-gray-600">Almoços este mês</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_this_month'] }}</div>
                            <div class="text-sm text-gray-600">Total este mês</div>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">{{ $stats['total_all_time'] }}</div>
                            <div class="text-sm text-gray-600">Total geral</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações da Conta</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Notificações por Email</h4>
                                <p class="text-sm text-gray-500">Receber confirmações de reservas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Lembretes Automáticos</h4>
                                <p class="text-sm text-gray-500">Lembrar das refeições agendadas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="reminder_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Relatórios Semanais</h4>
                                <p class="text-sm text-gray-500">Resumo semanal das suas reservas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="weekly_reports" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Atividade Recente</h3>
                    
                    <div class="space-y-3">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-center space-x-3 p-3 bg-{{ $activity['color'] }}-50 rounded-lg">
                                <div class="w-2 h-2 bg-{{ $activity['color'] }}-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['date']->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500">Nenhuma atividade recente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <span id="toast-message"></span>
        </div>
    </div>

    <script>
        // Show success message if it exists
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        // Função para obter o símbolo SVG baseado no posto/graduação
        function getRankSymbolSVG(rankName) {
            const rank = rankName.toLowerCase();
            
            // Subtenente - Losango
            if (rank.includes('subtenente')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L6 12l6 10 6-10L12 2z"/>
                </svg>`;
            }
            
            // Segundo Tenente - 1 Estrela
            else if (rank.includes('2º tenente') || rank.includes('segundo tenente')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>`;
            }
            
            // Primeiro Tenente - 2 Estrelas
            else if (rank.includes('1º tenente') || rank.includes('primeiro tenente')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 2l2.32 4.7L16 7.9l-3.75 3.65.88 5.17L8 14.34l-5.13 2.38.88-5.17L0 7.9l5.68-.8L8 2z"/>
                    <path d="M16 2l2.32 4.7L24 7.9l-3.75 3.65.88 5.17L16 14.34l-5.13 2.38.88-5.17L8 7.9l5.68-.8L16 2z"/>
                </svg>`;
            }
            
            // Capitão - 3 Estrelas
            else if (rank.includes('capitão')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <g transform="scale(0.6)">
                        <path d="M6 2l2.32 4.7L14 7.9l-3.75 3.65.88 5.17L6 14.34l-5.13 2.38.88-5.17L-2 7.9l5.68-.8L6 2z"/>
                        <path d="M20 2l2.32 4.7L28 7.9l-3.75 3.65.88 5.17L20 14.34l-5.13 2.38.88-5.17L12 7.9l5.68-.8L20 2z"/>
                        <path d="M34 2l2.32 4.7L42 7.9l-3.75 3.65.88 5.17L34 14.34l-5.13 2.38.88-5.17L26 7.9l5.68-.8L34 2z"/>
                    </g>
                </svg>`;
            }
            
            // Major - 1 Estrela + Losango
            else if (rank.includes('major')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    <path d="M12 18L9 21l3 3 3-3-3-3z" opacity="0.7"/>
                </svg>`;
            }
            
            // Tenente Coronel - 2 Estrelas + Losango
            else if (rank.includes('tenente coronel')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <g transform="scale(0.8)">
                        <path d="M8 2l2.32 4.7L16 7.9l-3.75 3.65.88 5.17L8 14.34l-5.13 2.38.88-5.17L0 7.9l5.68-.8L8 2z"/>
                        <path d="M16 2l2.32 4.7L24 7.9l-3.75 3.65.88 5.17L16 14.34l-5.13 2.38.88-5.17L8 7.9l5.68-.8L16 2z"/>
                        <path d="M12 18L9 21l3 3 3-3-3-3z" opacity="0.7"/>
                    </g>
                </svg>`;
            }
            
            // Coronel - 3 Estrelas + Losango
            else if (rank.includes('coronel') && !rank.includes('tenente')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <g transform="scale(0.7)">
                        <path d="M6 2l2.32 4.7L14 7.9l-3.75 3.65.88 5.17L6 14.34l-5.13 2.38.88-5.17L-2 7.9l5.68-.8L6 2z"/>
                        <path d="M20 2l2.32 4.7L28 7.9l-3.75 3.65.88 5.17L20 14.34l-5.13 2.38.88-5.17L12 7.9l5.68-.8L20 2z"/>
                        <path d="M34 2l2.32 4.7L42 7.9l-3.75 3.65.88 5.17L34 14.34l-5.13 2.38.88-5.17L26 7.9l5.68-.8L34 2z"/>
                        <path d="M20 20L17 23l3 3 3-3-3-3z" opacity="0.7"/>
                    </g>
                </svg>`;
            }
            
            // Generais
            else if (rank.includes('general')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <g transform="scale(0.6)">
                        <path d="M6 2l2.32 4.7L14 7.9l-3.75 3.65.88 5.17L6 14.34l-5.13 2.38.88-5.17L-2 7.9l5.68-.8L6 2z"/>
                        <path d="M20 2l2.32 4.7L28 7.9l-3.75 3.65.88 5.17L20 14.34l-5.13 2.38.88-5.17L12 7.9l5.68-.8L20 2z"/>
                        <path d="M34 2l2.32 4.7L42 7.9l-3.75 3.65.88 5.17L34 14.34l-5.13 2.38.88-5.17L26 7.9l5.68-.8L34 2z"/>
                        <path d="M48 2l2.32 4.7L56 7.9l-3.75 3.65.88 5.17L48 14.34l-5.13 2.38.88-5.17L40 7.9l5.68-.8L48 2z"/>
                        <path d="M27 20L24 23l3 3 3-3-3-3z" opacity="0.7"/>
                    </g>
                </svg>`;
            }
            
            // 3º Sargento - 1 Chevron
            else if (rank.includes('3º sargento') || rank.includes('terceiro sargento')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8L8 16h8L12 8z"/>
                </svg>`;
            }
            
            // 2º Sargento - 2 Chevrons
            else if (rank.includes('2º sargento') || rank.includes('segundo sargento')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6L8 12h8L12 6z"/>
                    <path d="M12 12L8 18h8L12 12z"/>
                </svg>`;
            }
            
            // 1º Sargento - 3 Chevrons
            else if (rank.includes('1º sargento') || rank.includes('primeiro sargento') || rank.includes('sargento')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4L8 8h8L12 4z"/>
                    <path d="M12 8L8 12h8L12 8z"/>
                    <path d="M12 12L8 16h8L12 12z"/>
                </svg>`;
            }
            
            // Cabo - 2 Chevrons menores
            else if (rank.includes('cabo')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6L9 12h6L12 6z"/>
                    <path d="M12 12L9 18h6L12 12z"/>
                </svg>`;
            }
            
            // Soldado - 1 Chevron pequeno
            else if (rank.includes('soldado')) {
                return `<svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8L10 14h4L12 8z"/>
                </svg>`;
            }
            
            // Símbolo genérico militar: Escudo
            else {
                return `<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>`;
            }
        }

        // Listener para mudança no combo de posto/graduação
        document.getElementById('rank_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const rankName = selectedOption.text;
            const rankSymbolElement = document.getElementById('rank-symbol');
            
            // Atualiza o símbolo com animação
            rankSymbolElement.style.transform = 'scale(0.8) rotate(6deg)';
            rankSymbolElement.style.opacity = '0.7';
            
            setTimeout(() => {
                rankSymbolElement.innerHTML = getRankSymbolSVG(rankName);
                rankSymbolElement.style.transform = 'scale(1) rotate(6deg)';
                rankSymbolElement.style.opacity = '1';
            }, 150);
        });

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

        // Handle preference changes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const preferences = {
                    email_notifications: document.querySelector('input[name="email_notifications"]')?.checked || false,
                    reminder_notifications: document.querySelector('input[name="reminder_notifications"]')?.checked || false,
                    weekly_reports: document.querySelector('input[name="weekly_reports"]')?.checked || false,
                };

                fetch('{{ route("profile.preferences") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(preferences)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast('Erro ao salvar preferências', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Erro ao salvar preferências', 'error');
                });
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fullName = document.getElementById('full_name').value.trim();
            const warName = document.getElementById('war_name').value.trim();
            
            if (!fullName || !warName) {
                e.preventDefault();
                showToast('Nome completo e nome de guerra são obrigatórios', 'error');
                return false;
            }
            
            if (fullName.length < 3) {
                e.preventDefault();
                showToast('Nome completo deve ter pelo menos 3 caracteres', 'error');
                return false;
            }
            
            if (warName.length < 2) {
                e.preventDefault();
                showToast('Nome de guerra deve ter pelo menos 2 caracteres', 'error');
                return false;
            }
        });
    </script>
</body>
</html>
