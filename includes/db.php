<?php
$host = 'localhost';
$dbname = 'online_shop';
$username = 'root';
$password = '';

// Define site constants
if (!defined('SITE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the project root dynamically
    $currentDir = str_replace('\\', '/', __DIR__);
    $documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    
    // Remove document root from current directory to get relative path
    $relativeDir = str_replace($documentRoot, '', $currentDir);
    // Go up one level from 'includes' to get project root
    $projectRoot = dirname($relativeDir);
    
    // Ensure project root starts with / and ends with /
    $projectRoot = '/' . trim($projectRoot, '/\\');
    if ($projectRoot === '/') {
        $baseUrl = $protocol . '://' . $host . '/';
    } else {
        $baseUrl = $protocol . '://' . $host . $projectRoot . '/';
    }
    
    define('SITE_URL', $baseUrl);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
