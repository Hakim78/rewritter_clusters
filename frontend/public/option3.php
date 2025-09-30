<?php
/**
 * Option 3 : Création d'un cluster de 3 articles
 * Fichier: frontend/public/option3.php
 */

$pageTitle = "Créer un cluster d'articles - SEO Article Generator";
require_once '../includes/header.php';
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
                <p class="text-gray-600 mt-2">Réécriture + 2 articles satellites liés avec maillage interne</p>
            </div>
        </div>
        
        <!-- Badges d'info -->
        <div class="flex flex-wrap gap-2 mt-6">
            <span class="badge bg-blue-100 text-blue-800">
                <i class="fas fa-star mr-1"></i> 1 Article principal
            </span>
            <span class="badge bg-indigo-100 text-indigo-800">
                <i class="fas fa-link mr-1"></i> 2 Articles satellites
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
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Article principal -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border-2 border-blue-300">
            <div class="bg-blue-500 text-white w-12 h-12 rounded-full flex items-center justify-center mb-4 mx-auto">
                <i class="fas fa-star text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-blue-900 mb-2">Article Principal</h3>
            <p class="text-sm text-blue-800 text-center">
                Votre article existant, réécrit et optimisé selon les normes RAG LLMO
            </p>
        </div>

        <!-- Article satellite 1 -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-lg border-2 border-indigo-300">
            <div class="bg-indigo-500 text-white w-12 h-12 rounded-full flex items-center justify-center mb-4 mx-auto">
                <i class="fas fa-link text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-indigo-900 mb-2">Article Satellite 1</h3>
            <p class="text-sm text-indigo-800 text-center">
                Nouvel article lié traitant un sous-sujet complémentaire
            </p>
        </div>

        <!-- Article satellite 2 -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border-2 border-purple-300">
            <div class="bg-purple-500 text-white w-12 h-12 rounded-full flex items-center justify-center mb-4 mx-auto">
                <i class="fas fa-link text-xl"></i>
            </div>
            <h3 class="font-bold text-center text-purple-900 mb-2">Article Satellite 2</h3>
            <p class="text-sm text-purple-800 text-center">
                Second article satellite pour renforcer le maillage thématique
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
            
            <!-- URL de l'article principal -->
            <div>
                <label for="article_url" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-star text-blue-600 mr-2"></i>
                    URL de l'article principal <span class="text-red-500">*</span>
                </label>
                <input 
                    type="url" 
                    id="article_url" 
                    name="article_url" 
                    required
                    placeholder="https://example.com/article-principal"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">L'article qui deviendra le pilier du cluster</p>
            </div>

            <!-- Stratégie du cluster -->
            <div>
                <label for="cluster_strategy" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-chess text-blue-600 mr-2"></i>
                    Stratégie du cluster
                </label>
                <select 
                    id="cluster_strategy" 
                    name="cluster_strategy"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                >
                    <option value="auto">Automatique - L'IA choisit les meilleurs sujets satellites</option>
                    <option value="depth">Profondeur - Articles détaillés sur sous-sujets spécifiques</option>
                    <option value="breadth">Largeur - Couvrir plusieurs aspects du sujet principal</option>
                    <option value="mixed">Mixte - Combinaison de profondeur et largeur</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Comment structurer votre cluster</p>
            </div>

            <!-- Ton des articles -->
            <div>
                <label for="tone" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-comments text-blue-600 mr-2"></i>
                    Ton de rédaction
                </label>
                <select 
                    id="tone" 
                    name="tone"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                >
                    <option value="professional">Professionnel</option>
                    <option value="casual">Décontracté</option>
                    <option value="educational">Pédagogique</option>
                    <option value="technical">Technique</option>
                    <option value="conversational" selected>Conversationnel</option>
                </select>
            </div>

            <!-- Options avancées -->
            <div class="bg-blue-50 p-6 rounded-lg">
                <h4 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    Options avancées
                </h4>
                
                <div class="space-y-4">
                    <!-- Générer images pour tous -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Images IA pour chaque article</label>
                            <p class="text-sm text-gray-600">Générer 3 images personnalisées</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="generate_all_images" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Optimiser maillage -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Maillage interne optimisé</label>
                            <p class="text-sm text-gray-600">Liens bidirectionnels entre tous les articles</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="bidirectional_links" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Longueur des satellites -->
                    <div>
                        <label for="satellite_length" class="block font-medium text-gray-700 mb-2">
                            Longueur des articles satellites
                        </label>
                        <select 
                            id="satellite_length" 
                            name="satellite_length"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        >
                            <option value="short">Courts (~800 mots)</option>
                            <option value="medium" selected>Moyens (~1200 mots)</option>
                            <option value="long">Longs (~2000 mots)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Mots-clés suggérés (optionnel) -->
            <div>
                <label for="suggested_keywords" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-blue-600 mr-2"></i>
                    Mots-clés suggérés pour les satellites (optionnel)
                </label>
                <textarea 
                    id="suggested_keywords" 
                    name="suggested_keywords" 
                    rows="2"
                    placeholder="marketing automation, email marketing, lead nurturing (séparés par des virgules)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                ></textarea>
                <p class="text-sm text-gray-500 mt-1">Laissez vide pour laisser l'IA choisir automatiquement</p>
            </div>

            <!-- Boutons de soumission -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Temps estimé : 8-12 minutes
                    <span class="block text-xs mt-1 text-gray-500">Génération de 3 articles complets</span>
                </div>
                <div class="space-x-4">
                    <button
                        type="button"
                        id="test-backend-btn"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition"
                    >
                        <i class="fas fa-flask mr-2"></i>
                        Tester Backend
                    </button>
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
        <p class="text-gray-600 mb-4">Génération de 3 articles optimisés et liés entre eux.</p>
        <div class="mt-6">
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: 15%" id="progress-fill"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2" id="progress-text">Étape : Analyse de l'article principal</p>
        </div>
        <div class="mt-6 space-y-2 text-left max-w-md mx-auto">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-circle text-blue-500 mr-2"></i>
                <span>Analyse et réécriture de l'article principal</span>
            </div>
            <div class="flex items-center text-sm text-gray-400">
                <i class="far fa-circle mr-2"></i>
                <span>Génération de l'article satellite 1</span>
            </div>
            <div class="flex items-center text-sm text-gray-400">
                <i class="far fa-circle mr-2"></i>
                <span>Génération de l'article satellite 2</span>
            </div>
            <div class="flex items-center text-sm text-gray-400">
                <i class="far fa-circle mr-2"></i>
                <span>Création du maillage interne</span>
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
                'Analyse de l\'article principal',
                'Réécriture et optimisation',
                'Génération article satellite 1',
                'Génération article satellite 2',
                'Création du maillage interne',
                'Finalisation'
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
            }, 15000); // Toutes les 15 secondes
        });

        // Gestionnaire pour le bouton de test backend
        const testButton = document.getElementById('test-backend-btn');
        if (testButton) {
            testButton.addEventListener('click', async function() {
                // Récupérer les données du formulaire
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Ajouter des données de test si les champs sont vides
                if (!data.article_url) data.article_url = 'https://example.com/article-cluster-principal';

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
