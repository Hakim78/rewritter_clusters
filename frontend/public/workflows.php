<?php
/**
 * Page Historique des Workflows
 * Fichier: frontend/public/workflows.php
 */

$pageTitle = "Mes Workflows - SEO Platform";
require_once '../includes/header.php';
requireAuth();
?>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<!-- Stats Cards -->
<div id="stats-section" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Stats will be loaded here -->
</div>

<!-- Loading State -->
<div id="loading" class="text-center py-12">
    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
    <p class="mt-4 text-gray-600">Chargement des workflows...</p>
</div>

<!-- Error State -->
<div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 text-center">
    <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-3"></i>
    <p class="text-red-800 font-medium">Erreur de chargement</p>
    <p id="error-message" class="text-red-600 mt-2"></p>
    <button onclick="loadWorkflows()" class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
        Réessayer
    </button>
</div>

<!-- Workflows Grid -->
<div id="workflows-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Workflows will be loaded here -->
</div>

<!-- Empty State -->
<div id="empty-state" class="hidden text-center py-12 bg-white rounded-lg shadow-sm">
    <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
    <h3 class="text-xl font-medium text-gray-700 mb-2">Aucun workflow</h3>
    <p class="text-gray-500 mb-6">Vous n'avez pas encore créé de workflow</p>
    <a href="/option1.php" class="inline-block px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:opacity-90">
        <i class="fas fa-plus mr-2"></i> Créer un workflow
    </a>
</div>

<script>
// Configuration
const API_BASE_URL = '/api';
const token = localStorage.getItem('auth_token');

if (!token) {
    window.location.href = '/auth/login.php';
}

// Types de workflows
const workflowTypes = {
    'scratch': { label: 'From Scratch', icon: 'fa-file-alt', color: 'bg-blue-100 text-blue-800' },
    'rewrite': { label: 'Réécriture', icon: 'fa-sync-alt', color: 'bg-green-100 text-green-800' },
    'cluster': { label: 'Cluster', icon: 'fa-layer-group', color: 'bg-purple-100 text-purple-800' }
};

// Status
const statusConfig = {
    'completed': { label: 'Terminé', icon: 'fa-check-circle', color: 'bg-green-100 text-green-800' },
    'processing': { label: 'En cours', icon: 'fa-spinner fa-spin', color: 'bg-yellow-100 text-yellow-800' },
    'failed': { label: 'Échoué', icon: 'fa-times-circle', color: 'bg-red-100 text-red-800' },
    'pending': { label: 'En attente', icon: 'fa-clock', color: 'bg-gray-100 text-gray-800' }
};

// Variables globales pour les fonctions de modal
let currentHtmlContent = '';
let currentMetadata = null;

// Charger les workflows
async function loadWorkflows() {
    try {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('error').classList.add('hidden');

        const response = await fetch(`${API_BASE_URL}/workflows?limit=50`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Erreur de chargement');
        }

        displayWorkflows(data.workflows);
        displayStats(data.workflows);

    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('error-message').textContent = error.message;
    }
}

// Afficher les stats
function displayStats(workflows) {
    const total = workflows.length;
    const completed = workflows.filter(w => w.status === 'completed').length;
    const avgTime = workflows.reduce((sum, w) => sum + (w.generation_time_seconds || 0), 0) / total || 0;
    const totalArticles = workflows.reduce((sum, w) => sum + (w.articles_count || 0), 0);

    const statsHTML = `
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Workflows</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">${total}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-project-diagram text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Terminés</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">${completed}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Temps Moyen</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">${Math.round(avgTime)}s</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-clock text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Articles Créés</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">${totalArticles}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-file-alt text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    `;

    document.getElementById('stats-section').innerHTML = statsHTML;
}

// Afficher les workflows
function displayWorkflows(workflows) {
    document.getElementById('loading').classList.add('hidden');

    if (workflows.length === 0) {
        document.getElementById('empty-state').classList.remove('hidden');
        return;
    }

    const grid = document.getElementById('workflows-grid');
    grid.classList.remove('hidden');

    grid.innerHTML = workflows.map(workflow => {
        const type = workflowTypes[workflow.workflow_type] || workflowTypes.scratch;
        const status = statusConfig[workflow.status] || statusConfig.pending;
        const date = new Date(workflow.created_at).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        return `
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${type.color}">
                            <i class="fas ${type.icon} mr-1"></i> ${type.label}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${status.color}">
                            <i class="fas ${status.icon} mr-1"></i> ${status.label}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">
                        ${workflow.title || 'Sans titre'}
                    </h3>
                    <p class="text-sm text-gray-600 mb-4 flex items-center">
                        <i class="fas fa-key mr-2 text-purple-500"></i>
                        <span class="line-clamp-1">${workflow.keyword || 'N/A'}</span>
                    </p>
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock mr-2 text-purple-400"></i>
                            <span class="font-medium">${workflow.generation_time_seconds || 0}s</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-file mr-2 text-purple-400"></i>
                            <span class="font-medium">${workflow.articles_count || 0} article(s)</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-calendar mr-1"></i> ${date}
                    </p>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 flex gap-2">
                    <button onclick="viewWorkflow('${workflow.workflow_id}')" id="btn-${workflow.workflow_id}"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:opacity-90 transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-eye mr-1"></i> Voir
                    </button>
                    <button onclick="downloadWorkflow('${workflow.workflow_id}')"
                            class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all border border-gray-200 shadow-sm">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Voir un workflow
async function viewWorkflow(workflowId) {
    const btn = document.getElementById(`btn-${workflowId}`);
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="border:2px solid white;border-top:2px solid transparent;border-radius:50%;width:14px;height:14px;animation:spin 0.6s linear infinite;display:inline-block;"></span> Chargement...</span>';
    btn.disabled = true;

    try {
        const filesResponse = await fetch(`${API_BASE_URL}/workflows/${workflowId}/files`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const filesData = await filesResponse.json();

        if (!filesData.success) {
            throw new Error(filesData.error || 'Erreur de chargement');
        }

        if (filesData.workflow_type === 'cluster' && filesData.files.length > 1) {
            showClusterModal(workflowId, filesData.files);
        } else {
            const mainFile = filesData.files.find(f => f.type === 'main' || f.type === 'pillar') || filesData.files[0];
            await openArticle(workflowId, mainFile.filename);
        }
    } catch (error) {
        alert('Erreur: ' + error.message);
    } finally {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }
}

// Ouvrir un article
async function openArticle(workflowId, filename) {
    try {
        const htmlResponse = await fetch(`${API_BASE_URL}/workflows/${workflowId}/download?filename=${filename}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const htmlData = await htmlResponse.json();

        if (!htmlData.success) {
            throw new Error(htmlData.error || 'Erreur de téléchargement');
        }

        let metadata = null;
        const metadataFilename = filename.includes('satellite')
            ? filename.replace('.html.gz', '_metadata.json.gz')
            : (filename.includes('pillar') ? 'pillar_metadata.json.gz' : 'metadata.json.gz');

        try {
            const metaResponse = await fetch(`${API_BASE_URL}/workflows/${workflowId}/download?filename=${metadataFilename}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const metaData = await metaResponse.json();
            if (metaData.success) {
                metadata = JSON.parse(metaData.content);
            }
        } catch (e) {
            console.log('Pas de métadonnées disponibles');
        }

        showArticleModal(htmlData.content, metadata);

    } catch (error) {
        alert('Erreur: ' + error.message);
    }
}

// Fonctions pour copier et exporter (définies globalement)
function copyArticleContent() {
    navigator.clipboard.writeText(currentHtmlContent).then(() => {
        const btn = document.getElementById('copy-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '✅ Copié !';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '#10b981';
        }, 2000);
    }).catch(err => {
        alert('Erreur lors de la copie: ' + err);
    });
}

function exportArticleHTML() {
    const blob = new Blob([currentHtmlContent], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = (currentMetadata?.seo_title?.replace(/[^a-z0-9]/gi, '_') || 'article') + '.html';
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function expandArticleContent() {
    document.getElementById('article-preview').innerHTML = currentHtmlContent;
    document.getElementById('expand-btn').style.display = 'none';
}

function openArticleNewWindow() {
    const w = window.open('', '_blank');
    w.document.write(`<!DOCTYPE html><html><head><meta charset='UTF-8'><style>body{max-width:800px;margin:40px auto;padding:20px;font-family:system-ui,-apple-system,sans-serif;line-height:1.8;color:#1e293b;}h2,h3{color:#1e40af;margin-top:2em;}ul,ol{margin:1em 0;}p{margin:1em 0;}</style></head><body>${currentHtmlContent}</body></html>`);
    w.document.close();
}

// Modal Article
function showArticleModal(htmlContent, metadata) {
    currentHtmlContent = htmlContent;
    currentMetadata = metadata;

    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.85);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;';

    const modalContent = document.createElement('div');
    modalContent.style.cssText = 'max-width:900px;width:100%;max-height:85vh;background:white;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;display:flex;flex-direction:column;';

    let html = `<div style="padding:25px 30px;border-bottom:2px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;background:#f8f9fa;border-radius:16px 16px 0 0;">
        <div>
            ${metadata?.seo_title ? `<h2 style="margin:0;color:#1e293b;font-size:24px;font-weight:700;">${metadata.seo_title}</h2>` : '<h2 style="margin:0;color:#1e293b;">Article généré</h2>'}
            <div style="display:flex;gap:15px;margin-top:10px;font-size:14px;color:#64748b;">
                ${metadata?.word_count ? `<span>📝 ${metadata.word_count} mots</span>` : ''}
                <span>📅 ${new Date().toLocaleDateString('fr-FR')}</span>
            </div>
        </div>
        <button onclick="this.closest('.modal-overlay').remove()" style="background:#ef4444;color:white;border:none;border-radius:50%;width:40px;height:40px;font-size:24px;cursor:pointer;line-height:1;transition:all 0.2s;flex-shrink:0;">×</button>
    </div>`;

    html += '<div style="overflow-y:auto;padding:25px 30px;flex:1;">';

    if (metadata) {
        if (metadata.meta_description) {
            html += `<details style="margin-bottom:20px;border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:white;" open>
                <summary style="cursor:pointer;font-weight:600;color:#3b82f6;font-size:16px;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">🎯</span> Meta Description
                </summary>
                <div style="margin-top:15px;padding:15px;background:#eff6ff;border-left:4px solid #3b82f6;border-radius:6px;">
                    <p style="margin:0;color:#1e293b;line-height:1.6;">${metadata.meta_description}</p>
                </div>
            </details>`;
        }

        if (metadata.wordpress_excerpt) {
            html += `<details style="margin-bottom:20px;border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:white;">
                <summary style="cursor:pointer;font-weight:600;color:#8b5cf6;font-size:16px;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">📝</span> Extrait WordPress
                </summary>
                <div style="margin-top:15px;padding:15px;background:#f5f3ff;border-left:4px solid #8b5cf6;border-radius:6px;">
                    <p style="margin:0;color:#1e293b;line-height:1.6;">${metadata.wordpress_excerpt}</p>
                </div>
            </details>`;
        }

        if (metadata.image_url) {
            html += `<details style="margin-bottom:20px;border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:white;">
                <summary style="cursor:pointer;font-weight:600;color:#10b981;font-size:16px;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">🖼️</span> Image générée
                </summary>
                <div style="margin-top:15px;text-align:center;">
                    <img src="${metadata.image_url}" alt="Image" style="max-width:100%;height:auto;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                </div>
            </details>`;
        }

        if (metadata.secondary_keywords && metadata.secondary_keywords.length > 0) {
            html += `<details style="margin-bottom:20px;border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:white;">
                <summary style="cursor:pointer;font-weight:600;color:#f59e0b;font-size:16px;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">🔑</span> Mots-clés secondaires (${metadata.secondary_keywords.length})
                </summary>
                <div style="margin-top:15px;display:flex;flex-wrap:wrap;gap:8px;">
                    ${metadata.secondary_keywords.map(kw => `<span style="background:#fef3c7;color:#92400e;padding:6px 12px;border-radius:20px;font-size:14px;font-weight:500;">${kw}</span>`).join('')}
                </div>
            </details>`;
        }

        if (metadata.faq_json && metadata.faq_json.length > 0) {
            html += `<details style="margin-bottom:20px;border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:white;">
                <summary style="cursor:pointer;font-weight:600;color:#ec4899;font-size:16px;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">❓</span> FAQ (${metadata.faq_json.length} questions)
                </summary>
                <div style="margin-top:15px;">`;
            metadata.faq_json.forEach((faq, i) => {
                html += `<div style="margin-bottom:15px;padding:18px;background:#fdf2f8;border-radius:8px;border-left:4px solid #ec4899;">
                    <strong style="color:#1e293b;display:block;margin-bottom:8px;font-size:15px;">Q${i+1}: ${faq.question}</strong>
                    <p style="margin:0;color:#475569;line-height:1.6;font-size:14px;">${faq.answer}</p>
                </div>`;
            });
            html += `</div></details>`;
        }
    }

    const previewLength = 500;
    const isLongContent = htmlContent.length > previewLength;
    const previewContent = isLongContent ? htmlContent.substring(0, previewLength) + '...' : htmlContent;

    html += `<details style="margin-bottom:20px;border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:white;" ${!metadata ? 'open' : ''}>
        <summary style="cursor:pointer;font-weight:600;color:#06b6d4;font-size:16px;display:flex;align-items:center;gap:8px;">
            <span style="font-size:20px;">📄</span> Contenu de l'article
        </summary>
        <div style="margin-top:15px;">
            <div style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;">
                <button onclick="copyArticleContent()" id="copy-btn"
                    style="background:#10b981;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.2s;">
                    📋 Copier HTML
                </button>
                <button onclick="exportArticleHTML()"
                    style="background:#8b5cf6;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.2s;">
                    💾 Exporter HTML
                </button>
                <button onclick="openArticleNewWindow()"
                    style="background:#6366f1;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.2s;">
                    🚀 Nouvelle fenêtre
                </button>
            </div>
            <div id="article-preview" style="padding:25px;background:#ffffff;border-radius:8px;line-height:1.8;color:#1e293b;border:2px solid #e0f2fe;max-height:600px;overflow-y:auto;">
                <style>
                    #article-preview h2{color:#0369a1;margin-top:1.5em;margin-bottom:0.5em;font-size:1.5em;}
                    #article-preview h3{color:#0c4a6e;margin-top:1.2em;margin-bottom:0.4em;font-size:1.2em;}
                    #article-preview p{margin:1em 0;text-align:justify;}
                    #article-preview ul,#article-preview ol{margin:1em 0;padding-left:2em;}
                    #article-preview li{margin:0.5em 0;}
                    #article-preview strong{color:#1e293b;font-weight:600;}
                    #article-preview a{color:#2563eb;text-decoration:underline;}
                </style>
                ${isLongContent ? previewContent : htmlContent}
            </div>
            ${isLongContent ? `<button onclick="expandArticleContent()" id="expand-btn" style="margin-top:15px;background:#06b6d4;color:white;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:600;">📖 Voir tout</button>` : ''}
        </div>
    </details>`;

    html += '</div>';
    modalContent.innerHTML = html;
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
}

// Modal Cluster
function showClusterModal(workflowId, files) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';

    const pillar = files.find(f => f.type === 'pillar');
    const satellites = files.filter(f => f.type === 'satellite').sort((a, b) => a.filename.localeCompare(b.filename));

    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-sitemap mr-3"></i> Cluster d'Articles
                    </h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-white hover:text-gray-200 text-2xl">×</button>
                </div>
                <p class="text-purple-100 mt-2">1 pilier + ${satellites.length} satellites</p>
            </div>
            <div class="p-6 space-y-6">
                ${pillar ? `
                <div class="border-2 border-purple-200 rounded-lg p-4 bg-purple-50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="bg-purple-600 text-white rounded-full w-10 h-10 flex items-center justify-center mr-3">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">${pillar.label}</h4>
                                <p class="text-xs text-gray-600">${(pillar.size / 1024).toFixed(1)} KB</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openArticle('${workflowId}', '${pillar.filename}')" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-eye mr-2"></i>Voir
                        </button>
                        <button onclick="downloadWorkflow('${workflowId}', '${pillar.filename}')" class="px-4 py-2 bg-white text-purple-600 border-2 border-purple-600 rounded-lg hover:bg-purple-50">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <button onclick="downloadAllCluster('${workflowId}')"
                                class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:opacity-90 font-medium">
                            <i class="fas fa-download mr-2"></i>Télécharger tout le cluster (ZIP)
                        </button>
                    </div>

                </div>
                ` : ''}
                <div>
                    <h4 class="font-bold text-gray-700 mb-3"><i class="fas fa-satellite-dish text-indigo-600 mr-2"></i>Articles Satellites</h4>
                    <div class="space-y-3">
                        ${satellites.map(sat => `
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center flex-1">
                                    <div class="bg-indigo-100 text-indigo-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">
                                        ${sat.filename.match(/\d+/)[0]}
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">${sat.label}</h5>
                                        <p class="text-xs text-gray-500">${(sat.size / 1024).toFixed(1)} KB</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openArticle('${workflowId}', '${sat.filename}')" class="flex-1 px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-eye mr-1"></i>Voir
                                </button>
                                <button onclick="downloadWorkflow('${workflowId}', '${sat.filename}')" class="px-3 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
}
// Télécharger tout le cluster (TODO: implémenter côté backend)
async function downloadAllCluster(workflowId) {
    alert('Fonctionnalité à venir : Téléchargement ZIP du cluster complet');
}
// Télécharger un workflow
async function downloadWorkflow(workflowId, filename = 'article_main.html.gz') {
    try {
        const response = await fetch(`${API_BASE_URL}/workflows/${workflowId}/download?filename=${filename}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Erreur de téléchargement');
        }

        const blob = new Blob([data.content], { type: 'text/html' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${workflowId}_${filename.replace('.gz', '')}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

    } catch (error) {
        alert('Erreur: ' + error.message);
    }
}

// Charger au démarrage
loadWorkflows();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
