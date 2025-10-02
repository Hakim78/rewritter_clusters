<?php
/**
 * Option 3 : Création d'un cluster de 3 articles
 * Fichier: frontend/public/option3.php
 */

$pageTitle = "Créer un cluster d'articles - SEO Article Generator";
require_once '../includes/header.php';
requireAuth(); // Vérification de l'authentification
?>

<div class="max-w-5xl mx-auto">
    <!-- En-tête de la page -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 rounded-lg mr-4">
                <i class="fas fa-sitemap text-white text-3xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Créer un cluster d'articles</h1>
                <p class="text-gray-600 mt-2">1 Pilier optimisé + 3 articles satellites avec maillage complet</p>
            </div>
        </div>

        <!-- Badges d'info -->
        <div class="flex flex-wrap gap-2 mt-6">
            <span class="badge bg-blue-100 text-blue-800">
                <i class="fas fa-star mr-1"></i> 1 Pilier optimisé
            </span>
            <span class="badge bg-indigo-100 text-indigo-800">
                <i class="fas fa-link mr-1"></i> 3 Articles satellites
            </span>
            <span class="badge badge-success">
                <i class="fas fa-project-diagram mr-1"></i> Maillage auto
            </span>
            <span class="badge badge-info">
                <i class="fas fa-search mr-1"></i> SEO optimisé
            </span>
        </div>
    </div>

    <!-- Explication du cluster -->
    <div class="grid md:grid-cols-4 gap-4 mb-8">
        <!-- Article pilier -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border-2 border-blue-400">
            <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mb-3 mx-auto">
                <i class="fas fa-star text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-blue-900 mb-2">Article Pilier</h3>
            <p class="text-sm text-blue-800 text-center">
                Votre article existant réécrit et optimisé RAG/LLMO/SEO
            </p>
        </div>

        <!-- Article satellite 1 -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-lg border-2 border-indigo-300">
            <div class="bg-indigo-500 text-white w-12 h-12 rounded-full flex items-center justify-center mb-3 mx-auto">
                <i class="fas fa-link text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-indigo-900 mb-2">Satellite 1</h3>
            <p class="text-sm text-indigo-800 text-center">
                Article complémentaire sur un sous-thème identifié
            </p>
        </div>

        <!-- Article satellite 2 -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border-2 border-purple-300">
            <div class="bg-purple-500 text-white w-12 h-12 rounded-full flex items-center justify-center mb-3 mx-auto">
                <i class="fas fa-link text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-purple-900 mb-2">Satellite 2</h3>
            <p class="text-sm text-purple-800 text-center">
                Deuxième article satellite thématique
            </p>
        </div>

        <!-- Article satellite 3 -->
        <div class="bg-gradient-to-br from-pink-50 to-pink-100 p-6 rounded-lg border-2 border-pink-300">
            <div class="bg-pink-500 text-white w-12 h-12 rounded-full flex items-center justify-center mb-3 mx-auto">
                <i class="fas fa-link text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-pink-900 mb-2">Satellite 3</h3>
            <p class="text-sm text-pink-800 text-center">
                Troisième article satellite pour couvrir tous les aspects
            </p>
        </div>
    </div>

    <!-- Avantages du cluster -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
        <h3 class="font-bold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-trophy mr-2"></i>
            Pourquoi créer un cluster ?
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                <span class="text-blue-800"><strong>Autorité thématique :</strong> Google reconnaît votre expertise</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                <span class="text-blue-800"><strong>Maillage interne :</strong> Renforce le SEO de toutes les pages</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                <span class="text-blue-800"><strong>Couverture complète :</strong> Répond à toutes les questions des utilisateurs</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                <span class="text-blue-800"><strong>Trafic multiplié :</strong> Plus de portes d'entrée vers votre contenu</span>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <form id="workflow3-form" class="space-y-6">

            <!-- URL de l'article pilier -->
            <div>
                <label for="pillar_url" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-star text-blue-600 mr-2"></i>
                    URL de l'article pilier <span class="text-red-500">*</span>
                </label>
                <input
                    type="url"
                    id="pillar_url"
                    name="pillar_url"
                    required
                    placeholder="https://example.com/article-pilier"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">L'article existant qui deviendra le pilier optimisé du cluster</p>
            </div>

            <!-- Mot-clé principal -->
            <div>
                <label for="keyword" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-blue-600 mr-2"></i>
                    Mot-clé principal du cluster <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    required
                    placeholder="Ex: isolation thermique, marketing automation"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">Le mot-clé SEO principal autour duquel le cluster sera construit</p>
            </div>

            <!-- Options avancées -->
            <div class="bg-blue-50 p-6 rounded-lg">
                <h4 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    Options
                </h4>

                <div class="space-y-4">
                    <!-- Générer images pour tous -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Générer les images IA</label>
                            <p class="text-sm text-gray-600">4 images (pilier + 3 satellites)</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="generate_images" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Temps estimé : 10-15 minutes
                    <span class="block text-xs mt-1 text-gray-500">Génération de 4 articles complets avec maillage</span>
                </div>
                <div class="space-x-4">
                    <button
                        type="submit"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition"
                    >
                        <i class="fas fa-rocket mr-2"></i>
                        Créer le cluster
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Loader -->
    <div id="loader" class="hidden bg-white rounded-lg shadow-xl p-12 text-center">
        <div class="loader mx-auto mb-6"></div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Création du cluster en cours...</h3>
        <p class="text-gray-600 mb-4">Génération de 1 pilier + 3 satellites avec maillage complet.</p>
        <div class="mt-6">
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: 15%" id="progress-fill"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2" id="progress-text">Étape : Analyse du pilier</p>
        </div>
        <div class="mt-6 space-y-2 text-left max-w-md mx-auto">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-circle text-blue-500 mr-2"></i>
                <span>Analyse du pilier et identification des thèmes</span>
            </div>
            <div class="flex items-center text-sm text-gray-400">
                <i class="far fa-circle mr-2"></i>
                <span>Réécriture et optimisation du pilier</span>
            </div>
            <div class="flex items-center text-sm text-gray-400">
                <i class="far fa-circle mr-2"></i>
                <span>Génération des 3 articles satellites</span>
            </div>
            <div class="flex items-center text-sm text-gray-400">
                <i class="far fa-circle mr-2"></i>
                <span>Génération des images IA</span>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div id="results" class="hidden"></div>
</div>

<script src="assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new WorkflowManager(3);
        
        // Animation progressive
        const form = document.getElementById('workflow3-form');
        form.addEventListener('submit', function() {
            const steps = [
                'Analyse du pilier et identification des thèmes',
                'Réécriture et optimisation du pilier',
                'Génération des 3 articles satellites',
                'Génération des images IA',
                'Finalisation du maillage'
            ];

            let currentStep = 0;
            const progressBar = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');

            const interval = setInterval(() => {
                currentStep++;
                if (currentStep >= steps.length) {
                    clearInterval(interval);
                    return;
                }

                const progress = (currentStep / steps.length) * 90;
                progressBar.style.width = progress + '%';
                progressText.textContent = 'Étape : ' + steps[currentStep];
            }, 20000); // Toutes les 20 secondes (plus long pour workflow 3)
        });

        // Gestionnaire pour le bouton de test backend
        const testButton = document.getElementById('test-backend-btn');
        if (testButton) {
            testButton.addEventListener('click', async function() {
                // Récupérer les données du formulaire
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Ajouter des données de test si les champs sont vides
                if (!data.pillar_url) data.pillar_url = 'https://example.com/article-pilier';
                if (!data.keyword) data.keyword = 'test cluster generation';

                try {
                    // Désactiver le bouton pendant le test
                    testButton.disabled = true;
                    testButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Test en cours...';

                    // Appel à l'API de test
                    const response = await fetch(`${CONFIG.API_URL}/api/workflow3`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        Toast.show('Test réussi ! Redirection vers les résultats...', 'success');

                        // Stocker les résultats du test dans sessionStorage
                        sessionStorage.setItem('testResults', JSON.stringify(result));

                        // Rediriger vers la page de résultats en mode test
                        setTimeout(() => {
                            window.location.href = 'result.php?test=1&workflow=3';
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Erreur lors du test');
                    }

                } catch (error) {
                    Toast.show(`Erreur de test: ${error.message}`, 'error');
                    console.error('Test error:', error);
                } finally {
                    // Réactiver le bouton
                    testButton.disabled = false;
                    testButton.innerHTML = '<i class="fas fa-flask mr-2"></i>Tester Backend';
                }
            });
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
