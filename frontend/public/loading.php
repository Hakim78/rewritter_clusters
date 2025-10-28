<?php
/**
 * Page de chargement avec suivi en temps réel
 * Fichier: frontend/public/loading.php
 */

require_once '../includes/functions.php';
requireAuth(); // Vérification de l'authentification AVANT le header

$pageTitle = "Génération en cours - SEO Article Generator";
require_once '../includes/header.php';
// Récupération du type de workflow
$workflowType = isset($_GET['workflow']) ? (int)$_GET['workflow'] : 1;
?>

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
                    if ($workflowType === 2) {
                        echo 'Réécriture en cours...';
                    } else if ($workflowType === 3) {
                        echo 'Création du cluster...';
                    } else {
                        echo 'Génération en cours...';
                    }
                ?>
            </h1>
            <p class="text-gray-600 text-lg" id="subtitle-text">
                <?php
                    if ($workflowType === 2) {
                        echo 'Notre IA optimise votre article existant';
                    } else if ($workflowType === 3) {
                        echo 'Création d\'un cluster de contenu thématique';
                    } else {
                        echo 'Notre IA rédige votre article SEO optimisé';
                    }
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
                <div id="global-progress-bar" class="bg-gradient-to-r from-blue-500 to-purple-500 h-4 rounded-full transition-all duration-500 ease-out" style="width: 0%">
                </div>
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
                                if ($workflowType === 2) {
                                    echo 'Étape 1 : Extraction de l\'article';
                                } else if ($workflowType === 3) {
                                    echo 'Étape 1 : Analyse du pilier';
                                } else {
                                    echo 'Étape 1 : Analyse du site';
                                }
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 2) {
                                    echo 'Récupération du contenu existant...';
                                } else if ($workflowType === 3) {
                                    echo 'Analyse de l\'article pilier et identification des thèmes...';
                                } else {
                                    echo 'Extraction du contenu et des liens...';
                                }
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
                                if ($workflowType === 2) {
                                    echo 'Étape 2 : Réécriture & Optimisation';
                                } else if ($workflowType === 3) {
                                    echo 'Étape 2 : Réécriture du pilier';
                                } else {
                                    echo 'Étape 2 : Analyse stratégique';
                                }
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 2) {
                                    echo 'Optimisation SEO, LLMO, RAG & People-first...';
                                } else if ($workflowType === 3) {
                                    echo 'Optimisation de l\'article pilier avec liens satellites...';
                                } else {
                                    echo 'IA analyse le contenu et identifie les opportunités...';
                                }
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
                                if ($workflowType === 2) {
                                    echo 'Étape 3 : Génération de l\'image';
                                } else if ($workflowType === 3) {
                                    echo 'Étape 3 : Génération des satellites';
                                } else {
                                    echo 'Étape 3 : Rédaction de l\'article';
                                }
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 2) {
                                    echo 'Création de l\'image avec Ideogram AI...';
                                } else if ($workflowType === 3) {
                                    echo 'Création de 3 articles satellites complémentaires...';
                                } else {
                                    echo 'Génération du contenu optimisé SEO, LLMO et RAG...';
                                }
                            ?>
                        </p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Étape 4: Image (for workflow 1 and 3) -->
            <?php if ($workflowType === 1 || $workflowType === 3): ?>
            <div id="step-4" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas fa-image text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">
                            <?php
                                if ($workflowType === 3) {
                                    echo 'Étape 4 : Génération des images';
                                } else {
                                    echo 'Étape 4 : Génération de l\'image';
                                }
                            ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php
                                if ($workflowType === 3) {
                                    echo '4 images IA (1 pilier + 3 satellites)...';
                                } else {
                                    echo 'Création de l\'image avec Ideogram AI...';
                                }
                            ?>
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

    <!-- Tips pendant le chargement -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6 border border-indigo-200">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
            Le saviez-vous ?
        </h3>
        <div id="loading-tip" class="text-gray-700">
            Notre IA analyse simultanément plus de 50 critères SEO pour optimiser votre contenu...
        </div>
    </div>
</div>

<!-- Bouton accès historique (fixé en bas à droite) -->
    <div class="fixed bottom-6 right-6 z-50">
        <a href="/workflows.php" 
           class="inline-flex items-center px-4 py-3 bg-white text-purple-600 font-medium rounded-lg shadow-lg hover:shadow-xl transition-all border-2 border-purple-600">
            <i class="fas fa-history mr-2"></i>
            Voir l'historique
        </a>
    </div>
<script src="assets/js/app.js"></script>
<script>
// Configuration
const workflowType = <?php echo $workflowType; ?>;
let startTime = Date.now();
let currentStep = 0;
let pollingInterval = null;
let elapsedTimeInterval = null;
let workflowId = null;

// Tips à afficher pendant le chargement
const loadingTips = [
    "Notre IA analyse simultanément plus de 50 critères SEO pour optimiser votre contenu...",
    "Saviez-vous ? Un article optimisé pour l'IA générative génère 3x plus de trafic organique.",
    "Nous intégrons automatiquement les meilleures pratiques People-First de Google.",
    "Votre article sera optimisé pour les bases RAG et les recherches par IA.",
    "Claude Sonnet 4.5 rédige avec un niveau d'expertise équivalent à un rédacteur senior.",
    "L'optimisation LLMO garantit que votre contenu sera bien compris par ChatGPT et autres IA.",
    "Nous structurons votre contenu en blocs autonomes pour maximiser sa réutilisation.",
    "Les FAQ générées sont au format JSON pour une intégration facile dans vos systèmes."
];

let currentTipIndex = 0;

document.addEventListener('DOMContentLoaded', async function() {
    // 1. D'abord vérifier s'il y a un workflow en cours
    await checkForExistingWorkflow();
    
    // 2. Récupérer les données du formulaire depuis sessionStorage
    const formData = sessionStorage.getItem('workflowFormData');

    if (!formData) {
        Toast.show('Aucune donnée de formulaire trouvée', 'error');
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 2000);
        return;
    }

    // Lancer le workflow
    startWorkflow(JSON.parse(formData));

    // Démarrer le compteur de temps
    startElapsedTimeCounter();

    // Changer les tips toutes les 8 secondes
    setInterval(rotateTip, 8000);
});

// Fonction pour vérifier s'il existe un workflow en cours
async function checkForExistingWorkflow() {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch('/api/workflows?limit=10', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();
        
        if (data.success && data.workflows) {
            // Chercher un workflow "processing" du même type
            const processingWorkflow = data.workflows.find(w => 
                w.status === 'processing' && 
                w.workflow_type === getWorkflowTypeString()
            );

            if (processingWorkflow) {
                // Afficher une notification
                const resume = confirm(
                    `Un workflow de type "${getWorkflowTypeLabel()}" est déjà en cours.\n\n` +
                    `Voulez-vous le reprendre ?\n\n` +
                    `- OUI : Reprendre le workflow en cours\n` +
                    `- NON : Démarrer un nouveau workflow`
                );

                if (resume) {
                    // Reprendre le workflow existant
                    workflowId = processingWorkflow.workflow_id;
                    console.log('Reprise du workflow:', workflowId);
                    
                    // Nettoyer sessionStorage pour éviter de relancer
                    sessionStorage.removeItem('workflowFormData');
                    
                    // Démarrer le polling directement
                    pollProgress();
                    startElapsedTimeCounter();
                    setInterval(rotateTip, 8000);
                    
                    // Empêcher le démarrage d'un nouveau workflow
                    return true;
                }
            }
        }
    } catch (error) {
        console.error('Erreur lors de la vérification des workflows:', error);
    }
    
    return false;
}

// Helper pour obtenir le type de workflow en string
function getWorkflowTypeString() {
    switch(workflowType) {
        case 1: return 'scratch';
        case 2: return 'rewrite';
        case 3: return 'cluster';
        default: return 'scratch';
    }
}

// Helper pour obtenir le label du workflow
function getWorkflowTypeLabel() {
    switch(workflowType) {
        case 1: return 'Création from scratch';
        case 2: return 'Réécriture';
        case 3: return 'Cluster';
        default: return 'Création';
    }
}

async function startWorkflow(data) {
    try {
        console.log('=== DEBUG START WORKFLOW ===');
        console.log('1. Workflow Type:', workflowType);
        console.log('2. Data to send:', data);
        
        // Appel API pour démarrer le workflow
        const token = localStorage.getItem('auth_token');
        console.log('3. Token:', token ? 'Present' : 'Missing');
        
        const url = `/api/workflow${workflowType}`;
        console.log('4. Calling URL:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        
        console.log('5. Response status:', response.status);
        console.log('6. Response ok:', response.ok);
        
        const startResult = await response.json();
        console.log('7. Response JSON:', startResult);

        if (startResult.status === 'error') {
            throw new Error(startResult.error || startResult.message || 'Workflow failed');
        }

        // Get workflow ID
        workflowId = startResult.workflow_id;
        console.log('8. Workflow ID received:', workflowId);

        if (!workflowId) {
            throw new Error('No workflow ID returned');
        }

        console.log('9. Starting polling...');
        // Start polling for progress
        pollProgress();

    } catch (error) {
        console.error('=== WORKFLOW ERROR ===');
        console.error('Error details:', error);
        console.error('Error stack:', error.stack);
        
        updateStepStatus(currentStep || 1, 'error');
        Toast.show(`Erreur: ${error.message}`, 'error');

        setTimeout(() => {
            window.location.href = `option${workflowType}.php`;
        }, 3000);
    }
}

async function pollProgress() {
    if (!workflowId) return;

    pollingInterval = setInterval(async () => {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`/api/workflow-progress/${workflowId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const progress = await response.json();

            if (progress.status === 'not_found') {
                clearInterval(pollingInterval);
                throw new Error('Workflow not found');
            }

            // Update global progress
            updateGlobalProgress(progress.progress_percent || 0);

            // Update step statuses
            if (progress.step_details) {
                for (let i = 1; i <= 4; i++) {
                    const stepKey = `step_${i}`;
                    if (progress.step_details[stepKey]) {
                        const status = progress.step_details[stepKey].status;
                        updateStepStatus(i, status);

                        // For workflow 3, update satellite count info when step 1 completes
                        if (workflowType === 3 && i === 1 && status === 'completed') {
                            updateClusterInfo(progress);
                        }
                    }
                }
            }

            // Update current step indicator
            if (progress.current_step) {
                const totalSteps = workflowType === 2 ? 3 : 4;
                document.getElementById('stat-step').textContent = `${progress.current_step}/${totalSteps}`;
                currentStep = progress.current_step;
            }

            // Check if completed
            if (progress.status === 'completed') {
                clearInterval(pollingInterval);
                clearInterval(elapsedTimeInterval);

                // Update final stats
                if (progress.result && progress.result.article && progress.result.article.word_count) {
                    document.getElementById('stat-words').textContent = progress.result.article.word_count;
                }

                // Save results and redirect
                sessionStorage.setItem('workflowResults', JSON.stringify(progress.result));
                sessionStorage.setItem('workflowType', workflowType);

                Toast.show('Article généré avec succès !', 'success');

                setTimeout(() => {
                    window.location.href = 'result.php';
                }, 1500);
            }

            // Check if error
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

            setTimeout(() => {
                window.location.href = `option${workflowType}.php`;
            }, 3000);
        }
    }, 2000); // Poll every 2 seconds
}


function updateStepStatus(stepNumber, status) {
    const stepEl = document.getElementById(`step-${stepNumber}`);

    // Skip if step doesn't exist (e.g., step 4 in workflow 2)
    if (!stepEl) {
        console.log(`Step ${stepNumber} element not found - skipping`);
        return;
    }

    const iconEl = stepEl.querySelector('.step-icon');
    const statusEl = stepEl.querySelector('.step-status');

    // Reset classes
    stepEl.classList.remove('bg-gray-50', 'bg-blue-50', 'bg-green-50', 'border-gray-200', 'border-blue-400', 'border-green-400');
    iconEl.classList.remove('bg-gray-300', 'bg-blue-500', 'bg-green-500', 'text-gray-500', 'text-white');

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
    const progressBar = document.getElementById('global-progress-bar');
    const progressPercent = document.getElementById('global-progress-percent');

    progressBar.style.width = `${percent}%`;
    progressPercent.textContent = `${percent}%`;
}

function startElapsedTimeCounter() {
    elapsedTimeInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;

        const timeStr = minutes > 0
            ? `${minutes}m ${seconds}s`
            : `${seconds}s`;

        document.getElementById('stat-time').textContent = timeStr;

        // Update estimated time
        const remaining = Math.max(0, 180 - elapsed); // 3 min = 180s
        const remMinutes = Math.floor(remaining / 60);
        const remSeconds = remaining % 60;

        if (remaining > 0) {
            document.getElementById('estimated-time').textContent =
                `${remMinutes}m ${remSeconds}s restantes`;
        } else {
            document.getElementById('estimated-time').textContent = 'Bientôt terminé...';
        }
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

// Update cluster info for workflow 3
function updateClusterInfo(progress) {
    if (workflowType !== 3) return;

    // Extract satellite count from progress data
    let satelliteCount = 3; // Default value

    if (progress.step_details && progress.step_details.step_1 && progress.step_details.step_1.data) {
        const stepData = progress.step_details.step_1.data;

        // Try to get satellite count from various possible fields
        if (stepData.satellite_themes && Array.isArray(stepData.satellite_themes)) {
            satelliteCount = stepData.satellite_themes.length;
        } else if (stepData.satellites_count) {
            satelliteCount = stepData.satellites_count;
        } else if (stepData.themes && Array.isArray(stepData.themes)) {
            satelliteCount = stepData.themes.length;
        }
    }

    // Update subtitle
    const subtitleEl = document.getElementById('subtitle-text');
    if (subtitleEl) {
        subtitleEl.innerHTML = `1 article pilier + <strong class="text-purple-600">${satelliteCount} articles satellites</strong> détectés`;
        subtitleEl.classList.add('animate-pulse-once');
        setTimeout(() => subtitleEl.classList.remove('animate-pulse-once'), 1000);
    }

    // Update step 3 description to show satellite count
    const step3El = document.getElementById('step-3');
    if (step3El) {
        const descEl = step3El.querySelector('.text-sm.text-gray-600');
        if (descEl) {
            descEl.textContent = `Création de ${satelliteCount} articles satellites complémentaires...`;
        }
    }

    // Update step 4 description to show total images
    const step4El = document.getElementById('step-4');
    if (step4El) {
        const descEl = step4El.querySelector('.text-sm.text-gray-600');
        if (descEl) {
            const totalImages = satelliteCount + 1;
            descEl.textContent = `${totalImages} images IA (1 pilier + ${satelliteCount} satellites)...`;
        }
    }

    console.log(`Cluster info updated: ${satelliteCount} satellites detected`);
}

// Empêcher l'utilisateur de quitter la page accidentellement
window.addEventListener('beforeunload', function (e) {
    if (currentStep < 3) {
        e.preventDefault();
        e.returnValue = 'La génération est en cours. Êtes-vous sûr de vouloir quitter ?';
        return e.returnValue;
    }
});
</script>

<style>
#loading-tip, #subtitle-text {
    transition: opacity 0.3s ease-in-out;
}

.step-container {
    transition: all 0.3s ease-in-out;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

@keyframes pulse-once {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.animate-pulse-once {
    animation: pulse-once 0.6s ease-in-out;
}

.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
