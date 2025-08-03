<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SAGA</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .header-enhanced {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            position: relative;
            overflow: hidden;
        }
        
        .header-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="90" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.1;
        }
        
        .logo-enhanced {
            animation: logoGlow 3s ease-in-out infinite alternate;
        }
        
        @keyframes logoGlow {
            from {
                filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
            }
            to {
                filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.8));
            }
        }
        
        .nav-button {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .nav-button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .status-indicator {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Enhanced Header -->
    <header class="header-enhanced shadow-2xl">
        <div class="relative max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo and Title Section -->
                <div class="flex items-center space-x-4">
                    <div class="logo-enhanced">
                        <img src="{{ asset('images/folhaint_transparent.png') }}" alt="11º D Sup Logo" class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white flex items-center">
                            <span class="bg-gradient-to-r from-yellow-200 to-yellow-100 bg-clip-text text-transparent">
                                SAGA
                            </span>
                        </h1>
                        <p class="text-green-100 text-sm font-medium">Sistema de Agendamento e Gestão de Arranchamento</p>
                    </div>
                </div>

                <!-- User Info and Navigation -->
                <div class="flex items-center space-x-6">
                    <!-- System Status -->
                    <div class="hidden md:flex items-center space-x-2 text-green-200">
                        <div class="w-3 h-3 bg-green-400 rounded-full status-indicator"></div>
                        <span class="text-sm font-medium">Sistema Online</span>
                    </div>

                    <!-- User Welcome -->
                    <div class="user-info rounded-xl px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-white to-blue-100 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-sm font-bold text-blue-800">
                                    {{ strtoupper(substr(auth()->user()->war_name ?? auth()->user()->full_name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="hidden lg:block">
                                <p class="text-white font-semibold text-sm">
                                    {{ auth()->user()->war_name ?? auth()->user()->full_name }}
                                </p>
                                <p class="text-green-200 text-xs">
                                    {{ auth()->user()->rank->name ?? 'Posto não definido' }}
                                    @if(auth()->user()->role === 'superuser')
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            Admin
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.edit') }}" class="nav-button rounded-lg px-4 py-2 text-white font-medium text-sm hover:text-green-200 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Perfil
                        </a>
                        
                        @if(auth()->user()->role === 'superuser')
                        <a href="{{ route('admin.users.index') }}" class="nav-button rounded-lg px-4 py-2 text-white font-medium text-sm hover:text-green-200 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Admin
                        </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Bar -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-lg p-4 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Hoje</p>
                            <p class="text-white text-2xl font-bold">{{ $todayStats['total'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-green-400 bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-lg p-4 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Esta Semana</p>
                            <p class="text-white text-2xl font-bold">{{ $weekStats['total'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-400 bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-lg p-4 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Este Mês</p>
                            <p class="text-white text-2xl font-bold">{{ $monthStats['total'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-400 bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-lg p-4 border border-white border-opacity-20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Usuários Online</p>
                            <p class="text-white text-2xl font-bold">{{ $onlineUsers ?? 1 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-orange-400 bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="space-y-6">
                <!-- Enhanced Navigation Menu -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-blue-800 bg-clip-text text-transparent">
                                    Central de Comando
                                </h3>
                                <p class="text-sm text-gray-600 font-medium">Acesse todas as funcionalidades do sistema</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Reservas de Arranchamento -->
                            <a href="{{ route('bookings.index') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-2xl p-6 border border-blue-200 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center group-hover:bg-blue-300 transition-colors">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-lg font-bold text-blue-900 mb-2">Reservas de Arranchamento</h4>
                                <p class="text-sm text-blue-700">Gerencie suas reservas de café da manhã e almoço</p>
                            </a>

                            <!-- Perfil -->
                            <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-2xl p-6 border border-green-200 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="w-6 h-6 bg-green-200 rounded-full flex items-center justify-center group-hover:bg-green-300 transition-colors">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-lg font-bold text-green-900 mb-2">Meu Perfil</h4>
                                <p class="text-sm text-green-700">Atualize suas informações pessoais e militares</p>
                            </a>

                            @if(auth()->user()->role === 'superuser')
                            <!-- Gestão de Usuários -->
                            <a href="{{ route('admin.users.index') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-2xl p-6 border border-purple-200 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="w-6 h-6 bg-purple-200 rounded-full flex items-center justify-center group-hover:bg-purple-300 transition-colors">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-lg font-bold text-purple-900 mb-2">Gestão de Usuários</h4>
                                <p class="text-sm text-purple-700">Administre usuários e permissões do sistema</p>
                            </a>

                            <!-- Relatórios Avançados -->
                            <a href="{{ route('admin.reports.index') }}" class="group bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 rounded-2xl p-6 border border-orange-200 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="w-6 h-6 bg-orange-200 rounded-full flex items-center justify-center group-hover:bg-orange-300 transition-colors">
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-lg font-bold text-orange-900 mb-2">Relatórios Avançados</h4>
                                <p class="text-sm text-orange-700">Analise dados e exporte relatórios detalhados</p>
                            </a>
                            @else
                            <!-- Para usuários normais - cards adicionais ou espaço -->
                            <div class="group bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200 opacity-50">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-lg font-bold text-gray-600 mb-2">Acesso Restrito</h4>
                                <p class="text-sm text-gray-500">Funcionalidades administrativas disponíveis apenas para superusuários</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 auto-rows-fr">
                    <!-- Daily Bookings Chart -->
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 relative flex flex-col h-full">
                        <!-- Header with gradient -->
                        <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-green-800 bg-clip-text text-transparent">
                                            Arranchados por Dia
                                        </h3>
                                        <p class="text-sm text-gray-600 font-medium">Distribuição diária das refeições</p>
                                    </div>
                                </div>
                                
                                <!-- Action buttons -->
                                <div class="flex items-center space-x-2">
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-green-200 hover:border-green-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-green-200 hover:border-green-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart content with enhanced styling -->
                        <div class="p-6 bg-gradient-to-br from-white to-gray-50">
                            <!-- Quick stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-green-50 rounded-xl border border-green-100">
                                    <div class="text-lg font-bold text-green-600">{{ $chartStats['today'] ?? 0 }}</div>
                                    <div class="text-xs text-green-500 font-medium">Hoje</div>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="text-lg font-bold text-blue-600">{{ $chartStats['week_avg'] ?? 0 }}</div>
                                    <div class="text-xs text-blue-500 font-medium">Média Semanal</div>
                                </div>
                            </div>
                            
                            <!-- Chart container with border -->
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl opacity-30"></div>
                                <div class="relative h-56 p-4 bg-white bg-opacity-80 rounded-xl border border-gray-200 shadow-inner">
                                    <canvas id="dailyBookingsChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Legend/Info -->
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-gray-600 font-medium">Café da Manhã</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-gray-600 font-medium">Almoço</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    Últimos 7 dias
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Origin Breakdown Chart -->
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 relative flex flex-col h-full">
                        <!-- Header with gradient -->
                        <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-blue-800 bg-clip-text text-transparent">
                                            Origem dos Arranchados
                                        </h3>
                                        <p class="text-sm text-gray-600 font-medium">Distribuição por organizações</p>
                                    </div>
                                </div>
                                
                                <!-- Action buttons -->
                                <div class="flex items-center space-x-2">
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-blue-200 hover:border-blue-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-blue-200 hover:border-blue-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart content with enhanced styling -->
                        <div class="p-6 bg-gradient-to-br from-white to-gray-50">
                            <!-- Quick stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="text-lg font-bold text-blue-600">{{ $chartStats['total_orgs'] ?? 0 }}</div>
                                    <div class="text-xs text-blue-500 font-medium">Organizações Ativas</div>
                                </div>
                                <div class="text-center p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                                    <div class="text-lg font-bold text-indigo-600">{{ $chartStats['top_org'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-indigo-500 font-medium">Maior Participação</div>
                                </div>
                            </div>
                            
                            <!-- Chart container with border -->
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl opacity-30"></div>
                                <div class="relative h-56 p-4 bg-white bg-opacity-80 rounded-xl border border-gray-200 shadow-inner">
                                    <canvas id="originChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Legend/Info -->
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    Gráfico de Rosca
                                </div>
                                <div class="text-xs text-gray-500">
                                    Dados do mês atual
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meal Comparison Chart -->
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 relative flex flex-col h-full">
                        <!-- Header with gradient -->
                        <div class="bg-gradient-to-r from-orange-50 via-yellow-50 to-amber-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-orange-800 bg-clip-text text-transparent">
                                            Comparativo Café vs Almoço
                                        </h3>
                                        <p class="text-sm text-gray-600 font-medium">Análise por tipo de refeição</p>
                                    </div>
                                </div>
                                
                                <!-- Action buttons -->
                                <div class="flex items-center space-x-2">
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-orange-200 hover:border-orange-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-orange-600 group-hover:text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-orange-200 hover:border-orange-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-orange-600 group-hover:text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart content with enhanced styling -->
                        <div class="p-6 bg-gradient-to-br from-white to-gray-50">
                            <!-- Quick stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-green-50 rounded-xl border border-green-100">
                                    <div class="text-lg font-bold text-green-600">{{ $chartStats['breakfast_total'] ?? 0 }}</div>
                                    <div class="text-xs text-green-500 font-medium">Café da Manhã</div>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="text-lg font-bold text-blue-600">{{ $chartStats['lunch_total'] ?? 0 }}</div>
                                    <div class="text-xs text-blue-500 font-medium">Almoço</div>
                                </div>
                            </div>
                            
                            <!-- Chart container with border -->
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl opacity-30"></div>
                                <div class="relative h-56 p-4 bg-white bg-opacity-80 rounded-xl border border-gray-200 shadow-inner">
                                    <canvas id="mealComparisonChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Legend/Info -->
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-gray-600 font-medium">Café da Manhã</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-gray-600 font-medium">Almoço</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    Gráfico de Barras
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Ranks Chart -->
                    <!-- Top Ranks Chart -->
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 relative flex flex-col h-full">
                        <!-- Header with gradient -->
                        <div class="bg-gradient-to-r from-purple-50 via-pink-50 to-red-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-green-800 bg-clip-text text-transparent">
                                            Cardápio da Semana
                                        </h3>
                                        <p class="text-sm text-gray-600 font-medium">Segunda a Quinta-feira</p>
                                    </div>
                                </div>
                                
                                <!-- Action buttons -->
                                <div class="flex items-center space-x-2">
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-green-200 hover:border-green-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                    <button class="p-2 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-lg border border-green-200 hover:border-green-300 transition-all duration-200 group">
                                        <svg class="w-4 h-4 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Menu content with enhanced styling -->
                        <div class="p-6 bg-gradient-to-br from-white to-gray-50">
                            <!-- Weekly menu days -->
                            <div class="space-y-4">
                                <!-- Segunda-feira -->
                                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">S</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-green-800">Segunda-feira</h4>
                                                <p class="text-sm text-green-600">{{ \Carbon\Carbon::now()->startOfWeek()->format('d/m') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-green-600 font-medium">🍽️ Café & Almoço</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terça-feira -->
                                <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">T</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-blue-800">Terça-feira</h4>
                                                <p class="text-sm text-blue-600">{{ \Carbon\Carbon::now()->startOfWeek()->addDay()->format('d/m') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-blue-600 font-medium">🍽️ Café & Almoço</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quarta-feira -->
                                <div class="p-4 bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">Q</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-purple-800">Quarta-feira</h4>
                                                <p class="text-sm text-purple-600">{{ \Carbon\Carbon::now()->startOfWeek()->addDays(2)->format('d/m') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-purple-600 font-medium">🍽️ Café & Almoço</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quinta-feira -->
                                <div class="p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">Q</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-orange-800">Quinta-feira</h4>
                                                <p class="text-sm text-orange-600">{{ \Carbon\Carbon::now()->startOfWeek()->addDays(3)->format('d/m') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-orange-600 font-medium">🍽️ Café & Almoço</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Info footer -->
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    📅 Semana Atual
                                </div>
                                <div class="text-xs text-gray-500">
                                    Sexta: Apenas café
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">CF</span>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Total Cafés</p>
                                    <p class="text-2xl font-semibold text-gray-900">145</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">AL</span>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Total Almoços</p>
                                    <p class="text-2xl font-semibold text-gray-900">198</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">OM</span>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Própria OM</p>
                                    <p class="text-2xl font-semibold text-gray-900">234</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">EX</span>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Outras OMs</p>
                                    <p class="text-2xl font-semibold text-gray-900">109</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Dados exemplo para os gráficos
        const sampleData = {
            dailyBookings: ['15/01', '16/01', '17/01', '18/01', '19/01', '20/01', '21/01'],
            dailyValues: [23, 34, 28, 45, 38, 29, 31],
            originLabels: ['Própria OM', 'Outras OMs'],
            originValues: [234, 109],
            mealLabels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex'],
            cafeValues: [28, 32, 25, 35, 29],
            almocoValues: [35, 38, 30, 42, 33],
            rankLabels: ['Soldado', 'Cabo', 'Sargento', 'Tenente', 'Capitão'],
            rankValues: [45, 32, 28, 18, 12]
        };

        // Daily Bookings Chart
        const dailyBookingsCtx = document.getElementById('dailyBookingsChart').getContext('2d');
        new Chart(dailyBookingsCtx, {
            type: 'line',
            data: {
                labels: sampleData.dailyBookings,
                datasets: [{
                    label: 'Arranchados',
                    data: sampleData.dailyValues,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Origin Chart
        const originCtx = document.getElementById('originChart').getContext('2d');
        new Chart(originCtx, {
            type: 'doughnut',
            data: {
                labels: sampleData.originLabels,
                datasets: [{
                    data: sampleData.originValues,
                    backgroundColor: ['#10b981', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });

        // Meal Comparison Chart
        const mealCtx = document.getElementById('mealComparisonChart').getContext('2d');
        new Chart(mealCtx, {
            type: 'bar',
            data: {
                labels: sampleData.mealLabels,
                datasets: [
                    {
                        label: 'Café',
                        data: sampleData.cafeValues,
                        backgroundColor: '#10b981'
                    },
                    {
                        label: 'Almoço',
                        data: sampleData.almocoValues,
                        backgroundColor: '#3b82f6'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { 
                        stacked: true,
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
