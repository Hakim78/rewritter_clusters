/**
 * JavaScript Principal - SEO Article Generator
 * Fichier: frontend/public/assets/js/app.js
 */

// Configuration globale
const CONFIG = {
    API_URL: 'http://localhost:5001',
    TIMEOUT: 300000 // 5 minutes
};

// Vérifier la validité du token au chargement
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('auth_token');
    
    if (token) {
        try {
            // Vérifier si le token est encore valide
            const response = await fetch(`${CONFIG.API_URL}/api/auth/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ token })
            });
            
            const result = await response.json();
            
            if (!result.valid) {
                // Token invalide, nettoyer
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                
                // Rediriger vers login si pas déjà sur la page
                if (!window.location.pathname.includes('/auth/login.php')) {
                    window.location.href = '/auth/login.php?error=expired';
                }
            }
        } catch (error) {
            console.error('Token verification failed:', error);
        }
    }
});

// Classe utilitaire pour les notifications
class Toast {
    static show(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Déterminer l'icône selon le type
        let icon;
        if (type === 'success') {
            icon = 'check-circle';
        } else if (type === 'error') {
            icon = 'exclamation-circle';
        } else if (type === 'info') {
            icon = 'info-circle';
        } else {
            icon = 'bell';
        }
        
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-${icon} mr-3 text-xl"></i>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }
}

// Classe pour gérer les appels API
class APIClient {
    static async call(endpoint, data = null, method = null) {
        try {
            // Déterminer la méthode HTTP
            const httpMethod = method || (data ? 'POST' : 'GET');
            
            const options = {
                method: httpMethod,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };
            
            // Ajouter le token JWT automatiquement si disponible
            const token = localStorage.getItem('auth_token');
            if (token) {
                options.headers['Authorization'] = `Bearer ${token}`;
            }
            
            if (data && (httpMethod === 'POST' || httpMethod === 'PUT')) {
                options.body = JSON.stringify(data);
            }
            
            const response = await fetch(`${CONFIG.API_URL}${endpoint}`, options);
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'Erreur API');
            }
            
            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
}

// Classe pour gérer les workflows
class WorkflowManager {
    constructor(workflowType) {
        this.workflowType = workflowType;
        this.form = document.getElementById(`workflow${workflowType}-form`);
        this.resultsDiv = document.getElementById('results');
        this.loaderDiv = document.getElementById('loader');
        
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }
    
    async handleSubmit(event) {
        event.preventDefault();

        // Vérifier que l'utilisateur est connecté
        const token = localStorage.getItem('auth_token');
        if (!token) {
            Toast.show('Vous devez être connecté pour utiliser cette fonctionnalité', 'error');
            setTimeout(() => {
                window.location.href = '/auth/login.php';
            }, 2000);
            return;
        }

        // Récupérer les données du formulaire
        const formData = new FormData(this.form);
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
        delete data.custom_domain;

        // Validation basique
        if (!this.validateForm(data)) {
            return;
        }

        // Store form data in sessionStorage
        sessionStorage.setItem('workflowFormData', JSON.stringify(data));

        // Redirect to loading page
        Toast.show('Redirection vers la génération...', 'info');
        setTimeout(() => {
            window.location.href = `loading.php?workflow=${this.workflowType}`;
        }, 500);
    }
    
    validateForm(data) {
        // Validation spécifique selon le workflow
        if (this.workflowType === 1) {
            if (!data.site_url || !data.domain || !data.keyword) {
                Toast.show('Veuillez remplir tous les champs obligatoires', 'error');
                return false;
            }
        } else if (this.workflowType === 2 || this.workflowType === 3) {
            if (!data.article_url) {
                Toast.show('Veuillez fournir l\'URL de l\'article', 'error');
                return false;
            }
        }
        return true;
    }
    
    showLoader() {
        if (this.loaderDiv) {
            this.loaderDiv.classList.remove('hidden');
        }
    }
    
    hideLoader() {
        if (this.loaderDiv) {
            this.loaderDiv.classList.add('hidden');
        }
    }
    
    hideResults() {
        if (this.resultsDiv) {
            this.resultsDiv.classList.add('hidden');
        }
    }
    
    displayResults(result) {
        if (!this.resultsDiv) return;
        
        this.resultsDiv.classList.remove('hidden');
        this.resultsDiv.classList.add('fade-in');
        
        if (this.workflowType === 3) {
            // Cluster : afficher plusieurs articles
            this.displayClusterResults(result);
        } else {
            // Single article
            this.displaySingleArticle(result.article);
        }
    }
    
    displaySingleArticle(article) {
        const seoTitle = article.seo_title || article.title || 'Article généré';
        const metaDesc = article.meta_description || '';
        const wordCount = article.word_count || 'N/A';
        const readabilityScore = article.readability_score || 'N/A';

        const html = `
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="flex items-center justify-between mb-6 border-b pb-4">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        Article généré
                    </h2>
                    <div class="space-x-2">
                        <button onclick="copyHTML('article-content-1')" class="btn-secondary">
                            <i class="fas fa-copy mr-2"></i>Copier le HTML
                        </button>
                        <button onclick="downloadHTML('article-content-1', '${seoTitle.replace(/'/g, "\\'")}')" class="btn-secondary">
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
                
                <!-- Image générée -->
                ${article.image_url ? `
                    <div class="mb-6">
                        <h3 class="text-xl font-bold mb-3">Image générée</h3>
                        <img src="${article.image_url}" alt="${article.title}" class="rounded-lg shadow-md max-w-full">
                        <button onclick="downloadImage('${article.image_url}', 'article-image')" class="mt-3 btn-secondary">
                            <i class="fas fa-download mr-2"></i>Télécharger l'image
                        </button>
                    </div>
                ` : ''}
                
                <!-- Contenu HTML -->
                <div class="mb-6">
                    <h3 class="text-xl font-bold mb-3 flex items-center justify-between">
                        <span>Aperçu de l'article</span>
                        <button onclick="toggleView('article-content-1', 'code-view-1')" class="text-sm btn-secondary">
                            <i class="fas fa-code mr-2"></i>Voir le code
                        </button>
                    </h3>

                    <!-- Vue rendue -->
                    <div id="article-content-1" class="article-preview border border-gray-200 p-6 rounded-lg">
                        ${article.html_content}
                    </div>

                    <!-- Vue code -->
                    <div id="code-view-1" class="hidden">
                        <pre class="code-block"><code>${this.escapeHtml(article.html_content)}</code></pre>
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

                <!-- Keywords & Entities -->
                ${article.keywords && (article.keywords.secondary.length > 0 || article.keywords.entities.length > 0) ? `
                    <div class="mb-6 bg-yellow-50 p-6 rounded-lg">
                        <h3 class="text-xl font-bold mb-3">
                            <i class="fas fa-tags text-yellow-600 mr-2"></i>
                            Mots-clés & Entités
                        </h3>
                        ${article.keywords.secondary.length > 0 ? `
                            <div class="mb-3">
                                <h4 class="font-semibold text-gray-700 mb-2">Mots-clés secondaires :</h4>
                                <div class="flex flex-wrap gap-2">
                                    ${article.keywords.secondary.map(kw => `<span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm">${kw}</span>`).join('')}
                                </div>
                            </div>
                        ` : ''}
                        ${article.keywords.entities.length > 0 ? `
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
        
        this.resultsDiv.innerHTML = html;
    }
    
    displayClusterResults(result) {
        let articlesHTML = '';
        
        result.articles.forEach((article, index) => {
            const articleId = `article-content-${index + 1}`;
            const codeId = `code-view-${index + 1}`;
            const type = article.type === 'main' ? 'Principal' : 'Satellite';
            const icon = article.type === 'main' ? 'fa-star' : 'fa-link';
            
            articlesHTML += `
                <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
                    <div class="flex items-center justify-between mb-6 border-b pb-4">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas ${icon} text-purple-500 mr-3"></i>
                            Article ${type}: ${article.title}
                        </h2>
                        <div class="space-x-2">
                            <button onclick="copyHTML('${articleId}')" class="btn-secondary">
                                <i class="fas fa-copy mr-2"></i>Copier
                            </button>
                        </div>
                    </div>
                    
                    ${article.image_url ? `
                        <img src="${article.image_url}" alt="${article.title}" class="rounded-lg shadow-md max-w-full mb-6">
                    ` : ''}
                    
                    <div class="mb-6">
                        <button onclick="toggleView('${articleId}', '${codeId}')" class="btn-secondary mb-3">
                            <i class="fas fa-code mr-2"></i>Voir le code
                        </button>
                        
                        <div id="${articleId}" class="article-preview border border-gray-200 p-6 rounded-lg">
                            ${article.html_content}
                        </div>
                        
                        <div id="${codeId}" class="hidden">
                            <pre class="code-block"><code>${this.escapeHtml(article.html_content)}</code></pre>
                        </div>
                    </div>
                </div>
            `;
        });
        
        // Afficher les liens internes
        let linksHTML = '';
        if (result.internal_links && result.internal_links.length > 0) {
            linksHTML = `
                <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
                    <h3 class="text-2xl font-bold mb-4">
                        <i class="fas fa-link text-blue-500 mr-3"></i>
                        Maillage interne suggéré
                    </h3>
                    <ul class="space-y-2">
                        ${result.internal_links.map(link => `<li class="text-gray-700"><i class="fas fa-arrow-right text-purple-500 mr-2"></i>${link}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        this.resultsDiv.innerHTML = articlesHTML + linksHTML;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Fonctions utilitaires globales
function copyHTML(elementId) {
    const element = document.getElementById(elementId);
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

function downloadImage(imageUrl, filename) {
    fetch(imageUrl)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${filename}.jpg`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            Toast.show('Image téléchargée !', 'success');
        })
        .catch(err => {
            Toast.show('Erreur lors du téléchargement', 'error');
            console.error('Download error:', err);
        });
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

// Menu mobile toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});