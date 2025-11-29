<?php
// inquiry.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_renter()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'] ?? null;
    $message = trim($_POST['message'] ?? '');
    $sender_id = $_SESSION['user_id'];

    // Validate property ID and message
    if (!$property_id || strlen($message) < 10) {
        header("Location: property.php?id=" . urlencode($property_id));
        exit;
    }
    // Get owner for property
    $stmt = $pdo->prepare("SELECT owner_id FROM properties WHERE id=?");
    $stmt->execute([$property_id]);
    $prop = $stmt->fetch();
    if (!$prop) {
        header("Location: index.php");
        exit;
    }
    $owner_id = $prop['owner_id'];
    // Insert inquiry
    $stmt = $pdo->prepare("INSERT INTO inquiries (property_id, sender_id, owner_id, message, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$property_id, $sender_id, $owner_id, $message]);
    header("Location: property.php?id=" . urlencode($property_id));
    exit;
} else {
    header("Location: index.php");
    exit;
}
