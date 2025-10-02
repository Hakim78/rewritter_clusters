# 🚀 Plateforme de Génération d'Articles SEO par IA

Plateforme complète de génération et optimisation d'articles SEO utilisant l'intelligence artificielle (Claude, GPT, Perplexity) avec génération d'images automatisée via Ideogram AI.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![Python](https://img.shields.io/badge/python-3.9+-green.svg)
![PHP](https://img.shields.io/badge/php-8.1+-purple.svg)
![License](https://img.shields.io/badge/license-MIT-orange.svg)

---

## 📋 Table des matières

- [Vue d'ensemble](#-vue-densemble)
- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Workflows](#-workflows)
- [Structure du projet](#-structure-du-projet)
- [API Endpoints](#-api-endpoints)
- [Administration](#-administration)
- [Technologies](#-technologies)
- [Contributeurs](#-contributeurs)

---

## 🎯 Vue d'ensemble

Cette plateforme permet de :
- **Créer** des articles SEO optimisés from scratch
- **Réécrire** et optimiser des articles existants
- **Générer des clusters** d'articles (1 pilier + 3 satellites) avec maillage interne automatique

Chaque article est optimisé selon **4 visions expertes** :
1. **SEO** : Optimisation pour les moteurs de recherche
2. **People-First** : Contenu centré utilisateur (Google Core Updates)
3. **LLMO** : Optimisation pour IA générative (ChatGPT, Claude, Gemini)
4. **RAG-Friendly** : Structure pour bases de connaissances vectorielles

---

## ✨ Fonctionnalités

### 🎨 Création de contenu

#### **Workflow 1 : Création d'article**
- Génération d'article SEO à partir d'un brief
- Analyse du site web cible
- Génération d'image IA personnalisée
- FAQ automatique avec schema.org
- Maillage interne et externe

#### **Workflow 2 : Réécriture d'article**
- Scraping d'article existant (URL ou contenu manuel)
- Correction orthographe, grammaire, style
- Mise à jour des données et statistiques
- Optimisation SEO + LLMO + RAG + People-first
- Génération de nouvelle image IA
- FAQ structurée

#### **Workflow 3 : Cluster d'articles**
- **1 Article pilier** : Réécriture et optimisation de l'article existant
- **3 Articles satellites** : Génération d'articles thématiques complémentaires
- **Maillage interne automatique** : Liens bidirectionnels entre tous les articles
- **4 images IA** : Une par article
- **Analyse thématique** : Identification automatique des sous-thèmes
- **FAQ pour chaque article**

### 🎨 Génération d'images
- Intégration **Ideogram AI** pour images réalistes
- Prompts optimisés automatiquement
- Images adaptées au contenu de l'article
- Support résolution haute qualité

### 📊 Suivi en temps réel
- **Barre de progression** pour chaque étape
- **Logs détaillés** de génération
- **Notifications toast** pour feedback utilisateur
- **Sections dépliantes** pour visualisation des résultats

### 🔐 Système d'authentification
- Authentification JWT sécurisée
- Gestion des rôles (admin/user)
- Sessions persistantes
- Protection des routes sensibles

### ⚙️ Panel d'administration
- **Gestion des utilisateurs** : CRUD complet
- **Éditeur de prompts** : Modification des templates IA
- **Versioning des prompts** : Historique et backup
- **Variables dynamiques** : Personnalisation par workflow
- **Statistiques** : Monitoring de l'utilisation

---

## 🏗 Architecture

### Architecture globale
```
┌─────────────────────────────────────────────────────────┐
│                    FRONTEND (PHP)                        │
│  ┌────────────┐  ┌────────────┐  ┌─────────────┐       │
│  │  Option 1  │  │  Option 2  │  │  Option 3   │       │
│  │  (Create)  │  │ (Rewrite)  │  │  (Cluster)  │       │
│  └────────────┘  └────────────┘  └─────────────┘       │
│         │                │                │              │
│         └────────────────┴────────────────┘              │
│                          │                               │
│                     JavaScript                           │
│                    (WorkflowManager)                     │
└─────────────────────────────────────────────────────────┘
                          │ HTTP/JSON
                          ▼
┌─────────────────────────────────────────────────────────┐
│                   BACKEND (Flask/Python)                 │
│  ┌──────────────────────────────────────────────────┐   │
│  │                 API Routes                        │   │
│  │  /api/workflow1  /api/workflow2  /api/workflow3  │   │
│  └──────────────────────────────────────────────────┘   │
│                          │                               │
│  ┌──────────────────────────────────────────────────┐   │
│  │            Workflow Managers                      │   │
│  │  ┌─────────┐  ┌──────────┐  ┌──────────────┐    │   │
│  │  │ WF1 Mgr │  │  WF2 Mgr │  │   WF3 Mgr    │    │   │
│  │  └─────────┘  └──────────┘  └──────────────┘    │   │
│  └──────────────────────────────────────────────────┘   │
│                          │                               │
│  ┌──────────────────────────────────────────────────┐   │
│  │                Step Executors                     │   │
│  │  • Scraper  • Analyzer  • Generator  • Rewriter  │   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
                          │
        ┌─────────────────┼─────────────────┐
        ▼                 ▼                 ▼
   ┌─────────┐      ┌──────────┐     ┌──────────┐
   │  MySQL  │      │ Claude AI│     │ Ideogram │
   │   RDS   │      │ OpenAI   │     │   API    │
   └─────────┘      └──────────┘     └──────────┘
```

### Stack technique
- **Frontend** : PHP 8.1+, Tailwind CSS, Vanilla JavaScript
- **Backend** : Python 3.9+, Flask, asyncio
- **Database** : MySQL 8.0 (AWS RDS)
- **IA** : Claude 3.5 Sonnet, GPT-4, Perplexity
- **Images** : Ideogram AI
- **Auth** : JWT tokens

---

## 📦 Prérequis

### Système
- **Python** 3.9 ou supérieur
- **PHP** 8.1 ou supérieur
- **MySQL** 8.0 ou supérieur
- **Composer** (pour dépendances PHP)
- **pip** (pour dépendances Python)

### Serveur web
- **MAMP/XAMPP** (développement)
- **Apache/Nginx** (production)

### APIs externes (clés requises)
- **Anthropic API** (Claude)
- **OpenAI API** (GPT)
- **Perplexity API**
- **Ideogram API** (génération d'images)

---

## 🚀 Installation

### 1. Cloner le projet
```bash
git clone <repository-url>
cd plateforme_python_clusters
```

### 2. Configuration de la base de données

#### Créer la base de données
```sql
CREATE DATABASE seo_articles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Importer le schéma
```bash
mysql -u root -p seo_articles < database/schema.sql
```

#### Tables principales
- `users` : Utilisateurs et authentification
- `workflows_results` : Résultats des générations
- `prompt_templates` : Templates IA versionnés
- `api_usage_logs` : Logs d'utilisation

### 3. Installation Backend (Python)

```bash
cd backend

# Créer un environnement virtuel
python3 -m venv venv

# Activer l'environnement
source venv/bin/activate  # macOS/Linux
# ou
venv\Scripts\activate  # Windows

# Installer les dépendances
pip install -r requirements.txt
```

### 4. Installation Frontend (PHP)

```bash
cd frontend

# Installer les dépendances PHP (si composer.json existe)
composer install

# Configuration des permissions
chmod -R 755 public/
chmod -R 777 logs/
```

### 5. Configuration des variables d'environnement

Créer un fichier `.env` à la racine du projet :

```bash
cp .env.example .env
```

Éditer `.env` avec vos configurations :

```env
# Application
APP_ENV=development
APP_DEBUG=True
SECRET_KEY=votre-clé-secrète-très-longue

# API Backend
PYTHON_API_URL=http://localhost:5001
PYTHON_API_PORT=5001

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=seo_articles
DB_USER=root
DB_PASSWORD=votre-mot-de-passe

# AI APIs
ANTHROPIC_API_KEY=sk-ant-xxxxx
OPENAI_API_KEY=sk-proj-xxxxx
PERPLEXITY_API_KEY=pplx-xxxxx
IDEOGRAM_API_KEY=xxxxx
```

---

## ⚙️ Configuration

### Backend (Flask)

Le fichier `backend/config.py` contient la configuration principale :

```python
class Config:
    # Database
    DB_HOST = os.getenv('DB_HOST')
    DB_PORT = int(os.getenv('DB_PORT', 3306))

    # APIs
    ANTHROPIC_API_KEY = os.getenv('ANTHROPIC_API_KEY')
    OPENAI_API_KEY = os.getenv('OPENAI_API_KEY')
    IDEOGRAM_API_KEY = os.getenv('IDEOGRAM_API_KEY')

    # Logging
    LOG_LEVEL = 'INFO'
    LOG_FILE = 'logs/python/app.log'
```

### Frontend (PHP)

Le fichier `frontend/config/database.php` configure la connexion MySQL :

```php
<?php
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASSWORD'));
```

---

## 🎮 Workflows

### Workflow 1 : Création d'article

**Endpoint** : `POST /api/workflow1`

**Paramètres** :
```json
{
  "site_url": "https://example.com",
  "domain": "Marketing Digital",
  "keyword": "marketing automation 2025",
  "guideline": "Brief détaillé de l'article...",
  "internal_links": ["url1", "url2"],
  "external_links": ["url3", "url4"]
}
```

**Étapes** :
1. **Website Scraper** : Analyse du site cible
2. **Content Analyzer** : Analyse stratégique du contenu
3. **Article Generator** : Génération de l'article optimisé
4. **Image Generator** : Création de l'image IA

**Durée estimée** : 3-5 minutes

---

### Workflow 2 : Réécriture d'article

**Endpoint** : `POST /api/workflow2`

**Paramètres** :
```json
{
  "article_url": "https://example.com/article",
  "keyword": "mot-clé principal",
  "internal_links": ["url1", "url2"]
}
```

**Étapes** :
1. **Article Scraper** : Extraction de l'article existant
2. **Article Rewriter** : Réécriture et optimisation
3. **Image Generator** : Nouvelle image IA

**Durée estimée** : 4-6 minutes

---

### Workflow 3 : Cluster d'articles

**Endpoint** : `POST /api/workflow3`

**Paramètres** :
```json
{
  "pillar_url": "https://example.com/article-pilier",
  "keyword": "mot-clé principal",
  "generate_images": true
}
```

**Étapes** :
1. **Cluster Analyzer** : Analyse du pilier et identification de 3 thèmes satellites
2. **Pillar Rewriter** : Optimisation du pilier avec liens vers satellites
3. **Satellite Generator** : Génération de 3 articles satellites (appels parallèles)
4. **Image Generator** : 4 images IA (pilier + 3 satellites)

**Résultat** :
- 1 article pilier optimisé
- 3 articles satellites thématiques
- Maillage interne complet (pilier ↔ satellites)
- 4 images IA personnalisées
- FAQ pour chaque article

**Durée estimée** : 10-15 minutes

---

## 📁 Structure du projet

```
plateforme_python_clusters/
│
├── backend/                          # API Python Flask
│   ├── app.py                        # Point d'entrée Flask
│   ├── config.py                     # Configuration
│   ├── requirements.txt              # Dépendances Python
│   │
│   ├── workflows/                    # Logique des workflows
│   │   ├── workflow_1/               # Workflow création
│   │   │   ├── workflow_manager.py
│   │   │   └── steps/
│   │   │       ├── website_scraper.py
│   │   │       ├── content_analyzer.py
│   │   │       ├── article_generator.py
│   │   │       └── image_generator.py
│   │   │
│   │   ├── workflow_2/               # Workflow réécriture
│   │   │   ├── workflow_manager.py
│   │   │   └── steps/
│   │   │       ├── article_scraper.py
│   │   │       ├── article_rewriter.py
│   │   │       └── image_generator.py
│   │   │
│   │   └── workflow_3/               # Workflow cluster
│   │       ├── workflow_manager.py
│   │       └── steps/
│   │           ├── cluster_analyzer.py
│   │           ├── pillar_rewriter.py
│   │           ├── satellite_generator.py
│   │           └── image_generator.py
│   │
│   ├── middleware/                   # Middlewares
│   │   ├── auth_middleware.py        # Authentification JWT
│   │   └── admin_middleware.py       # Vérification admin
│   │
│   ├── models/                       # Modèles de données
│   │   └── user.py
│   │
│   └── utils/                        # Utilitaires
│       └── jwt_helper.py
│
├── frontend/                         # Interface PHP
│   ├── config/
│   │   └── database.php              # Connexion DB
│   │
│   ├── includes/
│   │   ├── header.php                # Header commun
│   │   ├── footer.php                # Footer commun
│   │   └── functions.php             # Fonctions PHP
│   │
│   └── public/
│       ├── index.php                 # Page d'accueil
│       ├── option1.php               # Workflow 1 (création)
│       ├── option2.php               # Workflow 2 (réécriture)
│       ├── option3.php               # Workflow 3 (cluster)
│       ├── loading.php               # Page de progression
│       ├── result.php                # Affichage des résultats
│       │
│       ├── auth/                     # Authentification
│       │   ├── login.php
│       │   ├── logout.php
│       │   └── set_session.php
│       │
│       ├── admin/                    # Panel admin
│       │   ├── index.php
│       │   ├── users.php
│       │   ├── prompts.php
│       │   └── prompt_editor.php
│       │
│       └── assets/
│           ├── css/
│           │   └── style.css
│           └── js/
│               └── app.js            # JavaScript principal
│
├── logs/                             # Logs
│   └── python/
│       └── app.log
│
├── .env                              # Variables d'environnement
├── .gitignore
└── README.md                         # Ce fichier
```

---

## 🔌 API Endpoints

### Authentification

#### `POST /api/login`
Connexion utilisateur

**Body** :
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response** :
```json
{
  "status": "success",
  "token": "eyJ0eXAiOiJKV1QiLCJhbG...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "role": "user"
  }
}
```

---

### Workflows

#### `POST /api/workflow1`
Création d'article SEO

**Headers** :
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Response** :
```json
{
  "status": "success",
  "workflow_id": "wf1_20251002_123456_abc123",
  "message": "Workflow started"
}
```

#### `POST /api/workflow2`
Réécriture d'article

#### `POST /api/workflow3`
Génération de cluster

---

### Suivi de progression

#### `GET /api/workflow-progress/{workflow_id}`
Récupération de la progression en temps réel

**Response** :
```json
{
  "status": "in_progress",
  "progress": 65,
  "current_step": "Generating satellite 2/3",
  "step_details": {
    "step_1": {
      "status": "completed",
      "message": "Analysis completed"
    },
    "step_2": {
      "status": "in_progress",
      "message": "Generating satellites"
    }
  }
}
```

---

### Administration

#### `GET /api/admin/users`
Liste des utilisateurs (admin uniquement)

#### `POST /api/admin/users`
Créer un utilisateur

#### `PUT /api/admin/users/{id}`
Modifier un utilisateur

#### `DELETE /api/admin/users/{id}`
Supprimer un utilisateur

#### `GET /api/admin/prompts`
Liste des templates de prompts

#### `PUT /api/admin/prompts/{workflow_id}`
Mettre à jour un template

---

## 🛡️ Administration

### Accès au panel admin

URL : `http://localhost/plateforme_python_clusters/frontend/public/admin/`

Seuls les utilisateurs avec `role = 'admin'` peuvent accéder.

### Fonctionnalités admin

#### 1. Gestion des utilisateurs
- Créer, modifier, supprimer des utilisateurs
- Gestion des rôles (admin/user)
- Activation/désactivation de comptes

#### 2. Éditeur de prompts
- Modification des templates IA pour chaque workflow
- Variables dynamiques disponibles :
  - Workflow 1 : `{SITE_URL}`, `{DOMAIN}`, `{KEYWORD}`, `{GUIDELINE}`, `{CURRENT_DATE}`
  - Workflow 2 : `{ARTICLE_URL}`, `{ARTICLE_CONTENT}`, `{KEYWORD}`, `{CURRENT_DATE}`
  - Workflow 3 : `{PILLAR_TITLE}`, `{MAIN_KEYWORD}`, `{SATELLITE_NUMBER}`, `{SATELLITE_THEME}`, `{SATELLITE_KEYWORD}`

- Versioning automatique
- Backup des anciennes versions
- Prévisualisation avant sauvegarde

#### 3. Statistiques
- Nombre d'articles générés
- Utilisation des APIs
- Temps moyens de génération

---

## 🛠 Technologies

### Backend
| Technologie | Usage |
|------------|-------|
| **Flask** | Framework web Python |
| **asyncio** | Exécution asynchrone |
| **aiohttp** | Requêtes HTTP async |
| **BeautifulSoup4** | Parsing HTML |
| **PyMySQL** | Connexion MySQL |
| **PyJWT** | Authentification JWT |
| **bcrypt** | Hashing de mots de passe |
| **python-dotenv** | Variables d'environnement |
| **loguru** | Logging avancé |

### Frontend
| Technologie | Usage |
|------------|-------|
| **PHP 8.1+** | Langage serveur |
| **Tailwind CSS** | Framework CSS |
| **JavaScript ES6** | Interactivité |
| **Font Awesome** | Icônes |
| **TinyMCE** | Éditeur WYSIWYG |

### APIs IA
| Service | Usage |
|---------|-------|
| **Claude 3.5 Sonnet** | Génération de contenu |
| **GPT-4** | Analyse et génération |
| **Perplexity** | Recherche d'informations |
| **Ideogram AI** | Génération d'images |

---

## 🚦 Démarrage

### 1. Démarrer le backend Python

```bash
cd backend
source venv/bin/activate
python app.py
```

Le backend sera accessible sur `http://localhost:5001`

### 2. Démarrer MAMP/Apache

Configurer le document root sur :
```
/Applications/MAMP/htdocs/plateforme_python_clusters/frontend/public
```

L'application sera accessible sur `http://localhost:8888`

### 3. Premier compte admin

Créer le premier utilisateur admin via SQL :

```sql
INSERT INTO users (email, password, role, is_active, created_at)
VALUES (
  'admin@example.com',
  '$2b$12$...', -- Hash bcrypt du mot de passe
  'admin',
  1,
  NOW()
);
```

Ou utiliser le script Python :

```bash
cd backend
python -c "
from models.user import User
import bcrypt

password = bcrypt.hashpw('admin123'.encode('utf-8'), bcrypt.gensalt())
# Insérer dans la DB
"
```

---

## 📊 Monitoring et Logs

### Logs Backend
```bash
tail -f backend/logs/python/app.log
```

### Logs en temps réel (workflow 3)
```bash
tail -f backend/logs/python/app.log | grep -E "(Satellite|Step|Workflow)"
```

### Logs MySQL
Configurer le logging dans MySQL pour suivre les requêtes.

---

## 🔒 Sécurité

### Bonnes pratiques implémentées
- ✅ Authentification JWT avec expiration
- ✅ Hashing bcrypt pour les mots de passe
- ✅ Protection CSRF
- ✅ Validation des inputs
- ✅ Sanitization HTML
- ✅ Middleware d'authentification
- ✅ Séparation des rôles (admin/user)
- ✅ Variables d'environnement pour secrets
- ✅ HTTPS en production (recommandé)

### À faire avant la production
- [ ] Changer `SECRET_KEY` dans `.env`
- [ ] Activer HTTPS
- [ ] Configurer rate limiting
- [ ] Auditer les dépendances (`pip audit`, `composer audit`)
- [ ] Backup automatique de la DB
- [ ] Monitoring et alertes

---

## 🐛 Debugging

### Backend ne démarre pas
```bash
# Vérifier les dépendances
pip list

# Vérifier les logs
cat backend/logs/python/app.log

# Tester la connexion DB
python -c "import pymysql; pymysql.connect(host='localhost', user='root')"
```

### Frontend affiche une erreur
```bash
# Vérifier les logs Apache
tail -f /Applications/MAMP/logs/apache_error.log

# Vérifier la connexion backend
curl http://localhost:5001/api/health
```

### Workflow bloqué
```bash
# Vérifier les logs en temps réel
tail -f backend/logs/python/app.log | grep ERROR

# Vérifier l'état du workflow
curl http://localhost:5001/api/workflow-progress/{workflow_id}
```

---

## 📈 Performances

### Temps de génération moyens
- **Workflow 1** : 3-5 minutes
- **Workflow 2** : 4-6 minutes
- **Workflow 3** : 10-15 minutes (4 articles + 4 images)

### Optimisations possibles
- Mise en cache des résultats de scraping
- Parallélisation des appels API
- CDN pour les images générées
- Redis pour les sessions
- Queue workers (Celery) pour les tâches longues

---

## 🤝 Contributeurs

- **Développeur principal** : [Votre nom]
- **Frontend** : PHP + Tailwind CSS
- **Backend** : Python + Flask
- **IA** : Intégration Claude, GPT, Ideogram

---

## 📄 License

MIT License - Voir le fichier `LICENSE` pour plus de détails.

---

## 🆘 Support

Pour toute question ou problème :
1. Consulter les logs (`backend/logs/python/app.log`)
2. Vérifier la configuration (`.env`)
3. Ouvrir une issue sur GitHub

---

## 🎉 Remerciements

- Anthropic (Claude API)
- OpenAI (GPT API)
- Ideogram (Image Generation)
- Communauté open-source

---

**Dernière mise à jour** : Octobre 2025
**Version** : 1.0.0