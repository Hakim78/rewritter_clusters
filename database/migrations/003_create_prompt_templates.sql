-- Migration: Système de gestion des prompts templates avec versioning
-- Date: 2025-01-10
-- Description: Crée les tables pour stocker les prompts en BDD avec historique complet

-- Table principale des templates de prompts
CREATE TABLE IF NOT EXISTS prompt_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workflow_id INT NOT NULL COMMENT 'ID du workflow (1, 2, 3)',
    version INT NOT NULL COMMENT 'Numéro de version du template',
    content TEXT NOT NULL COMMENT 'Contenu complet du template',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
    created_by INT NOT NULL COMMENT 'ID de l\'utilisateur créateur',
    is_active BOOLEAN DEFAULT FALSE COMMENT 'Version actuellement active',
    notes TEXT COMMENT 'Notes de version (changements effectués)',
    backup_file VARCHAR(255) COMMENT 'Chemin du fichier backup original',

    -- Contraintes
    CONSTRAINT fk_template_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,

    -- Index pour performance
    INDEX idx_workflow_version (workflow_id, version),
    INDEX idx_workflow_active (workflow_id, is_active),
    INDEX idx_created_at (created_at DESC),

    -- Contrainte unique : un seul template actif par workflow
    UNIQUE KEY unique_active_per_workflow (workflow_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stockage des templates de prompts avec versioning complet';

-- Table des variables disponibles dans les prompts
CREATE TABLE IF NOT EXISTS prompt_variables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nom de la variable (ex: KEYWORD)',
    description TEXT COMMENT 'Description de la variable',
    example VARCHAR(255) COMMENT 'Exemple de valeur',
    is_required BOOLEAN DEFAULT TRUE COMMENT 'Variable obligatoire',
    default_value TEXT COMMENT 'Valeur par défaut si non fournie',
    data_type ENUM('string', 'text', 'list', 'date') DEFAULT 'string' COMMENT 'Type de données',
    workflow_specific INT DEFAULT NULL COMMENT 'Si NULL = toutes workflows, sinon workflow spécifique',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_required (is_required),
    INDEX idx_workflow (workflow_specific)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Définition des variables disponibles dans les templates';

-- Table de log des modifications (audit trail)
CREATE TABLE IF NOT EXISTS prompt_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL COMMENT 'ID du template modifié',
    action ENUM('create', 'activate', 'deactivate', 'view') NOT NULL COMMENT 'Action effectuée',
    user_id INT NOT NULL COMMENT 'Utilisateur ayant effectué l\'action',
    ip_address VARCHAR(45) COMMENT 'Adresse IP de l\'utilisateur',
    user_agent TEXT COMMENT 'User agent du navigateur',
    details TEXT COMMENT 'Détails supplémentaires (JSON)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_template FOREIGN KEY (template_id) REFERENCES prompt_templates(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_template (template_id),
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Journal d\'audit complet de toutes les modifications de templates';

-- Insertion des variables standard
INSERT INTO prompt_variables (name, description, example, is_required, data_type) VALUES
('DOMAIN', 'Domaine d\'activité de l\'entreprise', 'Technologie', TRUE, 'string'),
('KEYWORD', 'Mot-clé principal de l\'article', 'Python API', TRUE, 'string'),
('GUIDELINE', 'Brief utilisateur et consignes spécifiques', 'Article technique approfondi', TRUE, 'text'),
('SITE_URL', 'URL du site web', 'https://example.com', TRUE, 'string'),
('CONTENT_TONE', 'Ton du contenu identifié', 'Professionnel', FALSE, 'string'),
('TARGET_AUDIENCE', 'Audience cible', 'Développeurs', FALSE, 'string'),
('MAIN_TOPICS', 'Thèmes principaux du site', 'Python, API, Backend', FALSE, 'list'),
('SEO_OPPORTUNITIES', 'Opportunités SEO identifiées', 'Optimiser les meta tags', FALSE, 'list'),
('CONTENT_GAPS', 'Lacunes de contenu identifiées', 'Manque de tutoriels', FALSE, 'list'),
('CONTENT_STRATEGY', 'Stratégie de contenu recommandée', 'Focus technique', FALSE, 'text'),
('KEYWORD_OPPORTUNITIES', 'Mots-clés secondaires suggérés', 'python api, backend dev', FALSE, 'list'),
('INTERNAL_LINKS', 'Liens internes à intégrer', 'https://example.com/article1', FALSE, 'list'),
('EXTERNAL_REFS', 'Références externes pour contexte', 'example.com: Test Article', FALSE, 'list'),
('CURRENT_DATE', 'Date actuelle (YYYY-MM-DD)', '2025-01-10', TRUE, 'date');

-- Insertion de variables spécifiques au workflow 2
INSERT INTO prompt_variables (name, description, example, is_required, data_type, workflow_specific) VALUES
('ORIGINAL_CONTENT', 'Contenu original à réécrire', 'Article existant...', TRUE, 'text', 2);

-- Insertion de variables spécifiques au workflow 3
INSERT INTO prompt_variables (name, description, example, is_required, data_type, workflow_specific) VALUES
('CLUSTER_KEYWORDS', 'Mots-clés pour articles satellites', 'keyword1, keyword2', TRUE, 'list', 3);