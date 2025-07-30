#!/bin/bash

# Script para remover ícones SVG dos labels

echo "Removendo ícones SVG dos labels..."

# Função para processar arquivo
process_file() {
    local file=$1
    echo "Processando: $file"
    
    # Backup do arquivo
    cp "$file" "$file.backup"
    
    # Remover SVG e manter apenas o span
    sed -i '/<svg class="icon"/,/<\/svg>/d' "$file"
    
    # Limpar spans vazios e ajustar labels
    sed -i 's/<label for="\([^"]*\)" class="label-enhanced">[ ]*<span>/&/g' "$file"
    sed -i 's/<label class="label-enhanced">[ ]*<span>/&/g' "$file"
}

# Processar arquivos
process_file "resources/views/auth/register.blade.php"
process_file "resources/views/auth/traditional-login.blade.php"  
process_file "resources/views/auth/complete-registration.blade.php"

echo "Ícones removidos!"

# Verificar resultado
echo "Verificando..."
grep -c "svg" resources/views/auth/register.blade.php | head -1
grep -c "svg" resources/views/auth/traditional-login.blade.php | head -1
grep -c "svg" resources/views/auth/complete-registration.blade.php | head -1

echo "Processo concluído!"
