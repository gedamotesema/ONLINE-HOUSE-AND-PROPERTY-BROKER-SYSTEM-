<?php
require_once 'config.php';

try {
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    echo "Database and tables created successfully.";
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}
?>