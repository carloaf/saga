@extends('layouts.app')

@section('content')
<style>
    /* Modern Military Register UI Styling */
    .page-container {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        margin: 16px;
    }

    /* Animated Background Pattern */
    .page-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(22, 163, 74, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 20%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
        animation: float 20s ease-in-out infinite;
    }

    .page-container::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            repeating-linear-gradient(
                90deg,
                transparent,
                transparent 50px,
                rgba(255, 255, 255, 0.02) 50px,
                rgba(255, 255, 255, 0.02) 51px
            ),
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 50px,
                rgba(255, 255, 255, 0.02) 50px,
                rgba(255, 255, 255, 0.02) 51px
            );
        opacity: 0.3;
    }

    /* Background Logo Watermark - Cascata Diagonal */
    .bg-logo-cascade {
        position: absolute;
        width: 48px;
        height: 48px;
        background-image: url('{{ asset('images/folhaint_transparent.png') }}');
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.05;
        z-index: 0;
        animation: float-cascade 15s ease-in-out infinite;
    }

    .bg-logo-cascade:nth-child(1) {
        top: 5%;
        left: 5%;
        animation-delay: 0s;
    }

    .bg-logo-cascade:nth-child(2) {
        top: 15%;
        left: 15%;
        animation-delay: 0.5s;
    }

    .bg-logo-cascade:nth-child(3) {
        top: 25%;
        left: 25%;
        animation-delay: 1s;
    }

    .bg-logo-cascade:nth-child(4) {
        top: 35%;
        left: 35%;
        animation-delay: 1.5s;
    }

    .bg-logo-cascade:nth-child(5) {
        top: 45%;
        left: 45%;
        animation-delay: 2s;
    }

    .bg-logo-cascade:nth-child(6) {
        top: 55%;
        left: 55%;
        animation-delay: 2.5s;
    }

    .bg-logo-cascade:nth-child(7) {
        top: 65%;
        left: 65%;
        animation-delay: 3s;
    }

    .bg-logo-cascade:nth-child(8) {
        top: 75%;
        left: 75%;
        animation-delay: 3.5s;
    }

    .bg-logo-cascade:nth-child(9) {
        top: 85%;
        left: 85%;
        animation-delay: 4s;
    }

    .bg-logo-cascade:nth-child(10) {
        top: 10%;
        right: 10%;
        animation-delay: 4.5s;
    }

    .bg-logo-cascade:nth-child(11) {
        top: 30%;
        right: 20%;
        animation-delay: 5s;
    }

    .bg-logo-cascade:nth-child(12) {
        top: 50%;
        right: 30%;
        animation-delay: 5.5s;
    }

    .bg-logo-cascade:nth-child(13) {
        top: 70%;
        right: 15%;
        animation-delay: 6s;
    }

    .bg-logo-cascade:nth-child(14) {
        top: 90%;
        right: 5%;
        animation-delay: 6.5s;
    }

    @keyframes float-cascade {
        0%, 100% { 
            transform: translateY(0px) rotate(0deg);
            opacity: 0.05;
        }
        50% { 
            transform: translateY(-15px) rotate(5deg);
            opacity: 0.08;
        }
    }

    @keyframes float {
        0%, 100% { 
            transform: translateY(0px) scale(1); 
            opacity: 0.3;
        }
        50% { 
            transform: translateY(-30px) scale(1.1); 
            opacity: 0.5;
        }
    }

    .card-enhanced {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 
            0 25px 45px rgba(0, 0, 0, 0.2),
            0 0 80px rgba(22, 163, 74, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    /* Header Glow Effect */
    .header-glow {
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.5),
                     0 0 40px rgba(22, 163, 74, 0.3);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { 
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5),
                         0 0 40px rgba(22, 163, 74, 0.3);
        }
        50% { 
            text-shadow: 0 0 30px rgba(255, 255, 255, 0.7),
                         0 0 60px rgba(22, 163, 74, 0.5);
        }
    }

    /* Logo Glow */
    .logo-glow {
        filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5))
                drop-shadow(0 0 20px rgba(22, 163, 74, 0.3));
    }

    /* Divider Animation */
    .divider-animated {
        animation: expand 1s ease-out;
    }

    @keyframes expand {
        from {
            width: 0;
            opacity: 0;
        }
        to {
            width: 96px;
            opacity: 1;
        }
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

    /* Custom Select Styling */
    .custom-select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem;
        padding-right: 2.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .custom-select:hover {
        border-color: #10b981;
    }

    .custom-select:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* Remove default select styling for webkit browsers */
    .custom-select::-webkit-scrollbar {
        width: 8px;
    }

    .custom-select::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .custom-select::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .custom-select::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Date Picker Styling */
    .date-picker-container {
        position: relative;
    }

    .date-picker-container input[type="date"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 5;
    }

    .date-picker-container .date-display {
        pointer-events: none;
    }

    .date-picker-container .date-icon {
        pointer-events: none;
        z-index: 10;
    }

    /* Form Field Animations */
    .form-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    /* Input Modern Styling */
    .input-modern {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .input-modern:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .input-modern:hover {
        border-color: #10b981;
    }

    /* Enhanced Button Hover Effects */
    .btn-primary {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(22, 163, 74, 0.3);
    }
</style>

<div class="page-container flex items-center justify-center p-4">
    <!-- Background Logo Watermark - Cascata Diagonal -->
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    <div class="bg-logo-cascade"></div>
    
    <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8 relative z-10">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('images/folhaint_transparent.png') }}" alt="11º D Sup Logo" class="w-12 h-12 object-contain mr-3 logo-glow">
                <h1 class="text-4xl font-bold text-white header-glow">Sistema SAGA</h1>
            </div>
            <p class="text-xl text-blue-100 mb-2">Sistema de Agendamento e Gestão de Arranchamento</p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 via-green-300 to-green-400 mx-auto mt-4 rounded-full divider-animated"></div>
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
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    name="full_name" 
                                    value="{{ old('full_name') }}"
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder="Digite seu nome completo"
                                >
                            </div>

                            <!-- Identidade (IDT) -->
                            <div>
                                <label for="idt" class="block text-sm font-medium text-gray-700 mb-2">Identidade (IDT) *</label>
                                <input 
                                    type="text" 
                                    id="idt" 
                                    name="idt" 
                                    value="{{ old('idt') }}"
                                    required 
                                    maxlength="30"
                                    pattern="[0-9]*"
                                    inputmode="numeric"
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none tracking-wide"
                                    placeholder="Digite somente números"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                >
                                <p class="mt-1 text-xs text-gray-500">Documento militar único (somente números). Não poderá ser alterado depois.</p>
                                @error('idt')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nome de Guerra -->
                            <div>
                                <label for="war_name" class="block text-sm font-medium text-gray-700 mb-2">Nome de Guerra *</label>
                                <input 
                                    type="text" 
                                    id="war_name" 
                                    name="war_name" 
                                    value="{{ old('war_name') }}"
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder="Digite seu nome de guerra"
                                >
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
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha *</label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder="Digite sua senha"
                                >
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha *</label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required 
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    placeholder="Confirme sua senha"
                                >
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
                                <label for="rank_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-yellow-500 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>Posto/Graduação *
                                </label>
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
                                <label for="armed_force" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-500 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>Força Armada *
                                </label>
                                <div class="relative">
                                    <select
                                        id="armed_force"
                                        name="armed_force"
                                        required
                                        class="custom-select w-full px-4 py-3 rounded-lg border-2 focus:outline-none focus:border-green-500 bg-white"
                                        onchange="toggleOrganizationField()">
                                        <option value="">Selecione sua força armada</option>
                                        <option value="EB" {{ old('armed_force') == 'EB' ? 'selected' : '' }}>🏰 Exército Brasileiro (EB)</option>
                                        <option value="MB" {{ old('armed_force') == 'MB' ? 'selected' : '' }}>⚓ Marinha do Brasil (MB)</option>
                                        <option value="FAB" {{ old('armed_force') == 'FAB' ? 'selected' : '' }}>✈️ Força Aérea Brasileira (FAB)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Organização Militar (condicional) -->
                            <div id="organization-field" style="display: none;">
                                <label for="organization_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-purple-500 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                    </svg>Organização Militar *
                                </label>
                                <select
                                    id="organization_id"
                                    name="organization_id"
                                    class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                                    onchange="toggleSectionField()"
                                >
                                    <option value="">Selecione sua organização</option>
                                    @if(isset($organizations))
                                        @foreach($organizations as $organization)
                                            @php
                                                $is11DepSup = $organization->name === '11º D Sup';
                                            @endphp
                                            <option
                                                value="{{ $organization->id }}"
                                                data-name="{{ $organization->name }}"
                                                data-is-11dsup="{{ $is11DepSup ? '1' : '0' }}"
                                                {{ old('organization_id') == $organization->id ? 'selected' : '' }}
                                            >
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
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-pink-500 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0zM10 6a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM8.5 10.5a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd"/>
                                    </svg>Gênero *
                                </label>
                                <div class="relative">
                                    <select
                                        id="gender"
                                        name="gender"
                                        required
                                        class="custom-select w-full px-4 py-3 rounded-lg border-2 focus:outline-none focus:border-green-500 bg-white">
                                        <option value="">Selecione seu gênero</option>
                                        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>♂️ Masculino</option>
                                        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>♀️ Feminino</option>
                                    </select>
                                </div>
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
                <label for="ready_at_om_date_display" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-orange-500 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>Data Pronto OM *
                </label>
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
    
    if (!organizationSelect || !sectionField || !subunitSelect) {
        return;
    }
    
    // Buscar o texto da opção selecionada
    const selectedOption = organizationSelect.options[organizationSelect.selectedIndex];
    const is11DepSup = selectedOption && selectedOption.getAttribute('data-is-11dsup') === '1';
    
    // Só mostrar o campo Cia se for "11º D Sup"
    if (is11DepSup) {
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
    if (dateInput) {
        dateInput.focus();
        // Tentar abrir o date picker nativo
        if (dateInput.showPicker) {
            dateInput.showPicker();
        } else {
            // Fallback para navegadores que não suportam showPicker
            dateInput.click();
        }
    }
}

function updateDisplayDate(input) {
    const displayInput = document.getElementById('ready_at_om_date_display');
    if (input && input.value && displayInput) {
        const date = new Date(input.value + 'T00:00:00');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        displayInput.value = `${day}/${month}/${year}`;
    } else if (displayInput) {
        displayInput.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('SAGA registration form initialized successfully!');

    // Initialize form field visibility
    toggleOrganizationField();

    // Set initial date display if there's an old value
    const dateInput = document.getElementById('ready_at_om_date');
    if (dateInput && dateInput.value) {
        updateDisplayDate(dateInput);
    }

    // Add event listener to date input for real-time updates
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            updateDisplayDate(this);
        });
    }

    // Add click event to date display to open picker
    const dateDisplay = document.getElementById('ready_at_om_date_display');
    if (dateDisplay) {
        dateDisplay.addEventListener('click', function() {
            openDatePicker();
        });
    }
});
</script>
@endsection
