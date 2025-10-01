<?php
/**
 * Page de statut du système
 * Fichier: frontend/public/status.php
 */

$pageTitle = "Statut du Système - SEO Article Generator";
require_once '../includes/header.php';
requireAuth(); // Protection : accès réservé aux utilisateurs connectés

// Vérifier le backend Python
function checkBackend() {
    $result = callPythonAPI('/api/test', 'GET');
    return $result['success'] && $result['http_code'] === 200;
}

// Vérifier la base de données
function checkDatabase() {
    try {
        $db = Database::getInstance();
        $result = $db->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Vérifier les dossiers nécessaires
function checkDirectories() {
    $dirs = [
        'uploads' => '../../uploads',
        'temp' => '../../temp',
        'output' => '../../output',
        'logs/php' => '../../logs/php',
        'logs/python' => '../../logs/python'
    ];
    
    $status = [];
    foreach ($dirs as $name => $path) {
        $fullPath = __DIR__ . '/' . $path;
        $status[$name] = [
            'exists' => is_dir($fullPath),
            'writable' => is_writable($fullPath)
        ];
    }
    return $status;
}

$backendStatus = checkBackend();
$dbStatus = checkDatabase();
$dirsStatus = checkDirectories();

?>

<div class="max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="fas fa-heartbeat text-red-500 mr-3"></i>
            Statut du Système
        </h1>
        <p class="text-gray-600">Vérification de l'état de tous les composants</p>
    </div>

    <!-- Statut global -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <?php if ($backendStatus && $dbStatus): ?>
            <div class="text-center">
                <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-600 mb-2">Système Opérationnel</h2>
                <p class="text-gray-600">Tous les composants fonctionnent correctement</p>
            </div>
        <?php else: ?>
            <div class="text-center">
                <div class="bg-red-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-red-600 mb-2">Problème Détecté</h2>
                <p class="text-gray-600">Certains composants nécessitent votre attention</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Détails Backend -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-server text-purple-600 mr-2"></i>
                Backend Python (Flask)
            </h3>
            <?php if ($backendStatus): ?>
                <span class="badge badge-success">
                    <i class="fas fa-check mr-1"></i>En ligne
                </span>
            <?php else: ?>
                <span class="badge badge-error">
                    <i class="fas fa-times mr-1"></i>Hors ligne
                </span>
            <?php endif; ?>
        </div>
        
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">URL:</span>
                <span class="font-mono text-gray-900">http://localhost:5001</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Statut:</span>
                <span class="font-bold <?php echo $backendStatus ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $backendStatus ? 'Accessible' : 'Non accessible'; ?>
                </span>
            </div>
        </div>
        
        <?php if (!$backendStatus): ?>
            <div class="mt-4 bg-red-50 border border-red-200 rounded p-4 text-sm">
                <p class="text-red-800 mb-2"><strong>Action requise :</strong></p>
                <code class="bg-red-100 text-red-900 px-2 py-1 rounded">cd backend && source venv/bin/activate && python app.py</code>
            </div>
        <?php endif; ?>
    </div>

    <!-- Détails Base de données -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-database text-blue-600 mr-2"></i>
                Base de Données
            </h3>
            <?php if ($dbStatus): ?>
                <span class="badge badge-success">
                    <i class="fas fa-check mr-1"></i>Connectée
                </span>
            <?php else: ?>
                <span class="badge badge-error">
                    <i class="fas fa-times mr-1"></i>Déconnectée
                </span>
            <?php endif; ?>
        </div>
        
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Type:</span>
                <span class="font-mono text-gray-900">MySQL</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Host:</span>
                <span class="font-mono text-gray-900"><?php echo getenv('DB_HOST'); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Database:</span>
                <span class="font-mono text-gray-900"><?php echo getenv('DB_NAME'); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Statut:</span>
                <span class="font-bold <?php echo $dbStatus ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $dbStatus ? 'Opérationnelle' : 'Erreur de connexion'; ?>
                </span>
            </div>
        </div>
        
        <?php if ($dbStatus): ?>
            <?php
            $db = Database::getInstance();
            $tables = $db->query("SHOW TABLES");
            ?>
            <div class="mt-4 bg-green-50 border border-green-200 rounded p-4">
                <p class="text-green-800 font-bold mb-2">Tables détectées (<?php echo count($tables); ?>) :</p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tables as $table): ?>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                            <?php echo array_values($table)[0]; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Dossiers système -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-folder text-yellow-600 mr-2"></i>
                Dossiers Système
            </h3>
        </div>
        
        <div class="space-y-3">
            <?php foreach ($dirsStatus as $name => $status): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="font-mono text-sm"><?php echo $name; ?></span>
                    <div class="flex items-center space-x-2">
                        <?php if ($status['exists']): ?>
                            <span class="badge badge-success text-xs">Existe</span>
                        <?php else: ?>
                            <span class="badge badge-error text-xs">Manquant</span>
                        <?php endif; ?>
                        
                        <?php if ($status['exists'] && $status['writable']): ?>
                            <span class="badge badge-info text-xs">Écriture OK</span>
                        <?php elseif ($status['exists']): ?>
                            <span class="badge badge-warning text-xs">Lecture seule</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Configuration PHP -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fab fa-php text-indigo-600 mr-2"></i>
            Configuration PHP
        </h3>
        
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Version PHP:</span>
                <span class="font-mono"><?php echo phpversion(); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">cURL:</span>
                <span class="<?php echo function_exists('curl_version') ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo function_exists('curl_version') ? 'Activé' : 'Désactivé'; ?>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">PDO:</span>
                <span class="<?php echo extension_loaded('pdo') ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo extension_loaded('pdo') ? 'Activé' : 'Désactivé'; ?>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Memory Limit:</span>
                <span class="font-mono"><?php echo ini_get('memory_limit'); ?></span>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-xl p-8 text-white text-center">
        <h3 class="text-2xl font-bold mb-4">Tout est prêt ?</h3>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="index.php" class="bg-white text-purple-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-home mr-2"></i>Retour à l'accueil
            </a>
            <a href="test_connection.php" class="bg-purple-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-purple-400 transition">
                <i class="fas fa-flask mr-2"></i>Tests détaillés
            </a>
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>

<?php require_once '../includes/footer.php'; ?>