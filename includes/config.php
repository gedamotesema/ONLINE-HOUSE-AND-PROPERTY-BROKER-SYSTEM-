<?php
// includes/config.php

$db_host = 'localhost';
$db_name = 'rental_broker';
$db_user = 'root';        // Change if using other username
$db_pass = '';            // Change if you set a password

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Start session for authentication
session_start();
?>
