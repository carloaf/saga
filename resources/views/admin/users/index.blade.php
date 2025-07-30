@extends('layouts.app')

@section('title', 'Administração de Usuários')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">Administração de Usuários</h1>
            <p class="mt-2 text-sm text-gray-700">Gerencie os usuários do sistema SAGA</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button type="button" onclick="openCreateModal()" class="block rounded-md bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                Novo Usuário
            </button>
        </div>
    </div>

    <!-- Tabela -->
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Usuário
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Email
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Posto/Graduação
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Organização
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Data Prontidão OM
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Tipo
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Ações</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if($user->avatar_url)
                                                <img class="h-10 w-10 rounded-full" src="{{ $user->avatar_url }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">{{ substr($user->war_name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->war_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $user->rank ? $user->rank->name : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $user->organization ? $user->organization->name : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $user->ready_at_om_date ? \Carbon\Carbon::parse($user->ready_at_om_date)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                        {{ $user->role === 'superuser' ? 'bg-purple-50 text-purple-700 ring-purple-600/20' : 'bg-blue-50 text-blue-700 ring-blue-600/20' }}">
                                        {{ $user->role === 'superuser' ? '🛡️ Superusuário' : '👤 Usuário' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                        {{ $user->is_active ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-red-50 text-red-700 ring-red-600/20' }}">
                                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <button type="button" 
                                            data-user-id="{{ $user->id }}"
                                            data-full-name="{{ $user->full_name }}"
                                            data-war-name="{{ $user->war_name }}"
                                            data-email="{{ $user->email }}"
                                            data-rank-id="{{ $user->rank_id ?? '' }}"
                                            data-organization-id="{{ $user->organization_id ?? '' }}"
                                            data-gender="{{ $user->gender ?? '' }}"
                                            data-ready-date="{{ $user->ready_at_om_date ? \Carbon\Carbon::parse($user->ready_at_om_date)->format('Y-m-d') : '' }}"
                                            data-is-active="{{ $user->is_active ? 1 : 0 }}"
                                            data-role="{{ $user->role ?? 'user' }}"
                                            onclick="openEditModal(this)"
                                            class="text-green-600 hover:text-green-900">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>

    <!-- Cards de Estatísticas -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Usuários -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Usuários</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalUsers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários Ativos -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Usuários Ativos</dt>
                            <dd class="text-lg font-medium text-green-600">{{ $activeUsers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários Inativos -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Usuários Inativos</dt>
                            <dd class="text-lg font-medium text-red-600">{{ $inactiveUsers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários Recentes -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Novos (30 dias)</dt>
                            <dd class="text-lg font-medium text-blue-600">{{ $recentUsers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Criação -->
<div id="createModal" class="relative z-50 hidden" aria-labelledby="create-modal-title" role="dialog" aria-modal="true">
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal panel -->
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="create-modal-title">
                            Novo Usuário
                        </h3>
                        <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">Fechar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mt-5">
                        <form id="createUserForm" class="space-y-5">
                            <div>
                                <label for="createFullName" class="block text-sm font-medium leading-6 text-gray-900">
                                    Nome Completo
                                </label>
                                <div class="mt-2">
                                    <input type="text" id="createFullName" name="full_name" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="createWarName" class="block text-sm font-medium leading-6 text-gray-900">
                                    Nome de Guerra
                                </label>
                                <div class="mt-2">
                                    <input type="text" id="createWarName" name="war_name" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="createEmail" class="block text-sm font-medium leading-6 text-gray-900">
                                    Email
                                </label>
                                <div class="mt-2">
                                    <input type="email" id="createEmail" name="email" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="createRank" class="block text-sm font-medium leading-6 text-gray-900">
                                    Posto/Graduação
                                </label>
                                <div class="mt-2">
                                    <select id="createRank" name="rank_id" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="">Selecione um posto</option>
                                        @foreach(\App\Models\Rank::all() as $rank)
                                            <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="createOrganization" class="block text-sm font-medium leading-6 text-gray-900">
                                    Organização
                                </label>
                                <div class="mt-2">
                                    <select id="createOrganization" name="organization_id" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="">Selecione uma organização</option>
                                        @foreach(\App\Models\Organization::all() as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="createGender" class="block text-sm font-medium leading-6 text-gray-900">
                                    Gênero <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <select id="createGender" name="gender" required
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="">Selecione o gênero</option>
                                        <option value="male">Masculino</option>
                                        <option value="female">Feminino</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="createReadyDate" class="block text-sm font-medium leading-6 text-gray-900">
                                    Data de Prontidão na OM <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <input type="date" id="createReadyDate" name="ready_at_om_date" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="createRole" class="block text-sm font-medium leading-6 text-gray-900">
                                    Tipo de Usuário
                                </label>
                                <div class="mt-2">
                                    <select id="createRole" name="role" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="user" selected>Usuário Normal</option>
                                        <option value="superuser">Superusuário</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="createStatus" class="block text-sm font-medium leading-6 text-gray-900">
                                    Status
                                </label>
                                <div class="mt-2">
                                    <select id="createStatus" name="is_active" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="1" selected>Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                    <button type="button" onclick="createUser()" 
                            class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 sm:col-start-2">
                        Criar Usuário
                    </button>
                    <button type="button" onclick="closeCreateModal()" 
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div id="editModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal panel -->
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                            Editar Usuário
                        </h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">Fechar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mt-5">
                        <form id="editUserForm" class="space-y-5">
                            <input type="hidden" id="editUserId" name="user_id">
                            
                            <div>
                                <label for="editFullName" class="block text-sm font-medium leading-6 text-gray-900">
                                    Nome Completo
                                </label>
                                <div class="mt-2">
                                    <input type="text" id="editFullName" name="full_name" 
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="editWarName" class="block text-sm font-medium leading-6 text-gray-900">
                                    Nome de Guerra
                                </label>
                                <div class="mt-2">
                                    <input type="text" id="editWarName" name="war_name" 
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="editEmail" class="block text-sm font-medium leading-6 text-gray-900">
                                    Email
                                </label>
                                <div class="mt-2">
                                    <input type="email" id="editEmail" name="email" 
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="editRank" class="block text-sm font-medium leading-6 text-gray-900">
                                    Posto/Graduação
                                </label>
                                <div class="mt-2">
                                    <select id="editRank" name="rank_id" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="">Selecione um posto</option>
                                        @foreach(\App\Models\Rank::all() as $rank)
                                            <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="editOrganization" class="block text-sm font-medium leading-6 text-gray-900">
                                    Organização
                                </label>
                                <div class="mt-2">
                                    <select id="editOrganization" name="organization_id" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="">Selecione uma organização</option>
                                        @foreach(\App\Models\Organization::all() as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="editGender" class="block text-sm font-medium leading-6 text-gray-900">
                                    Gênero
                                </label>
                                <div class="mt-2">
                                    <select id="editGender" name="gender" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="">Selecione o gênero</option>
                                        <option value="male">Masculino</option>
                                        <option value="female">Feminino</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="editReadyDate" class="block text-sm font-medium leading-6 text-gray-900">
                                    Data de Prontidão na OM
                                </label>
                                <div class="mt-2">
                                    <input type="date" id="editReadyDate" name="ready_at_om_date"
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            
                            <div>
                                <label for="editStatus" class="block text-sm font-medium leading-6 text-gray-900">
                                    Status
                                </label>
                                <div class="mt-2">
                                    <select id="editStatus" name="is_active" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="editRole" class="block text-sm font-medium leading-6 text-gray-900">
                                    Tipo de Usuário
                                </label>
                                <div class="mt-2">
                                    <select id="editRole" name="role" 
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                                        <option value="user">👤 Usuário Normal</option>
                                        <option value="superuser">🛡️ Superusuário</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                    <button type="button" onclick="updateUser()" 
                            class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 sm:col-start-2">
                        Salvar
                    </button>
                    <button type="button" onclick="closeEditModal()" 
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function openCreateModal() {
        // Limpa o formulário
        document.getElementById('createUserForm').reset();
        
        // Mostra o modal
        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        
        // Adiciona animação de entrada
        setTimeout(() => {
            const backdrop = modal.querySelector('.bg-gray-500');
            const panel = modal.querySelector('.transform');
            backdrop.classList.add('opacity-100');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        const backdrop = modal.querySelector('.bg-gray-500');
        const panel = modal.querySelector('.transform');
        
        // Animação de saída
        backdrop.classList.remove('opacity-100');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        
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
                email: formData.get('email'),
                rank_id: formData.get('rank_id') || null,
                organization_id: formData.get('organization_id') || null,
                gender: formData.get('gender'),
                ready_at_om_date: formData.get('ready_at_om_date'),
                is_active: formData.get('is_active') === '1'
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
        const email = button.getAttribute('data-email');
        const rankId = button.getAttribute('data-rank-id');
        const organizationId = button.getAttribute('data-organization-id');
        const gender = button.getAttribute('data-gender');
        const readyDate = button.getAttribute('data-ready-date');
        const isActive = button.getAttribute('data-is-active');
        const role = button.getAttribute('data-role');
        
        // Preenche os campos do formulário
        document.getElementById('editUserId').value = userId;
        document.getElementById('editFullName').value = fullName;
        document.getElementById('editWarName').value = warName;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRank').value = rankId || '';
        document.getElementById('editOrganization').value = organizationId || '';
        document.getElementById('editGender').value = gender || '';
        document.getElementById('editReadyDate').value = readyDate || '';
        document.getElementById('editStatus').value = isActive;
        document.getElementById('editRole').value = role || 'user';
        
        // Mostra o modal
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        
        // Adiciona animação de entrada
        setTimeout(() => {
            const backdrop = modal.querySelector('.bg-gray-500');
            const panel = modal.querySelector('.transform');
            backdrop.classList.add('opacity-100');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        const backdrop = modal.querySelector('.bg-gray-500');
        const panel = modal.querySelector('.transform');
        
        // Animação de saída
        backdrop.classList.remove('opacity-100');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
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
                email: formData.get('email'),
                rank_id: formData.get('rank_id') || null,
                organization_id: formData.get('organization_id') || null,
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
            saveButton.textContent = 'Salvar';
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
</script>
@endsection
@endsection
