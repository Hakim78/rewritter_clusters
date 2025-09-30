<?php
/**
 * Option 2 : Réécriture d'un article existant
 * Fichier: frontend/public/option2.php
 */

$pageTitle = "Réécrire un article - SEO Article Generator";
require_once '../includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <!-- En-tête de la page -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-r from-green-500 to-teal-500 p-4 rounded-lg mr-4">
                <i class="fas fa-sync-alt text-white text-3xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Réécrire un article</h1>
                <p class="text-gray-600 mt-2">Optimisation SEO et mise aux normes RAG LLMO People-first</p>
            </div>
        </div>
        
        <!-- Badges d'info -->
        <div class="flex flex-wrap gap-2 mt-6">
            <span class="badge badge-success">
                <i class="fas fa-arrow-up mr-1"></i> Amélioration SEO
            </span>
            <span class="badge badge-info">
                <i class="fas fa-check-double mr-1"></i> RAG LLMO
            </span>
            <span class="badge badge-warning">
                <i class="fas fa-users mr-1"></i> People-first
            </span>
            <span class="badge bg-green-100 text-green-800">
                <i class="fas fa-image mr-1"></i> Nouvelle image IA
            </span>
        </div>
    </div>

    <!-- Informations sur le processus -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
        <h3 class="font-bold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Comment ça fonctionne ?
        </h3>
        <ul class="space-y-2 text-blue-800">
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Analyse complète de votre article existant</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Optimisation SEO selon les dernières normes Google</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Adaptation au format People-first pour meilleur ranking</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Génération d'une nouvelle image optimisée IA</span>
            </li>
        </ul>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <form id="workflow2-form" class="space-y-6">
            
            <!-- URL de l'article -->
            <div>
                <label for="article_url" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-link text-green-600 mr-2"></i>
                    URL de l'article à réécrire <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <input 
                        type="url" 
                        id="article_url" 
                        name="article_url" 
                        required
                        placeholder="https://example.com/mon-article"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />
                    <button 
                        type="button" 
                        id="preview-btn"
                        class="px-6 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-r-lg border border-l-0 border-gray-300 transition"
                    >
                        <i class="fas fa-eye mr-2"></i>Prévisualiser
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-1">L'URL complète de l'article que vous souhaitez optimiser</p>
            </div>

            <!-- Aperçu de l'article (si prévisualisé) -->
            <div id="article-preview" class="hidden bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h4 class="font-bold text-gray-900 mb-3">
                    <i class="fas fa-eye text-green-600 mr-2"></i>
                    Aperçu de l'article
                </h4>
                <div id="preview-content" class="text-gray-700">
                    <!-- Sera rempli dynamiquement -->
                </div>
            </div>

            <!-- Options d'optimisation -->
            <div class="bg-green-50 p-6 rounded-lg">
                <h4 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-sliders-h text-green-600 mr-2"></i>
                    Options d'optimisation
                </h4>
                
                <div class="space-y-4">
                    <!-- Conserver le style -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Conserver le style d'écriture</label>
                            <p class="text-sm text-gray-600">Garder le ton et la voix de l'auteur original</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="keep_style" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Ajouter des sections -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Enrichir avec de nouvelles sections</label>
                            <p class="text-sm text-gray-600">Ajouter du contenu complémentaire pertinent</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="add_sections" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Optimiser images -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Générer nouvelle image principale</label>
                            <p class="text-sm text-gray-600">Créer une image optimisée par IA</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="generate_image" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Maillage interne -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-700">Optimiser le maillage interne</label>
                            <p class="text-sm text-gray-600">Améliorer les liens internes existants</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="optimize_links" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Niveau d'optimisation -->
            <div>
                <label for="optimization_level" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>
                    Niveau d'optimisation SEO
                </label>
                <select 
                    id="optimization_level" 
                    name="optimization_level"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                    <option value="light">Légère - Corrections mineures uniquement</option>
                    <option value="moderate" selected>Modérée - Équilibre entre changements et conservation</option>
                    <option value="aggressive">Agressive - Réécriture complète pour SEO maximum</option>
                </select>
            </div>

            <!-- Boutons de soumission -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Temps estimé : 4-6 minutes
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
                        class="bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition"
                    >
                        <i class="fas fa-sync-alt mr-2"></i>
                        Réécrire et optimiser
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Loader -->
    <div id="loader" class="hidden bg-white rounded-lg shadow-xl p-12 text-center">
        <div class="loader mx-auto mb-6"></div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Réécriture en cours...</h3>
        <p class="text-gray-600">Analyse et optimisation de votre article...</p>
        <div class="mt-6">
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: 20%" id="progress-fill"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Étape : Analyse de l'article original</p>
        </div>
    </div>

    <!-- Résultats -->
    <div id="results" class="hidden"></div>
</div>

<script src="assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new WorkflowManager(2);
        
        // Preview button
        document.getElementById('preview-btn')?.addEventListener('click', async function() {
            const url = document.getElementById('article_url').value;
            if (!url) {
                Toast.show('Veuillez entrer une URL', 'error');
                return;
            }

            // Simuler la preview (à remplacer par un vrai appel API)
            document.getElementById('article-preview').classList.remove('hidden');
            document.getElementById('preview-content').innerHTML = `
                <div class="skeleton h-4 w-3/4 mb-3"></div>
                <div class="skeleton h-4 w-full mb-3"></div>
                <div class="skeleton h-4 w-5/6"></div>
            `;

            // Simulation
            setTimeout(() => {
                document.getElementById('preview-content').innerHTML = `
                    <div class="text-sm">
                        <strong>Titre détecté:</strong> Article exemple<br>
                        <strong>Mots:</strong> ~1200<br>
                        <strong>État SEO:</strong> À améliorer
                    </div>
                `;
            }, 1000);
        });

        // Gestionnaire pour le bouton de test backend
        const form = document.getElementById('workflow2-form');
        const testButton = document.getElementById('test-backend-btn');
        if (testButton) {
            testButton.addEventListener('click', async function() {
                // Récupérer les données du formulaire
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Ajouter des données de test si les champs sont vides
                if (!data.article_url) data.article_url = 'https://example.com/article-existant';

                try {
                    // Désactiver le bouton pendant le test
                    testButton.disabled = true;
                    testButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Test en cours...';

                    // Appel à l'API de test
                    const response = await fetch(`${CONFIG.API_URL}/api/workflow2`, {
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
                            window.location.href = 'result.php?test=1&workflow=2';
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
