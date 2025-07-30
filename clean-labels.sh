#!/bin/bash

# Script para limpar spans dos labels

echo "Limpando spans dos labels..."

# Função para processar arquivo
clean_spans() {
    local file=$1
    echo "Limpando spans em: $file"
    
    # Remover tags span e manter apenas o texto
    sed -i 's/<span>\([^<]*\)<\/span>/\1/g' "$file"
    
    # Limpar espaços extras
    sed -i 's/class="label-enhanced">[ ]*\([^<]*\)[ ]*$/class="label-enhanced">\n                            \1\n                        /g' "$file"
}

# Processar arquivos
clean_spans "resources/views/auth/register.blade.php"
clean_spans "resources/views/auth/traditional-login.blade.php"  
clean_spans "resources/views/auth/complete-registration.blade.php"

echo "Labels limpos!"

echo "Processo concluído - labels agora são apenas texto simples!"
