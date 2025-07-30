#!/bin/bash

# Script para corrigir ícones gigantes em todas as views

echo "Corrigindo ícones SVG para design simplificado..."

# Remover styles inline antigos e aplicar novos
sed -i 's/style="width: 14px; height: 14px;"//g' resources/views/auth/register.blade.php
sed -i 's/style="width: 14px; height: 14px;"//g' resources/views/auth/traditional-login.blade.php
sed -i 's/style="width: 14px; height: 14px;"//g' resources/views/auth/complete-registration.blade.php

# Adicionar novo style inline para todos os SVGs
sed -i 's/<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">/<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">/g' resources/views/auth/register.blade.php
sed -i 's/<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">/<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">/g' resources/views/auth/traditional-login.blade.php
sed -i 's/<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">/<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">/g' resources/views/auth/complete-registration.blade.php

echo "Ícones SVG corrigidos para 16px!"

# Verificar se as alterações foram aplicadas
echo "Verificando correções..."
grep -n "style=\"width: 16px; height: 16px;\"" resources/views/auth/register.blade.php | wc -l
grep -n "style=\"width: 16px; height: 16px;\"" resources/views/auth/traditional-login.blade.php | wc -l
grep -n "style=\"width: 16px; height: 16px;\"" resources/views/auth/complete-registration.blade.php | wc -l

echo "Design simplificado aplicado!"
