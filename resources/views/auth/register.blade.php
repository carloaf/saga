@extends('layouts.app')

@section('content')
<style>
    /* Modern UI Styling */
    .page-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    .page-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="70" cy="30" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="30" cy="70" r="4" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2.5" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .card-enhanced {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(226, 232, 240, 0.5);
    }

    .floating-label {
        position: relative;
    }

    .floating-label input:focus + label,
    .floating-label input:not(:placeholder-shown) + label,
    .floating-label input:valid + label {
        transform: translateY(-26px) scale(0.9);
        color: #16a34a;
        font-weight: 600;
    }

    .floating-label label {
        position: absolute;
        top: 20px;
        left: 16px;
        transform: translateY(0);
        transition: all 0.3s ease;
        pointer-events: none;
        color: #6b7280;
        z-index: 1;
    }

    .input-modern {
        border-color: #d1d5db;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .input-modern:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        background: rgba(255, 255, 255, 1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(22, 163, 74, 0.3);
    }
</style>

<div class="page-container flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8 relative z-10">
            <h1 class="text-4xl font-bold text-white mb-4">Sistema SAGA</h1>
            <p class="text-xl text-blue-100 mb-2">Sistema de Agendamento e Gestão de Arranchamento</p>
            <div class="w-24 h-1 bg-green-300 mx-auto mt-4 rounded-full"></div>
        </div>

        <!-- Register Card -->
        <div class="w-full max-w-2xl relative z-10 mx-auto">
            <div class="card-enhanced rounded-2xl px-8 py-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Criar Nova Conta</h2>
                    <p class="text-gray-600">Preencha seus dados militares para se cadastrar no sistema</p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-6 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Corrija os seguintes erros:</h3>
                                <ul class="list-disc list-inside space-y-1 text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('auth.register') }}" class="space-y-6">
                    @csrf

                    <!-- Dados Pessoais Section -->
                    <div class="form-section rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Dados Pessoais
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome Completo -->
                            <div class="floating-label">
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    name="full_name" 
                                    value="{{ old('full_name') }}"
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none peer"
                                    placeholder=" "
                                >
                                <label for="full_name" class="text-sm font-medium">Nome Completo *</label>
                            </div>

                            <!-- Identidade (IDT) -->
                            <div class="floating-label">
                                <input 
                                    type="text" 
                                    id="idt" 
                                    name="idt" 
                                    value="{{ old('idt') }}"
                                    required 
                                    maxlength="30"
                                    pattern="[0-9]*"
                                    inputmode="numeric"
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none tracking-wide peer"
                                    placeholder=" "
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                >
                                <label for="idt" class="text-sm font-medium">Identidade (IDT) *</label>
                                <p class="mt-1 text-xs text-gray-500">Documento militar único (somente números). Não poderá ser alterado depois.</p>
                                @error('idt')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nome de Guerra -->
                            <div class="floating-label">
                                <input 
                                    type="text" 
                                    id="war_name" 
                                    name="war_name" 
                                    value="{{ old('war_name') }}"
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder=" "
                                >
                                <label for="war_name" class="text-sm font-medium">Nome de Guerra *</label>
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail *</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder="nome.sobrenome@exercito.mil.br"
                                >
                                <p class="text-xs text-gray-500 mt-1">Ex: nome.sobrenome@exercito.mil.br</p>
                            </div>

                            <!-- Senha -->
                            <div class="floating-label">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder=" "
                                >
                                <label for="password" class="text-sm font-medium">Senha *</label>
                            </div>

                            <!-- Confirmar Senha -->
                            <div class="floating-label">
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder=" "
                                >
                                <label for="password_confirmation" class="text-sm font-medium">Confirmar Senha *</label>
                            </div>
                        </div>
                    </div>

                    <!-- Dados Militares Section -->
                    <div class="form-section rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Dados Militares
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Posto/Graduação -->
                            <div>
                                <label for="rank_id" class="block text-sm font-medium text-gray-700 mb-2">Posto/Graduação *</label>
                                <select 
                                    id="rank_id" 
                                    name="rank_id" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                >
                                    <option value="">Selecione seu posto/graduação</option>
                                    @if(isset($ranks))
                                        @foreach($ranks as $rank)
                                            <option value="{{ $rank->id }}" {{ old('rank_id') == $rank->id ? 'selected' : '' }}>
                                                {{ $rank->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Força Armada -->
                            <div>
                                <label for="armed_force" class="block text-sm font-medium text-gray-700 mb-2">Força Armada *</label>
                                <select 
                                    id="armed_force" 
                                    name="armed_force" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    onchange="toggleOrganizationField()">
                                    <option value="">Selecione sua força armada</option>
                                    <option value="EB" {{ old('armed_force') == 'EB' ? 'selected' : '' }}>🪖 Exército Brasileiro (EB)</option>
                                    <option value="MB" {{ old('armed_force') == 'MB' ? 'selected' : '' }}>⚓ Marinha do Brasil (MB)</option>
                                    <option value="FAB" {{ old('armed_force') == 'FAB' ? 'selected' : '' }}>✈️ Força Aérea Brasileira (FAB)</option>
                                </select>
                            </div>

                            <!-- Organização Militar (condicional) -->
                            <div id="organization-field" style="display: none;">
                                <label for="organization_id" class="block text-sm font-medium text-gray-700 mb-2">Organização Militar *</label>
                                <select 
                                    id="organization_id" 
                                    name="organization_id" 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    onchange="toggleSectionField()"
                                >
                                    <option value="">Selecione sua organização</option>
                                    @if(isset($organizations))
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                                {{ $organization->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Campo Cia (condicional - apenas para 11º Depósito) -->
                            <div id="section-field" style="display: none;">
                                <label for="subunit" class="block text-sm font-medium text-gray-700 mb-2">Selecionar sua Cia</label>
                                <select 
                                    id="subunit" 
                                    name="subunit" 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                >
                                    <option value="">Selecione sua companhia</option>
                                    <option value="1ª Cia" {{ old('subunit') == '1ª Cia' ? 'selected' : '' }}>1ª Cia</option>
                                    <option value="2ª Cia" {{ old('subunit') == '2ª Cia' ? 'selected' : '' }}>2ª Cia</option>
                                    <option value="EM" {{ old('subunit') == 'EM' ? 'selected' : '' }}>EM</option>
                                </select>
                            </div>

                            <!-- Gênero -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gênero *</label>
                                <select 
                                    id="gender" 
                                    name="gender" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                >
                                    <option value="">Selecione seu gênero</option>
                                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Data Pronto OM Section -->
                    <div class="form-section rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Data de Prontidão
                        </h3>

                        <div class="max-w-md">
                <label for="ready_at_om_date_display" class="block text-sm font-medium text-gray-700 mb-2">Data Pronto OM *</label>
                <div class="relative max-w-xs">
                    <input type="text" 
                        id="ready_at_om_date_display"
                        value="{{ old('ready_at_om_date') ? date('d/m/Y', strtotime(old('ready_at_om_date'))) : '' }}"
                        class="input-modern w-full px-4 py-3 pr-12 rounded-lg border-2 focus:outline-none"
                        placeholder="dd/mm/yyyy"
                        readonly
                        style="cursor: pointer;">
                    <input type="date" 
                        name="ready_at_om_date" 
                        id="ready_at_om_date" 
                        value="{{ old('ready_at_om_date') }}"
                        class="absolute top-0 left-0 w-full h-full opacity-0 z-5 cursor-pointer"
                        max="{{ date('Y-m-d') }}"
                        onchange="updateDisplayDate(this)">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer z-30"
                      onclick="openDatePicker()"
                      title="Clique para abrir o calendário">
                     <svg class="w-5 h-5 text-gray-400 hover:text-green-500 transition-colors duration-200 pointer-events-none" 
                          fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            class="pointer-events-none"></path>
                     </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Data em que ficou pronto na Organização Militar</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-6">
                        <button 
                            type="submit" 
                            class="btn-primary w-full md:w-auto px-12 py-4 text-white font-semibold rounded-lg focus:outline-none focus:ring-4 focus:ring-green-300"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Criar Conta
                            </span>
                        </button>
                    </div>

                </form>

                <!-- Footer -->
                <div class="text-center mt-8 pt-6 border-t border-gray-200">
                    <p class="text-gray-600">
                        Já possui uma conta? 
                        <a href="{{ route('auth.traditional-login') }}" class="text-green-600 hover:text-green-700 font-semibold transition-colors duration-200">
                            Fazer Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOrganizationField() {
    const armedForceSelect = document.getElementById('armed_force');
    const organizationField = document.getElementById('organization-field');
    const organizationSelect = document.getElementById('organization_id');
    const sectionField = document.getElementById('section-field');
    
    if (armedForceSelect.value === 'EB') {
        // Mostrar campo de organização para Exército Brasileiro
        organizationField.style.display = 'block';
        organizationSelect.required = true;
        
        // Verificar se já tem organização selecionada para mostrar SU
        toggleSectionField();
    } else {
        // Ocultar campo de organização para outras forças
        organizationField.style.display = 'none';
        organizationSelect.required = false;
        organizationSelect.value = '';
        
        // Ocultar campo SU também
        sectionField.style.display = 'none';
        const sectionInputs = document.querySelectorAll('input[name="section"]');
        sectionInputs.forEach(input => input.checked = false);
    }
}

function toggleSectionField() {
    const organizationSelect = document.getElementById('organization_id');
    const sectionField = document.getElementById('section-field');
    const subunitSelect = document.getElementById('subunit');
    
    // Buscar o texto da opção selecionada
    const selectedOption = organizationSelect.options[organizationSelect.selectedIndex];
    const organizationName = selectedOption ? selectedOption.text : '';
    
    // Só mostrar o campo Cia se for "11º Depósito de Suprimento"
    if (organizationName.includes('11º Depósito de Suprimento')) {
        sectionField.style.display = 'block';
        subunitSelect.required = true;
    } else {
        sectionField.style.display = 'none';
        subunitSelect.required = false;
        subunitSelect.value = '';
    }
}

function openDatePicker() {
    const dateInput = document.getElementById('ready_at_om_date');
    dateInput.focus();
    if (dateInput.showPicker) {
        dateInput.showPicker();
    }
}

function updateDisplayDate(input) {
    const displayInput = document.getElementById('ready_at_om_date_display');
    if (input.value) {
        const date = new Date(input.value + 'T00:00:00');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        displayInput.value = `${day}/${month}/${year}`;
    } else {
        displayInput.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleOrganizationField();
    
    // Set initial date display if there's an old value
    const dateInput = document.getElementById('ready_at_om_date');
    if (dateInput.value) {
        updateDisplayDate(dateInput);
    }
});
</script>
@endsection
