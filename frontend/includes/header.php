<?php
/**
 * Header principal de l'application
 * Fichier: frontend/includes/header.php
 */

// Démarrer la session si pas déjà fait
if (!isset($_SESSION)) {
    ini_set('session.cookie_lifetime', 0);
    ini_set('session.gc_maxlifetime', 1800);
    session_start();
}

// Charger les dépendances
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Page active pour le menu
$currentPage = basename($_SERVER['PHP_SELF']);

// Vérifier l'authentification pour le menu
$isAuthenticated = isAuthenticated();
$currentUser = getAuthUser();
$isAdminUser = isAdmin();

// Déterminer le chemin de base pour les assets
$inAdminFolder = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$inAuthFolder = strpos($_SERVER['PHP_SELF'], '/auth/') !== false;

// Chemin vers les assets CSS/JS
if ($inAdminFolder) {
    $assetsPath = '../assets';
} elseif ($inAuthFolder) {
    $assetsPath = '../assets';
} else {
    $assetsPath = 'assets';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SEO Article Generator'; ?></title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS personnalisé -->
    <link href="<?php echo $assetsPath; ?>/css/style.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-lg border-b-4 border-purple-600">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="/index.php" class="flex items-center space-x-3 hover:opacity-80 transition">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-3 rounded-lg">
                        <i class="fas fa-pen-fancy text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                            SEO Article Generator
                        </h1>
                        <p class="text-xs text-gray-500">People-first content powered by AI</p>
                    </div>
                </a>
                
                <!-- Menu Navigation Desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/index.php" 
                       class="px-3 py-2 rounded-lg <?php echo $currentPage === 'index.php' ? 'bg-purple-100 text-purple-700 font-bold' : 'text-gray-600 hover:bg-gray-100'; ?> transition">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    
                    <?php if ($isAuthenticated): ?>
                        <!-- Menu pour utilisateurs connectés -->
                        <a href="/option1.php" 
                           class="px-3 py-2 rounded-lg <?php echo $currentPage === 'option1.php' ? 'bg-purple-100 text-purple-700 font-bold' : 'text-gray-600 hover:bg-gray-100'; ?> transition">
                            <i class="fas fa-plus-circle mr-2"></i>Créer
                        </a>
                        <a href="/option2.php" 
                           class="px-3 py-2 rounded-lg <?php echo $currentPage === 'option2.php' ? 'bg-purple-100 text-purple-700 font-bold' : 'text-gray-600 hover:bg-gray-100'; ?> transition">
                            <i class="fas fa-sync-alt mr-2"></i>Réécrire
                        </a>
                        <a href="/option3.php" 
                           class="px-3 py-2 rounded-lg <?php echo $currentPage === 'option3.php' ? 'bg-purple-100 text-purple-700 font-bold' : 'text-gray-600 hover:bg-gray-100'; ?> transition">
                            <i class="fas fa-sitemap mr-2"></i>Cluster
                        </a>
                        
                        <?php if ($isAdminUser): ?>
                            <!-- Menu admin -->
                            <a href="/admin/users.php" 
                               class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-semibold transition">
                                <i class="fas fa-shield-alt mr-2"></i>Admin
                            </a>
                        <?php endif; ?>
                        
                        <!-- Séparateur -->
                        <div class="border-l border-gray-300 h-8 mx-2"></div>
                        
                        <!-- Info utilisateur -->
                        <div class="flex items-center space-x-3 px-3 py-2 bg-gray-50 rounded-lg">
                            <i class="fas fa-user-circle text-2xl text-gray-600"></i>
                            <div class="text-sm">
                                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['name'] ?? 'Utilisateur'); ?></p>
                                <p class="text-xs text-gray-500">
                                    <?php echo $isAdminUser ? 'Administrateur' : 'Utilisateur'; ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Bouton Déconnexion -->
                        <a href="/auth/logout.php" 
                           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </a>
                        
                    <?php else: ?>
                        <!-- Bouton Connexion pour visiteurs -->
                        <a href="/auth/login.php" 
                           class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                        </a>
                    <?php endif; ?>
                    
                    <a href="/test_connection.php" 
                       class="text-gray-400 hover:text-gray-600 transition text-sm">
                        <i class="fas fa-flask mr-1"></i>Test
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-purple-600 p-2">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200 pt-4">
                <a href="/index.php" class="block py-2 px-4 rounded-lg text-gray-600 hover:bg-gray-100 mb-1">
                    <i class="fas fa-home mr-2"></i>Accueil
                </a>
                
                <?php if ($isAuthenticated): ?>
                    <!-- Menu mobile pour utilisateurs connectés -->
                    <a href="/option1.php" class="block py-2 px-4 rounded-lg text-gray-600 hover:bg-gray-100 mb-1">
                        <i class="fas fa-plus-circle mr-2"></i>Créer un article
                    </a>
                    <a href="/option2.php" class="block py-2 px-4 rounded-lg text-gray-600 hover:bg-gray-100 mb-1">
                        <i class="fas fa-sync-alt mr-2"></i>Réécrire un article
                    </a>
                    <a href="/option3.php" class="block py-2 px-4 rounded-lg text-gray-600 hover:bg-gray-100 mb-1">
                        <i class="fas fa-sitemap mr-2"></i>Créer un cluster
                    </a>
                    
                    <?php if ($isAdminUser): ?>
                        <a href="/admin/users.php" class="block py-2 px-4 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-semibold mb-1">
                            <i class="fas fa-shield-alt mr-2"></i>Administration
                        </a>
                    <?php endif; ?>
                    
                    <!-- Info utilisateur mobile -->
                    <div class="border-t border-gray-200 mt-3 pt-3">
                        <div class="px-4 py-2 bg-gray-50 rounded-lg mb-2">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-user-circle text-2xl text-gray-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($currentUser['name'] ?? 'Utilisateur'); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="/auth/logout.php" class="block py-3 px-4 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg text-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </a>
                    </div>
                    
                <?php else: ?>
                    <!-- Bouton connexion mobile -->
                    <a href="/auth/login.php" class="block py-3 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-lg text-center mt-2">
                        <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Messages Flash -->
    <?php
    $flashMessage = getFlashMessage();
    if ($flashMessage):
    ?>
    <div class="container mx-auto px-6 mt-4">
        <div class="bg-<?php echo $flashMessage['type'] === 'success' ? 'green' : 'red'; ?>-100 border-l-4 border-<?php echo $flashMessage['type'] === 'success' ? 'green' : 'red'; ?>-500 text-<?php echo $flashMessage['type'] === 'success' ? 'green' : 'red'; ?>-700 p-4 rounded" role="alert">
            <p><?php echo htmlspecialchars($flashMessage['message']); ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Contenu principal -->
    <main class="container mx-auto px-6 py-8"><?php
// Note: La balise main reste ouverte, elle sera fermée dans footer.php
?>