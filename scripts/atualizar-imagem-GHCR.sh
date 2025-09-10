#!/bin/bash

echo "Iniciando a atualização da aplicação ip-validator..."

cd "$(dirname "$0")/.."

echo "Atualizando o repositório com 'git pull'..."
git pull origin master

if [ -f .env ]; then
    echo "Carregando variáveis de ambiente do .env..."
    set -a
    source .env
    set +a 
    echo "Variáveis carregadas: DB_USER=$MYSQL_USER, DB_NAME=$MYSQL_DATABASE"
else
    echo "ERRO: Arquivo .env não encontrado após git pull!"
    echo "Procurando em: $(pwd)/.env"
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

echo "Aguardando serviços ficarem prontos..."
sleep 10

echo "Verificando e criando usuário MySQL se necessário..."
sudo -E docker-compose exec db mysql -u root -p"$MYSQL_ROOT_PASSWORD" < scripts/init-mysql-user.sql || echo "Usuário MySQL já existe ou erro na criação."

echo "Executando setup do banco de dados..."
sudo -E docker-compose exec web php scripts/setup.php || echo "Setup já foi executado ou erro na execução."

echo ""
echo "Aplicação atualizada com sucesso!"
echo "Credenciais de login:"
echo "  Usuário: suporte"
echo "  Senha: 1n&\$p@r"
echo ""
echo "Acesse: http://192.168.3.2"
echo ""
echo "Status atual dos contêineres:"
sudo docker-compose ps
