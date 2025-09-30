<?php
/**
 * Option 1 : Création d'un nouvel article SEO
 * Fichier: frontend/public/option1.php
 */

$pageTitle = "Créer un article SEO - SEO Article Generator";
require_once '../includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <!-- En-tête de la page -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 rounded-lg mr-4">
                <i class="fas fa-plus-circle text-white text-3xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Créer un nouvel article</h1>
                <p class="text-gray-600 mt-2">Génération d'article SEO optimisé RAG LLMO People-first</p>
            </div>
        </div>
        
        <!-- Badges d'info -->
        <div class="flex flex-wrap gap-2 mt-6">
            <span class="badge badge-info">
                <i class="fas fa-robot mr-1"></i> IA Générée
            </span>
            <span class="badge badge-success">
                <i class="fas fa-search mr-1"></i> SEO Optimisé
            </span>
            <span class="badge badge-warning">
                <i class="fas fa-users mr-1"></i> People-first
            </span>
            <span class="badge bg-purple-100 text-purple-800">
                <i class="fas fa-link mr-1"></i> Maillage interne
            </span>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <form id="workflow1-form" class="space-y-6">
            
            <!-- URL du site -->
            <div>
                <label for="site_url" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-globe text-purple-600 mr-2"></i>
                    URL du site web <span class="text-red-500">*</span>
                </label>
                <input 
                    type="url" 
                    id="site_url" 
                    name="site_url" 
                    required
                    placeholder="https://example.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">Le site sur lequel l'article sera publié</p>
            </div>

            <!-- Domaine/Thématique -->
            <div>
                <label for="domain" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-tag text-purple-600 mr-2"></i>
                    Domaine / Thématique <span class="text-red-500">*</span>
                </label>
                <select 
                    id="domain" 
                    name="domain" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                >
                    <option value="">Sélectionner un domaine</option>
                    <option value="Marketing Digital">Marketing Digital</option>
                    <option value="E-commerce">E-commerce</option>
                    <option value="Technologie">Technologie</option>
                    <option value="Santé & Bien-être">Santé & Bien-être</option>
                    <option value="Finance">Finance</option>
                    <option value="Immobilier">Immobilier</option>
                    <option value="Voyage">Voyage</option>
                    <option value="Food & Lifestyle">Food & Lifestyle</option>
                    <option value="Business">Business</option>
                    <option value="Autre">Autre</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Le secteur d'activité principal</p>
            </div>

            <!-- Guideline -->
            <div>
                <label for="guideline" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                    Guideline / Brief de l'article <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="guideline" 
                    name="guideline" 
                    required
                    rows="4"
                    placeholder="Décrivez le sujet de l'article, le ton souhaité, les points clés à aborder..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                ></textarea>
                <p class="text-sm text-gray-500 mt-1">Plus votre brief est détaillé, meilleur sera le résultat</p>
            </div>

            <!-- Mot-clé principal -->
            <div>
                <label for="keyword" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-purple-600 mr-2"></i>
                    Mot-clé principal <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="keyword" 
                    name="keyword" 
                    required
                    placeholder="Ex: marketing automation 2025"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">Le mot-clé SEO principal à cibler</p>
            </div>

            <!-- Maillage interne -->
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="internal_linking" class="font-bold text-gray-700 flex items-center">
                            <i class="fas fa-link text-purple-600 mr-2"></i>
                            Ajouter du maillage interne
                        </label>
                        <p class="text-sm text-gray-600 mt-1">Suggérer des liens vers d'autres pages de votre site</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="internal_linking" name="internal_linking" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Longueur souhaitée -->
            <div>
                <label for="word_count" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-text-width text-purple-600 mr-2"></i>
                    Longueur souhaitée (optionnel)
                </label>
                <select 
                    id="word_count" 
                    name="word_count"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                >
                    <option value="">Automatique (basé sur le sujet)</option>
                    <option value="800">Court (~800 mots)</option>
                    <option value="1500">Moyen (~1500 mots)</option>
                    <option value="2500">Long (~2500 mots)</option>
                    <option value="4000">Très long (~4000 mots)</option>
                </select>
            </div>

            <!-- Bouton de soumission -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Temps estimé : 3-5 minutes
                </div>
                <button 
                    type="submit" 
                    class="btn-primary"
                >
                    <i class="fas fa-magic mr-2"></i>
                    Générer l'article
                </button>
            </div>
        </form>
    </div>

    <!-- Loader -->
    <div id="loader" class="hidden bg-white rounded-lg shadow-xl p-12 text-center">
        <div class="loader mx-auto mb-6"></div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Génération en cours...</h3>
        <p class="text-gray-600">L'IA travaille sur votre article, cela peut prendre quelques minutes.</p>
        <div class="mt-6">
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: 30%" id="progress-fill"></div>
            </div>
        </div>
    </div>

    <!-- Résultats (rempli dynamiquement par JavaScript) -->
    <div id="results" class="hidden"></div>
</div>

<!-- Charger le JavaScript -->
<script src="assets/js/app.js"></script>
<script>
    // Initialiser le workflow manager pour l'option 1
    document.addEventListener('DOMContentLoaded', function() {
        new WorkflowManager(1);
        
        // Animation de la barre de progression pendant le chargement
        const form = document.getElementById('workflow1-form');
        form.addEventListener('submit', function() {
            let progress = 30;
            const progressBar = document.getElementById('progress-fill');
            
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 95) {
                    progress = 95;
                    clearInterval(interval);
                }
                progressBar.style.width = progress + '%';
            }, 1000);
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>