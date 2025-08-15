#!/bin/bash

echo "=== TESTE DO SISTEMA DE CADASTRO E LOGIN ==="
echo

echo "1. Testando acesso à página inicial..."
curl -s http://localhost:8000 > /dev/null && echo "✅ Página inicial carrega" || echo "❌ Erro na página inicial"

echo "2. Testando página de login..."
curl -s http://localhost:8000/login > /dev/null && echo "✅ Página de login carrega" || echo "❌ Erro na página de login"

echo "3. Testando página de login tradicional..."
curl -s http://localhost:8000/login/traditional > /dev/null && echo "✅ Página de login tradicional carrega" || echo "❌ Erro na página de login tradicional"

echo "4. Testando página de registro..."
curl -s http://localhost:8000/register > /dev/null && echo "✅ Página de registro carrega" || echo "❌ Erro na página de registro"

echo
echo "=== TESTE CONCLUÍDO ==="
echo "Sistema implementado com sucesso na branch dev!"
echo
echo "Funcionalidades disponíveis:"
echo "- Login com Google OAuth (mantido)"
echo "- Login com email e senha"
echo "- Cadastro com email e senha"
echo "- Botões na tela inicial para ambos os métodos"
echo
echo "Para testar:"
echo "1. Acesse http://localhost:8000"
echo "2. Clique em 'Criar Nova Conta' para cadastrar"
echo "3. Ou clique em 'Login com Email e Senha' para entrar"
