<?php
/**
 * Option 2 : Réécriture d'un article existant
 * Fichier: frontend/public/option2.php
 */

$pageTitle = "Réécrire un article - SEO Article Generator";
require_once '../includes/header.php';
requireAuth(); // Vérification de l'authentification
?>

<div class="max-w-5xl mx-auto">
    <!-- En-tête de la page -->
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-xl p-8 mb-8 border border-green-100">
        <div class="flex items-start gap-4 mb-6">
            <div class="bg-green-500 p-4 rounded-xl shadow-lg flex-shrink-0">
                <i class="fas fa-sync-alt text-white text-3xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Réécrire un article</h1>
                <p class="text-gray-600 text-lg">Optimisation SEO et mise aux normes par IA</p>

                <!-- Badges optimisés -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        <i class="fas fa-arrow-up mr-1.5"></i> Amélioration SEO
                    </span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                        <i class="fas fa-check-double mr-1.5"></i> RAG & LLMO
                    </span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
                        <i class="fas fa-users mr-1.5"></i> People-first
                    </span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                        <i class="fas fa-image mr-1.5"></i> Image IA
                    </span>
                </div>
            </div>
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
                <span>Correction de grammaire, orthographe et style</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Mise à jour des données, chiffres et tendances récentes</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Optimisation SEO + LLMO + People-first</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                <span>Structuration RAG-friendly avec FAQ</span>
            </li>
        </ul>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <form id="workflow2-form" class="space-y-6">

            <!-- Mode de saisie -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    <i class="fas fa-file-import text-green-600 mr-2"></i>
                    Mode de saisie de l'article
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center cursor-pointer bg-gray-50 p-4 rounded-lg border-2 border-gray-300 hover:border-green-500 transition flex-1">
                        <input type="radio" name="input_mode" value="url" checked class="mr-3">
                        <div>
                            <div class="font-bold text-gray-900">
                                <i class="fas fa-link text-green-600 mr-1"></i>
                                Via URL
                            </div>
                            <div class="text-sm text-gray-600">Article déjà publié en ligne</div>
                        </div>
                    </label>
                    <label class="flex items-center cursor-pointer bg-gray-50 p-4 rounded-lg border-2 border-gray-300 hover:border-green-500 transition flex-1">
                        <input type="radio" name="input_mode" value="manual" class="mr-3">
                        <div>
                            <div class="font-bold text-gray-900">
                                <i class="fas fa-keyboard text-green-600 mr-1"></i>
                                Contenu manuel
                            </div>
                            <div class="text-sm text-gray-600">Article non publié ou copié</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Mode URL -->
            <div id="url-mode" class="space-y-4">
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

                <!-- Aperçu de l'article -->
                <div id="article-preview" class="hidden bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h4 class="font-bold text-gray-900 mb-3">
                        <i class="fas fa-eye text-green-600 mr-2"></i>
                        Aperçu de l'article
                    </h4>
                    <div id="preview-content" class="text-gray-700">
                        <!-- Sera rempli dynamiquement -->
                    </div>
                </div>
            </div>

            <!-- Mode Manuel -->
            <div id="manual-mode" class="hidden space-y-4">
                <div>
                    <label for="article_title" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-heading text-green-600 mr-2"></i>
                        Titre de l'article <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="article_title"
                        name="article_title"
                        placeholder="Le titre actuel de votre article"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />
                    <p class="text-sm text-gray-500 mt-1">Le titre actuel qui sera optimisé</p>
                </div>

                <div>
                    <label for="article_content" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-file-alt text-green-600 mr-2"></i>
                        Contenu de l'article <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="article_content"
                        name="article_content"
                        rows="15"
                        placeholder="Collez le contenu complet de votre article ici (HTML accepté)..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm"
                    ></textarea>
                    <p class="text-sm text-gray-500 mt-1">Le contenu complet de l'article (HTML ou texte brut)</p>
                </div>
            </div>

            <!-- Mot-clé principal -->
            <div>
                <label for="keyword" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-green-600 mr-2"></i>
                    Mot-clé principal <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    required
                    placeholder="Ex: marketing automation 2025"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">Le mot-clé SEO principal à optimiser dans l'article</p>
            </div>

            <!-- Liens internes optionnels -->
            <div class="bg-green-50 p-6 rounded-lg">
                <h4 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-link text-green-600 mr-2"></i>
                    Liens internes à ajouter (optionnel)
                </h4>
                <p class="text-sm text-gray-600 mb-4">Ajoutez 2 à 4 URLs de votre site à intégrer naturellement dans l'article</p>

                <div id="internal-links-container">
                    <div class="flex gap-2 mb-2">
                        <input
                            type="url"
                            name="internal_links[]"
                            placeholder="https://votre-site.com/autre-page"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        />
                        <button type="button" onclick="addLinkField('internal')" class="px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Pages de votre site à mentionner dans l'article réécrit</p>
            </div>

            <!-- Boutons de soumission -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Temps estimé : 4-6 minutes
                </div>
                <div class="space-x-4">
                    <button
                        type="submit"
                        class="bg-green-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition"
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
            <p class="text-sm text-gray-500 mt-2" id="progress-text">Étape : Extraction du contenu</p>
        </div>
    </div>

    <!-- Résultats -->
    <div id="results" class="hidden"></div>
</div>

<script src="assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('workflow2-form');
        const urlMode = document.getElementById('url-mode');
        const manualMode = document.getElementById('manual-mode');
        const articleUrl = document.getElementById('article_url');
        const articleTitle = document.getElementById('article_title');
        const articleContent = document.getElementById('article_content');

        // Gestion du mode de saisie
        document.querySelectorAll('input[name="input_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'url') {
                    urlMode.classList.remove('hidden');
                    manualMode.classList.add('hidden');
                    articleUrl.required = true;
                    articleTitle.required = false;
                    articleContent.required = false;
                } else {
                    urlMode.classList.add('hidden');
                    manualMode.classList.remove('hidden');
                    articleUrl.required = false;
                    articleTitle.required = true;
                    articleContent.required = true;
                }
            });
        });

        // Initialize WorkflowManager
        new WorkflowManager(2);

        // Preview button
        document.getElementById('preview-btn')?.addEventListener('click', async function() {
            const url = articleUrl.value;
            if (!url) {
                Toast.show('Veuillez entrer une URL', 'error');
                return;
            }

            const previewDiv = document.getElementById('article-preview');
            const previewContent = document.getElementById('preview-content');

            previewDiv.classList.remove('hidden');
            previewContent.innerHTML = `
                <div class="space-y-2">
                    <div class="skeleton h-4 w-3/4"></div>
                    <div class="skeleton h-4 w-full"></div>
                    <div class="skeleton h-4 w-5/6"></div>
                </div>
            `;

            try {
                // Call preview API endpoint
                const token = localStorage.getItem('auth_token');
                const response = await fetch(`${CONFIG.API_URL}/api/preview-article`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': token
                    },
                    body: JSON.stringify({ article_url: url })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    previewContent.innerHTML = `
                        <div class="space-y-2 text-sm">
                            <div><strong>Titre détecté:</strong> ${result.data.title || 'N/A'}</div>
                            <div><strong>Mots:</strong> ~${result.data.word_count || 0}</div>
                            <div><strong>État SEO:</strong> <span class="text-yellow-600">À optimiser</span></div>
                            <div class="mt-3 p-3 bg-white rounded border">
                                <strong>Extrait:</strong>
                                <p class="text-gray-600 mt-1">${(result.data.excerpt || '').substring(0, 200)}...</p>
                            </div>
                        </div>
                    `;
                } else {
                    throw new Error(result.message || 'Erreur lors de la prévisualisation');
                }
            } catch (error) {
                previewContent.innerHTML = `
                    <div class="text-red-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Impossible de charger l'aperçu: ${error.message}
                    </div>
                `;
            }
        });

        // Animation de la barre de progression
        form.addEventListener('submit', function() {
            let progress = 20;
            const progressBar = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');

            const steps = [
                'Extraction du contenu',
                'Correction et analyse',
                'Optimisation SEO',
                'Réécriture finale',
                'Génération de l\'image'
            ];
            let stepIndex = 0;

            const interval = setInterval(() => {
                progress += Math.random() * 8;
                if (progress >= 95) {
                    progress = 95;
                    clearInterval(interval);
                }
                progressBar.style.width = progress + '%';

                // Update step text
                const newStepIndex = Math.floor(progress / 20);
                if (newStepIndex < steps.length && newStepIndex !== stepIndex) {
                    stepIndex = newStepIndex;
                    progressText.textContent = `Étape : ${steps[stepIndex]}`;
                }
            }, 1500);
        });

        // Gestionnaire pour le bouton de test backend
        const testButton = document.getElementById('test-backend-btn');
        if (testButton) {
            testButton.addEventListener('click', async function() {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Get input mode
                const inputMode = data.input_mode;

                // Handle internal links
                const internalLinks = formData.getAll('internal_links[]').filter(link => link.trim() !== '');
                if (internalLinks.length > 0) data.internal_links = internalLinks;

                // Add test data if empty
                if (inputMode === 'url') {
                    if (!data.article_url) data.article_url = 'https://example.com/article-existant';
                } else {
                    if (!data.article_title) data.article_title = 'Test Article Title';
                    if (!data.article_content) data.article_content = '<h1>Test Article</h1><p>This is test content for the article rewriter.</p>';
                }
                if (!data.keyword) data.keyword = 'test article optimization';

                try {
                    testButton.disabled = true;
                    testButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Test en cours...';

                    const token = localStorage.getItem('auth_token');
                    const response = await fetch(`${CONFIG.API_URL}/api/test-workflow2`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': token
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        Toast.show('Test réussi ! Redirection vers les résultats...', 'success');
                        sessionStorage.setItem('testResults', JSON.stringify(result));
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
                    testButton.disabled = false;
                    testButton.innerHTML = '<i class="fas fa-flask mr-2"></i>Tester Backend';
                }
            });
        }
    });

    // Fonction pour ajouter des champs de liens dynamiquement
    function addLinkField(type) {
        const container = document.getElementById(`${type}-links-container`);
        const color = 'green';

        const newField = document.createElement('div');
        newField.className = 'flex gap-2 mb-2';
        newField.innerHTML = `
            <input
                type="url"
                name="${type}_links[]"
                placeholder="https://votre-site.com/autre-page"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-${color}-500"
            />
            <button type="button" onclick="removeLinkField(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                <i class="fas fa-minus"></i>
            </button>
        `;

        container.appendChild(newField);
    }

    // Fonction pour supprimer un champ de lien
    function removeLinkField(button) {
        button.parentElement.remove();
    }
</script>

<?php require_once '../includes/footer.php'; ?>