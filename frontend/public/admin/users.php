<?php
/**
 * Gestion des utilisateurs (Admin)
 * Fichier: frontend/public/admin/users.php
 */

$pageTitle = "Gestion des utilisateurs - Admin";
require_once '../../includes/header.php';
requireAdmin(); // Protection admin
?>

<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-users text-purple-600 mr-3"></i>
                    Gestion des utilisateurs
                </h1>
                <p class="text-gray-600">Créer et gérer les accès à la plateforme</p>
            </div>
            <a href="create_user.php" class="btn-primary">
                <i class="fas fa-user-plus mr-2"></i>
                Nouvel utilisateur
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid md:grid-cols-4 gap-6 mb-8" id="stats-container">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total</p>
                    <p class="text-3xl font-bold" id="stat-total">-</p>
                </div>
                <i class="fas fa-users text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Actifs</p>
                    <p class="text-3xl font-bold" id="stat-active">-</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Admins</p>
                    <p class="text-3xl font-bold" id="stat-admins">-</p>
                </div>
                <i class="fas fa-shield-alt text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Utilisateurs</p>
                    <p class="text-3xl font-bold" id="stat-users">-</p>
                </div>
                <i class="fas fa-user text-4xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-list mr-2 text-gray-500"></i>
                    Liste des utilisateurs
                </h2>
                <button onclick="loadUsers()" class="text-purple-600 hover:text-purple-800">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
            </div>
        </div>

        <!-- Loader -->
        <div id="table-loader" class="p-12 text-center">
            <div class="loader mx-auto mb-4"></div>
            <p class="text-gray-600">Chargement des utilisateurs...</p>
        </div>

        <!-- Tableau -->
        <div id="users-table" class="hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Rôle</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Dernière connexion</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-tbody" class="divide-y divide-gray-200">
                    <!-- Rempli par JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const API_URL = '<?php echo getenv('PYTHON_API_URL') ?: 'http://localhost:5001'; ?>';
const token = localStorage.getItem('auth_token');

// Charger les utilisateurs au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
    loadStats();
});

async function loadUsers() {
    const loader = document.getElementById('table-loader');
    const table = document.getElementById('users-table');
    const tbody = document.getElementById('users-tbody');
    
    loader.classList.remove('hidden');
    table.classList.add('hidden');
    
    try {
        const response = await fetch(`${API_URL}/api/admin/users`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.users) {
            // Remplir le tableau
            tbody.innerHTML = result.users.map(user => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">${user.name}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">${user.email}</td>
                    <td class="px-6 py-4">
                        <span class="badge ${user.role === 'admin' ? 'badge-error' : 'badge-info'}">
                            ${user.role === 'admin' ? 'Admin' : 'Utilisateur'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="badge ${user.status === 'active' ? 'badge-success' : 'badge-warning'}">
                            ${user.status === 'active' ? 'Actif' : user.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        ${user.last_login ? new Date(user.last_login).toLocaleString('fr-FR') : 'Jamais'}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-800 mr-3" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${user.id !== <?php echo $_SESSION['user']['id']; ?> ? `
                            <button onclick="deleteUser(${user.id}, '${user.name}')" class="text-red-600 hover:text-red-800" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
            
            loader.classList.add('hidden');
            table.classList.remove('hidden');
        } else {
            throw new Error(result.error || 'Erreur de chargement');
        }
    } catch (error) {
        console.error('Error loading users:', error);
        Toast.show('Erreur lors du chargement des utilisateurs', 'error');
        loader.classList.add('hidden');
    }
}

async function loadStats() {
    try {
        const response = await fetch(`${API_URL}/api/admin/stats`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.stats) {
            document.getElementById('stat-total').textContent = result.stats.total;
            document.getElementById('stat-active').textContent = result.stats.active;
            document.getElementById('stat-admins').textContent = result.stats.admins;
            document.getElementById('stat-users').textContent = result.stats.users;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function editUser(userId) {
    window.location.href = `edit_user.php?id=${userId}`;
}

async function deleteUser(userId, userName) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/api/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            Toast.show('Utilisateur supprimé avec succès', 'success');
            loadUsers();
            loadStats();
        } else {
            throw new Error(result.error || 'Erreur de suppression');
        }
    } catch (error) {
        console.error('Delete error:', error);
        Toast.show(error.message, 'error');
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>