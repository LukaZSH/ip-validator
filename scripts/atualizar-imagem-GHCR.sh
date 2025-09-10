#!/bin/bash

echo "Iniciando a atualização da aplicação ip-validator..."

echo "Atualizando o repositório com 'git pull'..."
git pull origin master

# Carrega as variáveis de ambiente do arquivo .env APÓS o git pull
if [ -f .env ]; then
    echo "Carregando variáveis de ambiente do .env..."
    # Usa source em vez de export para lidar melhor com caracteres especiais
    set -a  # automatically export all variables
    source .env
    set +a  # stop automatically exporting
    echo "Variáveis carregadas: DB_USER=$MYSQL_USER, DB_NAME=$MYSQL_DATABASE"
else
    echo "ERRO: Arquivo .env não encontrado após git pull!"
    exit 1
fi

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
