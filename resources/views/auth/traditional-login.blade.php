@extends('layouts.app')

@section('content')
<style>
    /* Modern Military Login UI Styling */
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

    .floating-label {
        position: relative;
    }

    .floating-label input:focus + label,
    .floating-label input:not(:placeholder-shown) + label {
        transform: translateY(-26px) scale(0.9);
        color: #16a34a;
        font-weight: 600;
    }

    .floating-label label {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
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
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(22, 163, 74, 0.4);
    }

    .btn-google {
        background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
        transition: all 0.3s ease;
        border: none;
    }

    .btn-google:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 15px rgba(66, 133, 244, 0.3);
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

        <!-- Login Card -->
        <div class="w-full max-w-md relative z-10 mx-auto">
            <div class="card-enhanced rounded-2xl px-8 py-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Login Tradicional</h2>
                    <p class="text-gray-600">Entre com seu email e senha</p>
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
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Erro no login:</h3>
                                <ul class="list-disc list-inside space-y-1 text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('auth.login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email -->
                    <div>
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

                    <!-- Password -->
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
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Lembrar de mim
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="text-green-600 hover:text-green-700 font-medium">
                                Esqueci minha senha
                            </a>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-6">
                        <button 
                            type="submit" 
                            class="btn-primary w-full px-6 py-3 text-white font-semibold rounded-lg focus:outline-none focus:ring-4 focus:ring-green-300"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Entrar
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Register Button -->
                    <a href="{{ route('auth.register') }}" class="w-full px-6 py-3 border-2 border-green-600 text-green-600 font-semibold rounded-lg hover:bg-green-50 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Criar Nova Conta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
    </div>
</body>
</html>
