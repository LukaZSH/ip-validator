#!/bin/bash

# Script para automatizar a atualização da imagem Docker da aplicação
# a partir do GitHub Container Registry (GHCR)

# --- Variáveis ---
# Substitua pelo seu nome de usuário ou organização no GitHub
USERNAME="LukaZSH"
# Nome da imagem no GHCR
IMAGE_NAME="ip-validator"
# Tag da imagem a ser puxada (geralmente 'latest' ou uma versão específica)
TAG="latest"

# Caminho para o arquivo docker-compose.yml
COMPOSE_FILE_PATH="/var/www/ip-validator/docker-compose.yml"

# --- Lógica ---

echo "\n[+] Logando no GitHub Container Registry..."
# É esperado que o Personal Access Token (PAT) seja passado via stdin
# Exemplo de uso: cat ~/gh_token.txt | ./atualizar-imagem-GHCR.sh
cat - | docker login ghcr.io -u $USERNAME --password-stdin

if [ $? -ne 0 ]; then
    echo "\n[!] Falha no login do Docker. Verifique seu token e permissões."
    exit 1
fi

echo "\n[+] Puxando a imagem mais recente: ghcr.io/$USERNAME/$IMAGE_NAME:$TAG"
docker pull ghcr.io/$USERNAME/$IMAGE_NAME:$TAG

if [ $? -ne 0 ]; then
    echo "\n[!] Falha ao puxar a imagem. Verifique o nome da imagem, tag e se ela existe no GHCR."
    exit 1
fi

echo "\n[+] Parando e recriando os contêineres com a nova imagem..."
# Usa o caminho completo para o arquivo de compose para garantir que o comando funcione de qualquer diretório
docker-compose -f $COMPOSE_FILE_PATH up -d --force-recreate

if [ $? -ne 0 ]; then
    echo "\n[!] Falha ao reiniciar os contêineres com o docker-compose."
    exit 1
fi

echo "\n[+] Limpando imagens antigas e não utilizadas do Docker..."
docker image prune -af

echo "\n[+] Atualização concluída com sucesso!"