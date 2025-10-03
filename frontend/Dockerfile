# Frontend PHP + Nginx - Dockerfile
FROM php:8.2-fpm-alpine

# Métadonnées
LABEL maintainer="SEO Platform"
LABEL description="Frontend PHP pour SEO Article Generator"

# Installer Nginx et dépendances PHP
RUN apk add --no-cache \
    nginx \
    curl \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Copier la configuration Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf

# Créer les répertoires nécessaires
RUN mkdir -p /var/www/html /run/nginx /var/log/nginx && \
    chown -R www-data:www-data /var/www/html

# Copier le code du frontend
COPY --chown=www-data:www-data . /var/www/html/

# Configuration PHP-FPM
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Exposer le port
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=20s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Script de démarrage pour Nginx + PHP-FPM
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]