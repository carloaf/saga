#!/usr/bin/env python3

from PIL import Image
import os

def remove_background(input_path, output_path):
    """
    Remove fundo branco/claro de uma imagem e salva com transparência
    """
    try:
        # Abrir a imagem
        img = Image.open(input_path)
        
        # Converter para RGBA se não estiver
        img = img.convert("RGBA")
        
        # Obter dados dos pixels
        data = img.getdata()
        
        # Nova lista de pixels
        new_data = []
        
        # Processar cada pixel
        for item in data:
            # Se o pixel for branco ou muito claro (tolerância), torná-lo transparente
            # Ajuste os valores conforme necessário
            if item[0] > 240 and item[1] > 240 and item[2] > 240:  # RGB muito claro
                new_data.append((255, 255, 255, 0))  # Transparente
            else:
                new_data.append(item)  # Manter o pixel original
        
        # Aplicar os novos dados
        img.putdata(new_data)
        
        # Salvar como PNG com transparência
        img.save(output_path, "PNG")
        print(f"✅ Fundo removido com sucesso! Salvo em: {output_path}")
        
    except Exception as e:
        print(f"❌ Erro ao processar imagem: {e}")

# Caminhos dos arquivos
input_image = "/home/sonnote/Pictures/folhaint.png"
output_image = "/home/sonnote/Documents/saga/public/images/folhaint_transparent.png"

# Verificar se o arquivo de entrada existe
if os.path.exists(input_image):
    print(f"🔄 Processando imagem: {input_image}")
    remove_background(input_image, output_image)
else:
    print(f"❌ Arquivo não encontrado: {input_image}")
