<?php
/**
 * Fonctions utilitaires PHP
 * Fichier: frontend/includes/functions.php
 */

// Charger la configuration de la base de données
require_once __DIR__ . '/../config/database.php';

// URL de l'API Python (depuis .env)
define('PYTHON_API_URL', getenv('PYTHON_API_URL') ?: 'http://localhost:5001');

/**
 * Appeler l'API Python
 */
function callPythonAPI($endpoint, $method = 'GET', $data = null) {
    $url = PYTHON_API_URL . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes timeout
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST' && $data !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
    
    return [
        'success' => true,
        'data' => json_decode($response, true),
        'http_code' => $httpCode
    ];
}

/**
 * ========================================
 * FONCTIONS D'AUTHENTIFICATION
 * ========================================
 */

/**
 * Vérifier si l'utilisateur est connecté et rediriger sinon
 * Usage: requireAuth() au début des pages protégées
 */
function requireAuth() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Vérifier si l'utilisateur est en session
    if (!isset($_SESSION['user']) || !isset($_SESSION['auth_token'])) {
        // Sauvegarder l'URL demandée pour redirection après login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        // Déterminer le chemin relatif correct
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        if (strpos($scriptDir, '/admin') !== false) {
            // Depuis /admin/ -> remonter de 2 niveaux
            header('Location: ../../auth/login.php');
        } elseif (strpos($scriptDir, '/auth') !== false) {
            // Déjà dans /auth/
            header('Location: login.php');
        } else {
            // Depuis /public/
            header('Location: auth/login.php');
        }
        exit;
    }
    
    // Vérifier la validité du token JWT
    $tokenValid = checkAuthToken($_SESSION['auth_token']);
    
    if (!$tokenValid) {
        // Token invalide - déconnecter
        session_destroy();
        
        // Redirection avec même logique
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($scriptDir, '/admin') !== false) {
            header('Location: ../../auth/login.php?error=expired');
        } elseif (strpos($scriptDir, '/auth') !== false) {
            header('Location: login.php?error=expired');
        } else {
            header('Location: auth/login.php?error=expired');
        }
        exit;
    }
    
    return true;
}

/**
 * Vérifier si l'utilisateur est authentifié (version mise à jour)
 */
function isAuthenticated() {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['user']) && isset($_SESSION['auth_token']);
}

/**
 * Vérifier si l'utilisateur est admin et rediriger sinon
 * Usage: requireAdmin() au début des pages d'administration
 */
function requireAdmin() {
    // D'abord vérifier l'authentification
    requireAuth();
    
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Vérifier le rôle admin
    if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
        // Non autorisé - rediriger vers l'accueil avec message
        setFlashMessage('error', 'Accès refusé. Vous devez être administrateur.');
        header('Location: /index.php');
        exit;
    }
    
    return true;
}

/**
 * Vérifier la validité du token JWT auprès du backend Python
 * 
 * @param string $token - Token JWT à vérifier
 * @return bool - True si le token est valide, false sinon
 */
function checkAuthToken($token) {
    if (empty($token)) {
        return false;
    }
    
    try {
        // Appel à l'API de vérification
        $response = callPythonAPI('/api/auth/verify', 'POST', [
            'token' => $token
        ]);
        
        // Vérifier la réponse
        if ($response['success'] && 
            isset($response['data']['success']) && 
            $response['data']['success'] === true &&
            isset($response['data']['valid']) &&
            $response['data']['valid'] === true) {
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        logMessage("Erreur vérification token: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Récupérer le token d'authentification depuis la session
 * 
 * @return string|null - Token JWT ou null si non connecté
 */
function getAuthToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    return $_SESSION['auth_token'] ?? null;
}

/**
 * Vérifier si l'utilisateur actuel est admin
 * 
 * @return bool - True si admin, false sinon
 */
function isAdmin() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Récupérer les informations de l'utilisateur connecté
 * 
 * @return array|null - Données utilisateur ou null si non connecté
 */
function getAuthUser() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    return $_SESSION['user'] ?? null;
}

/**
 * Déconnecter l'utilisateur (appelé depuis logout.php)
 */
function logoutUser() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Nettoyer toutes les variables de session
    $_SESSION = array();
    
    // Détruire le cookie de session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Détruire la session
    session_destroy();
}

/**
 * Appeler l'API Python avec authentification automatique
 * Version améliorée de callPythonAPI() qui ajoute le token automatiquement
 * 
 * @param string $endpoint - Endpoint de l'API
 * @param string $method - Méthode HTTP (GET, POST, PUT, DELETE)
 * @param array|null $data - Données à envoyer
 * @param bool $requiresAuth - Si true, ajoute le token d'authentification
 * @return array - Réponse de l'API
 */
function callAuthenticatedAPI($endpoint, $method = 'GET', $data = null, $requiresAuth = true) {
    $url = PYTHON_API_URL . $endpoint;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    // Ajouter le token d'authentification si nécessaire
    if ($requiresAuth) {
        $token = getAuthToken();
        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Configurer la méthode et les données
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
    
    return [
        'success' => true,
        'data' => json_decode($response, true),
        'http_code' => $httpCode
    ];
}

/**
 * ========================================
 * FIN DE LA FONCTIONS D'AUTHENTIFICATION
 * ========================================
 */

/**
 * Valider une URL
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Nettoyer les données d'entrée
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valider les données du workflow 1
 */
function validateWorkflow1Data($data) {
    $errors = [];
    
    if (empty($data['site_url'])) {
        $errors[] = "L'URL du site est requise";
    } elseif (!validateUrl($data['site_url'])) {
        $errors[] = "L'URL du site n'est pas valide";
    }
    
    if (empty($data['domain'])) {
        $errors[] = "Le domaine est requis";
    }
    
    if (empty($data['guideline'])) {
        $errors[] = "La guideline est requise";
    }
    
    if (empty($data['keyword'])) {
        $errors[] = "Le mot-clé est requis";
    }
    
    return $errors;
}

/**
 * Valider les données du workflow 2 et 3
 */
function validateWorkflow23Data($data) {
    $errors = [];
    
    if (empty($data['article_url'])) {
        $errors[] = "L'URL de l'article est requise";
    } elseif (!validateUrl($data['article_url'])) {
        $errors[] = "L'URL de l'article n'est pas valide";
    }
    
    return $errors;
}

/**
 * Générer un slug à partir d'un texte
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Générer une réponse JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Logger un message
 */
function logMessage($message, $level = 'INFO') {
    $logDir = __DIR__ . '/../../logs/php';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Vérifier si une requête est AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Rediriger vers une page
 */
function redirect($url, $statusCode = 302) {
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Obtenir l'URL de base du site
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $script;
}

/**
 * Inclure une vue
 */
function renderView($view, $data = []) {
    extract($data);
    $viewFile = __DIR__ . '/../views/' . $view . '.php';
    
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        die("Vue non trouvée: $view");
    }
}

/**
 * Afficher un message flash
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupérer et supprimer le message flash
 */
function getFlashMessage() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    return null;
}

/**
 * Vérifier si le backend Python est accessible
 */
function isPythonBackendAvailable() {
    $result = callPythonAPI('/api/test', 'GET');
    return $result['success'] && $result['http_code'] === 200;
}

/**
 * Télécharger une image depuis une URL
 */
function downloadImage($url, $destination) {
    $ch = curl_init($url);
    $fp = fopen($destination, 'wb');
    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $result = curl_exec($ch);
    
    curl_close($ch);
    fclose($fp);
    
    return $result;
}

/**
 * Calculer le temps de lecture estimé
 */
function estimateReadingTime($wordCount) {
    $wordsPerMinute = 200;
    $minutes = ceil($wordCount / $wordsPerMinute);
    return $minutes;
}

/**
 * Tronquer un texte
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Convertir la taille de fichier en format lisible
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}


/**
 * Obtenir l'utilisateur actuel (pour plus tard)
 */
function getCurrentUser() {
    if (!isset($_SESSION)) {
        session_start();
    }
    return $_SESSION['user'] ?? null;
}

/**
 * Protéger contre les attaques CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier le token CSRF
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Créer un breadcrumb
 */
function createBreadcrumb($items) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $item) {
        if (isset($item['url'])) {
            $html .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . 
                     $item['label'] . '</a></li>';
        } else {
            $html .= '<li class="breadcrumb-item active">' . $item['label'] . '</li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}
?>

