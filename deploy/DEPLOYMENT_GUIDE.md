# 🚀 Guide de Déploiement en Production (VPS IONOS)

Guide complet pour déployer la plateforme SEO Article Generator sur un VPS IONOS.

---

## 📋 Prérequis

### Sur votre VPS IONOS
- Ubuntu 22.04 LTS ou 24.04 LTS
- Minimum 2 GB RAM
- 20 GB espace disque
- Accès root (SSH)

### Informations nécessaires
- ✅ Nom de domaine (ex: `exemple.com`)
- ✅ Adresse IP du VPS
- ✅ Clés API (OpenAI, Anthropic, Perplexity, Ideogram, etc.)

---

## 🎯 Étapes de Déploiement

### 1️⃣ Configurer le DNS

Avant de commencer, configurez vos enregistrements DNS chez IONOS :

| Type | Nom | Valeur | TTL |
|------|-----|--------|-----|
| A | @ | `IP_DU_VPS` | 3600 |
| A | www | `IP_DU_VPS` | 3600 |

⏱️ **Propagation DNS**: 15 min à 48h (généralement < 2h)

---

### 2️⃣ Connexion au VPS

```bash
ssh root@VOTRE_IP_VPS
```

---

### 3️⃣ Configuration Initiale du Serveur

Copier le projet sur le serveur :

```bash
# Depuis votre machine locale
cd /chemin/vers/plateforme_python_clusters
scp -r . root@VOTRE_IP_VPS:/tmp/plateforme_python_clusters
```

Se connecter au serveur et lancer la configuration :

```bash
# Sur le VPS
cd /tmp/plateforme_python_clusters/deploy
chmod +x quick-setup.sh
./quick-setup.sh
```

Ce script va :
- ✅ Mettre à jour le système
- ✅ Installer les outils de base
- ✅ Configurer le pare-feu UFW
- ✅ Configurer Fail2ban (protection SSH)
- ✅ Optimiser les paramètres système

---

### 4️⃣ Déploiement de l'Application

```bash
cd /tmp/plateforme_python_clusters/deploy
chmod +x deploy.sh

# Lancer le déploiement avec votre domaine
DOMAIN=votre-domaine.com ./deploy.sh
```

Le script va automatiquement :
- ✅ Installer Nginx, Python, PHP, MySQL
- ✅ Créer l'utilisateur système `seoapp`
- ✅ Configurer l'environnement Python avec virtualenv
- ✅ Créer la base de données MySQL
- ✅ Générer le fichier `.env` de production
- ✅ Configurer le service systemd pour Flask
- ✅ Configurer Nginx avec reverse proxy
- ✅ Installer le certificat SSL (Let's Encrypt)
- ✅ Configurer les permissions

**⏱️ Durée**: 5-10 minutes

---

### 5️⃣ Configuration des Clés API

Éditer le fichier `.env` :

```bash
nano /var/www/seo-platform/.env
```

Configurer les clés API :

```env
# APIs LLM
OPENAI_API_KEY=sk-...
ANTHROPIC_API_KEY=sk-ant-...
PERPLEXITY_API_KEY=pplx-...

# API Ideogram
IDEOGRAM_API_KEY=...

# API Génération d'images
REPLICATE_API_TOKEN=r8_...
STABILITY_API_KEY=sk-...

# Services externes
SCRAPING_API_KEY=...
```

Sauvegarder : `Ctrl+O` puis `Enter`, quitter : `Ctrl+X`

Redémarrer le backend :

```bash
systemctl restart seo-backend
```

---

### 6️⃣ Créer un Utilisateur Admin

```bash
cd /var/www/seo-platform/backend
source venv/bin/activate
python create_admin_proper.py
```

Suivre les instructions pour créer votre compte admin.

---

### 7️⃣ Vérification

Accéder à votre site :

```
https://votre-domaine.com
```

Vérifier les services :

```bash
# Statut du backend
systemctl status seo-backend

# Statut de Nginx
systemctl status nginx

# Statut de MySQL
systemctl status mysql
```

---

## 🔧 Commandes Utiles

### Gestion des Services

```bash
# Backend Flask
systemctl start seo-backend
systemctl stop seo-backend
systemctl restart seo-backend
systemctl status seo-backend

# Nginx
systemctl restart nginx
systemctl reload nginx

# MySQL
systemctl restart mysql
```

### Logs

```bash
# Logs du backend Flask
journalctl -u seo-backend -f

# Logs Gunicorn
tail -f /var/www/seo-platform/logs/gunicorn-error.log

# Logs Nginx
tail -f /var/log/nginx/seo-platform-error.log
tail -f /var/log/nginx/seo-platform-access.log

# Logs PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

### Base de Données

```bash
# Se connecter à MySQL
mysql -u seoapp -p seo_articles

# Voir les infos de connexion
cat /var/www/seo-platform/DEPLOYMENT_INFO.txt
```

---

## 🛡️ Sécurité

### Pare-feu UFW

```bash
# Voir les règles actuelles
ufw status

# Bloquer une IP
ufw deny from IP_ADDRESS

# Autoriser une IP spécifique pour SSH
ufw allow from IP_ADDRESS to any port 22
```

### Fail2ban

```bash
# Statut
fail2ban-client status

# Débloquer une IP
fail2ban-client set sshd unbanip IP_ADDRESS
```

### SSL/HTTPS

Renouvellement automatique configuré. Pour forcer le renouvellement :

```bash
certbot renew --dry-run
```

---

## 🔄 Mises à Jour

### Mettre à jour le code

```bash
cd /var/www/seo-platform

# Pull les dernières modifications
git pull origin main

# Backend Python
cd backend
source venv/bin/activate
pip install -r requirements.txt
deactivate

# Redémarrer
systemctl restart seo-backend
systemctl reload nginx
```

### Mettre à jour les dépendances système

```bash
apt update && apt upgrade -y
```

---

## 🐛 Dépannage

### Le backend ne démarre pas

```bash
# Vérifier les logs
journalctl -u seo-backend -n 50

# Vérifier la configuration
cat /etc/systemd/system/seo-backend.service

# Tester manuellement
cd /var/www/seo-platform/backend
source venv/bin/activate
python app.py
```

### Erreur 502 Bad Gateway

```bash
# Le backend n'est pas accessible
systemctl status seo-backend

# Redémarrer
systemctl restart seo-backend

# Vérifier que le port 5001 écoute
netstat -tlnp | grep 5001
```

### Erreur 500 PHP

```bash
# Logs PHP
tail -f /var/log/php8.2-fpm.log

# Vérifier les permissions
ls -la /var/www/seo-platform/frontend/public

# Permissions doivent être:
# Propriétaire: seoapp:www-data
# Répertoires: 755
# Fichiers: 644
```

### Base de données inaccessible

```bash
# Vérifier MySQL
systemctl status mysql

# Tester la connexion
mysql -u seoapp -p seo_articles

# Réinitialiser le mot de passe (voir DEPLOYMENT_INFO.txt)
cat /var/www/seo-platform/DEPLOYMENT_INFO.txt
```

---

## 📊 Monitoring

### Vérifier les ressources

```bash
# CPU et RAM
htop

# Espace disque
df -h

# Connexions actives
netstat -anp | grep ESTABLISHED | wc -l
```

### Statistiques Nginx

```bash
# Nombre de requêtes par heure
grep "$(date +%d/%b/%Y:%H)" /var/log/nginx/seo-platform-access.log | wc -l

# Top 10 des URLs
awk '{print $7}' /var/log/nginx/seo-platform-access.log | sort | uniq -c | sort -rn | head -10
```

---

## 🔐 Sauvegarde

### Sauvegarde Manuelle

```bash
#!/bin/bash
BACKUP_DIR="/root/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p ${BACKUP_DIR}

# Sauvegarde de la base de données
mysqldump -u seoapp -p$(grep DB_PASSWORD /var/www/seo-platform/.env | cut -d '=' -f2) \
    seo_articles > ${BACKUP_DIR}/db_${DATE}.sql

# Sauvegarde des fichiers
tar -czf ${BACKUP_DIR}/files_${DATE}.tar.gz \
    /var/www/seo-platform/uploads \
    /var/www/seo-platform/output \
    /var/www/seo-platform/.env

# Conserver seulement les 7 dernières sauvegardes
find ${BACKUP_DIR} -type f -mtime +7 -delete

echo "Sauvegarde terminée: ${BACKUP_DIR}"
```

### Automatiser avec Cron

```bash
# Éditer crontab
crontab -e

# Ajouter (sauvegarde tous les jours à 3h du matin)
0 3 * * * /root/backup.sh >> /var/log/backup.log 2>&1
```

---

## 📞 Support

### Fichiers Importants

- Configuration app: `/var/www/seo-platform/.env`
- Logs app: `/var/www/seo-platform/logs/`
- Configuration Nginx: `/etc/nginx/sites-available/seo-platform`
- Service systemd: `/etc/systemd/system/seo-backend.service`
- Infos déploiement: `/var/www/seo-platform/DEPLOYMENT_INFO.txt`

### Vérification Rapide

```bash
# Tout-en-un check
echo "=== SERVICES ==="
systemctl is-active seo-backend nginx mysql

echo "=== PORTS ==="
netstat -tlnp | grep -E ':(80|443|5001|3306)'

echo "=== DISQUE ==="
df -h /

echo "=== MÉMOIRE ==="
free -h

echo "=== PROCESSUS ==="
ps aux | grep -E '(gunicorn|nginx|mysql)' | grep -v grep
```

---

## ✅ Checklist Post-Déploiement

- [ ] Site accessible via HTTPS
- [ ] Certificat SSL valide (cadenas vert)
- [ ] Login admin fonctionne
- [ ] Test Workflow 1 (création article)
- [ ] Test Workflow 2 (réécriture)
- [ ] Test Workflow 3 (cluster)
- [ ] Logs sans erreurs critiques
- [ ] Sauvegarde configurée
- [ ] Monitoring en place

---

## 🎉 Félicitations !

Votre plateforme SEO est maintenant en production et accessible au monde entier ! 🌍

Pour toute question, consultez les logs ou contactez votre administrateur système.