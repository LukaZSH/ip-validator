#!/bin/bash

echo "Iniciando atualização da aplicação..."

if [ ! -f "docker-compose.yml" ]; then
    echo "Arquivo docker-compose.yml não encontrado!"
    exit 1
fi

DOCKER_CMD="docker"
COMPOSE_CMD="docker compose"

if ! docker ps >/dev/null 2>&1; then
    echo "Usando sudo para comandos Docker..."
    DOCKER_CMD="sudo docker"
    COMPOSE_CMD="sudo docker compose"
fi

echo "Parando containers..."
$COMPOSE_CMD down

echo "Removendo imagem antiga..."
$DOCKER_CMD rmi ghcr.io/lukazsh/ip-validator:latest 2>/dev/null || true

echo "Baixando imagem mais recente..."
$DOCKER_CMD pull ghcr.io/lukazsh/ip-validator:latest

if [ $? -ne 0 ]; then
    echo "Erro ao baixar imagem!"
    exit 1
fi

echo "Iniciando containers..."
$COMPOSE_CMD up -d

if [ $? -eq 0 ]; then
    echo "Aplicação atualizada com sucesso!"
    echo "Acesse: http://192.168.3.2"
    echo ""
    echo "Status dos containers:"
    $COMPOSE_CMD ps
else
    echo "Erro ao iniciar containers!"
    exit 1
fi