#!/bin/bash

###############################################################################
# Script de déploiement automatique pour VPS IONOS
# SEO Article Generator Platform
###############################################################################

set -e  # Arrêt en cas d'erreur

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="seo-platform"
APP_DIR="/var/www/${PROJECT_NAME}"
DOMAIN="${DOMAIN:-example.com}"  # Remplacer par votre domaine
BACKEND_PORT=5001

echo -e "${BLUE}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Déploiement SEO Article Generator - Production   ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════╝${NC}"
echo ""

# Vérification root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}❌ Ce script doit être exécuté en root (sudo)${NC}"
    exit 1
fi

echo -e "${YELLOW}⚙️  Configuration:${NC}"
echo -e "   Domaine: ${GREEN}${DOMAIN}${NC}"
echo -e "   Répertoire: ${GREEN}${APP_DIR}${NC}"
echo -e "   Backend Port: ${GREEN}${BACKEND_PORT}${NC}"
echo ""

read -p "Voulez-vous continuer? (y/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

###############################################################################
# 1. Installation des dépendances système
###############################################################################
echo -e "${BLUE}[1/8] Installation des dépendances système...${NC}"

apt update
apt install -y \
    nginx \
    python3 \
    python3-pip \
    python3-venv \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-xml \
    mysql-server \
    git \
    curl \
    certbot \
    python3-certbot-nginx \
    supervisor

echo -e "${GREEN}✓ Dépendances installées${NC}"

###############################################################################
# 2. Création de l'utilisateur système
###############################################################################
echo -e "${BLUE}[2/8] Création de l'utilisateur système...${NC}"

if ! id "seoapp" &>/dev/null; then
    useradd -r -s /bin/bash -d ${APP_DIR} -m seoapp
    echo -e "${GREEN}✓ Utilisateur seoapp créé${NC}"
else
    echo -e "${YELLOW}⚠ Utilisateur seoapp existe déjà${NC}"
fi

###############################################################################
# 3. Copie des fichiers de l'application
###############################################################################
echo -e "${BLUE}[3/8] Copie des fichiers de l'application...${NC}"

# Créer le répertoire
mkdir -p ${APP_DIR}

# Copier les fichiers (depuis le répertoire actuel)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
rsync -av --exclude='venv' --exclude='node_modules' --exclude='.git' --exclude='__pycache__' \
    ${SCRIPT_DIR}/ ${APP_DIR}/

echo -e "${GREEN}✓ Fichiers copiés${NC}"

###############################################################################
# 4. Configuration de l'environnement Python
###############################################################################
echo -e "${BLUE}[4/8] Configuration de l'environnement Python...${NC}"

cd ${APP_DIR}/backend

# Créer l'environnement virtuel
python3 -m venv venv
source venv/bin/activate

# Installer les dépendances
pip install --upgrade pip
pip install -r requirements.txt
pip install gunicorn  # Pour la production

deactivate

echo -e "${GREEN}✓ Environnement Python configuré${NC}"

###############################################################################
# 5. Configuration de la base de données
###############################################################################
echo -e "${BLUE}[5/8] Configuration de la base de données...${NC}"

# Générer un mot de passe aléatoire pour MySQL
DB_PASSWORD=$(openssl rand -base64 32)

# Créer la base de données et l'utilisateur
mysql -e "CREATE DATABASE IF NOT EXISTS seo_articles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'seoapp'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON seo_articles.* TO 'seoapp'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo -e "${GREEN}✓ Base de données configurée${NC}"
echo -e "${YELLOW}   DB Password: ${DB_PASSWORD}${NC}"

###############################################################################
# 6. Configuration du fichier .env de production
###############################################################################
echo -e "${BLUE}[6/8] Configuration du fichier .env...${NC}"

cat > ${APP_DIR}/.env << EOF
# Configuration Production
APP_ENV=production
APP_DEBUG=False
SECRET_KEY=$(openssl rand -hex 32)

# Backend Python API
PYTHON_API_URL=http://127.0.0.1:${BACKEND_PORT}
PYTHON_API_PORT=${BACKEND_PORT}

# Base de données
DB_HOST=localhost
DB_PORT=3306
DB_NAME=seo_articles
DB_USER=seoapp
DB_PASSWORD=${DB_PASSWORD}

# APIs LLM (À CONFIGURER MANUELLEMENT)
OPENAI_API_KEY=your_openai_key_here
ANTHROPIC_API_KEY=your_anthropic_key_here
PERPLEXITY_API_KEY=your_perplexity_key_here

# API Ideogram
IDEOGRAM_API_KEY=your_ideogram_key_here

# API Génération d'images
REPLICATE_API_TOKEN=your_replicate_token_here
STABILITY_API_KEY=your_stability_key_here

# Services externes
SCRAPING_API_KEY=your_scraping_key_here

# Limites et configuration
MAX_ARTICLE_LENGTH=5000
TIMEOUT_SECONDS=300
MAX_RETRIES=3

# Logging
LOG_LEVEL=INFO
LOG_FILE_PATH=${APP_DIR}/logs/app.log
EOF

chmod 600 ${APP_DIR}/.env

echo -e "${GREEN}✓ Fichier .env créé${NC}"
echo -e "${RED}⚠ N'OUBLIEZ PAS de configurer vos clés API dans ${APP_DIR}/.env${NC}"

###############################################################################
# 7. Configuration du service systemd pour Flask
###############################################################################
echo -e "${BLUE}[7/8] Configuration du service systemd...${NC}"

cat > /etc/systemd/system/seo-backend.service << EOF
[Unit]
Description=SEO Article Generator - Flask Backend
After=network.target mysql.service

[Service]
Type=notify
User=seoapp
Group=www-data
WorkingDirectory=${APP_DIR}/backend
Environment="PATH=${APP_DIR}/backend/venv/bin"
EnvironmentFile=${APP_DIR}/.env

ExecStart=${APP_DIR}/backend/venv/bin/gunicorn \
    --bind 127.0.0.1:${BACKEND_PORT} \
    --workers 4 \
    --worker-class sync \
    --timeout 300 \
    --access-logfile ${APP_DIR}/logs/gunicorn-access.log \
    --error-logfile ${APP_DIR}/logs/gunicorn-error.log \
    --log-level info \
    app:app

Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Créer les répertoires de logs
mkdir -p ${APP_DIR}/logs
chown -R seoapp:www-data ${APP_DIR}/logs

# Activer et démarrer le service
systemctl daemon-reload
systemctl enable seo-backend.service
systemctl start seo-backend.service

echo -e "${GREEN}✓ Service Flask configuré et démarré${NC}"

###############################################################################
# 8. Configuration Nginx
###############################################################################
echo -e "${BLUE}[8/8] Configuration Nginx...${NC}"

cat > /etc/nginx/sites-available/${PROJECT_NAME} << 'NGINX_EOF'
# SEO Article Generator - Configuration Nginx Production

upstream flask_backend {
    server 127.0.0.1:5001;
    keepalive 32;
}

# Redirection HTTP -> HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name DOMAIN_PLACEHOLDER www.DOMAIN_PLACEHOLDER;

    # Certbot challenge
    location /.well-known/acme-challenge/ {
        root /var/www/html;
    }

    location / {
        return 301 https://$server_name$request_uri;
    }
}

# Configuration HTTPS
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name DOMAIN_PLACEHOLDER www.DOMAIN_PLACEHOLDER;

    # SSL (sera configuré par Certbot)
    ssl_certificate /etc/letsencrypt/live/DOMAIN_PLACEHOLDER/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/DOMAIN_PLACEHOLDER/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Sécurité
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Logs
    access_log /var/log/nginx/seo-platform-access.log;
    error_log /var/log/nginx/seo-platform-error.log;

    # Frontend PHP
    root APP_DIR_PLACEHOLDER/frontend/public;
    index index.php index.html;

    # Max upload size
    client_max_body_size 20M;

    # Frontend routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;

        # Timeouts pour les workflows longs
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
    }

    # Backend Python API
    location /api/ {
        proxy_pass http://flask_backend;
        proxy_http_version 1.1;

        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header Connection "";

        # Timeouts pour les workflows longs
        proxy_connect_timeout 300s;
        proxy_send_timeout 300s;
        proxy_read_timeout 300s;

        # Buffering
        proxy_buffering on;
        proxy_buffer_size 4k;
        proxy_buffers 8 4k;
    }

    # Assets statiques
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Sécurité - Bloquer les fichiers sensibles
    location ~ /\. {
        deny all;
    }

    location ~ /(\.env|\.git|logs|temp|uploads) {
        deny all;
    }
}
NGINX_EOF

# Remplacer les placeholders
sed -i "s|DOMAIN_PLACEHOLDER|${DOMAIN}|g" /etc/nginx/sites-available/${PROJECT_NAME}
sed -i "s|APP_DIR_PLACEHOLDER|${APP_DIR}|g" /etc/nginx/sites-available/${PROJECT_NAME}

# Activer le site
ln -sf /etc/nginx/sites-available/${PROJECT_NAME} /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Tester la configuration
nginx -t

# Recharger Nginx
systemctl reload nginx

echo -e "${GREEN}✓ Nginx configuré${NC}"

###############################################################################
# 9. Permissions
###############################################################################
echo -e "${BLUE}Configuration des permissions...${NC}"

# Propriétaire des fichiers
chown -R seoapp:www-data ${APP_DIR}

# Permissions
find ${APP_DIR} -type d -exec chmod 755 {} \;
find ${APP_DIR} -type f -exec chmod 644 {} \;

# Permissions spéciales
chmod 755 ${APP_DIR}/backend/venv/bin/*
chmod 600 ${APP_DIR}/.env
chmod 775 ${APP_DIR}/logs
chmod 775 ${APP_DIR}/temp
chmod 775 ${APP_DIR}/uploads
chmod 775 ${APP_DIR}/output

echo -e "${GREEN}✓ Permissions configurées${NC}"

###############################################################################
# 10. Configuration SSL avec Certbot
###############################################################################
echo -e "${BLUE}Configuration SSL (Let's Encrypt)...${NC}"

if [ "${DOMAIN}" != "example.com" ]; then
    echo -e "${YELLOW}Installation du certificat SSL...${NC}"
    certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@${DOMAIN} || {
        echo -e "${RED}⚠ Échec de l'installation SSL. Vous pourrez le faire manuellement plus tard.${NC}"
    }
else
    echo -e "${YELLOW}⚠ Domaine par défaut détecté. Configurez SSL manuellement:${NC}"
    echo -e "   sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com"
fi

###############################################################################
# Résumé final
###############################################################################
echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║       Déploiement terminé avec succès ! 🎉        ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📍 Informations importantes:${NC}"
echo ""
echo -e "   🌐 URL Frontend: ${GREEN}https://${DOMAIN}${NC}"
echo -e "   🔧 API Backend:  ${GREEN}https://${DOMAIN}/api/${NC}"
echo ""
echo -e "${BLUE}📝 Actions à effectuer:${NC}"
echo ""
echo -e "   1. ${YELLOW}Configurer les clés API dans:${NC}"
echo -e "      ${APP_DIR}/.env"
echo ""
echo -e "   2. ${YELLOW}Créer un utilisateur admin:${NC}"
echo -e "      cd ${APP_DIR}/backend"
echo -e "      source venv/bin/activate"
echo -e "      python create_admin_proper.py"
echo ""
echo -e "   3. ${YELLOW}Pointer votre DNS vers l'IP du serveur:${NC}"
echo -e "      $(curl -s ifconfig.me)"
echo ""
echo -e "${BLUE}🔍 Commandes utiles:${NC}"
echo ""
echo -e "   • Statut backend:    ${GREEN}systemctl status seo-backend${NC}"
echo -e "   • Logs backend:      ${GREEN}journalctl -u seo-backend -f${NC}"
echo -e "   • Logs Nginx:        ${GREEN}tail -f /var/log/nginx/seo-platform-*.log${NC}"
echo -e "   • Redémarrer:        ${GREEN}systemctl restart seo-backend nginx${NC}"
echo ""
echo -e "${BLUE}📊 Base de données:${NC}"
echo -e "   • User: ${GREEN}seoapp${NC}"
echo -e "   • Pass: ${YELLOW}${DB_PASSWORD}${NC}"
echo -e "   • DB:   ${GREEN}seo_articles${NC}"
echo ""

# Sauvegarder les infos dans un fichier
cat > ${APP_DIR}/DEPLOYMENT_INFO.txt << EOF
Deployment Information
======================

Date: $(date)
Domain: ${DOMAIN}
Server IP: $(curl -s ifconfig.me)

Database:
  Name: seo_articles
  User: seoapp
  Password: ${DB_PASSWORD}

Directories:
  App: ${APP_DIR}
  Logs: ${APP_DIR}/logs

Services:
  Backend: seo-backend.service

Next Steps:
  1. Configure API keys in ${APP_DIR}/.env
  2. Create admin user: cd ${APP_DIR}/backend && source venv/bin/activate && python create_admin_proper.py
  3. Point DNS to server IP: $(curl -s ifconfig.me)
EOF

chmod 600 ${APP_DIR}/DEPLOYMENT_INFO.txt

echo -e "${GREEN}✓ Informations sauvegardées dans ${APP_DIR}/DEPLOYMENT_INFO.txt${NC}"
echo ""