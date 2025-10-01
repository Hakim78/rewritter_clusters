<?php
/**
 * Helper pour stocker le token et user en session PHP
 * Fichier: frontend/public/auth/set_session.php
 */

session_start();
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['token']) && isset($input['user'])) {
        $_SESSION['auth_token'] = $input['token'];
        $_SESSION['user'] = $input['user'];
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>