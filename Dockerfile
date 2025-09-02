# Usa a imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instala dependências necessárias (incluindo git, zip, unzip para o Composer)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    tzdata \
    git \
    unzip \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Define o fuso horário para o container
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Instala as extensões PHP (com a adição de 'gd' e 'zip')
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita o mod_rewrite do Apache para URLs amigáveis
RUN a2enmod rewrite
RUN echo '<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos de dependência do Composer primeiro para aproveitar o cache do Docker
COPY composer.json composer.lock ./

# Instala as dependências do Composer (apenas produção para otimizar)
# Isso cria uma camada de cache que só é invalidada quando o composer.lock muda
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Agora copia o resto dos arquivos da aplicação
COPY . .

# Dá permissão para o Apache escrever nos arquivos, se necessário
RUN chown -R www-data:www-data /var/www/html