-- Schéma de base de données pour SEO Article Generator
-- Fichier: database/migrations/init.sql

-- Base de données
CREATE DATABASE IF NOT EXISTS seo_articles 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE seo_articles;

-- Table des utilisateurs (optionnel pour plus tard)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des projets/sites
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    site_url VARCHAR(500) NOT NULL,
    site_name VARCHAR(255),
    domain VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_site_url (site_url(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des requêtes/jobs
CREATE TABLE IF NOT EXISTS article_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    workflow_type ENUM('creation', 'rewrite', 'cluster') NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    
    -- Paramètres de la requête (JSON)
    input_params JSON,
    
    -- Résultats
    result_data JSON,
    error_message TEXT,
    
    -- Métadonnées
    processing_time INT COMMENT 'Temps en secondes',
    api_calls_count INT DEFAULT 0,
    
    -- Dates
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_workflow (workflow_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des articles générés
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    
    -- Type d'article
    article_type ENUM('main', 'satellite') DEFAULT 'main',
    cluster_id INT COMMENT 'Pour lier les articles d un cluster',
    
    -- Contenu
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500),
    html_content LONGTEXT NOT NULL,
    meta_description TEXT,
    keywords JSON,
    
    -- SEO
    word_count INT,
    seo_score DECIMAL(5,2),
    readability_score DECIMAL(5,2),
    
    -- Image
    featured_image_url VARCHAR(1000),
    featured_image_alt VARCHAR(500),
    
    -- Liens internes
    internal_links JSON,
    
    -- Dates
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (request_id) REFERENCES article_requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_cluster (cluster_id),
    INDEX idx_slug (slug(255)),
    FULLTEXT idx_content (title, html_content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de logs (optionnel)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT,
    level ENUM('DEBUG', 'INFO', 'WARNING', 'ERROR') DEFAULT 'INFO',
    message TEXT,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (request_id) REFERENCES article_requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_level (level),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des statistiques (optionnel)
CREATE TABLE IF NOT EXISTS stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    workflow_type ENUM('creation', 'rewrite', 'cluster'),
    requests_count INT DEFAULT 0,
    success_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    avg_processing_time INT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_date_workflow (date, workflow_type),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données de test (optionnel)
INSERT INTO users (email, name) VALUES 
('test@example.com', 'Test User');

INSERT INTO projects (user_id, site_url, site_name, domain) VALUES 
(1, 'https://example.com', 'Mon Site Test', 'Marketing Digital');

-- Vues utiles (optionnel)
CREATE OR REPLACE VIEW v_recent_requests AS
SELECT 
    ar.id,
    ar.workflow_type,
    ar.status,
    p.site_name,
    ar.processing_time,
    ar.created_at,
    COUNT(a.id) as articles_count
FROM article_requests ar
LEFT JOIN projects p ON ar.project_id = p.id
LEFT JOIN articles a ON ar.id = a.request_id
GROUP BY ar.id
ORDER BY ar.created_at DESC
LIMIT 100;

-- Procédures stockées pour les statistiques (optionnel)
DELIMITER //

CREATE PROCEDURE update_daily_stats(IN target_date DATE)
BEGIN
    INSERT INTO stats (date, workflow_type, requests_count, success_count, failed_count, avg_processing_time)
    SELECT 
        DATE(created_at) as date,
        workflow_type,
        COUNT(*) as requests_count,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as success_count,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
        AVG(processing_time) as avg_processing_time
    FROM article_requests
    WHERE DATE(created_at) = target_date
    GROUP BY DATE(created_at), workflow_type
    ON DUPLICATE KEY UPDATE
        requests_count = VALUES(requests_count),
        success_count = VALUES(success_count),
        failed_count = VALUES(failed_count),
        avg_processing_time = VALUES(avg_processing_time);
END //

DELIMITER ;

-- Afficher la structure créée
SHOW TABLES;
