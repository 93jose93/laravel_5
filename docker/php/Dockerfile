FROM php:7.1-cli

# 🔧 Fix Debian Buster EOL
RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list \
 && sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
 && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until

# Dependencias del sistema + PostgreSQL
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
 && docker-php-ext-install pdo pdo_pgsql mbstring zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Composer 2.2 LTS
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www