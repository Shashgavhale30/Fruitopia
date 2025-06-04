<?php
// config.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration for Fruitopia
define('DB_HOST', 'localhost');
define('DB_NAME', 'fruitopia');  // Your DB name
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');

// Timezone
date_default_timezone_set('Asia/Kolkata');

try {
    $dsn = "mysql:host=" . DB_HOST . 
           ";port=" . DB_PORT . 
           ";dbname=" . DB_NAME;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    exit("Database connection failed.");
}
