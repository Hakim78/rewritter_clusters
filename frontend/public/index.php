<?php
/**
 * Page d'accueil - SEO Article Generator
 * Fichier: frontend/public/index.php
 */

$pageTitle = "Accueil - SEO Article Generator";
require_once '../includes/header.php';
?>

<!-- Hero Section -->
<div class="max-w-6xl mx-auto mb-12">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-2xl p-12 text-white text-center">
        <h1 class="text-5xl font-bold mb-4 animate-fade-in">
            🚀 Générateur d'Articles SEO
        </h1>
        <p class="text-xl mb-8 text-purple-100">
            Créez du contenu optimisé People-first avec l'IA en quelques minutes
        </p>
        <div class="flex flex-wrap justify-center gap-4 text-sm">
            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                <i class="fas fa-robot mr-2"></i>Powered by OpenAI & Anthropic
            </div>
            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                <i class="fas fa-search mr-2"></i>Optimisé SEO
            </div>
            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                <i class="fas fa-users mr-2"></i>People-first Content
            </div>
        </div>
    </div>
</div>

<!-- Options principales -->
<div class="max-w-6xl mx-auto mb-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
        Choisissez votre workflow
    </h2>
    
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Option 1 : Création -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-8 text-white">
                <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-plus-circle text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-center">Créer un Article</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6">
                    Générez un article SEO complet à partir de zéro avec vos paramètres personnalisés.
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Contenu 100% unique et original</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Optimisé pour le référencement</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Image IA générée automatiquement</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Maillage interne suggéré</span>
                    </li>
                </ul>
                <a href="option1.php" class="block w-full text-center btn-primary">
                    <i class="fas fa-arrow-right mr-2"></i>Commencer
                </a>
            </div>
        </div>

        <!-- Option 2 : Réécriture -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-green-500 to-teal-500 p-8 text-white">
                <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-sync-alt text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-center">Réécrire un Article</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6">
                    Optimisez un article existant selon les normes RAG LLMO et People-first.
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Amélioration SEO complète</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Adaptation RAG LLMO compatible</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Nouvelle image optimisée</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Conservation du style original</span>
                    </li>
                </ul>
                <a href="option2.php" class="block w-full text-center bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-arrow-right mr-2"></i>Commencer
                </a>
            </div>
        </div>

        <!-- Option 3 : Cluster -->
        <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden border-2 border-blue-300">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-8 text-white relative">
                <div class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">
                    POPULAIRE
                </div>
                <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-sitemap text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-center">Cluster d'Articles</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6">
                    Créez 1 article principal réécrit + 2 articles satellites liés avec maillage automatique.
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>3 articles complets générés</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Maillage interne optimisé</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Autorité thématique renforcée</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>3 images IA personnalisées</span>
                    </li>
                </ul>
                <a href="option3.php" class="block w-full text-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-arrow-right mr-2"></i>Commencer
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Fonctionnalités -->
<div class="max-w-6xl mx-auto mb-12">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
            <i class="fas fa-star text-yellow-500 mr-3"></i>
            Fonctionnalités principales
        </h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-robot text-purple-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">IA Avancée</h3>
                <p class="text-sm text-gray-600">GPT-4 & Claude pour un contenu de qualité</p>
            </div>
            
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">SEO Optimisé</h3>
                <p class="text-sm text-gray-600">Conforme aux dernières normes Google</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-image text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Images IA</h3>
                <p class="text-sm text-gray-600">Visuels générés automatiquement</p>
            </div>
            
            <div class="text-center">
                <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-code text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">HTML Prêt</h3>
                <p class="text-sm text-gray-600">Code HTML copiable directement</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="max-w-6xl mx-auto mb-12">
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-8 text-white text-center">
            <div class="text-5xl font-bold mb-2">3-5 min</div>
            <div class="text-purple-100">Temps de génération moyen</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-teal-500 rounded-xl p-8 text-white text-center">
            <div class="text-5xl font-bold mb-2">100%</div>
            <div class="text-green-100">Contenu unique et original</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-8 text-white text-center">
            <div class="text-5xl font-bold mb-2">SEO</div>
            <div class="text-blue-100">Optimisé People-first</div>
        </div>
    </div>
</div>

<!-- CTA Final -->
<div class="max-w-4xl mx-auto mb-12">
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-12 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Prêt à créer du contenu de qualité ?</h2>
        <p class="text-gray-300 mb-8">Commencez maintenant et générez votre premier article SEO optimisé</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="option1.php" class="bg-white text-gray-900 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">
                Créer un article
            </a>
            <a href="test_connection.php" class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-lg hover:bg-white hover:text-gray-900 transition">
                Tester l'environnement
            </a>
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>

<?php require_once '../includes/footer.php'; ?>