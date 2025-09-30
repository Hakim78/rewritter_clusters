<?php
// Charge le .env
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value, '"\'');
        putenv(trim($name) . '=' . trim($value, '"\''));
    }
}

loadEnv('.env');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

echo "Test de connexion à la BDD distante...\n\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $db\n";
echo "User: $user\n\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ CONNEXION RÉUSSIE !\n\n";
    
    // Test requête
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables trouvées (" . count($tables) . ") :\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}
?>
