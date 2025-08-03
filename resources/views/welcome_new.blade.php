<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAGA - Sistema de Agendamento e Gestão de Arranchamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo e Título -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('images/folhaint_transparent.png') }}" alt="11º D Sup Logo" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">SAGA</h1>
            <p class="text-lg text-gray-600 mb-2">Sistema de Agendamento e Gestão de Arranchamento</p>
            <p class="text-sm text-gray-500">11º D Sup</p>
        </div>

        <!-- Descrição -->
        <div class="w-full sm:max-w-md bg-white shadow-md overflow-hidden sm:rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 text-center">Bem-vindo</h2>
            <div class="text-gray-600 text-sm space-y-2">
                <p>Sistema militar para agendamento de refeições do 11º D Sup.</p>
                
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Funcionalidades:</h3>
                    <ul class="space-y-1 text-xs">
                        <li>• Agendamento de café da manhã e almoço</li>
                        <li>• Calendário de reservas</li>
                        <li>• Relatórios e estatísticas</li>
                        <li>• Gestão de usuários</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Botão de Login -->
        <div class="w-full sm:max-w-md">
            <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Fazer Login
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-500">
            <p>Acesso restrito a militares autorizados</p>
            <p class="mt-1">© 2025 SAGA - Todos os direitos reservados</p>
        </div>
    </div>
</body>
</html>
