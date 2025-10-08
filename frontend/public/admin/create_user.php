<?php
/**
 * Création d'un nouvel utilisateur (Admin)
 * Fichier: frontend/public/admin/create_user.php
 */

$pageTitle = "Créer un utilisateur - Admin";
require_once '../../includes/header.php';
requireAdmin(); // Protection admin
?>

<div class="max-w-3xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user-plus text-purple-600 mr-3"></i>
                    Créer un utilisateur
                </h1>
                <p class="text-gray-600">Ajouter un nouvel accès à la plateforme</p>
            </div>
            <a href="users.php" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-xl p-8">
        <form id="create-user-form" class="space-y-6">
            
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
                    placeholder="Jean Dupont"
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
                    placeholder="utilisateur@example.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">L'utilisateur recevra ses identifiants par email</p>
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-purple-600 mr-2"></i>
                    Mot de passe <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    minlength="8"
                    placeholder="Minimum 8 caractères"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
                <p class="text-sm text-gray-500 mt-1">Minimum 8 caractères recommandés</p>
            </div>

            <!-- Confirmation mot de passe -->
            <div>
                <label for="password_confirm" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-purple-600 mr-2"></i>
                    Confirmer le mot de passe <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    required
                    minlength="8"
                    placeholder="Répéter le mot de passe"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                />
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
                <p class="text-sm text-gray-500 mt-1">
                    <strong>Utilisateur :</strong> Accès aux workflows uniquement<br>
                    <strong>Admin :</strong> Accès complet + gestion des utilisateurs
                </p>
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
                    <i class="fas fa-save mr-2"></i>Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const API_URL = '';
const token = localStorage.getItem('auth_token');

document.getElementById('create-user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    
    // Récupérer les données
    const formData = new FormData(e.target);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        password: formData.get('password'),
        role: formData.get('role'),
        status: formData.get('status')
    };
    
    // Validation mot de passe
    const passwordConfirm = formData.get('password_confirm');
    if (data.password !== passwordConfirm) {
        errorText.textContent = 'Les mots de passe ne correspondent pas';
        errorMessage.classList.remove('hidden');
        return;
    }
    
    // Désactiver le bouton
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Création...';
    errorMessage.classList.add('hidden');
    
    try {
        const response = await fetch(`${API_URL}/api/admin/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Toast.show('Utilisateur créé avec succès !', 'success');
            setTimeout(() => {
                window.location.href = 'users.php';
            }, 1500);
        } else {
            throw new Error(result.error || 'Erreur de création');
        }
        
    } catch (error) {
        console.error('Create error:', error);
        errorText.textContent = error.message;
        errorMessage.classList.remove('hidden');
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Créer l\'utilisateur';
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>