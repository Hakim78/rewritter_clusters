<?php
/**
 * Option 1 : Création d'un nouvel article SEO
 * Fichier: frontend/public/option1.php
 */

$pageTitle = "Créer un article SEO - SEO Article Generator";
require_once '../includes/header.php';
requireAuth(); // Vérification de l'authentification
?>

<div class="max-w-5xl mx-auto">
    <!-- En-tête de la page -->
    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-xl p-8 mb-8 border border-purple-100">
        <div class="flex items-start gap-4 mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 rounded-xl shadow-lg flex-shrink-0">
                <i class="fas fa-plus-circle text-white text-3xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Créer un nouvel article</h1>
                <p class="text-gray-600 text-lg">Génération d'article SEO optimisé par IA</p>

                <!-- Badges optimisés -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                        <i class="fas fa-robot mr-1.5"></i> IA Générée
                    </span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        <i class="fas fa-search mr-1.5"></i> SEO
                    </span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
                        <i class="fas fa-users mr-1.5"></i> People-first
                    </span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                        <i class="fas fa-brain mr-1.5"></i> LLMO & RAG
                    </span>
                </div>
            </div>
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

                <!-- Champ personnalisé pour "Autre" -->
                <div id="custom-domain" class="hidden mt-3">
                    <input
                        type="text"
                        id="custom_domain_text"
                        name="custom_domain"
                        placeholder="Précisez votre secteur d'activité..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                    />
                    <p class="text-sm text-gray-500 mt-1">Décrivez votre secteur d'activité en quelques mots</p>
                </div>
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

            <!-- Gestion des liens -->
            <div class="bg-purple-50 p-6 rounded-lg">
                <h4 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-link text-purple-600 mr-2"></i>
                    Maillage de liens (optionnel)
                </h4>
                <p class="text-sm text-gray-600 mb-4">Ajoutez des URLs pour enrichir votre article avec des liens internes et externes</p>

                <!-- Liens internes -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-home text-blue-600 mr-1"></i>
                        Liens internes (vers votre site)
                    </label>
                    <div id="internal-links-container">
                        <div class="flex gap-2 mb-2">
                            <input
                                type="url"
                                name="internal_links[]"
                                placeholder="https://votre-site.com/autre-page"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            />
                            <button type="button" onclick="addLinkField('internal')" class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Pages de votre site à mentionner dans l'article</p>
                </div>

                <!-- Liens externes -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-external-link-alt text-green-600 mr-1"></i>
                        Liens externes (sources, références)
                    </label>
                    <div id="external-links-container">
                        <div class="flex gap-2 mb-2">
                            <input
                                type="url"
                                name="external_links[]"
                                placeholder="https://site-externe.com/ressource"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                            />
                            <button type="button" onclick="addLinkField('external')" class="px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Sources externes, études, outils à référencer</p>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    Temps estimé : 3-5 minutes
                </div>
                <div class="space-x-4">
                    <button
                        type="submit"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5"
                    >
                        <i class="fas fa-magic mr-2"></i>
                        Générer l'article
                    </button>
                </div>
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

        // Gestion du champ domaine personnalisé
        const domainSelect = document.getElementById('domain');
        const customDomainDiv = document.getElementById('custom-domain');
        const customDomainInput = document.getElementById('custom_domain_text');

        domainSelect.addEventListener('change', function() {
            if (this.value === 'Autre') {
                customDomainDiv.classList.remove('hidden');
                customDomainInput.required = true;
            } else {
                customDomainDiv.classList.add('hidden');
                customDomainInput.required = false;
                customDomainInput.value = '';
            }
        });

        // Gestionnaire pour le bouton de test backend
        const testButton = document.getElementById('test-backend-btn');
        if (testButton) {
            testButton.addEventListener('click', async function() {
                // Récupérer les données du formulaire
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Traitement spécial pour les arrays (liens)
                const internalLinks = formData.getAll('internal_links[]').filter(link => link.trim() !== '');
                const externalLinks = formData.getAll('external_links[]').filter(link => link.trim() !== '');

                if (internalLinks.length > 0) data.internal_links = internalLinks;
                if (externalLinks.length > 0) data.external_links = externalLinks;

                // Gestion du domaine personnalisé
                if (data.domain === 'Autre' && data.custom_domain) {
                    data.domain = data.custom_domain;
                }

                // Ajouter des données de test si les champs sont vides
                if (!data.site_url) data.site_url = 'https://example.com';
                if (!data.domain) data.domain = 'Marketing Digital';
                if (!data.keyword) data.keyword = 'test frontend backend communication';
                if (!data.guideline) data.guideline = 'Test de communication entre le frontend et le backend Python Flask';

                try {
                    // Désactiver le bouton pendant le test
                    testButton.disabled = true;
                    testButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Test en cours...';

                    // Appel à l\'API de test
                    const response = await fetch(`${CONFIG.API_URL}/api/test-post`, {
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
                            window.location.href = 'result.php?test=1&workflow=1';
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

    // Fonction pour ajouter des champs de liens dynamiquement
    function addLinkField(type) {
        const container = document.getElementById(`${type}-links-container`);
        const color = type === 'internal' ? 'blue' : 'green';

        const newField = document.createElement('div');
        newField.className = 'flex gap-2 mb-2';
        newField.innerHTML = `
            <input
                type="url"
                name="${type}_links[]"
                placeholder="${type === 'internal' ? 'https://votre-site.com/autre-page' : 'https://site-externe.com/ressource'}"
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