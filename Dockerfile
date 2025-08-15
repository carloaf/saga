# =============================================================================
# Multi-Architecture Dockerfile for SAGA
# Supports: linux/amd64, linux/arm64
# =============================================================================

# Build arguments (automatically injected by buildx)
ARG BUILDPLATFORM
ARG TARGETPLATFORM

# -----------------------------------------------------------------------------
# Stage 1: Frontend Build (Node.js)
# -----------------------------------------------------------------------------
FROM node:20.14.0-alpine AS frontend
LABEL stage="frontend-build"
LABEL description="Build frontend assets with Vite"

WORKDIR /app

# Show build info for debugging
ARG TARGETPLATFORM
ARG BUILDPLATFORM
RUN echo "Building frontend for TARGETPLATFORM=${TARGETPLATFORM:-current} (build host: ${BUILDPLATFORM:-current})"

# Copy package files first for better caching
COPY package.json package-lock.json ./

# Install dependencies with cache mount for faster rebuilds
RUN --mount=type=cache,target=/root/.npm \
    npm ci --silent

# Copy source files and build
COPY resources/ ./resources/
COPY vite.config.js tailwind.config.js ./

# Build production assets
RUN npm run build

# -----------------------------------------------------------------------------
# Stage 2: PHP Dependencies (Composer)
# -----------------------------------------------------------------------------
FROM composer:2.6 AS vendor
LABEL stage="composer-deps"
LABEL description="Install PHP dependencies"

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies without dev packages
RUN --mount=type=cache,target=/tmp/cache \
    composer install \
        --no-dev \
        --no-scripts \
        --optimize-autoloader \
        --prefer-dist \
        --ignore-platform-req=ext-gd \
        --ignore-platform-req=php

# -----------------------------------------------------------------------------
# Stage 3: Final Runtime Image
# -----------------------------------------------------------------------------
FROM php:8.4-apache AS runtime
LABEL maintainer="SAGA Team"
LABEL description="SAGA - Sistema de Agendamento e Gestão de Arranchamento"
LABEL version="1.0"

# Build info
ARG TARGETPLATFORM
RUN echo "Runtime image for ${TARGETPLATFORM:-current}"

# Install system dependencies in single layer
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    zip \
    postgresql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy vendor dependencies from composer stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy built frontend assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Copy application source
COPY . /var/www/html

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Configure Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
