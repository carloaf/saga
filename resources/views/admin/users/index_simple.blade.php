@extends('layouts.app')

@section('title', 'Administração de Usuários')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Administração de Usuários</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->war_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->rank ? $user->rank->name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->full_name) }}', '{{ addslashes($user->war_name) }}', '{{ $user->email }}', {{ $user->rank_id ?? 'null' }}, {{ $user->organization_id ?? 'null' }}, {{ $user->is_active ? 'true' : 'false' }})" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>

<!-- Simple Edit User Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" id="modalContent">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3">
                <h3 class="text-lg font-bold text-gray-900">Editar Usuário</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="editUserForm" class="space-y-4">
                <input type="hidden" id="editUserId" name="user_id">
                
                <div class="field-group">
                    <label class="label-enhanced">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Nome Completo</span>
                    </label>
                    <input type="text" id="editFullName" name="full_name" class="input-enhanced">
                </div>
                
                <div class="field-group">
                    <label class="label-enhanced">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Nome de Guerra</span>
                    </label>
                    <input type="text" id="editWarName" name="war_name" class="input-enhanced">
                </div>
                
                <div class="field-group">
                    <label class="label-enhanced">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        <span>Email</span>
                    </label>
                    <input type="email" id="editEmail" name="email" class="input-enhanced">
                </div>
                
                <div class="field-group">
                    <label class="label-enhanced">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        <span>Posto/Graduação</span>
                    </label>
                    <select id="editRank" name="rank_id" class="select-enhanced">
                        <option value="">Selecione um posto/graduação</option>
                        @foreach(\App\Models\Rank::all() as $rank)
                            <option value="{{ $rank->id }}">{{ $rank->abbreviation }} - {{ $rank->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="field-group">
                    <label class="label-enhanced">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Organização Militar</span>
                    </label>
                    <select id="editOrganization" name="organization_id" class="select-enhanced">
                        <option value="">Selecione uma organização</option>
                        @foreach(\App\Models\Organization::all() as $org)
                            <option value="{{ $org->id }}">{{ $org->abbreviation }} - {{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="field-group">
                    <label class="label-enhanced">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Status do Usuário</span>
                    </label>
                    <select id="editStatus" name="is_active" class="select-enhanced">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
            </form>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 space-x-2">
                <button onclick="closeEditModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="updateUser()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function openEditModal(userId, fullName, warName, email, rankId, organizationId, isActive) {
        // Preenche os campos do formulário
        document.getElementById('editUserId').value = userId;
        document.getElementById('editFullName').value = fullName;
        document.getElementById('editWarName').value = warName;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRank').value = rankId || '';
        document.getElementById('editOrganization').value = organizationId || '';
        document.getElementById('editStatus').value = isActive ? '1' : '0';
        
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
        
        fetch(`/admin/users/${userId}`, {
            method: 'PUT',
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
                is_active: formData.get('is_active') === '1'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                location.reload(); // Recarrega a página para mostrar as mudanças
            } else {
                alert('Erro ao atualizar usuário');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar usuário');
        });
    }

    // Fecha modal ao clicar fora dele
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    // Fecha modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('editModal').classList.contains('hidden')) {
            closeEditModal();
        }
    });
</script>
@endsection
@endsection
