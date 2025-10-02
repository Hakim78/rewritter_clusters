<?php
/**
 * Sélection des Workflows pour édition de prompts
 * Fichier: frontend/public/admin/prompts.php
 */

$pageTitle = "Configuration des Prompts - Admin";
require_once '../../includes/header.php';
requireAdmin(); // Protection admin
?>

<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-edit text-blue-600 mr-3"></i>
                    Configuration des Prompts
                </h1>
                <p class="text-gray-600">Sélectionnez un workflow pour modifier son template de génération</p>
            </div>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille des workflows -->
    <div class="grid md:grid-cols-3 gap-8">

        <!-- Workflow 1 : Création d'article -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-8 text-white relative">
                <div class="absolute top-2 right-2 bg-green-400 text-green-900 text-xs font-bold px-3 py-1 rounded-full">
                    ACTIF
                </div>
                <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-plus-circle text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-center">Workflow 1</h3>
                <p class="text-center text-purple-100 text-sm mt-2">Création d'article</p>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-sm">
                    Template de génération pour la création d'articles SEO complets à partir de zéro.
                </p>
                <ul class="space-y-2 mb-6 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-cog text-purple-500 mr-2 mt-1"></i>
                        <span>4 experts : SEO, People First, LLMO, RAG</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cog text-purple-500 mr-2 mt-1"></i>
                        <span>Format structuré XML</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cog text-purple-500 mr-2 mt-1"></i>
                        <span>Variables dynamiques injectées</span>
                    </li>
                </ul>
                <a href="prompt_editor.php?workflow=1" class="block w-full text-center bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-edit mr-2"></i>Modifier le prompt
                </a>
            </div>
        </div>

        <!-- Workflow 2 : Réécriture -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden opacity-75">
            <div class="bg-gradient-to-br from-green-500 to-teal-500 p-8 text-white relative">
                <div class="absolute top-2 right-2 bg-gray-400 text-gray-900 text-xs font-bold px-3 py-1 rounded-full">
                    EN COURS
                </div>
                <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-sync-alt text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-center">Workflow 2</h3>
                <p class="text-center text-green-100 text-sm mt-2">Réécriture d'article</p>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-sm">
                    Template de génération pour la réécriture et optimisation d'articles existants.
                </p>
                <ul class="space-y-2 mb-6 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-cog text-green-500 mr-2 mt-1"></i>
                        <span>Optimisation RAG LLMO</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cog text-green-500 mr-2 mt-1"></i>
                        <span>Conservation du style</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cog text-green-500 mr-2 mt-1"></i>
                        <span>Amélioration SEO</span>
                    </li>
                </ul>
                <a href="prompt_editor.php?workflow=2" class="block w-full text-center bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition opacity-50 cursor-not-allowed" onclick="return false;">
                    <i class="fas fa-lock mr-2"></i>En développement
                </a>
            </div>
        </div>

        <!-- Workflow 3 : Cluster -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden opacity-75">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-8 text-white relative">
                <div class="absolute top-2 right-2 bg-gray-400 text-gray-900 text-xs font-bold px-3 py-1 rounded-full">
                    EN COURS
                </div>
                <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-sitemap text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-center">Workflow 3</h3>
                <p class="text-center text-blue-100 text-sm mt-2">Cluster d'articles</p>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-sm">
                    Template de génération pour les clusters avec maillage interne automatique.
                </p>
                <ul class="space-y-2 mb-6 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-cog text-blue-500 mr-2 mt-1"></i>
                        <span>Génération multi-articles</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cog text-blue-500 mr-2 mt-1"></i>
                        <span>Maillage intelligent</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-cog text-blue-500 mr-2 mt-1"></i>
                        <span>Cohérence thématique</span>
                    </li>
                </ul>
                <a href="prompt_editor.php?workflow=3" class="block w-full text-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition opacity-50 cursor-not-allowed" onclick="return false;">
                    <i class="fas fa-lock mr-2"></i>En développement
                </a>
            </div>
        </div>

    </div>

    <!-- Informations -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mt-8 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-2xl mr-4 mt-1"></i>
            <div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">À propos de l'édition des prompts</h3>
                <ul class="text-blue-800 space-y-2 text-sm">
                    <li><strong>Variables dynamiques :</strong> Les placeholders comme <code>{'{KEYWORD}'}</code> sont automatiquement remplacés par les données utilisateur</li>
                    <li><strong>Backup automatique :</strong> Chaque modification crée une sauvegarde horodatée</li>
                    <li><strong>Validation :</strong> Le système vérifie que toutes les variables requises sont présentes</li>
                    <li><strong>Effet immédiat :</strong> Les modifications sont appliquées dès la sauvegarde</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>