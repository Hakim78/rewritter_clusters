<?php
/**
 * Test de connexion PHP vers Backend Python
 * Fichier: frontend/public/test_connection.php
 */

// Configuration de l'URL du backend Python
define('PYTHON_API_URL', 'http://localhost:5001');

/**
 * Fonction pour faire des appels à l'API Python
 */
function callPythonAPI($endpoint, $method = 'GET', $data = null) {
    $url = PYTHON_API_URL . $endpoint;
    
    $ch = curl_init($url);
    
    // Configuration de base
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Headers
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Méthode et données
    if ($method === 'POST' && $data !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // Exécution
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Gestion des erreurs
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
    
    return [
        'success' => true,
        'data' => json_decode($response, true),
        'http_code' => $httpCode
    ];
}

// Protection : réservé aux utilisateurs connectés
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit;
}
?>
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Connexion PHP-Python</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .test-section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .result {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .success {
            border-left: 4px solid #28a745;
            background-color: #d4edda;
        }
        
        .error {
            border-left: 4px solid #dc3545;
            background-color: #f8d7da;
        }
        
        pre {
            background: #272822;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .status-success {
            background: #28a745;
            color: white;
        }
        
        .status-error {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Test de Connexion PHP ↔ Python</h1>
        
        <?php
        // Test 1: Connexion basique GET
        echo '<div class="test-section">';
        echo '<h2>Test 1: Connexion GET basique</h2>';
        
        $test1 = callPythonAPI('/api/test', 'GET');
        
        if ($test1['success']) {
            echo '<div class="result success">';
            echo '<span class="status-badge status-success">✓ Succès</span>';
            echo '<strong>Code HTTP:</strong> ' . $test1['http_code'];
            echo '<pre>' . json_encode($test1['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
        } else {
            echo '<div class="result error">';
            echo '<span class="status-badge status-error">✗ Erreur</span>';
            echo '<strong>Erreur:</strong> ' . $test1['error'];
            echo '</div>';
        }
        echo '</div>';
        
        // Test 2: Envoi de données POST
        echo '<div class="test-section">';
        echo '<h2>Test 2: Envoi de données POST</h2>';
        
        $testData = [
            'test_field' => 'Valeur de test',
            'number' => 42,
            'array' => ['item1', 'item2', 'item3']
        ];
        
        $test2 = callPythonAPI('/api/test-post', 'POST', $testData);
        
        if ($test2['success']) {
            echo '<div class="result success">';
            echo '<span class="status-badge status-success">✓ Succès</span>';
            echo '<strong>Données envoyées:</strong>';
            echo '<pre>' . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '<strong>Réponse reçue:</strong>';
            echo '<pre>' . json_encode($test2['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
        } else {
            echo '<div class="result error">';
            echo '<span class="status-badge status-error">✗ Erreur</span>';
            echo '<strong>Erreur:</strong> ' . $test2['error'];
            echo '</div>';
        }
        echo '</div>';
        
        // Test 3: Simulation Workflow 1
        echo '<div class="test-section">';
        echo '<h2>Test 3: Simulation Workflow 1 (Création article)</h2>';
        
        $workflow1Data = [
            'site_url' => 'https://example.com',
            'domain' => 'Marketing Digital',
            'guideline' => 'Article informatif sur le SEO',
            'keyword' => 'optimisation SEO 2025',
            'internal_linking' => true
        ];
        
        $test3 = callPythonAPI('/api/workflow1', 'POST', $workflow1Data);
        
        if ($test3['success']) {
            echo '<div class="result success">';
            echo '<span class="status-badge status-success">✓ Succès</span>';
            echo '<strong>Paramètres envoyés:</strong>';
            echo '<pre>' . json_encode($workflow1Data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '<strong>Résultat:</strong>';
            echo '<pre>' . json_encode($test3['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
        } else {
            echo '<div class="result error">';
            echo '<span class="status-badge status-error">✗ Erreur</span>';
            echo '<strong>Erreur:</strong> ' . $test3['error'];
            echo '</div>';
        }
        echo '</div>';
        ?>
        
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px;">
            <p><strong>📝 Instructions:</strong></p>
            <ol style="text-align: left; display: inline-block; margin-top: 10px;">
                <li>Assure-toi que le backend Python tourne: <code>python backend/app.py</code></li>
                <li>Vérifie que l'URL est correcte: <code>http://localhost:5000</code></li>
                <li>Si ça ne fonctionne pas, vérifie les logs Python</li>
            </ol>
        </div>
    </div>
</body>
</html>
