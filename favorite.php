<?php
// favorite.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_renter()) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($property_id && in_array($action, ['add', 'remove'])) {
    if ($action === 'add') {
        // Only add if not already favorited
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id=? AND property_id=?");
        $stmt->execute([$user_id, $property_id]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $property_id]);
        }
    } elseif ($action === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id=? AND property_id=?");
        $stmt->execute([$user_id, $property_id]);
    }
}
header("Location: property.php?id=" . urlencode($property_id));
exit;
