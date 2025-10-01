<?php
/**
 * Page de déconnexion
 * Fichier: frontend/public/auth/logout.php
 */

session_start();

// Détruire toutes les données de session
$_SESSION = array();

// Détruire le cookie de session si existant
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: login.php?logout=success');
exit;
?>