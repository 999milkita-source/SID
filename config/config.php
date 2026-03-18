<?php
// config.php - Database & environment
define('DB_HOST', 'localhost');
define('DB_NAME', 'sid_wolokota');
define('DB_USER', 'root');
define('DB_PASS', ''); // simpan aman, non-public

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Secure session cookie defaults
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

define('BASE_URL', '/sidwolokota/');

$storage_path = "../storage/";
$storage_url  = "../storage/";