# 🏠 Déploiement sur Synology NAS avec Docker

Guide complet pour déployer la plateforme SEO Article Generator sur votre Synology NAS.

---

## 📋 Prérequis

### Matériel Synology
- ✅ Synology DS218+, DS220+, DS720+, DS920+ ou supérieur
- ✅ 4 GB RAM minimum (8 GB recommandé)
- ✅ DSM 7.x installé
- ✅ 20 GB d'espace disque disponible

### Packages à installer
- ✅ Docker (via Package Center)
- ✅ Container Manager (anciennement Docker)

### Réseau
- ✅ IP locale fixe pour le Synology (ex: 192.168.1.50)
- ✅ Accès administrateur à votre box internet

---

## 🚀 Installation étape par étape

### 1️⃣ Installer Docker sur Synology

1. Ouvrir **Package Center**
2. Rechercher **Container Manager**
3. Cliquer sur **Installer**
4. Attendre la fin de l'installation

---

### 2️⃣ Transférer le projet sur le Synology

#### Option A : Via File Station (Interface web)

1. Ouvrir **File Station**
2. Créer un dossier : `/docker/seo-platform/`
3. Glisser-déposer tout le contenu de `plateforme_python_clusters/` dans ce dossier

#### Option B : Via SSH/SCP (Plus rapide)

```bash
# Activer SSH sur Synology: Panneau de configuration > Terminal & SNMP > Activer SSH
# Depuis votre ordinateur:
scp -r plateforme_python_clusters/ admin@SYNOLOGY_IP:/volume1/docker/seo-platform/
```

---

### 3️⃣ Configuration du fichier .env

1. Ouvrir File Station
2. Naviguer vers `/docker/seo-platform/`
3. Copier `.env.docker` vers `.env`
4. Éditer `.env` avec les vraies valeurs :

```env
# Sécurité (générer des valeurs aléatoires)
SECRET_KEY=votre-secret-key-random-ici
DB_ROOT_PASSWORD=MotDePasseRootMySQL123!
DB_PASSWORD=MotDePasseAppMySQL456!

# APIs (remplacer par vos vraies clés)
OPENAI_API_KEY=sk-proj-...
ANTHROPIC_API_KEY=sk-ant-...
PERPLEXITY_API_KEY=pplx-...
IDEOGRAM_API_KEY=...
REPLICATE_API_TOKEN=r8_...
STABILITY_API_KEY=sk-...

# Port exposé (choisir un port libre sur votre Synology)
FRONTEND_PORT=8080
```

---

### 4️⃣ Déployer avec Container Manager

#### Via SSH (Recommandé - Plus rapide)

```bash
# Se connecter en SSH au Synology
ssh admin@SYNOLOGY_IP

# Aller dans le dossier du projet
cd /volume1/docker/seo-platform

# Lancer les containers
sudo docker-compose --env-file .env up -d --build

# Vérifier le statut
sudo docker-compose ps
```

#### Via Interface Web Container Manager

1. Ouvrir **Container Manager**
2. Aller dans **Projet**
3. Cliquer sur **Créer**
4. Remplir :
   - Nom du projet : `seo-platform`
   - Chemin : `/docker/seo-platform`
   - Source : Créer docker-compose.yml
5. Copier le contenu de `docker-compose.yml`
6. Cliquer sur **Créer**

---

### 5️⃣ Vérification de l'installation

#### Vérifier les containers

```bash
sudo docker-compose ps

# Devrait afficher :
# seo-database   (healthy)
# seo-backend    (healthy)
# seo-frontend   (healthy)
```

#### Vérifier les logs

```bash
# Logs du backend
sudo docker-compose logs -f backend

# Logs du frontend
sudo docker-compose logs -f frontend

# Logs de la base de données
sudo docker-compose logs -f database
```

#### Tester l'accès

Ouvrir un navigateur :
```
http://SYNOLOGY_IP:8080
```

Vous devriez voir la page de connexion de la plateforme.

---

### 6️⃣ Créer un utilisateur admin

```bash
# Se connecter au container backend
sudo docker exec -it seo-backend /bin/bash

# Créer l'admin
python create_admin_proper.py

# Suivre les instructions puis quitter
exit
```

---

## 🌐 Configuration du Reverse Proxy (Accès via nom de domaine)

### Dans DSM : Panneau de configuration > Connexion > Portail d'applications > Reverse Proxy

1. Cliquer sur **Créer**
2. Remplir :
   - **Nom** : SEO Platform
   - **Source** :
     - Protocole : HTTP
     - Nom d'hôte : `seo.votre-domaine.com` (ou laissez vide pour tout)
     - Port : 80
   - **Destination** :
     - Protocole : HTTP
     - Nom d'hôte : localhost
     - Port : 8080

3. Onglet **Règles personnalisées** :
   Coller dans les en-têtes personnalisés :
   ```
   proxy_set_header X-Real-IP $remote_addr;
   proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   proxy_set_header X-Forwarded-Proto $scheme;
   proxy_read_timeout 300s;
   proxy_send_timeout 300s;
   ```

4. Cliquer sur **Enregistrer**

Maintenant accessible via : `http://SYNOLOGY_IP/` (port 80)

---

## 🔐 Configuration HTTPS avec Let's Encrypt

### Dans DSM : Panneau de configuration > Sécurité > Certificat

1. Cliquer sur **Ajouter** > **Ajouter un nouveau certificat**
2. Sélectionner **Obtenir un certificat de Let's Encrypt**
3. Remplir :
   - Nom de domaine : `seo.votre-domaine.com`
   - Email : votre-email@example.com
4. Cliquer sur **Appliquer**

### Puis dans le Reverse Proxy :

1. Éditer la règle créée précédemment
2. Changer le protocole source de **HTTP** à **HTTPS**
3. Sélectionner le certificat SSL
4. Enregistrer

Accès sécurisé : `https://seo.votre-domaine.com`

---

## 📡 Ouvrir l'accès depuis internet

### 1. Configuration DNS

Chez votre registrar (OVH, Gandi, Cloudflare, etc.) :

| Type | Nom | Valeur | TTL |
|------|-----|--------|-----|
| A | seo | `VOTRE_IP_PUBLIQUE` | 3600 |

**Trouver votre IP publique** : https://www.whatismyip.com/

### 2. Redirection de ports sur votre box

Accéder à l'interface de votre box (192.168.1.1 généralement) :

| Service | Port externe | IP interne | Port interne |
|---------|-------------|------------|--------------|
| HTTP | 80 | IP_SYNOLOGY | 80 |
| HTTPS | 443 | IP_SYNOLOGY | 443 |

### 3. Si vous avez une IP dynamique (change régulièrement)

Utiliser **DynDNS** (gratuit) :

1. Dans DSM : **Panneau de configuration** > **Accès externe** > **DDNS**
2. Cliquer sur **Ajouter**
3. Choisir un fournisseur (Synology recommande Synology DDNS)
4. Créer votre sous-domaine : `monseo.synology.me`
5. Enregistrer

Votre plateforme sera accessible via : `https://monseo.synology.me`

---

## 🔧 Commandes utiles

### Gestion des containers

```bash
# Démarrer tous les services
sudo docker-compose up -d

# Arrêter tous les services
sudo docker-compose down

# Redémarrer un service spécifique
sudo docker-compose restart backend

# Voir les logs en temps réel
sudo docker-compose logs -f

# Reconstruire après modification du code
sudo docker-compose up -d --build

# Voir l'utilisation des ressources
sudo docker stats
```

### Accès aux containers

```bash
# Backend Python
sudo docker exec -it seo-backend /bin/bash

# Frontend PHP
sudo docker exec -it seo-frontend /bin/sh

# Base de données MySQL
sudo docker exec -it seo-database mysql -u root -p
```

### Mise à jour du code

```bash
cd /volume1/docker/seo-platform

# Arrêter les containers
sudo docker-compose down

# Mettre à jour le code (git pull ou copier les nouveaux fichiers)
# ...

# Reconstruire et redémarrer
sudo docker-compose up -d --build
```

---

## 💾 Sauvegarde

### Sauvegarder les données

```bash
# Créer un dossier de backup
sudo mkdir -p /volume1/backups/seo-platform

# Exporter la base de données
sudo docker exec seo-database mysqldump -u root -p'VOTRE_MDP_ROOT' seo_articles \
    > /volume1/backups/seo-platform/db_$(date +%Y%m%d).sql

# Sauvegarder les volumes Docker
sudo docker run --rm -v seo-platform_backend_uploads:/data \
    -v /volume1/backups/seo-platform:/backup \
    alpine tar czf /backup/uploads_$(date +%Y%m%d).tar.gz -C /data .
```

### Automatiser avec Hyper Backup (Interface DSM)

1. Ouvrir **Hyper Backup**
2. Créer une tâche de sauvegarde
3. Sélectionner :
   - `/docker/seo-platform/` (fichiers)
   - `/volume1/backups/seo-platform/` (bases de données)
4. Choisir la destination (disque externe, cloud, etc.)
5. Planifier (quotidien recommandé)

---

## 🐛 Dépannage

### Les containers ne démarrent pas

```bash
# Vérifier les logs
sudo docker-compose logs

# Vérifier l'espace disque
df -h

# Vérifier la RAM disponible
free -h
```

### Erreur de connexion à la base de données

```bash
# Vérifier que le container MySQL est healthy
sudo docker-compose ps

# Vérifier les logs MySQL
sudo docker-compose logs database

# Se connecter manuellement pour tester
sudo docker exec -it seo-database mysql -u seoapp -p
```

### Le site n'est pas accessible

```bash
# Vérifier que le port 8080 est bien mappé
sudo netstat -tlnp | grep 8080

# Vérifier les logs du frontend
sudo docker-compose logs frontend

# Redémarrer le frontend
sudo docker-compose restart frontend
```

### Erreur 502 Bad Gateway

```bash
# Le backend ne répond pas
sudo docker-compose logs backend

# Redémarrer le backend
sudo docker-compose restart backend
```

---

## 📊 Monitoring

### Voir l'utilisation des ressources

Dans DSM :
1. Ouvrir **Moniteur de ressources**
2. Onglet **Docker** pour voir la consommation par container

Via SSH :
```bash
# Temps réel
sudo docker stats

# Utilisation disque
sudo docker system df
```

---

## ⚡ Optimisations

### Augmenter les performances

Éditer `/volume1/docker/seo-platform/.env` :

```env
# Augmenter le nombre de workers Gunicorn (backend)
# Formule : (2 x CPU cores) + 1
# Pour un Synology avec 4 cores : 9 workers
```

Modifier `backend/Dockerfile` :
```dockerfile
CMD ["gunicorn", "--workers", "9", ...]
```

Puis :
```bash
sudo docker-compose up -d --build backend
```

### Limiter l'utilisation mémoire

Éditer `docker-compose.yml` :
```yaml
services:
  backend:
    deploy:
      resources:
        limits:
          memory: 2G
        reservations:
          memory: 1G
```

---

## ✅ Checklist Post-Installation

- [ ] Containers démarrés et healthy
- [ ] Site accessible via `http://SYNOLOGY_IP:8080`
- [ ] Utilisateur admin créé
- [ ] Reverse Proxy configuré
- [ ] Certificat SSL installé (si accès externe)
- [ ] DNS configuré (si accès externe)
- [ ] Ports ouverts sur la box (si accès externe)
- [ ] Test complet des 3 workflows
- [ ] Sauvegarde automatique configurée

---

## 🎉 Félicitations !

Votre plateforme SEO est maintenant hébergée sur votre Synology ! 🏠

### Avantages de cette solution :
- ✅ Aucun coût mensuel
- ✅ Contrôle total des données
- ✅ Performances dédiées
- ✅ Sauvegardes locales

### Accès :
- **Local** : `http://SYNOLOGY_IP:8080`
- **Avec Reverse Proxy** : `http://SYNOLOGY_IP`
- **Depuis internet** : `https://seo.votre-domaine.com`

---

## 📞 Support

### Fichiers de configuration importants
- Docker Compose : `/volume1/docker/seo-platform/docker-compose.yml`
- Configuration : `/volume1/docker/seo-platform/.env`
- Logs : `sudo docker-compose logs [service]`

### Communauté Synology
- Forum officiel : https://community.synology.com/
- Documentation Docker DSM : https://kb.synology.com/DSM/help/Docker/

---

**Profitez de votre plateforme SEO auto-hébergée ! 🚀**