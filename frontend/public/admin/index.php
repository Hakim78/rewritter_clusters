<?php
/**
 * Dashboard Admin - Menu principal
 * Fichier: frontend/public/admin/index.php
 */

$pageTitle = "Administration - SEO Article Generator";
require_once '../../includes/header.php';
requireAdmin(); // Protection admin
?>

<!-- Hero Section -->
<div class="max-w-7xl mx-auto mb-12">
    <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-2xl shadow-2xl p-12 text-white text-center">
        <h1 class="text-5xl font-bold mb-4 animate-fade-in">
            <i class="fas fa-shield-alt mr-3"></i>Administration
        </h1>
        <p class="text-xl mb-2 text-red-100">
            Gérez les utilisateurs et configurez les workflows
        </p>
        <p class="text-sm text-red-200">
            Connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'); ?></strong>
        </p>
    </div>
</div>

<!-- Menu principal -->
<div class="max-w-7xl mx-auto mb-12">
    <div class="grid md:grid-cols-2 gap-8">

        <!-- Gestion des utilisateurs -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-8 text-white">
                <div class="bg-white/20 backdrop-blur-sm w-20 h-20 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-users text-5xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-center">Gestion des Utilisateurs</h3>
            </div>
            <div class="p-8">
                <p class="text-gray-600 mb-6 text-center">
                    Créez, modifiez et gérez les accès à la plateforme
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Créer de nouveaux utilisateurs</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Modifier les rôles et permissions</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Désactiver/Supprimer des comptes</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Statistiques d'utilisation</span>
                    </li>
                </ul>
                <a href="users.php" class="block w-full text-center bg-gradient-to-r from-purple-500 to-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-arrow-right mr-2"></i>Accéder
                </a>
            </div>
        </div>

        <!-- Configuration des Prompts -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden border-2 border-blue-300">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-8 text-white relative">
                <div class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">
                    NOUVEAU
                </div>
                <div class="bg-white/20 backdrop-blur-sm w-20 h-20 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-edit text-5xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-center">Configuration des Prompts</h3>
            </div>
            <div class="p-8">
                <p class="text-gray-600 mb-6 text-center">
                    Personnalisez les templates de génération pour chaque workflow
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Modifier les règles d'écriture SEO</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Ajuster le ton et le style</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Optimiser pour LLMO/RAG</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>Backup automatique</span>
                    </li>
                </ul>
                <a href="prompts.php" class="block w-full text-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-arrow-right mr-2"></i>Accéder
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Statistiques rapides -->
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Statistiques Rapides
        </h2>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Utilisateurs</p>
                        <p class="text-3xl font-bold" id="stat-users">-</p>
                    </div>
                    <i class="fas fa-users text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Articles générés</p>
                        <p class="text-3xl font-bold">-</p>
                    </div>
                    <i class="fas fa-file-alt text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Workflows actifs</p>
                        <p class="text-3xl font-bold">3</p>
                    </div>
                    <i class="fas fa-cogs text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Dernière modif</p>
                        <p class="text-sm font-bold" id="stat-last-modified">-</p>
                    </div>
                    <i class="fas fa-clock text-4xl opacity-20"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Charger les statistiques utilisateurs
async function loadStats() {
    try {
        // Charger le nombre d'utilisateurs
        const token = localStorage.getItem('auth_token');
        const response = await fetch('/api/admin/stats', {
		headers: {
	             'Authorization': `Bearer ${token}`
	    }
	});
	
	const data = await response.json();

        if (data.success && data.stats) {
            document.getElementById('stat-users').textContent = data.stats.total || '-';
        }
    } catch (error) {
        console.error('Erreur chargement stats:', error);
    }

    try {
        // Charger la date de dernière modification des prompts
        const response = await fetch('api_prompt.php?action=load&workflow=1');
        const data = await response.json();

        if (data.success && data.lastModified) {
            const date = new Date(data.lastModified * 1000);
            document.getElementById('stat-last-modified').textContent = date.toLocaleDateString('fr-FR');
        }
    } catch (error) {
        console.error('Erreur chargement date:', error);
    }
}

// Charger au démarrage
loadStats();
</script>

<?php require_once '../../includes/footer.php'; ?>
