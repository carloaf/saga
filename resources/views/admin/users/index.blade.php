@extends('layouts.admin')

@section('title', 'Administração de Usuários')

@section('content')
<div class="bg-gray-50">
    <!-- Enhanced Header with Gradient -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 shadow-xl">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <div class="flex items-center space-x-4">
                        <!-- Back to Dashboard Button -->
                        <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm hover:bg-opacity-30 transition-all duration-200 group">
                            <svg class="w-6 h-6 text-white group-hover:text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">Administração de Usuários</h1>
                            <p class="mt-2 text-blue-100 font-medium">Gerencie todos os usuários do sistema SAGA</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-0 sm:ml-16 flex flex-col sm:flex-row gap-3 header-controls">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="searchUsers" placeholder="Buscar usuários..." 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-white bg-opacity-90 backdrop-blur-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent sm:text-sm">
                    </div>
                    <!-- Filter Dropdown -->
                    <select id="filterRole" class="rounded-lg bg-white bg-opacity-90 backdrop-blur-sm border border-gray-300 px-3 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent sm:text-sm">
                        <option value="">Todos os tipos</option>
                        <option value="user">Usuários</option>
                        <option value="manager">Gerentes</option>
                        <option value="furriel">Furriéis</option>
                        <option value="aprov">Aprov</option>
                        <option value="sgtte">Sgtte</option>
                    </select>
                    <!-- New User Button -->
                    <button type="button" onclick="openCreateModal()" 
                            class="bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 text-white font-semibold px-6 py-3 rounded-lg border border-white border-opacity-30 transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Novo Usuário</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <!-- Enhanced Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-4 cards-grid">
            <!-- Total de Usuários -->
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total de Usuários</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-blue-600 font-medium">●</span>
                                <span class="text-xs text-gray-500 ml-1">Cadastrados no sistema</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuários Ativos -->
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Usuários Ativos</p>
                            <p class="text-2xl font-bold text-green-600">{{ $activeUsers }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-green-600 font-medium">●</span>
                                <span class="text-xs text-gray-500 ml-1">Podem fazer reservas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuários Inativos -->
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Usuários Inativos</p>
                            <p class="text-2xl font-bold text-red-600">{{ $inactiveUsers }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-red-600 font-medium">●</span>
                                <span class="text-xs text-gray-500 ml-1">Bloqueados temporariamente</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuários Recentes -->
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Novos (30 dias)</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $recentUsers }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-purple-600 font-medium">●</span>
                                <span class="text-xs text-gray-500 ml-1">Cadastros recentes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Enhanced Users Table -->
        <div class="bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Lista de Usuários</h3>
                            <p class="text-sm text-gray-500">{{ $users->total() }} usuários encontrados</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Usuário & Contato
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Posto/Grad
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                SU
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                OM
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Força
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Pronto OM
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative py-4 pl-3 pr-6">
                                <span class="sr-only">Ações</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-blue-50 transition-colors duration-200 group">
                            <td class="whitespace-nowrap py-5 pl-6 pr-3">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 relative">
                                        @if($user->avatar_url)
                                            <img class="h-12 w-12 rounded-xl object-cover shadow-md" src="{{ $user->avatar_url }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                                                <span class="text-lg font-bold text-white">{{ substr($user->war_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $user->is_active ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">
                                            {{ $user->full_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 font-medium">{{ $user->war_name }}</div>
                                        <div class="text-xs text-gray-400 font-medium flex items-center mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $user->rank ? $user->rank->getDisplayName() : 'N/A' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600">
                                @if($user->subunit)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $user->subunit }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">N/A</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800">
                                    @if($user->organization)
                                        {{ $user->organization->getDisplayName() }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600">
                                @if($user->armed_force)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium 
                                        {{ $user->armed_force === 'FAB' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $user->armed_force === 'MB' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                        {{ $user->armed_force === 'EB' ? 'bg-green-100 text-green-800' : '' }}">
                                        <span class="w-2 h-2 rounded-full mr-1.5
                                            {{ $user->armed_force === 'FAB' ? 'bg-blue-500' : '' }}
                                            {{ $user->armed_force === 'MB' ? 'bg-indigo-500' : '' }}
                                            {{ $user->armed_force === 'EB' ? 'bg-green-500' : '' }}"></span>
                                        {{ $user->armed_force }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">N/A</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600 font-medium">
                                {{ $user->ready_at_om_date ? \Carbon\Carbon::parse($user->ready_at_om_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm
                                    @if($user->role === 'manager')
                                        bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300
                                    @elseif($user->role === 'furriel')
                                        bg-gradient-to-r from-red-100 to-orange-200 text-red-800 border border-red-300
                                    @elseif($user->role === 'aprov')
                                        bg-gradient-to-r from-yellow-100 to-amber-200 text-amber-800 border border-amber-300
                                    @else
                                        bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300
                                    @endif">
                                    @if($user->role === 'manager')
                                        🛡️ Gerente
                                    @elseif($user->role === 'furriel')
                                        ⚔️ Furriel
                                    @elseif($user->role === 'aprov')
                                        ⭐ Aprov
                                    @elseif($user->role === 'sgtte')
                                        🛠️ Sgtte
                                    @else
                                        👤 Usuário
                                    @endif
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm
                                    {{ $user->is_active ? 'bg-gradient-to-r from-green-100 to-emerald-200 text-green-800 border border-green-300' : 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300' }}">
                                    <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                <button type="button" 
                    data-user-id="{{ $user->id }}"
                    data-full-name="{{ $user->full_name }}"
                    data-war-name="{{ $user->war_name }}"
                    data-idt="{{ $user->idt }}"
                    data-email="{{ $user->email }}"
                                        data-rank-id="{{ $user->rank_id ?? '' }}"
                                        data-organization-id="{{ $user->organization_id ?? '' }}"
                                        data-subunit="{{ $user->subunit ?? '' }}"
                                        data-armed-force="{{ $user->armed_force ?? '' }}"
                                        data-gender="{{ $user->gender ?? '' }}"
                                        data-ready-date="{{ $user->ready_at_om_date ? \Carbon\Carbon::parse($user->ready_at_om_date)->format('Y-m-d') : '' }}"
                                        data-is-active="{{ $user->is_active ? 1 : 0 }}"
                                        data-role="{{ $user->role ?? 'user' }}"
                                        onclick="openEditModal(this)"
                                        title="Editar usuário"
                                        class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 group-hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Pagination -->
        <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-100 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                <!-- Results Info - Left Side -->
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full shadow-sm"></div>
                    <div class="flex items-center space-x-2">
                        <p class="text-sm text-gray-700 font-medium">
                            Mostrando
                            <span class="font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">{{ $users->firstItem() ?? 0 }}</span>
                            até
                            <span class="font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">{{ $users->lastItem() ?? 0 }}</span>
                            de
                            <span class="font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">{{ $users->total() }}</span>
                            resultados
                        </p>
                    </div>
                </div>
                
                <!-- Pagination Controls - Right Side -->
                <div class="pagination-wrapper">
                    {{ $users->appends(request()->query())->links('custom-pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Criação -->
<div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal panel -->
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100" style="max-height:90vh; overflow-y:auto;">
                <!-- Header com gradiente -->
                <div class="bg-gradient-to-r from-green-600 via-green-700 to-emerald-800 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-xl font-bold text-white" id="modal-title">
                                Novo Usuário
                            </h3>
                            <p class="text-green-100 text-sm font-medium">
                                Preencha os dados para criar um novo usuário no sistema SAGA
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo do modal -->
                <div class="bg-white px-6 py-6">
                    
                    <div class="mt-2">
                        <form id="createUserForm" class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label for="createFullName" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Nome Completo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="createFullName" name="full_name" required
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                </div>
                                
                                <div>
                                    <label for="createWarName" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Nome de Guerra <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="createWarName" name="war_name" required
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                </div>

                                <div>
                                    <label for="createIdt" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Identidade (IDT) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="createIdt" name="idt" required maxlength="30"
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all"
                                           placeholder="Ex: 123456789" />
                                </div>
                                
                                <div>
                                    <label for="createEmail" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="createEmail" name="email" required
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                </div>

                                <div>
                                    <label for="createRank" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Posto/Graduação <span class="text-red-500">*</span>
                                    </label>
                                    <select id="createRank" name="rank_id" required
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                        <option value="">Selecione...</option>
                                        @foreach($ranks as $rank)
                                            <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="createArmedForce" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Força Armada
                                    </label>
                                    <select id="createArmedForce" name="armed_force"
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all"
                                            onchange="toggleOrganizationFieldsCreate()">
                                        <option value="">Selecione...</option>
                                        <option value="FAB">🛩️ FAB - Força Aérea Brasileira</option>
                                        <option value="MB">⚓ MB - Marinha do Brasil</option>
                                        <option value="EB">🪖 EB - Exército Brasileiro</option>
                                    </select>
                                </div>

                                <div id="createOrganizationGroup">
                                    <label for="createOrganization" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Organização <span class="text-red-500">*</span>
                                    </label>
                                    <select id="createOrganization" name="organization_id" required
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all"
                                            onchange="toggleSubunitFieldCreate()">
                                        <option value="">Selecione...</option>
                                        @foreach($organizations as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="createSubunitGroup">
                                    <label for="createSubunit" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Subunidade (Cia)
                                    </label>
                                    <select id="createSubunit" name="subunit"
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                        <option value="">Selecione a subunidade</option>
                                        <option value="1ª Cia">1ª Cia</option>
                                        <option value="2ª Cia">2ª Cia</option>
                                        <option value="EM">EM</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="createGender" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Gênero <span class="text-red-500">*</span>
                                    </label>
                                    <select id="createGender" name="gender" required
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                        <option value="">Selecione o gênero</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Feminino</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="createReadyDate" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Pronto OM <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="createReadyDate" name="ready_at_om_date" required
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                </div>
                                
                                <div>
                                    <label for="createRole" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Tipo de Usuário
                                    </label>
                                    <select id="createRole" name="role" 
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                        <option value="user" selected>👤 Usuário Normal</option>
                                        <option value="manager">🛡️ Gerente</option>
                                        <option value="furriel">⚔️ Furriel</option>
                                        <option value="aprov">⭐ Aprov</option>
                                        <option value="sgtte">🛠️ Sgtte (Serviço)</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="createStatus" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Status
                                    </label>
                                    <select id="createStatus" name="is_active" 
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm transition-all">
                                        <option value="1" selected>✅ Ativo</option>
                                        <option value="0">❌ Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Footer com botões -->
                <div class="bg-gradient-to-r from-gray-50 to-green-50 px-6 py-4 flex justify-end space-x-3 rounded-b-2xl border-t border-gray-200">
                    <button type="button" onclick="closeCreateModal()" 
                            class="inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </button>
                    <button type="button" onclick="createUser()" 
                            class="inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl shadow-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 transform hover:scale-105 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Criar Usuário
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição (único, sempre presente, visível só quando ativado) -->
<div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100" style="max-height:90vh; overflow-y:auto;">
                <!-- Header com gradiente -->
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-xl font-bold text-white" id="modal-title">Editar Usuário</h3>
                            <p class="text-blue-100 text-sm font-medium">
                                Atualize as informações do usuário no sistema SAGA
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo do modal -->
                <div class="bg-white px-6 py-6">
                <form id="editUserForm" class="space-y-5">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="editFullName" class="block text-sm font-semibold text-gray-900 mb-2">Nome Completo</label>
                            <input type="text" id="editFullName" name="full_name" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                        </div>
                        <div>
                            <label for="editWarName" class="block text-sm font-semibold text-gray-900 mb-2">Nome de Guerra</label>
                            <input type="text" id="editWarName" name="war_name" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                        </div>

                        <div>
                            <label for="editIdt" class="block text-sm font-semibold text-gray-900 mb-2">
                                Identidade (IDT)
                            </label>
                            <input type="text" id="editIdt" name="idt" readonly
                                   class="block w-full rounded-xl border-0 py-3 px-4 text-gray-500 shadow-sm ring-1 ring-inset ring-gray-300 bg-gray-100 cursor-not-allowed sm:text-sm"
                                   title="IDT não pode ser alterado" />
                        </div>
                        <div>
                            <label for="editEmail" class="block text-sm font-semibold text-gray-900 mb-2">Email</label>
                            <input type="email" id="editEmail" name="email" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                        </div>
                        <div>
                            <label for="editRank" class="block text-sm font-semibold text-gray-900 mb-2">Posto/Graduação</label>
                            <select id="editRank" name="rank_id" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                                <option value="">Selecione...</option>
                                @foreach($ranks as $rank)
                                    <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="editArmedForce" class="block text-sm font-semibold text-gray-900 mb-2">Força Armada</label>
                            <select id="editArmedForce" name="armed_force" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all"
                                    onchange="toggleOrganizationFieldsEdit()">
                                <option value="">Selecione...</option>
                                <option value="FAB">🛩️ FAB - Força Aérea Brasileira</option>
                                <option value="MB">⚓ MB - Marinha do Brasil</option>
                                <option value="EB">🪖 EB - Exército Brasileiro</option>
                            </select>
                        </div>
                        <div id="editOrganizationGroup">
                            <label for="editOrganization" class="block text-sm font-semibold text-gray-900 mb-2">Organização</label>
                            <select id="editOrganization" name="organization_id" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all"
                                    onchange="toggleSubunitFieldEdit()">
                                <option value="">Selecione...</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="editSubunitGroup">
                            <label for="editSubunit" class="block text-sm font-semibold text-gray-900 mb-2">Subunidade (Cia)</label>
                            <select id="editSubunit" name="subunit"
                                    class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                                <option value="">Selecione a subunidade</option>
                                <option value="1ª Cia">1ª Cia</option>
                                <option value="2ª Cia">2ª Cia</option>
                                <option value="EM">EM</option>
                            </select>
                        </div>
                        <div>
                            <label for="editGender" class="block text-sm font-semibold text-gray-900 mb-2">Gênero</label>
                            <select id="editGender" name="gender" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                                <option value="">Selecione o gênero</option>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                        </div>
                        <div>
                            <label for="editReadyDate" class="block text-sm font-semibold text-gray-900 mb-2">Pronto OM</label>
                            <input type="date" id="editReadyDate" name="ready_at_om_date" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                        </div>
                        <div>
                            <label for="editStatus" class="block text-sm font-semibold text-gray-900 mb-2">Status</label>
                            <select id="editStatus" name="is_active" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                                <option value="1">✅ Ativo</option>
                                <option value="0">❌ Inativo</option>
                            </select>
                        </div>
                        <div>
                            <label for="editRole" class="block text-sm font-semibold text-gray-900 mb-2">Tipo de Usuário</label>
                            <select id="editRole" name="role" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all">
                                <option value="user">👤 Usuário Normal</option>
                                <option value="manager">🛡️ Gerente</option>
                                <option value="furriel">⚔️ Furriel</option>
                                <option value="aprov">⭐ Aprov</option>
                                <option value="sgtte">🛠️ Sgtte (Serviço)</option>
                            </select>
                        </div>
                    </div>
                </form>
                    <!-- Footer com botões -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 flex justify-end space-x-3 rounded-b-2xl border-t border-gray-200 -mx-6 -mb-6 mt-8">
                        <button type="button" onclick="closeEditModal()" class="inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </button>
                        <button type="button" onclick="updateUser()" class="inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transform hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Função para controlar visibilidade dos campos de organização na criação
    function toggleOrganizationFieldsCreate() {
        const armedForce = document.getElementById('createArmedForce').value;
        const orgGroup = document.getElementById('createOrganizationGroup');
        const subunitGroup = document.getElementById('createSubunitGroup');
        const orgField = document.getElementById('createOrganization');
        
        if (armedForce === 'EB') {
            orgGroup.style.display = 'block';
            orgField.required = true;
            // Verifica se deve mostrar subunidade baseado na organização atual
            toggleSubunitFieldCreate();
        } else {
            orgGroup.style.display = 'none';
            subunitGroup.style.display = 'none';
            orgField.required = false;
            orgField.value = '';
            document.getElementById('createSubunit').value = '';
        }
    }

    // Função para controlar visibilidade da subunidade na criação
    function toggleSubunitFieldCreate() {
        const orgValue = document.getElementById('createOrganization').value;
        const subunitGroup = document.getElementById('createSubunitGroup');
        const subunitField = document.getElementById('createSubunit');
        
        // ID 14 corresponde ao "11º Depósito de Suprimento"
        if (orgValue === '14') {
            subunitGroup.style.display = 'block';
            console.log('Mostrando subunidade para 11º Depósito de Suprimento');
        } else {
            subunitGroup.style.display = 'none';
            subunitField.value = '';
            console.log('Ocultando subunidade - organização não é 11º Depósito');
        }
    }

    // Função para controlar visibilidade dos campos de organização na edição
    function toggleOrganizationFieldsEdit() {
        const armedForce = document.getElementById('editArmedForce').value;
        const orgGroup = document.getElementById('editOrganizationGroup');
        const subunitGroup = document.getElementById('editSubunitGroup');
        const orgField = document.getElementById('editOrganization');
        
        if (armedForce === 'EB') {
            orgGroup.style.display = 'block';
            orgField.required = true;
            // Verifica se deve mostrar subunidade baseado na organização atual
            toggleSubunitFieldEdit();
        } else {
            orgGroup.style.display = 'none';
            subunitGroup.style.display = 'none';
            orgField.required = false;
            orgField.value = '';
            document.getElementById('editSubunit').value = '';
        }
    }

    // Função para controlar visibilidade da subunidade na edição
    function toggleSubunitFieldEdit() {
        const orgValue = document.getElementById('editOrganization').value;
        const subunitGroup = document.getElementById('editSubunitGroup');
        const subunitField = document.getElementById('editSubunit');
        
        console.log('toggleSubunitFieldEdit - Organização selecionada:', orgValue);
        console.log('Elemento subunitGroup encontrado:', subunitGroup);
        
        // ID 14 corresponde ao "11º Depósito de Suprimento"
        if (orgValue === '14') {
            subunitGroup.style.display = 'block';
            console.log('✅ Mostrando subunidade para 11º Depósito de Suprimento (ID: 14)');
        } else {
            subunitGroup.style.display = 'none';
            subunitField.value = '';
            console.log('❌ Ocultando subunidade - organização ID:', orgValue, '(não é 14)');
        }
    }

    function openCreateModal() {
        // Limpa o formulário
        document.getElementById('createUserForm').reset();
        
        // Inicializa os campos de organização como ocultos
        toggleOrganizationFieldsCreate();
        
        // Garante que subunidade comece oculta
        document.getElementById('createSubunitGroup').style.display = 'none';
        
        // Mostra o modal
        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        
        // Adiciona animação de entrada
        setTimeout(() => {
            const backdrop = modal.querySelector('.bg-gray-900');
            const panel = modal.querySelector('.transform');
            if (backdrop) backdrop.classList.add('opacity-100');
            if (panel) panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        const backdrop = modal.querySelector('.bg-gray-900');
        const panel = modal.querySelector('.transform');
        
        // Animação de saída
        if (backdrop) backdrop.classList.remove('opacity-100');
        if (panel) panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function createUser() {
        const formData = new FormData(document.getElementById('createUserForm'));
        
        // Validação básica
        if (!formData.get('full_name') || !formData.get('war_name') || !formData.get('email') || !formData.get('gender') || !formData.get('ready_at_om_date')) {
            alert('Por favor, preencha todos os campos obrigatórios.');
            return;
        }
        
        // Desabilita o botão durante a requisição
        const createButton = event.target;
        createButton.disabled = true;
        createButton.textContent = 'Criando...';
        
        fetch('/admin/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                full_name: formData.get('full_name'),
                war_name: formData.get('war_name'),
                idt: formData.get('idt'),
                email: formData.get('email'),
                rank_id: formData.get('rank_id') || null,
                organization_id: formData.get('organization_id') || null,
                subunit: formData.get('subunit') || null,
                armed_force: formData.get('armed_force') || null,
                gender: formData.get('gender'),
                ready_at_om_date: formData.get('ready_at_om_date'),
                is_active: formData.get('is_active') === '1',
                role: formData.get('role')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCreateModal();
                // Mostra mensagem de sucesso
                alert('Usuário criado com sucesso!');
                location.reload(); // Recarrega a página para mostrar o novo usuário
            } else {
                alert('Erro ao criar usuário: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao criar usuário');
        })
        .finally(() => {
            // Reabilita o botão
            createButton.disabled = false;
            createButton.textContent = 'Criar Usuário';
        });
    }

    function openEditModal(button) {
        // Pega os dados dos data attributes
        const userId = button.getAttribute('data-user-id');
        const fullName = button.getAttribute('data-full-name');
        const warName = button.getAttribute('data-war-name');
        const idt = button.getAttribute('data-idt');
        const email = button.getAttribute('data-email');
        const rankId = button.getAttribute('data-rank-id');
        const organizationId = button.getAttribute('data-organization-id');
        const subunit = button.getAttribute('data-subunit');
        const armedForce = button.getAttribute('data-armed-force');
        const gender = button.getAttribute('data-gender');
        const readyDate = button.getAttribute('data-ready-date');
        const isActive = button.getAttribute('data-is-active');
        const role = button.getAttribute('data-role');
        
        // Preenche os campos do formulário
    document.getElementById('editUserId').value = userId;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editWarName').value = warName;
    document.getElementById('editIdt').value = idt || '';
    document.getElementById('editEmail').value = email;
        document.getElementById('editRank').value = rankId || '';
        document.getElementById('editOrganization').value = organizationId || '';
        document.getElementById('editSubunit').value = subunit || '';
        document.getElementById('editArmedForce').value = armedForce || '';
        document.getElementById('editGender').value = gender || '';
        document.getElementById('editReadyDate').value = readyDate || '';
        document.getElementById('editStatus').value = isActive;
        document.getElementById('editRole').value = role || 'user';
        
        // Inicializa a visibilidade dos campos condicionais após um pequeno delay
        setTimeout(() => {
            toggleOrganizationFieldsEdit();
            console.log('Valores atuais - Força Armada:', armedForce, 'Organização ID:', organizationId);
        }, 100);
        
        // Mostra o modal
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
    }

    function updateUser() {
        const formData = new FormData(document.getElementById('editUserForm'));
        const userId = formData.get('user_id');
        
        // Desabilita o botão de salvar durante a requisição
        const saveButton = event.target;
        saveButton.disabled = true;
        saveButton.textContent = 'Salvando...';
        
        fetch(`/admin/users/${userId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                full_name: formData.get('full_name'),
                war_name: formData.get('war_name'),
                idt: formData.get('idt'), // enviado para validação; backend ignora alteração se já setado
                email: formData.get('email'),
                rank_id: formData.get('rank_id') || null,
                organization_id: formData.get('organization_id') || null,
                subunit: formData.get('subunit') || null,
                armed_force: formData.get('armed_force') || null,
                gender: formData.get('gender'),
                ready_at_om_date: formData.get('ready_at_om_date'),
                is_active: formData.get('is_active') === '1',
                role: formData.get('role')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                location.reload(); // Recarrega a página para mostrar as mudanças
            } else {
                alert('Erro ao atualizar usuário: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar usuário');
        })
        .finally(() => {
            // Reabilita o botão
            saveButton.disabled = false;
            saveButton.textContent = 'Salvar Alterações';
        });
    }

    // Fecha modal ao clicar no backdrop
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editModal');
        const createModal = document.getElementById('createModal');
        
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });
        
        createModal.addEventListener('click', function(e) {
            if (e.target === createModal) {
                closeCreateModal();
            }
        });
    });

    // Fecha modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('editModal').classList.contains('hidden')) {
                closeEditModal();
            }
            if (!document.getElementById('createModal').classList.contains('hidden')) {
                closeCreateModal();
            }
        }
    });

    // Enhanced Search and Filter Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUsers');
        const roleFilter = document.getElementById('filterRole');
        const tableRows = document.querySelectorAll('tbody tr');

        // Debounce function for better performance
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            }
        }

        // Filter function
        function filterUsers() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedRole = roleFilter.value.toLowerCase();
            let visibleCount = 0;

            tableRows.forEach(row => {
                const userName = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const userRole = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                
                const matchesSearch = userName.includes(searchTerm);
                const matchesRole = selectedRole === '' || userRole.includes(selectedRole);
                
                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update results count
            const tableHeader = document.querySelector('.bg-gradient-to-r p');
            if (tableHeader) {
                tableHeader.textContent = `${visibleCount} usuários encontrados`;
            }
        }

        // Event listeners with debouncing
        if (searchInput) {
            searchInput.addEventListener('input', debounce(filterUsers, 300));
        }
        
        if (roleFilter) {
            roleFilter.addEventListener('change', filterUsers);
        }
    });
</script>

<style>
    /* Compact layout optimizations */
    .min-h-full { min-height: auto !important; }
    
    /* Optimize table spacing */
    .whitespace-nowrap.py-5 { padding-top: 1rem; padding-bottom: 1rem; }
    
    /* Modal width constraints */
    #editModal .sm\\:max-w-2xl {
        max-width: 42rem !important;
        width: 90% !important;
    }
    
    #createModal .sm\\:max-w-2xl {
        max-width: 42rem !important;
        width: 90% !important;
    }
    
    /* Enhanced modal animations */
    .modal-enter {
        animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .modal-exit {
        animation: modalSlideOut 0.2s ease-in-out;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    @keyframes modalSlideOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
    }
    
    /* Enhanced form inputs */
    .form-input-enhanced {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .form-input-enhanced:focus {
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }
    
    /* Enhanced buttons */
    .btn-enhanced {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .btn-enhanced:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .btn-primary-enhanced {
        background: linear-gradient(145deg, #3b82f6, #2563eb);
        box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.4);
        transition: all 0.3s ease;
    }
    
    .btn-primary-enhanced:hover {
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
        transform: translateY(-3px);
    }
    
    /* Force proper responsive behavior */
    @media (max-width: 640px) {
        .header-controls {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .cards-grid {
            grid-template-columns: 1fr;
        }
        
        #editModal .sm\\:max-w-2xl,
        #createModal .sm\\:max-w-2xl {
            max-width: 95vw !important;
            width: 95% !important;
            margin: 1rem !important;
        }
        
        /* Mobile form adjustments */
        .grid.md\\:grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (min-width: 640px) and (max-width: 1024px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        #editModal .sm\\:max-w-2xl,
        #createModal .sm\\:max-w-2xl {
            max-width: 80vw !important;
            width: 80% !important;
        }
    }
    
    @media (min-width: 1024px) {
        #editModal .sm\\:max-w-2xl,
        #createModal .sm\\:max-w-2xl {
            max-width: 42rem !important;
            width: auto !important;
        }
    }
    
    /* Custom scrollbar for modals */
    div[style*="max-height:90vh"]::-webkit-scrollbar {
        width: 8px;
    }
    
    div[style*="max-height:90vh"]::-webkit-scrollbar-track {
        background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
        border-radius: 6px;
    }
    
    div[style*="max-height:90vh"]::-webkit-scrollbar-thumb {
        background: linear-gradient(145deg, #cbd5e1, #94a3b8);
        border-radius: 6px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    
    div[style*="max-height:90vh"]::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(145deg, #94a3b8, #64748b);
    }
    
    /* Enhanced modal backdrop */
    .modal-backdrop {
        backdrop-filter: blur(8px);
        background: rgba(17, 24, 39, 0.4);
    }
    
    /* Enhanced gradient headers */
    .modal-header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .modal-header-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Enhanced form labels */
    .form-label-enhanced {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    
    .form-label-enhanced::before {
        content: '';
        width: 3px;
        height: 3px;
        background: #3b82f6;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    /* Required field indicator */
    .required-field::after {
        content: '*';
        color: #ef4444;
        margin-left: 0.25rem;
        font-weight: bold;
    }
    
    /* Enhanced Pagination Styles */
    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
    }
    
    .pagination-wrapper nav {
        width: auto;
    }
    
    /* Ensure pagination controls stay on the right */
    @media (min-width: 640px) {
        .pagination-wrapper {
            margin-left: auto;
            width: auto;
        }
    }
    
    /* Mobile pagination full width */
    @media (max-width: 639px) {
        .pagination-wrapper {
            width: 100%;
            justify-content: center;
        }
        
        .pagination-wrapper nav {
            width: 100%;
        }
    }
    
    /* Additional pagination hover effects */
    .pagination-wrapper .pagination-number-link:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-color: #3b82f6;
        color: #1d4ed8;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
    }
    
    .pagination-wrapper .pagination-arrow-active:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-color: #3b82f6;
        color: #1d4ed8;
        transform: translateY(-2px) scale(1.1);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
    }
    
    /* Results info styling enhancement */
    .text-blue-600.bg-blue-50 {
        transition: all 0.2s ease;
    }
    
    .text-blue-600.bg-blue-50:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        transform: scale(1.05);
    }
    
    /* Inicializar campos condicionais como ocultos */
    #createSubunitGroup,
    #editSubunitGroup,
    #createOrganizationGroup,
    #editOrganizationGroup {
        display: none;
    }
</style>
@endsection
