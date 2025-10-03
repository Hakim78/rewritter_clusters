#!/bin/bash

###############################################################################
# Script de configuration rapide pour VPS fraîchement installé
# À exécuter EN PREMIER sur le serveur vierge
###############################################################################

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     Configuration Rapide VPS - Première Étape     ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════╝${NC}"
echo ""

# Vérification root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}❌ Ce script doit être exécuté en root (sudo)${NC}"
    exit 1
fi

###############################################################################
# 1. Mise à jour du système
###############################################################################
echo -e "${BLUE}[1/5] Mise à jour du système...${NC}"
apt update && apt upgrade -y
echo -e "${GREEN}✓ Système mis à jour${NC}"

###############################################################################
# 2. Installation des outils de base
###############################################################################
echo -e "${BLUE}[2/5] Installation des outils de base...${NC}"
apt install -y \
    curl \
    wget \
    git \
    unzip \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release \
    ufw \
    fail2ban \
    htop \
    vim

echo -e "${GREEN}✓ Outils installés${NC}"

###############################################################################
# 3. Configuration du pare-feu
###############################################################################
echo -e "${BLUE}[3/5] Configuration du pare-feu UFW...${NC}"

# Réinitialiser UFW
ufw --force reset

# Autoriser SSH
ufw allow 22/tcp

# Autoriser HTTP/HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Activer UFW
ufw --force enable

echo -e "${GREEN}✓ Pare-feu configuré${NC}"

###############################################################################
# 4. Configuration de Fail2ban
###############################################################################
echo -e "${BLUE}[4/5] Configuration de Fail2ban...${NC}"

systemctl enable fail2ban
systemctl start fail2ban

cat > /etc/fail2ban/jail.local << EOF
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
EOF

systemctl restart fail2ban

echo -e "${GREEN}✓ Fail2ban configuré${NC}"

###############################################################################
# 5. Optimisation système
###############################################################################
echo -e "${BLUE}[5/5] Optimisation système...${NC}"

# Augmenter les limites de fichiers ouverts
cat >> /etc/security/limits.conf << EOF

# Limites pour applications web
* soft nofile 65536
* hard nofile 65536
EOF

# Optimisation réseau
cat >> /etc/sysctl.conf << EOF

# Optimisations réseau
net.core.somaxconn = 65536
net.ipv4.tcp_max_syn_backlog = 8192
net.ipv4.ip_local_port_range = 1024 65535
EOF

sysctl -p

echo -e "${GREEN}✓ Système optimisé${NC}"

###############################################################################
# Résumé
###############################################################################
echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║         Configuration initiale terminée ! 🎉       ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📝 Prochaines étapes:${NC}"
echo ""
echo -e "   1. ${YELLOW}Copier le projet sur le serveur:${NC}"
echo -e "      scp -r plateforme_python_clusters root@VOTRE_IP:/tmp/"
echo ""
echo -e "   2. ${YELLOW}Configurer le domaine dans deploy.sh:${NC}"
echo -e "      export DOMAIN=votre-domaine.com"
echo ""
echo -e "   3. ${YELLOW}Lancer le déploiement:${NC}"
echo -e "      cd /tmp/plateforme_python_clusters/deploy"
echo -e "      chmod +x deploy.sh"
echo -e "      DOMAIN=votre-domaine.com ./deploy.sh"
echo ""
echo -e "${GREEN}✓ Le serveur est prêt pour le déploiement !${NC}"
echo ""