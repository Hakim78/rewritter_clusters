<?php
/**
 * Configuration et connexion à la base de données
 * Fichier: frontend/config/database.php
 */

// Charger les variables d'environnement depuis le fichier .env parent
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parser la ligne
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Supprimer les guillemets
        $value = trim($value, '"\'');
        
        // Définir la variable d'environnement
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Charger .env depuis la racine du projet
loadEnv(__DIR__ . '/../../.env');

// Configuration de la base de données
class Database {
    private static $instance = null;
    private $connection;
    
    // Paramètres de connexion
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;
    
    private function __construct() {
        // Récupérer les variables d'environnement
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->port = getenv('DB_PORT') ?: '3306';
        $this->database = getenv('DB_NAME') ?: 'seo_articles';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        
        $this->connect();
    }
    
    /**
     * Singleton - Obtenir l'instance unique
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Établir la connexion à la base de données
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir la connexion PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Exécuter une requête SELECT
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur de requête: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exécuter une requête INSERT/UPDATE/DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur d'exécution: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Démarrer une transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Annuler une transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Fermer la connexion
     */
    public function close() {
        $this->connection = null;
    }
}

/**
 * Helper function pour obtenir une connexion PDO
 */
function getDBConnection() {
    return Database::getInstance()->getConnection();
}

/**
 * Classe pour gérer les requêtes d'articles
 */
class ArticleRequestModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Créer une nouvelle requête
     */
    public function create($projectId, $workflowType, $inputParams) {
        $sql = "INSERT INTO article_requests (project_id, workflow_type, status, input_params) 
                VALUES (?, ?, 'pending', ?)";
        
        $params = [
            $projectId,
            $workflowType,
            json_encode($inputParams)
        ];
        
        if ($this->db->execute($sql, $params)) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Mettre à jour le statut d'une requête
     */
    public function updateStatus($requestId, $status, $resultData = null, $error = null) {
        $sql = "UPDATE article_requests 
                SET status = ?, result_data = ?, error_message = ?, 
                    completed_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $params = [
            $status,
            $resultData ? json_encode($resultData) : null,
            $error,
            $requestId
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Récupérer une requête par ID
     */
    public function getById($requestId) {
        $sql = "SELECT * FROM article_requests WHERE id = ?";
        $results = $this->db->query($sql, [$requestId]);
        return $results ? $results[0] : null;
    }
    
    /**
     * Récupérer les requêtes récentes
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT ar.*, p.site_name 
                FROM article_requests ar
                LEFT JOIN projects p ON ar.project_id = p.id
                ORDER BY ar.created_at DESC 
                LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
}

/**
 * Classe pour gérer les articles
 */
class ArticleModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Créer un nouvel article
     */
    public function create($requestId, $data) {
        $sql = "INSERT INTO articles 
                (request_id, article_type, cluster_id, title, slug, html_content, 
                 meta_description, keywords, word_count, featured_image_url, 
                 featured_image_alt, internal_links) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $requestId,
            $data['article_type'] ?? 'main',
            $data['cluster_id'] ?? null,
            $data['title'],
            $data['slug'] ?? $this->generateSlug($data['title']),
            $data['html_content'],
            $data['meta_description'] ?? null,
            isset($data['keywords']) ? json_encode($data['keywords']) : null,
            $data['word_count'] ?? null,
            $data['featured_image_url'] ?? null,
            $data['featured_image_alt'] ?? null,
            isset($data['internal_links']) ? json_encode($data['internal_links']) : null
        ];
        
        if ($this->db->execute($sql, $params)) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Récupérer les articles d'une requête
     */
    public function getByRequestId($requestId) {
        $sql = "SELECT * FROM articles WHERE request_id = ? ORDER BY created_at DESC";
        return $this->db->query($sql, [$requestId]);
    }
    
    /**
     * Générer un slug à partir d'un titre
     */
    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}

// Empêcher les accès directs
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    die('Accès direct interdit');
}
?>
