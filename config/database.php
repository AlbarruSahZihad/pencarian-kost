<?php
$mysql_url = getenv('MYSQL_URL');

if ($mysql_url) {
    $url = parse_url($mysql_url);
    $host = $url['host'];
    $port = $url['port'] ?? 3306;
    $dbname = ltrim($url['path'], '/');
    $username = $url['user'];
    $password = $url['pass'];
} else {
    $host = 'localhost';
    $dbname = 'kos';
    $username = 'root';
    $password = '';
    $port = 3306;
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

session_start();
?>
