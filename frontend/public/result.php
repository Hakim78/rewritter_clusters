<?php
/**
 * Page de résultats - SEO Article Generator
 * Fichier: frontend/public/result.php
 */

$pageTitle = "Résultats - SEO Article Generator";
require_once '../includes/header.php';

// Récupération des paramètres URL pour le test
$testMode = isset($_GET['test']) ? true : false;
$workflowType = isset($_GET['workflow']) ? (int)$_GET['workflow'] : 1;
?>

<div class="max-w-6xl mx-auto">
    <!-- En-tête de la page -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-green-500 to-teal-500 p-4 rounded-lg mr-4">
                    <i class="fas fa-check-circle text-white text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">
                        <?php if ($testMode): ?>
                            Résultats du Test
                        <?php else: ?>
                            Article Généré
                        <?php endif; ?>
                    </h1>
                    <p class="text-gray-600 mt-2">
                        <?php if ($testMode): ?>
                            Test de communication Frontend ↔ Backend réussi
                        <?php else: ?>
                            Votre contenu SEO optimisé est prêt
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="space-x-2">
                <button id="btn-copy-all" class="btn-primary">
                    <i class="fas fa-copy mr-2"></i>Tout copier
                </button>
                <button id="btn-download-all" class="btn-secondary">
                    <i class="fas fa-download mr-2"></i>Télécharger
                </button>
            </div>
        </div>

        <!-- Badges de status -->
        <div class="flex flex-wrap gap-2 mt-6">
            <span class="badge badge-success">
                <i class="fas fa-server mr-1"></i> Backend OK
            </span>
            <span class="badge badge-info">
                <i class="fas fa-exchange-alt mr-1"></i> Communication OK
            </span>
            <?php if (!$testMode): ?>
                <span class="badge badge-warning">
                    <i class="fas fa-search mr-1"></i> SEO Optimisé
                </span>
                <span class="badge bg-purple-100 text-purple-800">
                    <i class="fas fa-users mr-1"></i> People-first
                </span>
            <?php endif; ?>
        </div>
    </div>

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
    // Récupération des vraies données de l'API
    // Cette fonction sera utilisée quand les vrais workflows seront implémentés
    const resultsContainer = document.getElementById('production-results');
    resultsContainer.innerHTML = `
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-construction text-yellow-600 text-3xl mb-4"></i>
            <h3 class="text-lg font-bold text-yellow-800 mb-2">Mode Production</h3>
            <p class="text-yellow-700">
                Les workflows de production seront disponibles après implémentation des algorithmes IA.
            </p>
        </div>
    `;
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