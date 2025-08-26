#!/bin/bash

set -e

echo "Iniciando a atualização da aplicação ip-validator..."

echo "Atualizar o repositório com 'git pull'..."
git pull origin master


echo "Puxando a imagem mais recente do GHCR..."
sudo docker-compose pull

echo "Subindo os containers com a nova versão..."
sudo docker-compose up -d


echo "A limpar imagens Docker antigas..."
sudo docker image prune -f

echo "Aplicação atualizada com sucesso!"
echo "Acesse: http://192.168.3.2"
echo ""
echo "Status atual dos contentores:"
sudo docker-compose ps
