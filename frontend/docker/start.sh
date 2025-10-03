#!/bin/sh

# Script de démarrage pour Nginx + PHP-FPM

# Démarrer PHP-FPM en arrière-plan
php-fpm -D

# Vérifier que PHP-FPM a démarré
sleep 2
if ! pgrep -x "php-fpm" > /dev/null; then
    echo "ERROR: PHP-FPM failed to start"
    exit 1
fi

echo "✓ PHP-FPM started successfully"

# Démarrer Nginx au premier plan
echo "✓ Starting Nginx..."
nginx -g "daemon off;"