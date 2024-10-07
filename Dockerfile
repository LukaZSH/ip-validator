# Use uma imagem base do PHP com Apache
FROM php:8.1-apache

# Instalar extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita o módulo de reescrita do Apache
RUN a2enmod rewrite

# Copiar os arquivos do projeto para o diretório do Apache
COPY . /var/www/html/

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto para o diretório de trabalho
COPY . .

# Definindo as permissões
RUN chown -R www-data:www-data /var/www/html

# Instala as dependências do Composer
RUN composer install
