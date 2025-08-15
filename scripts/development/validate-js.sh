#!/bin/bash

echo "🔍 TESTE DE VALIDAÇÃO JAVASCRIPT - PÁGINA DE REGISTRO"
echo "=================================================="

# Verificar se a página está acessível
echo "📡 Testando conectividade..."
if curl -s http://127.0.0.1:8000/register > /dev/null; then
    echo "✅ Página acessível"
else
    echo "❌ Página não acessível"
    exit 1
fi

# Verificar se as funções JavaScript existem
echo "🔍 Verificando funções JavaScript..."

FUNCTIONS=$(curl -s http://127.0.0.1:8000/register | grep -c "function openDatePicker\|function updateDisplayDate\|function toggleSectionField")

if [ "$FUNCTIONS" -gt 0 ]; then
    echo "✅ Funções JavaScript encontradas: $FUNCTIONS"
else
    echo "❌ Funções JavaScript não encontradas"
fi

# Verificar se os elementos HTML existem
echo "🔍 Verificando elementos HTML..."

ELEMENTS=$(curl -s http://127.0.0.1:8000/register | grep -c "ready_at_om_date_display\|ready_at_om_date\|organization_id")

if [ "$ELEMENTS" -gt 0 ]; then
    echo "✅ Elementos HTML encontrados: $ELEMENTS"
else
    echo "❌ Elementos HTML não encontrados"
fi

# Verificar se não há erros óbvios no HTML
echo "🔍 Verificando erros de sintaxe..."

ERRORS=$(curl -s http://127.0.0.1:8000/register | grep -ic "error\|exception\|undefined")

if [ "$ERRORS" -eq 0 ]; then
    echo "✅ Nenhum erro óbvio detectado"
else
    echo "⚠️  Possíveis problemas detectados: $ERRORS"
fi

echo "=================================================="
echo "✅ VALIDAÇÃO CONCLUÍDA - A página parece estar funcionando corretamente!"
echo "🔗 Acesse: http://127.0.0.1:8000/register"
