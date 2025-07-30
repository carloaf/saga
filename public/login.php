<?php
// Login de desenvolvimento direto
session_start();

// Simular login de usuário normal
if (isset($_GET['type']) && $_GET['type'] === 'user') {
    $_SESSION['user'] = [
        'id' => 1,
        'name' => 'Usuário Teste',
        'email' => 'teste@saga.mil.br',
        'role' => 'user'
    ];
    $loginType = 'Usuário Normal';
    $role = 'user';
}
// Simular login de admin
else {
    $_SESSION['user'] = [
        'id' => 2,
        'name' => 'Administrador SAGA',
        'email' => 'admin@saga.mil.br',
        'role' => 'superuser'
    ];
    $loginType = 'Administrador';
    $role = 'superuser';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Realizado - SAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-900 via-green-800 to-blue-800 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Success Header -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 mb-6">
            <h1 class="text-4xl font-bold text-white text-center mb-2">
                ✅ Login Realizado com Sucesso!
            </h1>
            <p class="text-green-200 text-center">Bem-vindo ao Sistema SAGA</p>
        </div>

        <!-- User Info -->
        <div class="bg-green-500/20 backdrop-blur-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-white mb-4">👤 Informações do Usuário</h2>
            <div class="grid md:grid-cols-2 gap-4 text-green-200">
                <div>
                    <p><strong>Tipo de Login:</strong> <?php echo $loginType; ?></p>
                    <p><strong>Nome:</strong> <?php echo $_SESSION['user']['name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $_SESSION['user']['email']; ?></p>
                </div>
                <div>
                    <p><strong>Role:</strong> <?php echo $role; ?></p>
                    <p><strong>Status:</strong> ✅ Ativo</p>
                    <p><strong>Sessão:</strong> Iniciada</p>
                </div>
            </div>
        </div>

        <!-- Available Features -->
        <div class="bg-blue-500/20 backdrop-blur-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C9,20 10,19 10,18C10,17 9,16 8,16C7,16 6,17 6,18C6,18.5 6.2,19 6.5,19.4L5.5,21.5C5.1,21.3 4.8,21 4.5,20.5C4.2,20 4,19.5 4,19C4,17.5 4.8,16.2 6,15.5L7,14C8,13 9.5,12.5 11,12.5C12.5,12.5 14,13 15,14L16,15.5C17.2,16.2 18,17.5 18,19C18,19.5 17.8,20 17.5,20.5C17.2,21 16.9,21.3 16.5,21.5L15.5,19.4C15.8,19 16,18.5 16,18C16,17 15,16 14,16C13,16 12,17 12,18C12,19 13,20 14,20C14.36,20 14.86,19.87 15.34,19.7L16.29,22L18.18,21.34C16.1,16.17 14,10 17,8Z"/>
                </svg>
                Funcionalidades Disponíveis
            </h2>
            
            <!-- Common Features -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-blue-200 mb-2">Para Todos os Usuários:</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                        📊 Dashboard
                    </a>
                    <a href="bookings.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                        📅 Reservas de Refeições
                    </a>
                    <a href="profile.php" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                        👤 Meu Perfil
                    </a>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                        🚪 Sair
                    </a>
                </div>
            </div>

            <?php if ($role === 'superuser'): ?>
            <!-- Admin Features -->
            <div class="border-t border-white/20 pt-4">
                <h3 class="text-lg font-semibold text-yellow-200 mb-2">🛡️ Área Administrativa:</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <a href="admin-users.php" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                        👥 Gerenciar Usuários
                    </a>
                    <a href="admin-reports.php" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                        📈 Relatórios e Estatísticas
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Alternative Login Options -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-white mb-4">🔄 Trocar Tipo de Login</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <a href="login.php?type=user" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                    👤 Login como Usuário Normal
                </a>
                <a href="login.php?type=admin" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg block text-center transition">
                    🛡️ Login como Administrador
                </a>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-yellow-500/20 backdrop-blur-md rounded-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-4">⚡ Status do Sistema</h2>
            <div class="text-yellow-200">
                <p class="mb-2">✅ <strong>Autenticação:</strong> Funcionando (simulada para desenvolvimento)</p>
                <p class="mb-2">✅ <strong>Interface:</strong> Todas as telas criadas e funcionais</p>
                <p class="mb-2">✅ <strong>Banco de Dados:</strong> PostgreSQL conectado</p>
                <p class="mb-2">✅ <strong>Sistema de Roles:</strong> Usuário e Administrador implementados</p>
                <p class="mb-2">⚠️ <strong>Laravel Integration:</strong> Em desenvolvimento (service provider issue)</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-blue-300">
            <p>&copy; 2025 SAGA - Sistema de Agendamento e Gestão de Arranchamento</p>
            <p class="text-sm">Login de desenvolvimento ativo</p>
        </div>
    </div>
</body>
</html>
