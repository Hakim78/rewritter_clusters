<?php
$pageTitle = "Accueil - SEO Article Generator";
require_once '../includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            Bienvenue sur SEO Article Generator 🚀
        </h2>
        <p class="text-gray-600 mb-6">
            Environnement de développement prêt ! Choisis une option pour commencer :
        </p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Option 1 -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-4xl mb-4">✍️</div>
            <h3 class="text-xl font-bold mb-2">Option 1</h3>
            <p class="text-gray-600 mb-4">Création d'un nouvel article SEO optimisé</p>
            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 inline-block">
                Bientôt disponible
            </a>
        </div>

        <!-- Option 2 -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-4xl mb-4">🔄</div>
            <h3 class="text-xl font-bold mb-2">Option 2</h3>
            <p class="text-gray-600 mb-4">Réécriture d'un article existant</p>
            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 inline-block">
                Bientôt disponible
            </a>
        </div>

        <!-- Option 3 -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-4xl mb-4">🔗</div>
            <h3 class="text-xl font-bold mb-2">Option 3</h3>
            <p class="text-gray-600 mb-4">Cluster de 3 articles liés</p>
            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 inline-block">
                Bientôt disponible
            </a>
        </div>
    </div>

    <!-- Test connexion -->
    <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <p class="text-blue-800">
            🧪 <strong>Test de l'environnement :</strong>
            <a href="test_connection.php" class="underline hover:text-blue-600">
                Vérifier la connexion PHP ↔ Python
            </a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
