<?php
/**
 * Éditeur de prompt pour article_generator
 * Fichier: frontend/public/admin/prompt_editor.php
 */

$pageTitle = "Éditeur de Prompt - Admin";
require_once '../../includes/header.php';
requireAdmin(); // Protection admin

// Récupérer le workflow sélectionné
$workflow = $_GET['workflow'] ?? '1';
$workflow = in_array($workflow, ['1', '2', '3']) ? $workflow : '1';

$workflowNames = [
    '1' => 'Création d\'article',
    '2' => 'Réécriture d\'article',
    '3' => 'Cluster d\'articles'
];

$workflowColors = [
    '1' => 'purple',
    '2' => 'green',
    '3' => 'blue'
];

$workflowName = $workflowNames[$workflow];
$workflowColor = $workflowColors[$workflow];

$templatePath = "../../../backend/workflows/workflow_{$workflow}/templates/article_prompt_template.txt";
$templateRealPath = realpath($templatePath);
?>

<div class="max-w-7xl mx-auto">
    <!-- En-tête avec gradient -->
    <div class="bg-gradient-to-r from-<?php echo $workflowColor; ?>-600 to-<?php echo $workflowColor; ?>-700 rounded-2xl shadow-2xl p-8 mb-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-4">
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-bold mr-3">
                        Workflow <?php echo $workflow; ?>
                    </div>
                    <span class="text-<?php echo $workflowColor; ?>-100"><?php echo $workflowName; ?></span>
                </div>
                <h1 class="text-4xl font-bold mb-2">
                    <i class="fas fa-edit mr-3"></i>
                    Éditeur de Prompt
                </h1>
                <p class="text-<?php echo $workflowColor; ?>-100">Personnalisez le template de génération pour ce workflow</p>
            </div>
            <a href="prompts.php" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-3 px-6 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations sur les variables -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded-lg">
        <h3 class="text-lg font-bold text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            Variables disponibles pour Workflow <?php echo $workflow; ?>
        </h3>
        <p class="text-blue-800 mb-4">Ces variables sont automatiquement remplacées par les données utilisateur :</p>
        <div class="grid md:grid-cols-2 gap-4">
            <?php if ($workflow == '1'): ?>
            <!-- Variables Workflow 1: Création d'article -->
            <div>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{DOMAIN}')" title="Cliquez pour copier">{DOMAIN}</code> - Domaine d'activité<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{KEYWORD}')" title="Cliquez pour copier">{KEYWORD}</code> - Mot-clé principal<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{GUIDELINE}')" title="Cliquez pour copier">{GUIDELINE}</code> - Brief utilisateur<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{SITE_URL}')" title="Cliquez pour copier">{SITE_URL}</code> - URL du site<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{CONTENT_TONE}')" title="Cliquez pour copier">{CONTENT_TONE}</code> - Ton du contenu<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{TARGET_AUDIENCE}')" title="Cliquez pour copier">{TARGET_AUDIENCE}</code> - Audience cible<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{MAIN_TOPICS}')" title="Cliquez pour copier">{MAIN_TOPICS}</code> - Thèmes principaux
            </div>
            <div>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{SEO_OPPORTUNITIES}')" title="Cliquez pour copier">{SEO_OPPORTUNITIES}</code> - Opportunités SEO<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{CONTENT_GAPS}')" title="Cliquez pour copier">{CONTENT_GAPS}</code> - Lacunes de contenu<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{CONTENT_STRATEGY}')" title="Cliquez pour copier">{CONTENT_STRATEGY}</code> - Stratégie de contenu<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{KEYWORD_OPPORTUNITIES}')" title="Cliquez pour copier">{KEYWORD_OPPORTUNITIES}</code> - Mots-clés suggérés<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{INTERNAL_LINKS}')" title="Cliquez pour copier">{INTERNAL_LINKS}</code> - Liens internes<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{EXTERNAL_REFS}')" title="Cliquez pour copier">{EXTERNAL_REFS}</code> - Références externes<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{CURRENT_DATE}')" title="Cliquez pour copier">{CURRENT_DATE}</code> - Date actuelle
            </div>
            <?php elseif ($workflow == '2'): ?>
            <!-- Variables Workflow 2: Réécriture d'article -->
            <div>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{ORIGINAL_TITLE}')" title="Cliquez pour copier">{ORIGINAL_TITLE}</code> - Titre actuel<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{ORIGINAL_CONTENT}')" title="Cliquez pour copier">{ORIGINAL_CONTENT}</code> - Contenu HTML original<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{ORIGINAL_TEXT}')" title="Cliquez pour copier">{ORIGINAL_TEXT}</code> - Texte brut original<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{ORIGINAL_META_DESC}')" title="Cliquez pour copier">{ORIGINAL_META_DESC}</code> - Meta description actuelle<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{WORD_COUNT}')" title="Cliquez pour copier">{WORD_COUNT}</code> - Nombre de mots
            </div>
            <div>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{KEYWORD}')" title="Cliquez pour copier">{KEYWORD}</code> - Mot-clé principal<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{INTERNAL_LINKS}')" title="Cliquez pour copier">{INTERNAL_LINKS}</code> - Liens internes à ajouter<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{CURRENT_DATE}')" title="Cliquez pour copier">{CURRENT_DATE}</code> - Date actuelle<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{SOURCE_URL}')" title="Cliquez pour copier">{SOURCE_URL}</code> - URL source de l'article
            </div>
            <?php elseif ($workflow == '3'): ?>
            <!-- Variables Workflow 3: Cluster d'articles -->
            <div>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{KEYWORD}')" title="Cliquez pour copier">{KEYWORD}</code> - Mot-clé principal<br>
                <code class="variable-tag cursor-pointer hover:bg-blue-200 transition" onclick="copyVariable('{CURRENT_DATE}')" title="Cliquez pour copier">{CURRENT_DATE}</code> - Date actuelle
            </div>
            <div>
                <span class="text-gray-600 text-sm italic">À compléter selon les besoins du workflow 3</span>
            </div>
            <?php endif; ?>
        </div>
        <p class="text-blue-800 mt-4 text-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Important :</strong> Ne supprimez pas ces variables ! Elles sont nécessaires au bon fonctionnement du workflow.
        </p>
    </div>

    <!-- Statistiques du prompt -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Caractères</p>
                    <p class="text-3xl font-bold" id="stat-chars">0</p>
                </div>
                <i class="fas fa-font text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Lignes</p>
                    <p class="text-3xl font-bold" id="stat-lines">0</p>
                </div>
                <i class="fas fa-align-left text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Variables</p>
                    <p class="text-3xl font-bold" id="stat-vars">0</p>
                </div>
                <i class="fas fa-code text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Statut</p>
                    <p class="text-lg font-bold" id="stat-status">Non modifié</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Éditeur -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <!-- Barre d'outils -->
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h3 class="font-bold text-gray-700">
                        <i class="fas fa-code mr-2"></i>
                        Éditeur de Template
                    </h3>
                    <button type="button" onclick="increaseFontSize()" class="text-gray-600 hover:text-gray-900 text-sm">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" onclick="decreaseFontSize()" class="text-gray-600 hover:text-gray-900 text-sm">
                        <i class="fas fa-search-minus"></i>
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-keyboard mr-2"></i>
                    Police monospace
                </div>
            </div>
        </div>

        <form id="promptForm" class="p-8">
            <div class="mb-6">
                <textarea
                    id="promptContent"
                    name="promptContent"
                    rows="25"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-<?php echo $workflowColor; ?>-500 focus:border-transparent font-mono text-sm bg-gray-50 leading-relaxed"
                    placeholder="Chargement du template..."
                    spellcheck="false"
                ></textarea>
            </div>

            <div class="flex justify-between items-center">
                <div>
                    <div id="saveStatus" class="text-sm font-medium"></div>
                    <div id="versionInfo" class="text-xs text-gray-500 mt-1"></div>
                </div>
                <div class="space-x-4">
                    <button type="button" id="historyBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-history mr-2"></i>
                        Historique
                    </button>
                    <button type="button" id="resetBtn" class="btn-secondary">
                        <i class="fas fa-undo mr-2"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Historique des modifications -->
    <div class="bg-white rounded-lg shadow-xl p-8 mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-database mr-2 text-green-600"></i>
            Stockage Base de Données
        </h2>
        <div class="text-sm text-gray-600">
            <p class="mb-2"><strong>Mode :</strong> <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-bold">Base de données avec versioning</span></p>
            <p class="mb-2"><strong>Version active :</strong> <span id="activeVersion" class="font-bold">-</span></p>
            <p class="mb-2"><strong>Auteur :</strong> <span id="author">-</span></p>
            <p><strong>Dernière modification :</strong> <span id="lastModified">-</span></p>
        </div>
    </div>
</div>

<!-- Autocomplete dropdown -->
<div id="autocomplete" class="hidden fixed bg-white border-2 border-blue-500 rounded-lg shadow-xl z-50 max-h-64 overflow-y-auto">
    <!-- Will be populated dynamically -->
</div>

<style>
/* Style pour les variables dans les tags cliquables */
.variable-tag {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 3px;
    box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.variable-tag::before {
    content: '\f0c5';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    opacity: 0;
    transition: opacity 0.2s;
}

.variable-tag:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
    background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
}

.variable-tag:hover::before {
    opacity: 1;
}

.variable-tag:active {
    transform: translateY(0px);
}

/* Animation de copie */
@keyframes copyPulse {
    0% {
        transform: scale(1);
        box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
    }
    50% {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
    }
}

.variable-tag.copied {
    animation: copyPulse 0.5s ease;
}

.variable-tag.copied::before {
    content: '\f00c';
    opacity: 1;
    color: white;
}

/* Toast notification */
.copy-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(34, 197, 94, 0.3);
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    z-index: 9999;
    animation: slideInRight 0.3s ease, slideOutRight 0.3s ease 2.7s;
}

.copy-toast i {
    font-size: 20px;
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}

/* Style pour les variables dans le textarea */
#promptContent {
    line-height: 1.8 !important;
}

/* Autocomplete dropdown */
#autocomplete {
    backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, 0.98);
    border: 2px solid #8b5cf6;
    box-shadow: 0 20px 60px rgba(139, 92, 246, 0.3);
}

/* Autocomplete items */
.autocomplete-item {
    padding: 14px 18px;
    cursor: pointer;
    border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 12px;
}

.autocomplete-item:hover,
.autocomplete-item.selected {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    color: white;
    transform: translateX(4px);
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item::before {
    content: '\f101';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 14px;
    opacity: 0;
    transition: opacity 0.2s;
}

.autocomplete-item:hover::before,
.autocomplete-item.selected::before {
    opacity: 1;
}

.autocomplete-variable {
    font-weight: 700;
    font-size: 14px;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
    background: rgba(255, 255, 255, 0.15);
    padding: 4px 8px;
    border-radius: 4px;
}

.autocomplete-item:not(:hover):not(.selected) .autocomplete-variable {
    background: rgba(139, 92, 246, 0.1);
    color: #8b5cf6;
}

.autocomplete-description {
    font-size: 13px;
    opacity: 0.85;
    flex: 1;
}
</style>

<!-- Modal d'historique des versions -->
<div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Header du modal -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-history mr-2"></i>
                        Historique des Versions
                    </h2>
                    <button onclick="closeHistory()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <p class="text-blue-100 mt-2">Toutes les versions enregistrées avec possibilité de rollback</p>
            </div>

            <!-- Contenu du modal -->
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div id="versionsContainer">
                    <div class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Chargement de l'historique...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let originalContent = '';

// Charger le contenu du template
async function loadTemplate() {
    const workflow = '<?php echo $workflow; ?>';
    try {
        const response = await fetch(`api_prompt.php?action=load&workflow=${workflow}`);
        const data = await response.json();

        if (data.success) {
            originalContent = data.content;
            document.getElementById('promptContent').value = data.content;

            if (data.lastModified) {
                document.getElementById('lastModified').textContent = new Date(data.lastModified * 1000).toLocaleString('fr-FR');
            }
        } else {
            showStatus('Erreur : ' + data.error, 'error');
        }
    } catch (error) {
        showStatus('Erreur de chargement : ' + error.message, 'error');
    }
}

// Sauvegarder le template
document.getElementById('promptForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const workflow = '<?php echo $workflow; ?>';
    const content = document.getElementById('promptContent').value;

    try {
        const response = await fetch(`api_prompt.php?action=save&workflow=${workflow}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ content: content })
        });

        const data = await response.json();

        if (data.success) {
            originalContent = content;
            showStatus('✓ Template enregistré avec succès', 'success');

            // Actualiser la date de modification
            if (data.lastModified) {
                document.getElementById('lastModified').textContent = new Date(data.lastModified * 1000).toLocaleString('fr-FR');
            }
        } else {
            showStatus('Erreur : ' + data.error, 'error');
        }
    } catch (error) {
        showStatus('Erreur de sauvegarde : ' + error.message, 'error');
    }
});

// Réinitialiser les modifications
document.getElementById('resetBtn').addEventListener('click', () => {
    if (confirm('Voulez-vous vraiment annuler toutes les modifications ?')) {
        document.getElementById('promptContent').value = originalContent;
        showStatus('Modifications annulées', 'info');
    }
});

// Afficher un message de statut
function showStatus(message, type) {
    const statusDiv = document.getElementById('saveStatus');
    statusDiv.textContent = message;

    statusDiv.className = 'text-sm font-medium ';
    if (type === 'success') {
        statusDiv.className += 'text-green-600';
    } else if (type === 'error') {
        statusDiv.className += 'text-red-600';
    } else {
        statusDiv.className += 'text-blue-600';
    }

    if (type !== 'error') {
        setTimeout(() => {
            statusDiv.textContent = '';
        }, 3000);
    }
}

// Avertir si modifications non sauvegardées
window.addEventListener('beforeunload', (e) => {
    const currentContent = document.getElementById('promptContent').value;
    if (currentContent !== originalContent) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Mettre à jour les statistiques
function updateStats() {
    const content = document.getElementById('promptContent').value;

    // Caractères
    document.getElementById('stat-chars').textContent = content.length.toLocaleString();

    // Lignes
    const lines = content.split('\n').length;
    document.getElementById('stat-lines').textContent = lines;

    // Variables
    const varMatches = content.match(/\{[A-Z_]+\}/g);
    const varsCount = varMatches ? new Set(varMatches).size : 0;
    document.getElementById('stat-vars').textContent = varsCount;

    // Statut
    const isModified = content !== originalContent;
    document.getElementById('stat-status').textContent = isModified ? 'Modifié' : 'Non modifié';
    document.getElementById('stat-status').parentElement.parentElement.className = isModified ?
        'bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-6 text-white' :
        'bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-6 text-white';
}

// Écouter les changements
document.getElementById('promptContent').addEventListener('input', updateStats);

// Fonctions de zoom
let currentFontSize = 14;

function increaseFontSize() {
    if (currentFontSize < 20) {
        currentFontSize += 2;
        document.getElementById('promptContent').style.fontSize = currentFontSize + 'px';
    }
}

function decreaseFontSize() {
    if (currentFontSize > 10) {
        currentFontSize -= 2;
        document.getElementById('promptContent').style.fontSize = currentFontSize + 'px';
    }
}

// Ouvrir le modal d'historique
document.getElementById('historyBtn').addEventListener('click', openHistory);

async function openHistory() {
    document.getElementById('historyModal').classList.remove('hidden');
    await loadVersions();
}

function closeHistory() {
    document.getElementById('historyModal').classList.add('hidden');
}

// Charger toutes les versions
async function loadVersions() {
    const workflow = '<?php echo $workflow; ?>';
    try {
        const response = await fetch(`api_prompt.php?action=versions&workflow=${workflow}`);
        const data = await response.json();

        if (data.success && data.versions) {
            displayVersions(data.versions);
        } else {
            document.getElementById('versionsContainer').innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                    <p class="text-red-600">Erreur : ${data.error || 'Impossible de charger les versions'}</p>
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('versionsContainer').innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                <p class="text-red-600">Erreur de chargement : ${error.message}</p>
            </div>
        `;
    }
}

// Afficher les versions
function displayVersions(versions) {
    if (versions.length === 0) {
        document.getElementById('versionsContainer').innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Aucune version trouvée</p>
            </div>
        `;
        return;
    }

    const html = versions.map(version => `
        <div class="border ${version.is_active ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-lg p-6 mb-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="text-2xl font-bold text-gray-900 mr-3">Version ${version.version}</span>
                        ${version.is_active ? '<span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">ACTIVE</span>' : ''}
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><i class="fas fa-user mr-2"></i><strong>Auteur :</strong> ${version.author_name || 'Inconnu'}</p>
                        <p><i class="fas fa-clock mr-2"></i><strong>Date :</strong> ${new Date(version.created_at).toLocaleString('fr-FR')}</p>
                        <p><i class="fas fa-sticky-note mr-2"></i><strong>Notes :</strong> ${version.notes || 'Aucune note'}</p>
                        <p><i class="fas fa-font mr-2"></i><strong>Taille :</strong> ${version.content.length.toLocaleString()} caractères</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="viewVersion(${version.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-eye mr-1"></i>Voir
                    </button>
                    ${!version.is_active ? `
                        <button onclick="activateVersion(${version.id}, ${version.version})" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-check mr-1"></i>Activer
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');

    document.getElementById('versionsContainer').innerHTML = html;
}

// Voir une version spécifique
async function viewVersion(versionId) {
    const workflow = '<?php echo $workflow; ?>';
    try {
        const response = await fetch(`api_prompt.php?action=view&workflow=${workflow}&version_id=${versionId}`);
        const data = await response.json();

        if (data.success) {
            // Afficher dans un modal de prévisualisation
            alert(`Version ${data.version}\n\nAuteur: ${data.author}\nDate: ${new Date(data.created_at).toLocaleString('fr-FR')}\n\nContenu:\n${data.content.substring(0, 500)}...`);
        } else {
            alert('Erreur : ' + data.error);
        }
    } catch (error) {
        alert('Erreur : ' + error.message);
    }
}

// Activer une version (rollback)
async function activateVersion(versionId, versionNumber) {
    if (!confirm(`Voulez-vous vraiment activer la version ${versionNumber} ?\n\nCela désactivera la version actuelle et appliquera cette version pour toutes les futures générations d'articles.`)) {
        return;
    }

    const workflow = '<?php echo $workflow; ?>';
    try {
        const formData = new FormData();
        formData.append('version_id', versionId);

        const response = await fetch(`api_prompt.php?action=activate&workflow=${workflow}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert(`✓ Version ${data.version} activée avec succès !`);
            closeHistory();
            await loadTemplate();
            updateStats();
        } else {
            alert('Erreur : ' + data.error);
        }
    } catch (error) {
        alert('Erreur : ' + error.message);
    }
}

// ====================
// COPIE AU CLIC DES VARIABLES
// ====================

function copyVariable(variable) {
    // Trouver le tag cliqué
    const tags = document.querySelectorAll('.variable-tag');
    let clickedTag = null;

    tags.forEach(tag => {
        if (tag.textContent.trim() === variable) {
            clickedTag = tag;
        }
    });

    navigator.clipboard.writeText(variable).then(() => {
        // Animation sur le tag
        if (clickedTag) {
            clickedTag.classList.add('copied');
            setTimeout(() => {
                clickedTag.classList.remove('copied');
            }, 500);
        }

        // Toast notification
        showCopyToast(variable);
    }).catch(err => {
        console.error('Erreur de copie:', err);
        showStatus('Erreur de copie', 'error');
    });
}

function showCopyToast(variable) {
    // Créer le toast
    const toast = document.createElement('div');
    toast.className = 'copy-toast';
    toast.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span><strong>${variable}</strong> copié dans le presse-papiers !</span>
    `;

    document.body.appendChild(toast);

    // Supprimer après animation
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// ====================
// AUTOCOMPLÉTION DES VARIABLES
// ====================

const variables = [
    { name: '{DOMAIN}', description: 'Domaine d\'activité' },
    { name: '{KEYWORD}', description: 'Mot-clé principal' },
    { name: '{GUIDELINE}', description: 'Brief utilisateur' },
    { name: '{SITE_URL}', description: 'URL du site' },
    { name: '{CONTENT_TONE}', description: 'Ton du contenu' },
    { name: '{TARGET_AUDIENCE}', description: 'Audience cible' },
    { name: '{MAIN_TOPICS}', description: 'Thèmes principaux' },
    { name: '{SEO_OPPORTUNITIES}', description: 'Opportunités SEO' },
    { name: '{CONTENT_GAPS}', description: 'Lacunes de contenu' },
    { name: '{CONTENT_STRATEGY}', description: 'Stratégie de contenu' },
    { name: '{KEYWORD_OPPORTUNITIES}', description: 'Mots-clés suggérés' },
    { name: '{INTERNAL_LINKS}', description: 'Liens internes' },
    { name: '{EXTERNAL_REFS}', description: 'Références externes' },
    { name: '{CURRENT_DATE}', description: 'Date actuelle' }
];

let selectedIndex = -1;
let filteredVariables = [];

const textarea = document.getElementById('promptContent');
const autocompleteDiv = document.getElementById('autocomplete');

// Écouter les touches
textarea.addEventListener('input', handleInput);
textarea.addEventListener('keydown', handleKeydown);

// Fermer l'autocomplétion si clic ailleurs
document.addEventListener('click', (e) => {
    if (!autocompleteDiv.contains(e.target) && e.target !== textarea) {
        hideAutocomplete();
    }
});

function handleInput(e) {
    const cursorPos = textarea.selectionStart;
    const textBeforeCursor = textarea.value.substring(0, cursorPos);

    // Chercher si on est en train de taper une variable (après une accolade ouvrante)
    const match = textBeforeCursor.match(/\{([A-Z_]*)$/);

    if (match) {
        const query = match[1];
        filteredVariables = variables.filter(v =>
            v.name.substring(1).startsWith(query)  // Enlever le { du nom
        );

        if (filteredVariables.length > 0) {
            showAutocomplete(filteredVariables);
        } else {
            hideAutocomplete();
        }
    } else {
        hideAutocomplete();
    }
}

function handleKeydown(e) {
    if (!autocompleteDiv.classList.contains('hidden')) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, filteredVariables.length - 1);
            updateSelection();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            updateSelection();
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            insertVariable(filteredVariables[selectedIndex].name);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            hideAutocomplete();
        }
    }
}

function showAutocomplete(vars) {
    selectedIndex = 0;

    const html = vars.map((v, index) => `
        <div class="autocomplete-item ${index === 0 ? 'selected' : ''}"
             data-index="${index}"
             onclick="insertVariable('${v.name}')">
            <div class="autocomplete-variable">${v.name}</div>
            <div class="autocomplete-description">${v.description}</div>
        </div>
    `).join('');

    autocompleteDiv.innerHTML = html;

    // Positionner le dropdown près du curseur
    const rect = textarea.getBoundingClientRect();
    const lineHeight = parseInt(window.getComputedStyle(textarea).lineHeight);
    const lines = textarea.value.substring(0, textarea.selectionStart).split('\n').length;

    autocompleteDiv.style.left = rect.left + 'px';
    autocompleteDiv.style.top = (rect.top + (lines * lineHeight) - textarea.scrollTop) + 'px';
    autocompleteDiv.style.minWidth = '300px';

    autocompleteDiv.classList.remove('hidden');
}

function hideAutocomplete() {
    autocompleteDiv.classList.add('hidden');
    selectedIndex = -1;
}

function updateSelection() {
    const items = autocompleteDiv.querySelectorAll('.autocomplete-item');
    items.forEach((item, index) => {
        if (index === selectedIndex) {
            item.classList.add('selected');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('selected');
        }
    });
}

function insertVariable(variableName) {
    const cursorPos = textarea.selectionStart;
    const textBeforeCursor = textarea.value.substring(0, cursorPos);
    const textAfterCursor = textarea.value.substring(cursorPos);

    // Trouver où commence le { pour remplacer
    const match = textBeforeCursor.match(/\{([A-Z_]*)$/);
    if (match) {
        const startPos = cursorPos - match[0].length;
        textarea.value = textarea.value.substring(0, startPos) + variableName + textAfterCursor;

        // Placer le curseur après la variable insérée
        textarea.selectionStart = textarea.selectionEnd = startPos + variableName.length;
    }

    hideAutocomplete();
    textarea.focus();
    updateStats();
}

// Charger au démarrage
loadTemplate().then(() => {
    updateStats();
});
</script>

<?php require_once '../../includes/footer.php'; ?>