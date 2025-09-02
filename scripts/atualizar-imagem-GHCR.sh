#!/bin/bash

echo "Iniciando a atualização da aplicação ip-validator..."

echo "Atualizando o repositório com 'git pull'..."
git pull origin master

echo "Parando os contêineres atuais"
sudo docker-compose down

echo "Limpando imagens Docker antigas..."
sudo docker image prune -f

echo "Puxando a imagem mais recente do GHCR..."
sudo docker-compose pull

echo "Subindo os contêineres com a nova versão..."
sudo docker-compose up --build --no-cache -d

echo ""
echo "Aplicação atualizada com sucesso!"
echo "Acesse: http://192.168.3.2"
echo ""
echo "Status atual dos contêineres:"
sudo docker-compose ps
