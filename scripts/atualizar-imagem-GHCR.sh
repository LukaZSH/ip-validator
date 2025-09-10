#!/bin/bash

echo "Iniciando a atualização da aplicação ip-validator..."

# Carrega as variáveis de ambiente do arquivo .env
if [ -f .env ]; then
    echo "Carregando variáveis de ambiente do .env..."
    export $(grep -v '^#' .env | xargs)
else
    echo "AVISO: Arquivo .env não encontrado!"
fi

echo "Atualizando o repositório com 'git pull'..."
git pull origin master

echo "Parando os contêineres atuais"
sudo -E docker-compose down

echo "Limpando imagens Docker antigas..."
sudo docker image prune -f

echo "Puxando a imagem mais recente do GHCR..."
sudo -E docker-compose pull

echo "Subindo os contêineres com a nova versão..."
sudo -E docker-compose up -d

echo ""
echo "Aplicação atualizada com sucesso!"
echo "Acesse: http://192.168.3.2"
echo ""
echo "Status atual dos contêineres:"
sudo -E docker-compose ps
