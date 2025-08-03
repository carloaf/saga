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
                <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C9,20 10,19 10,18C10,17 9,16 8,16C7,16 6,17 6,18C6,18.5 6.2,19 6.5,19.4L5.5,21.5C5.1,21.3 4.8,21 4.5,20.5C4.2,20 4,19.5 4,19C4,17.5 4.8,16.2 6,15.5L7,14C8,13 9.5,12.5 11,12.5C12.5,12.5 14,13 15,14L16,15.5C17.2,16.2 18,17.5 18,19C18,19.5 17.8,20 17.5,20.5C17.2,21 16.9,21.3 16.5,21.5L15.5,19.4C15.8,19 16,18.5 16,18C16,17 15,16 14,16C13,16 12,17 12,18C12,19 13,20 14,20C14.36,20 14.86,19.87 15.34,19.7L16.29,22L18.18,21.34C16.1,16.17 14,10 17,8Z"/>
                </svg>
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
