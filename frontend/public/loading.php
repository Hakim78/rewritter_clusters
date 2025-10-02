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
                <i class="fas fa-robot text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                Génération en cours...
            </h1>
            <p class="text-gray-600 text-lg">
                Notre IA rédige votre article SEO optimisé
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
            <!-- Étape 1: Scraping -->
            <div id="step-1" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas fa-globe text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">Étape 1 : Analyse du site</h3>
                        <p class="text-sm text-gray-600">Extraction du contenu et des liens...</p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Étape 2: Analyse -->
            <div id="step-2" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas fa-brain text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">Étape 2 : Analyse stratégique</h3>
                        <p class="text-sm text-gray-600">IA analyse le contenu et identifie les opportunités...</p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Étape 3: Génération -->
            <div id="step-3" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas fa-pen-fancy text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">Étape 3 : Rédaction de l'article</h3>
                        <p class="text-sm text-gray-600">Génération du contenu optimisé SEO, LLMO et RAG...</p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>
            <!-- Étape 4: Image -->
            <div id="step-4" class="step-container bg-gray-50 rounded-lg p-4 border-2 border-gray-200 transition-all duration-300">
                <div class="flex items-center">
                    <div class="step-icon bg-gray-300 text-gray-500 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                        <i class="fas fa-image text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-700 mb-1">Étape 4 : Génération de l'image</h3>
                        <p class="text-sm text-gray-600">Création de l'image avec Ideogram AI...</p>
                    </div>
                    <div class="step-status">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- Temps estimé -->
        <div class="mt-8 text-center">
            <div class="inline-flex items-center bg-blue-50 px-6 py-3 rounded-lg">
                <i class="fas fa-hourglass-half text-blue-600 mr-3"></i>
                <div class="text-left">
                    <div class="text-xs text-gray-600">Temps estimé</div>
                    <div id="estimated-time" class="text-lg font-bold text-blue-600">3-5 minutes</div>
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
            <div class="text-3xl font-bold text-purple-500 mb-2" id="stat-step">1/4</div>
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

// Démarrage
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les données du formulaire depuis sessionStorage
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

async function startWorkflow(data) {
    try {
        // Appel API pour démarrer le workflow
       const token = localStorage.getItem('auth_token');
        const response = await fetch(`http://localhost:5001/api/workflow${workflowType}`, {
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

        // Get workflow ID
        workflowId = startResult.workflow_id;

        if (!workflowId) {
            throw new Error('No workflow ID returned');
        }

        // Start polling for progress
        pollProgress();

    } catch (error) {
        console.error('Workflow error:', error);
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
            const response = await fetch(`http://localhost:5001/api/workflow-progress/${workflowId}`, {
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
                    }
                }
            }

            // Update current step indicator
            if (progress.current_step) {
                document.getElementById('stat-step').textContent = `${progress.current_step}/4`;
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
#loading-tip {
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