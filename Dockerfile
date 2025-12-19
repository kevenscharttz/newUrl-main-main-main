FROM php:8.2-cli

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev

# Instalar extensões do PHP necessárias para Laravel e Postgres
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Instalar Node.js (para compilar o front-end)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Obter o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do projeto
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Expor a porta que o Render usa
EXPOSE 10000

# Comando para iniciar o servidor
CMD php artisan serve --host=0.0.0.0 --port=10000
