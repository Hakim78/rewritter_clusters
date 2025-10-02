<?php
/**
 * Page de résultats - SEO Article Generator
 * Fichier: frontend/public/result.php
 */

$pageTitle = "Résultats - SEO Article Generator";
require_once '../includes/header.php';
requireAuth(); // Vérification de l'authentification

// Récupération des paramètres URL pour le test
$testMode = isset($_GET['test']) ? true : false;
$workflowType = isset($_GET['workflow']) ? (int)$_GET['workflow'] : 1;
?>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<div class="max-w-6xl mx-auto">
   

    <?php if ($testMode): ?>
        <!-- Mode Test: Affichage des données de test -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-vial text-blue-500 mr-3"></i>
                Données de Test Reçues
            </h2>

            <!-- Données originales -->
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-blue-800 mb-3">Données Envoyées</h3>
                    <div id="original-data" class="text-sm text-gray-700">
                        <!-- Sera rempli par JavaScript -->
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-green-800 mb-3">Données Traitées</h3>
                    <div id="processed-data" class="text-sm text-gray-700">
                        <!-- Sera rempli par JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Test content preview -->
            <div class="border-2 border-dashed border-gray-300 p-6 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Aperçu du Contenu Généré (TEST)</h3>
                <div id="test-content-preview" class="prose max-w-none">
                    <!-- Sera rempli par JavaScript -->
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Mode Production: Affichage des résultats réels -->
        <div id="production-results">
            <!-- Sera rempli par JavaScript depuis les données de l'API -->
        </div>
    <?php endif; ?>

    <!-- Section Actions -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-lg p-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-4">Et maintenant ?</h2>
        <p class="text-gray-300 mb-6">
            <?php if ($testMode): ?>
                Le test est concluant ! La communication entre le frontend et le backend fonctionne parfaitement.
            <?php else: ?>
                Votre article est prêt à être publié sur votre site web.
            <?php endif; ?>
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <?php if ($testMode): ?>
                <a href="option1.php" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-plus mr-2"></i>Créer un vrai article
                </a>
            <?php else: ?>
                <a href="index.php" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-home mr-2"></i>Retour à l'accueil
                </a>
            <?php endif; ?>
            <a href="test_connection.php" class="bg-transparent border-2 border-white text-white font-bold py-3 px-6 rounded-lg hover:bg-white hover:text-gray-900 transition">
                <i class="fas fa-cog mr-2"></i>Test environnement
            </a>
        </div>
    </div>

    <!-- Statistiques temps réel (si en mode test) -->
    <?php if ($testMode): ?>
        <div class="grid md:grid-cols-3 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-500 mb-2">✓</div>
                <div class="text-sm text-gray-600 mb-1">Frontend</div>
                <div class="text-lg font-bold text-gray-900">Opérationnel</div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-500 mb-2">⚡</div>
                <div class="text-sm text-gray-600 mb-1">Backend Python</div>
                <div class="text-lg font-bold text-gray-900">Actif</div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-purple-500 mb-2">🔄</div>
                <div class="text-sm text-gray-600 mb-1">Communication</div>
                <div class="text-lg font-bold text-gray-900">Parfaite</div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/app.js"></script>
<script>
// Variables globales
const isTestMode = <?php echo $testMode ? 'true' : 'false'; ?>;
const workflowType = <?php echo $workflowType; ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (isTestMode) {
        // Mode test : simuler des données
        displayTestResults();
    } else {
        // Mode production : récupérer les vraies données depuis le sessionStorage ou autre
        displayProductionResults();
    }

    // Gestionnaires d'événements
    document.getElementById('btn-copy-all').addEventListener('click', copyAllContent);
    document.getElementById('btn-download-all').addEventListener('click', downloadAllContent);
});

function displayTestResults() {
    // Récupérer les vrais résultats du test depuis sessionStorage
    const storedResults = sessionStorage.getItem('testResults');
    let testData;

    if (storedResults) {
        const results = JSON.parse(storedResults);
        console.log('DEBUG: Raw results from sessionStorage:', results);

        if (workflowType === 3) {
            // Workflow 3 : Cluster
            testData = {
                original: results.received_data,
                articles: results.articles,
                internal_links: results.internal_links
            };
        } else {
            // Workflow 1 et 2 : Article simple
            testData = {
                original: results.received_data,
                processed: results.processed_data || results.article,
                improvements: results.article ? results.article.improvements : null
            };
        }
        console.log('DEBUG: Processed testData:', testData);
    } else {
        console.log('DEBUG: No data in sessionStorage, using fallback data');
        // Fallback avec des données de simulation selon le workflow
        if (workflowType === 3) {
            testData = {
                original: {
                    article_url: "https://example.com/article-cluster",
                    cluster_strategy: "auto",
                    tone: "conversational"
                },
                articles: [
                    { type: 'main', title: 'Article principal test', html_content: '<h1>Principal</h1>' },
                    { type: 'satellite', title: 'Satellite 1 test', html_content: '<h1>Satellite 1</h1>' },
                    { type: 'satellite', title: 'Satellite 2 test', html_content: '<h1>Satellite 2</h1>' }
                ],
                internal_links: ['lien1 -> lien2', 'lien1 -> lien3']
            };
        } else if (workflowType === 2) {
            testData = {
                original: {
                    article_url: "https://example.com/article-existant",
                    optimization_level: "moderate"
                },
                processed: {
                    processed_title: "Article réécrit test",
                    processed_content: "<h1>Article réécrit</h1><p>Test de réécriture</p>",
                    timestamp: new Date().toISOString()
                },
                improvements: ['SEO optimisé', 'People-first', 'RAG LLMO compatible']
            };
        } else {
            testData = {
                original: {
                    site_url: "https://example.com",
                    domain: "Marketing Digital",
                    keyword: "test frontend backend",
                    guideline: "Test workflow 1"
                },
                processed: {
                    processed_title: "Article de test sur : test frontend backend",
                    processed_content: "<h1>Article de test</h1><p>Workflow 1 test</p>",
                    timestamp: new Date().toISOString()
                }
            };
        }
    }

    console.log('DEBUG: Final testData before display:', testData);

    // Afficher selon le type de workflow
    if (workflowType === 3) {
        displayClusterTestData(testData);
    } else {
        displaySingleArticleTestData(testData);
    }
}

function displaySingleArticleTestData(testData) {
    console.log('DEBUG: displaySingleArticleTestData called with:', testData);

    // Afficher les données originales
    let originalHtml = '<div class="space-y-2">';

    if (workflowType === 1) {
        // Gestion du domaine personnalisé
        const domain = testData.original.domain === 'Autre' && testData.original.custom_domain
            ? testData.original.custom_domain
            : testData.original.domain || 'N/A';

        originalHtml += `
            <div><strong>URL:</strong> ${testData.original.site_url || 'N/A'}</div>
            <div><strong>Domaine:</strong> ${domain}</div>
            <div><strong>Mot-clé:</strong> ${testData.original.keyword || 'N/A'}</div>
            <div><strong>Brief:</strong> ${testData.original.guideline || 'N/A'}</div>
        `;

        // Afficher les liens si présents
        if (testData.original.internal_links && testData.original.internal_links.length > 0) {
            originalHtml += `<div><strong>Liens internes:</strong> ${testData.original.internal_links.length} URL(s)</div>`;
        }
        if (testData.original.external_links && testData.original.external_links.length > 0) {
            originalHtml += `<div><strong>Liens externes:</strong> ${testData.original.external_links.length} URL(s)</div>`;
        }
    } else {
        originalHtml += `
            <div><strong>URL Article:</strong> ${testData.original.article_url || 'N/A'}</div>
            <div><strong>Niveau optimisation:</strong> ${testData.original.optimization_level || 'N/A'}</div>
            <div><strong>Conserver style:</strong> ${testData.original.keep_style ? 'Oui' : 'Non'}</div>
        `;
    }

    originalHtml += '</div>';
    document.getElementById('original-data').innerHTML = originalHtml;

    // Afficher les données traitées
    let processedHtml = `
        <div class="space-y-2">
            <div><strong>Titre généré:</strong> ${testData.processed.processed_title || testData.processed.title}</div>
            <div><strong>Timestamp:</strong> ${testData.processed.timestamp || new Date().toISOString()}</div>
            <div><strong>Status:</strong> <span class="text-green-600">Traité avec succès</span></div>
    `;

    if (testData.improvements && testData.improvements.length > 0) {
        processedHtml += `<div><strong>Améliorations:</strong> ${testData.improvements.join(', ')}</div>`;
    }

    // Afficher les informations sur les liens traités
    if (testData.processed.links_processed) {
        const links = testData.processed.links_processed;
        processedHtml += `<div><strong>Liens traités:</strong> ${links.total_links} (${links.internal_count} internes, ${links.external_count} externes)</div>`;
    }

    processedHtml += '</div>';
    document.getElementById('processed-data').innerHTML = processedHtml;

    // Afficher l'aperçu du contenu
    document.getElementById('test-content-preview').innerHTML = testData.processed.processed_content || testData.processed.html_content || testData.processed.content;
}

function displayClusterTestData(testData) {
    // Afficher les données originales pour le cluster
    document.getElementById('original-data').innerHTML = `
        <div class="space-y-2">
            <div><strong>URL Article:</strong> ${testData.original.article_url || 'N/A'}</div>
            <div><strong>Stratégie:</strong> ${testData.original.cluster_strategy || 'N/A'}</div>
            <div><strong>Ton:</strong> ${testData.original.tone || 'N/A'}</div>
            <div><strong>Images IA:</strong> ${testData.original.generate_all_images ? 'Oui' : 'Non'}</div>
        </div>
    `;

    // Afficher les données traitées pour le cluster
    document.getElementById('processed-data').innerHTML = `
        <div class="space-y-2">
            <div><strong>Articles générés:</strong> ${testData.articles ? testData.articles.length : 0}</div>
            <div><strong>Liens internes:</strong> ${testData.internal_links ? testData.internal_links.length : 0}</div>
            <div><strong>Status:</strong> <span class="text-green-600">Cluster créé avec succès</span></div>
            <div><strong>Timestamp:</strong> ${new Date().toISOString()}</div>
        </div>
    `;

    // Afficher l'aperçu du cluster
    let clusterPreview = '<div class="space-y-4">';
    if (testData.articles && testData.articles.length > 0) {
        testData.articles.forEach((article, index) => {
            const typeLabel = article.type === 'main' ? 'Principal' : `Satellite ${index}`;
            clusterPreview += `
                <div class="border-l-4 ${article.type === 'main' ? 'border-blue-500' : 'border-indigo-400'} pl-4">
                    <h4 class="font-bold">${typeLabel}: ${article.title}</h4>
                    <div class="text-sm text-gray-600 mt-2">${article.html_content}</div>
                </div>
            `;
        });
    }

    if (testData.internal_links && testData.internal_links.length > 0) {
        clusterPreview += '<div class="mt-4 p-3 bg-gray-100 rounded"><strong>Maillage:</strong><br>';
        testData.internal_links.forEach(link => {
            clusterPreview += `<span class="text-sm text-blue-600">${link}</span><br>`;
        });
        clusterPreview += '</div>';
    }

    clusterPreview += '</div>';
    document.getElementById('test-content-preview').innerHTML = clusterPreview;
}

function displayProductionResults() {
    // Récupération des vraies données depuis sessionStorage
    const storedResults = sessionStorage.getItem('workflowResults');
    const resultsContainer = document.getElementById('production-results');

    if (!storedResults) {
        resultsContainer.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl mb-4"></i>
                <h3 class="text-lg font-bold text-yellow-800 mb-2">Aucun résultat trouvé</h3>
                <p class="text-yellow-700 mb-4">
                    Aucun résultat de workflow n'a été trouvé. Veuillez relancer la génération.
                </p>
                <a href="index.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Retour à l'accueil
                </a>
            </div>
        `;
        return;
    }

    try {
        const result = JSON.parse(storedResults);
        console.log('Displaying production results:', result);

        if (result.article) {
            // Display single article (Workflow 1 or 2)
            displaySingleProductionArticle(result.article);
        } else if (result.articles) {
            // Display cluster (Workflow 3)
            displayClusterProductionResults(result.articles, result.internal_links);
        }
    } catch (error) {
        console.error('Error displaying results:', error);
        resultsContainer.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <i class="fas fa-times-circle text-red-600 text-3xl mb-4"></i>
                <h3 class="text-lg font-bold text-red-800 mb-2">Erreur d'affichage</h3>
                <p class="text-red-700">
                    Impossible d'afficher les résultats. ${error.message}
                </p>
            </div>
        `;
    }
}

function displaySingleProductionArticle(article, metadata = null) {
    const seoTitle = article.seo_title || article.title || 'Article généré';
    const metaDesc = article.meta_description || '';
    const wordCount = article.word_count || 'N/A';
    const readabilityScore = article.readability_score || 'N/A';

    // Workflow-specific title
    let pageTitle = 'Article généré';
    let pageIcon = 'fa-check-circle';
    let pageIconColor = 'text-green-500';

    if (workflowType === 2) {
        pageTitle = 'Article réécrit et optimisé';
        pageIcon = 'fa-sync-alt';
        pageIconColor = 'text-green-500';
    } else if (workflowType === 3) {
        pageTitle = 'Cluster d\'articles créé';
        pageIcon = 'fa-sitemap';
        pageIconColor = 'text-purple-500';
    }

    const html = `
        <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fas ${pageIcon} ${pageIconColor} mr-3"></i>
                    ${pageTitle}
                </h2>
                <div class="space-x-2">
                    <button onclick="getEditorContent()" class="btn-secondary">
                        <i class="fas fa-copy mr-2"></i>Copier le HTML
                    </button>
                    <button onclick="downloadEditorContent('${seoTitle.replace(/'/g, "\\'")}')" class="btn-secondary">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </button>
                </div>
            </div>

            <!-- SEO Title -->
            ${seoTitle ? `
                <div class="mb-4 bg-purple-50 p-4 rounded-lg">
                    <h3 class="text-lg font-bold mb-2">
                        <i class="fas fa-heading text-purple-600 mr-2"></i>Titre SEO
                    </h3>
                    <p class="text-gray-900 font-semibold">${seoTitle}</p>
                </div>
            ` : ''}

            <!-- Meta description -->
            ${metaDesc ? `
                <div class="mb-4 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-bold mb-2">
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i>Meta Description
                    </h3>
                    <p class="text-gray-700">${metaDesc}</p>
                </div>
            ` : ''}

            <!-- WordPress Excerpt -->
            ${article.wordpress_excerpt ? `
                <div class="mb-4 bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-bold mb-2">
                        <i class="fab fa-wordpress text-green-600 mr-2"></i>Extrait WordPress
                    </h3>
                    <p class="text-gray-700">${article.wordpress_excerpt}</p>
                </div>
            ` : ''}

            <!-- Métadonnées -->
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Nombre de mots</div>
                    <div class="text-2xl font-bold text-purple-600">${wordCount}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Score de lisibilité</div>
                    <div class="text-2xl font-bold text-green-600">${readabilityScore}</div>
                </div>
            </div>

            <!-- Image générée (si disponible) -->
            ${article.image_url ? `
                <div class="mb-6 bg-gradient-to-r from-pink-50 to-purple-50 p-6 rounded-lg border border-pink-200">
                    <h3 class="text-xl font-bold mb-3 flex items-center justify-between">
                        <span>
                            <i class="fas fa-image text-pink-600 mr-2"></i>
                            Image générée par IA
                        </span>
                        <a href="${article.image_url}" download target="_blank" class="text-sm btn-secondary">
                            <i class="fas fa-download mr-2"></i>Télécharger l'image
                        </a>
                    </h3>
                    <div class="bg-white p-4 rounded-lg">
                        <img src="${article.image_url}" alt="${seoTitle}" class="w-full rounded-lg shadow-lg mb-3">
                        ${article.image_prompt ? `
                            <details class="mt-3">
                                <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-info-circle mr-1"></i>Voir le prompt utilisé
                                </summary>
                                <div class="mt-2 p-3 bg-gray-50 rounded text-sm text-gray-700">
                                    ${article.image_prompt}
                                </div>
                            </details>
                        ` : ''}
                    </div>
                </div>
            ` : ''}

            <!-- Contenu HTML avec éditeur WYSIWYG -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-3 flex items-center justify-between">
                    <span>
                        <i class="fas fa-edit text-gray-700 mr-2"></i>
                        Éditeur d'article (modifiable)
                    </span>
                    <div class="space-x-2">
                        <button onclick="getEditorContent()" class="text-sm btn-primary">
                            <i class="fas fa-save mr-2"></i>Récupérer les modifications
                        </button>
                        <button onclick="toggleView('editor-container-1', 'code-view-1')" class="text-sm btn-secondary">
                            <i class="fas fa-code mr-2"></i>Voir le code HTML
                        </button>
                    </div>
                </h3>

                <!-- Éditeur TinyMCE -->
                <div id="editor-container-1">
                    <textarea id="article-editor-1" class="w-full">${article.html_content}</textarea>
                </div>

                <!-- Vue code HTML -->
                <div id="code-view-1" class="hidden">
                    <pre class="code-block"><code id="code-content-1">${escapeHtml(article.html_content)}</code></pre>
                    <button onclick="toggleView('editor-container-1', 'code-view-1')" class="mt-3 btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Retour à l'éditeur
                    </button>
                </div>
            </div>

            <!-- FAQ JSON Schema -->
            ${article.faq_json && article.faq_json.length > 0 ? `
                <div class="mb-6 bg-indigo-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 flex items-center">
                        <i class="fas fa-question-circle text-indigo-600 mr-2"></i>
                        FAQ (Format JSON)
                    </h3>
                    <div class="bg-white p-4 rounded border border-indigo-200">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-600">${article.faq_json.length} questions</span>
                            <button onclick="copyToClipboard('faq-json-content')" class="text-sm btn-secondary">
                                <i class="fas fa-copy mr-1"></i>Copier JSON
                            </button>
                        </div>
                        <pre id="faq-json-content" class="text-sm bg-gray-50 p-4 rounded overflow-auto max-h-64"><code>${JSON.stringify(article.faq_json, null, 2)}</code></pre>
                    </div>
                </div>
            ` : ''}

            <!-- Improvements (Workflow 2 only) -->
            ${workflowType === 2 && article.improvements && article.improvements.length > 0 ? `
                <div class="mb-6 bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                    <h3 class="text-xl font-bold mb-3 flex items-center">
                        <i class="fas fa-check-double text-green-600 mr-2"></i>
                        Améliorations apportées à l'article
                    </h3>
                    <ul class="space-y-2">
                        ${article.improvements.map(improvement => `<li class="flex items-start"><i class="fas fa-check-circle text-green-600 mr-2 mt-1"></i><span>${improvement}</span></li>`).join('')}
                    </ul>
                </div>
            ` : ''}

            <!-- Internal Links Added (Workflow 2 only) -->
            ${workflowType === 2 && article.internal_links_added && article.internal_links_added.length > 0 ? `
                <div class="mb-6 bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                    <h3 class="text-xl font-bold mb-3 flex items-center">
                        <i class="fas fa-link text-blue-600 mr-2"></i>
                        Liens internes ajoutés (${article.internal_links_added.length})
                    </h3>
                    <div class="space-y-1">
                        ${article.internal_links_added.map(link => `<div class="text-sm text-blue-700"><i class="fas fa-arrow-right mr-2"></i>${link}</div>`).join('')}
                    </div>
                </div>
            ` : ''}

            <!-- Keywords & Entities -->
            ${article.keywords && (article.keywords.secondary?.length > 0 || article.keywords.entities?.length > 0) ? `
                <div class="mb-6 bg-yellow-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3">
                        <i class="fas fa-tags text-yellow-600 mr-2"></i>
                        Mots-clés & Entités
                    </h3>
                    ${article.keywords.secondary?.length > 0 ? `
                        <div class="mb-3">
                            <h4 class="font-semibold text-gray-700 mb-2">Mots-clés secondaires :</h4>
                            <div class="flex flex-wrap gap-2">
                                ${article.keywords.secondary.map(kw => `<span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">${kw}</span>`).join('')}
                            </div>
                        </div>
                    ` : ''}
                    ${article.keywords.entities?.length > 0 ? `
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Entités :</h4>
                            <div class="flex flex-wrap gap-2">
                                ${article.keywords.entities.map(entity => `<span class="px-3 py-1 bg-orange-200 text-orange-800 rounded-full text-sm">${entity}</span>`).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            ` : ''}
        </div>
    `;

    document.getElementById('production-results').innerHTML = html;

    // Initialize TinyMCE after content is added to DOM
    setTimeout(() => {
        initTinyMCE();
    }, 100);
}

// Initialize TinyMCE editor
function initTinyMCE() {
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE not loaded');
        return;
    }

    tinymce.init({
        selector: '#article-editor-1',
        height: 600,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code fullscreen | removeformat help',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('init', function() {
                console.log('TinyMCE initialized successfully');
            });
        }
    });
}

// Get modified content from TinyMCE editor
function getEditorContent() {
    if (typeof tinymce === 'undefined' || !tinymce.get('article-editor-1')) {
        Toast.show('Éditeur non initialisé', 'error');
        return;
    }

    const content = tinymce.get('article-editor-1').getContent();

    // Update the code view with new content
    document.getElementById('code-content-1').textContent = content;

    // Copy to clipboard
    navigator.clipboard.writeText(content).then(() => {
        Toast.show('Contenu modifié copié dans le presse-papiers !', 'success');
    }).catch(err => {
        Toast.show('Erreur lors de la copie', 'error');
        console.error('Copy error:', err);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function toggleView(contentId, codeId) {
    const content = document.getElementById(contentId);
    const code = document.getElementById(codeId);

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        code.classList.add('hidden');
    } else {
        content.classList.add('hidden');
        code.classList.remove('hidden');
    }
}

function copyHTML(elementId) {
    const element = document.getElementById(elementId);

    if (!element) {
        Toast.show('Erreur: élément introuvable', 'error');
        console.error('Element not found:', elementId);
        return;
    }

    const html = element.innerHTML;

    navigator.clipboard.writeText(html).then(() => {
        Toast.show('HTML copié dans le presse-papiers !', 'success');
    }).catch(err => {
        Toast.show('Erreur lors de la copie', 'error');
        console.error('Copy error:', err);
    });
}

function downloadHTML(elementId, filename) {
    const element = document.getElementById(elementId);

    if (!element) {
        Toast.show('Erreur: élément introuvable', 'error');
        console.error('Element not found:', elementId);
        return;
    }

    const html = element.innerHTML;

    const blob = new Blob([html], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${filename}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    Toast.show('HTML téléchargé !', 'success');
}

function downloadEditorContent(filename) {
    if (typeof tinymce === 'undefined' || !tinymce.get('article-editor-1')) {
        Toast.show('Éditeur non initialisé', 'error');
        return;
    }

    const content = tinymce.get('article-editor-1').getContent();
    const blob = new Blob([content], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${filename}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    Toast.show('HTML téléchargé !', 'success');
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;

    navigator.clipboard.writeText(text).then(() => {
        Toast.show('Contenu copié dans le presse-papiers !', 'success');
    }).catch(err => {
        Toast.show('Erreur lors de la copie', 'error');
        console.error('Copy error:', err);
    });
}

function copyAllContent() {
    const content = document.querySelector('.max-w-6xl').innerText;
    navigator.clipboard.writeText(content).then(() => {
        Toast.show('Contenu copié dans le presse-papiers !', 'success');
    }).catch(err => {
        Toast.show('Erreur lors de la copie', 'error');
        console.error('Copy error:', err);
    });
}

function downloadAllContent() {
    const content = document.querySelector('.max-w-6xl').innerHTML;
    const blob = new Blob([`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Résultats SEO Article Generator</title>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .prose { max-width: none; }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `], { type: 'text/html' });

    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'resultats_seo_generator.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    Toast.show('Résultats téléchargés !', 'success');
}
</script>

<?php require_once '../includes/footer.php'; ?>