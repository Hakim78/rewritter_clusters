<?php
/**
 * Page de connexion
 * Fichier: frontend/public/auth/login.php
 */

session_start();

// Si déjà connecté, rediriger vers l'accueil
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = "Connexion - SEO Article Generator";
require_once '../../includes/header.php';
?>

<div class="max-w-md mx-auto mt-20">
    <div class="bg-white rounded-lg shadow-2xl p-8">
        <!-- Logo/Titre -->
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Connexion</h1>
            <p class="text-gray-600 mt-2">Accédez à votre espace</p>
        </div>

        <!-- Messages d'erreur -->
        <div id="error-message" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                <p class="text-red-700" id="error-text"></p>
            </div>
        </div>

        <!-- Formulaire de connexion -->
        <form id="login-form" class="space-y-6">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-purple-600 mr-2"></i>
                    Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    autocomplete="email"
                    placeholder="votre@email.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition"
                />
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-purple-600 mr-2"></i>
                    Mot de passe
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition"
                />
            </div>

            <!-- Bouton de soumission -->
            <button
                type="submit"
                id="submit-btn"
                class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Se connecter
            </button>
        </form>

        <!-- Loader -->
        <div id="loader" class="hidden text-center mt-6">
            <div class="loader mx-auto"></div>
            <p class="text-gray-600 mt-3">Connexion en cours...</p>
        </div>
    </div>

    <!-- Info box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-bold mb-1">Accès réservé</p>
                <p>Seuls les utilisateurs autorisés peuvent se connecter. Contactez votre administrateur pour obtenir un accès.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // class toast notifactions
    class Toast {
    static show(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        }
    }
document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('submit-btn');
    const loader = document.getElementById('loader');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    
    // Récupérer les données
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Désactiver le formulaire
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion...';
    errorMessage.classList.add('hidden');
    
    try {
        // Appel API de connexion
        const response = await fetch('<?php echo getenv('PYTHON_API_URL') ?: 'http://localhost:5001'; ?>/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        const result = await response.json();
        
        if (result.success && result.token) {
            // Stocker le token et les infos utilisateur
            localStorage.setItem('auth_token', result.token);
            localStorage.setItem('user', JSON.stringify(result.user));
            
            // Stocker en session PHP via AJAX
            const sessionResponse = await fetch('set_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: result.token,
                    user: result.user
                })
            });
            
            // Afficher succès
            Toast.show('Connexion réussie ! Redirection...', 'success');
            
            // Rediriger selon le rôle
            setTimeout(() => {
                if (result.user.role === 'admin') {
                    window.location.href = '/admin/users.php';
                } else {
                    window.location.href = '/index.php';
                }
            }, 1000);
            
        } else {
            throw new Error(result.error || 'Erreur de connexion');
        }
        
    } catch (error) {
        console.error('Login error:', error);
        
        // Afficher l'erreur
        errorText.textContent = error.message || 'Email ou mot de passe incorrect';
        errorMessage.classList.remove('hidden');
        
        // Réactiver le formulaire
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Se connecter';
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>