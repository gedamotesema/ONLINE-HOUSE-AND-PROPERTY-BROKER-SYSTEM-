<?php
require_once 'config.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS viewing_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        property_id INT NOT NULL,
        tenant_id INT NOT NULL,
        preferred_date DATETIME NOT NULL,
        message TEXT,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
        FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'viewing_requests' checked/created.<br>";

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'secret_code'");
    $cols = $stmt->fetchAll();

    if (empty($cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN secret_code VARCHAR(255) AFTER password");
        echo "Column 'secret_code' added successfully.<br>";
    } else {
        echo "Column 'secret_code' already exists.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>