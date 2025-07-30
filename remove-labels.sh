#!/bin/bash

# Script para remover labels e melhorar placeholders

echo "Removendo labels e melhorando placeholders..."

# Função para processar cada arquivo
process_file() {
    local file=$1
    echo "Processando: $file"
    
    # Backup
    cp "$file" "$file.backup"
    
    # Remover labels completamente (incluindo as linhas vazias)
    sed -i '/<label.*class="label-enhanced">/,/<\/label>/d' "$file"
    
    # Melhorar placeholders específicos para cada campo
    sed -i 's/placeholder="Digite seu nome completo"/placeholder="Nome Completo"/g' "$file"
    sed -i 's/placeholder="Digite seu nome de guerra"/placeholder="Nome de Guerra"/g' "$file"
    sed -i 's/placeholder="seu.email@militar.gov.br"/placeholder="Email Institucional (ex: nome@exercito.mil.br)"/g' "$file"
    sed -i 's/placeholder="Mínimo 8 caracteres, incluindo números"/placeholder="Senha (mín. 8 caracteres)"/g' "$file"
    sed -i 's/placeholder="Repita a senha acima"/placeholder="Confirmar Senha"/g' "$file"
    sed -i 's/placeholder="Digite seu email institucional"/placeholder="Email Institucional"/g' "$file"
    sed -i 's/placeholder="Digite sua senha"/placeholder="Senha"/g' "$file"
    
    # Ajustar selects para ter placeholders como primeira opção
    sed -i 's/<option value="">Selecione seu posto\/graduação<\/option>/<option value="" disabled selected>Selecione seu Posto\/Graduação<\/option>/g' "$file"
    sed -i 's/<option value="">Selecione sua Organização Militar<\/option>/<option value="" disabled selected>Selecione sua Organização Militar<\/option>/g' "$file"
}

# Processar arquivos
process_file "resources/views/auth/register.blade.php"
process_file "resources/views/auth/traditional-login.blade.php"
process_file "resources/views/auth/complete-registration.blade.php"

echo "Labels removidos e placeholders melhorados!"
echo "Processo concluído!"
