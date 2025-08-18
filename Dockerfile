# Usa a imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instala o pacote tzdata e outras dependências
RUN apt-get update && apt-get install -y \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

# Define o fuso horário para o container
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Instala as extensões PHP necessárias para o MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita o mod_rewrite do Apache para URLs amigáveis
RUN a2enmod rewrite

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto para o container
COPY . .

# Dá permissão para o Apache escrever nos arquivos, se necessário
RUN chown -R www-data:www-data /var/www/html

# Instala as dependências do Composer
RUN composer install
