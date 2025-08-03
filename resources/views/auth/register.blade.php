<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - SAGA</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/enhanced-forms.css') }}" rel="stylesheet">
    <style>
        @import url('https://cdn.jsdelivr.net/npm/@tailwindcss/forms@0.5.3/dist/forms.min.css');
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6">
            <div class="flex items-center">
                <img src="{{ asset('images/folhaint_transparent.png') }}" alt="11º D Sup Logo" class="w-10 h-10 object-contain">
                <h1 class="ml-2 text-2xl font-bold text-gray-900">SAGA</h1>
            </div>
            <p class="text-sm text-gray-600 text-center mt-1">Sistema de Agendamento e Gestão de Arranchamento</p>
        </div>

        <!-- Register Card -->
        <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Criar Nova Conta</h2>
                <p class="mt-1 text-sm text-gray-600">Preencha os dados para se cadastrar</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erro no cadastro</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('auth.register') }}">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nome Completo -->
                    <div class="field-group">
                        <input 
                            id="full_name" 
                            name="full_name" 
                            type="text" 
                            required 
                            value="{{ old('full_name') }}"
                            class="input-enhanced"
                            placeholder="Nome Completo (ex: João Silva Santos)"
                        >
                    </div>

                    <!-- Nome de Guerra -->
                    <div class="field-group">
                        <input 
                            id="war_name" 
                            name="war_name" 
                            type="text" 
                            required 
                            value="{{ old('war_name') }}"
                            class="input-enhanced"
                            placeholder="Nome de Guerra (ex: Silva, Santos)"
                        >
                    </div>

                    <!-- Email -->
                    <div class="field-group">
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="input-enhanced"
                            placeholder="Email Institucional (ex: nome@exercito.mil.br)"
                        >
                    </div>

                    <!-- Senha -->
                    <div class="field-group">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="input-enhanced"
                            placeholder="Senha (mín. 8 caracteres)"
                        >
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="field-group">
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required 
                            class="input-enhanced"
                            placeholder="Confirmar Senha"
                        >
                    </div>

                    <!-- Posto/Graduação -->
                    <div class="field-group">
                        <select 
                            id="rank_id" 
                            name="rank_id" 
                            required 
                            class="select-enhanced"
                        >
                            <option value="" disabled selected>Selecione seu Posto/Graduação</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->id }}" {{ old('rank_id') == $rank->id ? 'selected' : '' }}>
                                    {{ $rank->abbreviation }} - {{ $rank->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Organização Militar -->
                    <div class="field-group">
                        <select 
                            id="organization_id" 
                            name="organization_id" 
                            required 
                            class="select-enhanced"
                        >
                            <option value="" disabled selected>Selecione sua Organização Militar</option>
                            @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->abbreviation }} - {{ $organization->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sexo -->
                    <div class="field-group">
                        <div class="mt-3 space-y-3">
                            <div class="flex items-center">
                                <input 
                                    id="male" 
                                    name="gender" 
                                    type="radio" 
                                    value="male" 
                                    {{ old('gender') == 'male' ? 'checked' : '' }}
                                    class="radio-enhanced"
                                >
                                <label for="male" class="ml-3 block text-sm font-medium text-gray-900">Masculino</label>
                            </div>
                            <div class="flex items-center">
                                <input 
                                    id="female" 
                                    name="gender" 
                                    type="radio" 
                                    value="female" 
                                    {{ old('gender') == 'female' ? 'checked' : '' }}
                                    class="radio-enhanced"
                                >
                                <label for="female" class="ml-3 block text-sm font-medium text-gray-900">Feminino</label>
                            </div>
                        </div>
                    </div>

                    <!-- Data de Apresentação na OM -->
                    <div class="field-group">
                        <input 
                            id="ready_at_om_date" 
                            name="ready_at_om_date" 
                            type="date" 
                            required 
                            value="{{ old('ready_at_om_date') }}"
                            class="input-enhanced"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Criar Conta
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-gray-300"></span>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">ou</span>
                </div>
            </div>

            <!-- Other Options -->
            <div class="space-y-3">
                <a href="{{ route('auth.traditional-login') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🔑 Já tenho conta - Fazer Login
                </a>
                
                <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🔄 Voltar para Google OAuth
                </a>
            </div>

            <!-- Info -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    Sistema restrito para militares autorizados
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-500">
            © 2025 SAGA - 11º D Sup
        </div>
    </div>
</body>
</html>
