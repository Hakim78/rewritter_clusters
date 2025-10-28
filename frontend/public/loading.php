<?php
/**
 * Page de chargement hybride avec modal de choix
 * Fichier: frontend/public/loading.php
 */

require_once '../includes/functions.php';
requireAuth();

$pageTitle = "Génération en cours - SEO Article Generator";
require_once '../includes/header.php';
$workflowType = isset($_GET['workflow']) ? (int)$_GET['workflow'] : 1;
?>

<!-- Modal de choix (overlay complet) -->
<div id="choice-modal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full p-8 relative animate-fade-in">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <i class="fas fa-check text-green-600 text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Workflow lancé avec succès !</h2>
            <p class="text-gray-600">ID: <span id="workflow-id-modal" class="font-mono text-purple-600"></span></p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <div onclick="continueWatching()" class="choice-card bg-gradient-to-br from-purple-500 to-indigo-600 text-white p-6 rounded-xl shadow-lg cursor-pointer hover:scale-105 transition-transform">
                <div class="text-center">
                    <i class="fas fa-chart-line text-5xl mb-4 opacity-90"></i>
                    <h3 class="text-xl font-bold mb-2">Suivre ici</h3>
                    <p class="text-sm opacity-90">Voir la progression en temps réel</p>
                </div>
            </div>

            <div onclick="goToHistory()" class="choice-card bg-gradient-to-br from-blue-500 to-cyan-600 text-white p-6 rounded-xl shadow-lg cursor-pointer hover:scale-105 transition-transform">
                <div class="text-center">
                    <i class="fas fa-history text-5xl mb-4 opacity-90"></i>
                    <h3 class="text-xl font-bold mb-2">Aller à l'historique</h3>
                    <p class="text-sm opacity-90">Retrouver tous vos workflows</p>
                </div>
            </div>

            <div onclick="launchNew()" class="choice-card bg-gradient-to-br from-green-500 to-emerald-600 text-white p-6 rounded-xl shadow-lg cursor-pointer hover:scale-105 transition-transform">
                <div class="text-center">
                    <i class="fas fa-plus-circle text-5xl mb-4 opacity-90"></i>
                    <h3 class="text-xl font-bold mb-2">Nouveau workflow</h3>
                    <p class="text-sm opacity-90">Lancer une autre automatisation</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Info :</strong> Votre workflow continue en arrière-plan. Vous pouvez quitter cette page et le retrouver dans l'historique.
            </p>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <!-- En-tête de progression -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="text-center mb-8">
            <div class="inline-block bg-gradient-to-r from-blue-500 to-purple-500 p-4 rounded-full mb-4">
                <i class="fas <?php
                    if ($workflowType === 2) echo 'fa-sync-alt';
                    else if ($workflowType === 3) echo 'fa-sitemap';
                    else echo 'fa-robot';
                ?> text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <?php
                    if ($workflowType === 2) echo 'Réécriture en cours...';
                    else if ($workflowType === 3) echo 'Création du cluster...';
                    else echo 'Génération en cours...';
                ?>
            </h1>
            <p class="text-gray-600 text-lg" id="subtitle-text">
                <?php
                    if ($workflowType === 2) echo 'Notre IA optimise votre article existant';
                    else if ($workflowType === 3) echo 'Création d\'un cluster de contenu thématique';
                    else echo 'Notre IA rédige votre article SEO optimisé';
                ?>
            </p>
        </div>

        <!-- Barre de progression globale -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Progression globale</span>
                <span id="global-progress-percent" class="text-sm font-bold text-blue-600">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div id="global-progress-bar" class="bg-gradient-to-r from-blue-500 to-purple-500 h-4 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
            </div>
        </div>

        <!-- Étapes du workflow -->
        <div class="space-y-4">
            <!-- Étape 1 -->
            <div id="step-1" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas <?php
                            if ($workflowType === 2) echo 'fa-file-alt';
                            else if ($workflowType === 3) echo 'fa-search';
                            else echo 'fa-globe';
                        ?> text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">
                            <?php
                                if ($workflowType === 2) echo 'Étape 1 : Extraction de l\'article';
                                else if ($workflowType === 3) echo 'Étape 1 : Analyse du pilier';
                                else echo 'Étape 1 : Analyse du site';
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 2) echo 'Récupération du contenu existant...';
                                else if ($workflowType === 3) echo 'Analyse de l\'article pilier...';
                                else echo 'Extraction du contenu et des liens...';
                            ?>
                        </p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Étape 2 -->
            <div id="step-2" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas <?php
                            if ($workflowType === 2) echo 'fa-sync-alt';
                            else if ($workflowType === 3) echo 'fa-star';
                            else echo 'fa-brain';
                        ?> text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">
                            <?php
                                if ($workflowType === 2) echo 'Étape 2 : Réécriture & Optimisation';
                                else if ($workflowType === 3) echo 'Étape 2 : Réécriture du pilier';
                                else echo 'Étape 2 : Analyse stratégique';
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 2) echo 'Optimisation SEO, LLMO, RAG...';
                                else if ($workflowType === 3) echo 'Optimisation du pilier...';
                                else echo 'IA analyse le contenu...';
                            ?>
                        </p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Étape 3 -->
            <div id="step-3" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas <?php
                            if ($workflowType === 2) echo 'fa-image';
                            else if ($workflowType === 3) echo 'fa-sitemap';
                            else echo 'fa-pen-fancy';
                        ?> text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">
                            <?php
                                if ($workflowType === 2) echo 'Étape 3 : Génération de l\'image';
                                else if ($workflowType === 3) echo 'Étape 3 : Génération des satellites';
                                else echo 'Étape 3 : Rédaction de l\'article';
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 2) echo 'Création de l\'image...';
                                else if ($workflowType === 3) echo 'Création de 3 satellites...';
                                else echo 'Génération du contenu...';
                            ?>
                        </p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Étape 4 (pour workflow 1 et 3) -->
            <?php if ($workflowType === 1 || $workflowType === 3): ?>
            <div id="step-4" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas fa-image text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">
                            <?php echo $workflowType === 3 ? 'Étape 4 : Génération des images' : 'Étape 4 : Génération de l\'image'; ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php echo $workflowType === 3 ? '4 images IA...' : 'Création de l\'image avec Ideogram AI...'; ?>
                        </p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Temps estimé -->
        <div class="mt-8 text-center">
            <div class="inline-flex items-center bg-blue-50 px-6 py-3 rounded-lg">
                <i class="fas fa-hourglass-half text-blue-600 mr-3"></i>
                <div class="text-left">
                    <div class="text-xs text-gray-600">Temps estimé</div>
                    <div id="estimated-time" class="text-lg font-bold text-blue-600">
                        <?php echo $workflowType === 3 ? '10-15 minutes' : '3-5 minutes'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques en temps réel -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-blue-500 mb-2" id="stat-words">---</div>
            <div class="text-sm text-gray-600">Mots générés</div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-green-500 mb-2" id="stat-time">0s</div>
            <div class="text-sm text-gray-600">Temps écoulé</div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-purple-500 mb-2" id="stat-step">1/<?php echo $workflowType === 2 ? '3' : '4'; ?></div>
            <div class="text-sm text-gray-600">Étape actuelle</div>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6 border border-indigo-200">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i> Le saviez-vous ?
        </h3>
        <div id="loading-tip" class="text-gray-700">
            Notre IA analyse simultanément plus de 50 critères SEO...
        </div>
    </div>
</div>

<!-- Bouton historique fixe -->
<div class="fixed bottom-6 right-6 z-40">
    <a href="/workflows.php" class="inline-flex items-center px-4 py-3 bg-white text-purple-600 font-medium rounded-lg shadow-lg hover:shadow-xl transition-all border-2 border-purple-600">
        <i class="fas fa-history mr-2"></i> Voir l'historique
    </a>
</div>

<script src="assets/js/app.js"></script>
<script>
const workflowType = <?php echo $workflowType; ?>;
let startTime = Date.now();
let currentStep = 0;
let pollingInterval = null;
let elapsedTimeInterval = null;
let workflowId = null;
let modalShown = false;

const loadingTips = [
    "Notre IA analyse simultanément plus de 50 critères SEO pour optimiser votre contenu...",
    "Saviez-vous ? Un article optimisé pour l'IA générative génère 3x plus de trafic.",
    "Nous intégrons les meilleures pratiques People-First de Google.",
    "Optimisation automatique pour les bases RAG et les recherches par IA.",
    "Claude Sonnet 4.5 rédige avec un niveau d'expertise équivalent à un senior.",
    "L'optimisation LLMO garantit une excellente compréhension par ChatGPT.",
    "Contenu structuré en blocs autonomes pour maximiser la réutilisation.",
    "FAQ générées au format JSON pour intégration facile."
];

let currentTipIndex = 0;

document.addEventListener('DOMContentLoaded', async function() {
    await checkForExistingWorkflow();
    
    const formData = sessionStorage.getItem('workflowFormData');
    if (!formData) {
        Toast.show('Aucune donnée de formulaire trouvée', 'error');
        setTimeout(() => window.location.href = 'index.php', 2000);
        return;
    }

    startWorkflow(JSON.parse(formData));
    startElapsedTimeCounter();
    setInterval(rotateTip, 8000);
});

async function checkForExistingWorkflow() {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch('/api/workflows?limit=10', {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const data = await response.json();
        
        if (data.success && data.workflows) {
            const processingWorkflow = data.workflows.find(w => 
                w.status === 'processing' && 
                w.workflow_type === getWorkflowTypeString()
            );

            if (processingWorkflow) {
                const resume = confirm(
                    `Un workflow "${getWorkflowTypeLabel()}" est déjà en cours.\n\n` +
                    `Voulez-vous le reprendre ?\n\n` +
                    `OUI : Reprendre | NON : Nouveau workflow`
                );

                if (resume) {
                    workflowId = processingWorkflow.workflow_id;
                    sessionStorage.removeItem('workflowFormData');
                    pollProgress();
                    startElapsedTimeCounter();
                    setInterval(rotateTip, 8000);
                    return true;
                }
            }
        }
    } catch (error) {
        console.error('Erreur vérification workflows:', error);
    }
    return false;
}

function getWorkflowTypeString() {
    return ['scratch', 'rewrite', 'cluster'][workflowType - 1] || 'scratch';
}

function getWorkflowTypeLabel() {
    return ['From Scratch', 'Réécriture', 'Cluster'][workflowType - 1] || 'Création';
}

async function startWorkflow(data) {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/workflow${workflowType}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        
        const startResult = await response.json();

        if (startResult.status === 'error') {
            throw new Error(startResult.error || startResult.message || 'Workflow failed');
        }

        workflowId = startResult.workflow_id;

        if (!workflowId) {
            throw new Error('No workflow ID returned');
        }

        // Sauvegarder dans localStorage pour Celery
        localStorage.setItem('current_workflow_id', workflowId);
        document.getElementById('workflow-id-modal').textContent = workflowId;

        // Afficher la modal après 3 secondes
        setTimeout(() => {
            if (!modalShown) {
                document.getElementById('choice-modal').classList.remove('hidden');
                modalShown = true;
            }
        }, 3000);

        pollProgress();

    } catch (error) {
        console.error('Workflow error:', error);
        updateStepStatus(currentStep || 1, 'error');
        Toast.show(`Erreur: ${error.message}`, 'error');
        setTimeout(() => window.location.href = `option${workflowType}.php`, 3000);
    }
}

async function pollProgress() {
    if (!workflowId) return;

    pollingInterval = setInterval(async () => {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`/api/workflow-progress/${workflowId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const progress = await response.json();

            if (progress.status === 'not_found') {
                clearInterval(pollingInterval);
                throw new Error('Workflow not found');
            }

            updateGlobalProgress(progress.progress_percent || 0);

            if (progress.step_details) {
                for (let i = 1; i <= 4; i++) {
                    const stepKey = `step_${i}`;
                    if (progress.step_details[stepKey]) {
                        updateStepStatus(i, progress.step_details[stepKey].status);

                        if (workflowType === 3 && i === 1 && progress.step_details[stepKey].status === 'completed') {
                            updateClusterInfo(progress);
                        }
                    }
                }
            }

            if (progress.current_step) {
                const totalSteps = workflowType === 2 ? 3 : 4;
                document.getElementById('stat-step').textContent = `${progress.current_step}/${totalSteps}`;
                currentStep = progress.current_step;
            }

            if (progress.status === 'completed') {
                clearInterval(pollingInterval);
                clearInterval(elapsedTimeInterval);

                if (progress.result && progress.result.article && progress.result.article.word_count) {
                    document.getElementById('stat-words').textContent = progress.result.article.word_count;
                }

                localStorage.removeItem('current_workflow_id');
                Toast.show('Article généré avec succès !', 'success');

                setTimeout(() => {
                    window.location.href = `workflows.php?highlight=${workflowId}`;
                }, 1500);
            }

            if (progress.status === 'error') {
                clearInterval(pollingInterval);
                clearInterval(elapsedTimeInterval);
                throw new Error(progress.error || 'Workflow failed');
            }

        } catch (error) {
            clearInterval(pollingInterval);
            clearInterval(elapsedTimeInterval);
            console.error('Polling error:', error);
            updateStepStatus(currentStep || 1, 'error');
            Toast.show(`Erreur: ${error.message}`, 'error');
            setTimeout(() => window.location.href = `option${workflowType}.php`, 3000);
        }
    }, 2000);
}

function updateStepStatus(stepNumber, status) {
    const stepEl = document.getElementById(`step-${stepNumber}`);
    if (!stepEl) return;

    const iconEl = stepEl.querySelector('.step-icon');
    const statusEl = stepEl.querySelector('.step-status');

    stepEl.classList.remove('bg-gray-50', 'bg-blue-50', 'bg-green-50', 'bg-red-50', 'border-gray-200', 'border-blue-400', 'border-green-400', 'border-red-400');
    iconEl.classList.remove('bg-gray-300', 'bg-blue-500', 'bg-green-500', 'bg-red-500', 'text-gray-500', 'text-white');

    if (status === 'in_progress') {
        stepEl.classList.add('bg-blue-50', 'border-blue-400');
        iconEl.classList.add('bg-blue-500', 'text-white');
        statusEl.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500 text-xl"></i>';
    } else if (status === 'completed') {
        stepEl.classList.add('bg-green-50', 'border-green-400');
        iconEl.classList.add('bg-green-500', 'text-white');
        statusEl.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
    } else if (status === 'error') {
        stepEl.classList.add('bg-red-50', 'border-red-400');
        iconEl.classList.add('bg-red-500', 'text-white');
        statusEl.innerHTML = '<i class="fas fa-times-circle text-red-500 text-xl"></i>';
    } else {
        stepEl.classList.add('bg-gray-50', 'border-gray-200');
        iconEl.classList.add('bg-gray-300', 'text-gray-500');
        statusEl.innerHTML = '<i class="fas fa-clock text-gray-400 text-xl"></i>';
    }
}

function updateGlobalProgress(percent) {
    document.getElementById('global-progress-bar').style.width = `${percent}%`;
    document.getElementById('global-progress-percent').textContent = `${percent}%`;
}

function startElapsedTimeCounter() {
    elapsedTimeInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;

        document.getElementById('stat-time').textContent = minutes > 0 ? `${minutes}m ${seconds}s` : `${seconds}s`;

        const remaining = Math.max(0, 180 - elapsed);
        const remMinutes = Math.floor(remaining / 60);
        const remSeconds = remaining % 60;

        document.getElementById('estimated-time').textContent = remaining > 0 
            ? `${remMinutes}m ${remSeconds}s restantes` 
            : 'Bientôt terminé...';
    }, 1000);
}

function rotateTip() {
    currentTipIndex = (currentTipIndex + 1) % loadingTips.length;
    const tipEl = document.getElementById('loading-tip');
    tipEl.style.opacity = '0';
    setTimeout(() => {
        tipEl.textContent = loadingTips[currentTipIndex];
        tipEl.style.opacity = '1';
    }, 300);
}

function updateClusterInfo(progress) {
    if (workflowType !== 3) return;

    let satelliteCount = 3;

    if (progress.step_details && progress.step_details.step_1 && progress.step_details.step_1.data) {
        const stepData = progress.step_details.step_1.data;
        if (stepData.satellite_themes && Array.isArray(stepData.satellite_themes)) {
            satelliteCount = stepData.satellite_themes.length;
        } else if (stepData.satellites_count) {
            satelliteCount = stepData.satellites_count;
        }
    }

    const subtitleEl = document.getElementById('subtitle-text');
    if (subtitleEl) {
        subtitleEl.innerHTML = `1 article pilier + <strong class="text-purple-600">${satelliteCount} satellites</strong> détectés`;
    }

    const step3El = document.getElementById('step-3');
    if (step3El) {
        const descEl = step3El.querySelector('.text-sm.text-gray-600');
        if (descEl) descEl.textContent = `Création de ${satelliteCount} satellites...`;
    }

    const step4El = document.getElementById('step-4');
    if (step4El) {
        const descEl = step4El.querySelector('.text-sm.text-gray-600');
        if (descEl) descEl.textContent = `${satelliteCount + 1} images IA (1 pilier + ${satelliteCount} satellites)...`;
    }
}

// Fonctions modal
function continueWatching() {
    document.getElementById('choice-modal').classList.add('hidden');
}

function goToHistory() {
    window.location.href = '/workflows.php';
}

function launchNew() {
    const pages = { 1: '/option1.php', 2: '/option2.php', 3: '/option3.php' };
    window.location.href = pages[workflowType] || '/';
}

window.addEventListener('beforeunload', function (e) {
    if (currentStep < 3 && workflowId) {
        e.preventDefault();
        e.returnValue = 'Génération en cours. Workflow sauvegardé dans l\'historique.';
    }
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.choice-card {
    transition: all 0.3s ease;
}

.choice-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

#loading-tip, #subtitle-text {
    transition: opacity 0.3s ease-in-out;
}

.step-container {
    transition: all 0.3s ease-in-out;
}

.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php require_once '../includes/footer.php'; ?>
