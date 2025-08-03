<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SAGA</title>
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
                <svg class="w-10 h-10 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C9,20 10,19 10,18C10,17 9,16 8,16C7,16 6,17 6,18C6,18.5 6.2,19 6.5,19.4L5.5,21.5C5.1,21.3 4.8,21 4.5,20.5C4.2,20 4,19.5 4,19C4,17.5 4.8,16.2 6,15.5L7,14C8,13 9.5,12.5 11,12.5C12.5,12.5 14,13 15,14L16,15.5C17.2,16.2 18,17.5 18,19C18,19.5 17.8,20 17.5,20.5C17.2,21 16.9,21.3 16.5,21.5L15.5,19.4C15.8,19 16,18.5 16,18C16,17 15,16 14,16C13,16 12,17 12,18C12,19 13,20 14,20C14.36,20 14.86,19.87 15.34,19.7L16.29,22L18.18,21.34C16.1,16.17 14,10 17,8Z"/>
                </svg>
                <h1 class="ml-2 text-2xl font-bold text-gray-900">SAGA</h1>
            </div>
            <p class="text-sm text-gray-600 text-center mt-1">Sistema de Agendamento e Gestão de Arranchamento</p>
        </div>

        <!-- Login Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Entrar com Email e Senha</h2>
                <p class="mt-1 text-sm text-gray-600">Faça login em sua conta</p>
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
                            <h3 class="text-sm font-medium text-red-800">Erro no login</h3>
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

            <!-- Login Form -->
            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                
                <!-- Email -->
                <div class="field-group">
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        value="{{ old('email') }}"
                        class="input-enhanced"
                        placeholder="Email Institucional (ex: nome@exercito.mil.br)"
                    >
                </div>

                <!-- Password -->
                <div class="field-group">
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required 
                        class="input-enhanced"
                        placeholder="Senha"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="checkbox-enhanced"
                        >
                        <label for="remember" class="ml-3 block text-sm font-medium text-gray-900">
                            Lembrar de mim
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mb-6">
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Entrar
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-gray-300"></span>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">ou</span>
                </div>
            </div>

            <!-- Other Options -->
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🔄 Voltar para Google OAuth
                </a>
                
                <a href="{{ route('auth.register') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ➕ Criar Nova Conta
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
