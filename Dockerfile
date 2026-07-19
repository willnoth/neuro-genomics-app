# ========================================================
# STAGE 1: Base PHP Environment & Extensions
# ========================================================
FROM php:8.5-fpm-alpine AS base

# Install absolute core Linux packages and PHP engines
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    postgresql-dev \
    $PHPIZE_DEPS \
    linux-headers \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set up local project directory
WORKDIR /var/www

# ========================================================
# STAGE 2: Specialized Genomics Background Worker
# ========================================================
FROM base AS genomics_worker

# Install Python 3 development toolchains
RUN apk add --no-cache \
    python3 \
    py3-pip \
    python3-dev \
    build-base \
    g++

# Set up virtual environment isolated from system layers
RUN python3 -m venv /opt/bioenv \
    && /opt/bioenv/bin/pip install --no-cache-dir pandas requests

# Bind python3 directly to the virtual environment path
ENV PATH="/opt/bioenv/bin:$PATH"

# Tell the container to actively listen to the Redis queue pipeline
CMD ["php", "/var/www/artisan", "queue:work", "--queue=genomics-heavy,default"]

