<?php
/**
 * Modification d'un utilisateur (Admin)
 * Fichier: frontend/public/admin/edit_user.php
 */

$pageTitle = "Modifier un utilisateur - Admin";
require_once '../../includes/header.php';
requireAdmin(); // Protection admin

// Récupérer l'ID utilisateur
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$userId) {
    header('Location: users.php');
    exit;
}
?>

<div class="max-w-3xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user-edit text-purple-600 mr-3"></i>
                    Modifier un utilisateur
                </h1>
                <p class="text-gray-600">Mettre à jour les informations de l'utilisateur</p>
            </div>
            <a href="users.php" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Loader -->
    <div id="page-loader" class="bg-white rounded-lg shadow-xl p-12 text-center">
        <div class="loader mx-auto mb-4"></div>
        <p class="text-gray-600">Chargement des données...</p>
    </div>

    <!-- Formulaire (caché par défaut) -->
    <div id="edit-form-container" class="hidden bg-white rounded-lg shadow-xl p-8">
        <form id="edit-user-form" class="space-y-6">
            <input type="hidden" id="user_id" value="<?php echo $userId; ?>">
            
            <!-- Nom complet -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-user text-purple-600 mr-2"></i>
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-purple-600 mr-2"></i>
                    Email <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
            </div>

            <!-- Nouveau mot de passe (optionnel) -->
            <div class="bg-yellow-50 p-6 rounded-lg">
                <h3 class="font-bold text-gray-900 mb-3">
                    <i class="fas fa-key text-yellow-600 mr-2"></i>
                    Modifier le mot de passe (optionnel)
                </h3>
                <p class="text-sm text-gray-600 mb-4">Laissez vide pour conserver le mot de passe actuel</p>
                
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                            Nouveau mot de passe
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            minlength="8"
                            placeholder="Minimum 8 caractères"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                        />
                    </div>
                    
                    <div>
                        <label for="password_confirm" class="block text-sm font-bold text-gray-700 mb-2">
                            Confirmer le nouveau mot de passe
                        </label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            minlength="8"
                            placeholder="Répéter le mot de passe"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                        />
                    </div>
                </div>
            </div>

            <!-- Rôle -->
            <div>
                <label for="role" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-shield-alt text-purple-600 mr-2"></i>
                    Rôle <span class="text-red-500">*</span>
                </label>
                <select 
                    id="role" 
                    name="role" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                >
                    <option value="user">Utilisateur standard</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <!-- Statut -->
            <div>
                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-toggle-on text-purple-600 mr-2"></i>
                    Statut <span class="text-red-500">*</span>
                </label>
                <select 
                    id="status" 
                    name="status" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                >
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="pending">En attente</option>
                </select>
            </div>

            <!-- Messages d'erreur -->
            <div id="error-message" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <p class="text-red-700" id="error-text"></p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="users.php" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Annuler
                </a>
                <button type="submit" id="submit-btn" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const API_URL = '<?php echo getenv('PYTHON_API_URL') ?: 'http://localhost:5001'; ?>';
const token = localStorage.getItem('auth_token');
const userId = <?php echo $userId; ?>;

// Charger les données utilisateur
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch(`${API_URL}/api/admin/users/${userId}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.user) {
            const user = result.user;
            
            // Remplir le formulaire
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('status').value = user.status;
            
            // Afficher le formulaire
            document.getElementById('page-loader').classList.add('hidden');
            document.getElementById('edit-form-container').classList.remove('hidden');
        } else {
            throw new Error(result.error || 'Utilisateur non trouvé');
        }
        
    } catch (error) {
        console.error('Load error:', error);
        Toast.show('Erreur de chargement', 'error');
        setTimeout(() => {
            window.location.href = 'users.php';
        }, 2000);
    }
});

// Soumission du formulaire
document.getElementById('edit-user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    
    // Récupérer les données
    const formData = new FormData(e.target);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        role: formData.get('role'),
        status: formData.get('status')
    };
    
    // Ajouter le mot de passe si renseigné
    const password = formData.get('password');
    const passwordConfirm = formData.get('password_confirm');
    
    if (password) {
        if (password !== passwordConfirm) {
            errorText.textContent = 'Les mots de passe ne correspondent pas';
            errorMessage.classList.remove('hidden');
            return;
        }
        data.password = password;
    }
    
    // Désactiver le bouton
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
    errorMessage.classList.add('hidden');
    
    try {
        const response = await fetch(`${API_URL}/api/admin/users/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Toast.show('Utilisateur modifié avec succès !', 'success');
            setTimeout(() => {
                window.location.href = 'users.php';
            }, 1500);
        } else {
            throw new Error(result.error || 'Erreur de modification');
        }
        
    } catch (error) {
        console.error('Update error:', error);
        errorText.textContent = error.message;
        errorMessage.classList.remove('hidden');
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Enregistrer les modifications';
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>