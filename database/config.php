<?php

$host = 'localhost:3307';
$dbname = '2thsnd4_db';
$username = 'root';
$password = '';

// Set DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

// PDO options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);
    
    $pdo->exec("SET time_zone = '+08:00'");
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>