<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - SAGA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .military-gradient {
            background: linear-gradient(135deg, #1a472a 0%, #2d5a3d 100%);
        }
        
        .input-modern {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }
        
        .input-modern:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 military-gradient rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Recuperar Senha</h2>
            <p class="mt-2 text-sm text-gray-600">
                Digite sua identidade militar e email para recuperar sua senha
            </p>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm text-red-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- IDT Field -->
                <div>
                    <label for="idt" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2"></i>Identidade Militar (IDT) *
                    </label>
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
                        placeholder="Digite sua identidade militar"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >
                    <p class="text-xs text-gray-500 mt-1">Digite apenas números (ex: 123456789)</p>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email Cadastrado *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required 
                        class="input-modern w-full px-4 py-3 rounded-lg border-2 focus:outline-none"
                        placeholder="Digite seu email cadastrado"
                    >
                    <p class="text-xs text-gray-500 mt-1">Ex: nome.sobrenome@exercito.mil.br</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="btn-primary w-full py-3 px-4 rounded-lg text-white font-semibold text-sm"
                >
                    <i class="fas fa-check-circle mr-2"></i>
                    Verificar Dados e Continuar
                </button>
            </div>

            <!-- Links -->
            <div class="text-center space-y-2">
                <a href="{{ route('auth.traditional-login') }}" 
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Voltar ao Login
                </a>
                
                <div class="text-sm text-gray-500">
                    Não tem conta? 
                    <a href="{{ route('auth.register') }}" class="text-green-600 hover:text-green-700 font-medium">
                        Cadastre-se
                    </a>
                </div>
            </div>
        </form>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Como funciona?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Digite sua identidade militar (IDT) e email cadastrado</li>
                            <li>Se os dados estiverem corretos, você será direcionado para a página de redefinição</li>
                            <li>Defina sua nova senha</li>
                            <li>Faça login com sua nova senha</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
