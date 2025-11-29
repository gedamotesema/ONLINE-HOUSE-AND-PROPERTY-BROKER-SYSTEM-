<?php
// delete_property.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_login();
if (!is_owner()) {
    header("Location: index.php");
    exit;
}
$owner_id = $_SESSION['user_id'];
$prop_id = $_GET['id'] ?? null;

if ($prop_id) {
    // Confirm property belongs to owner
    $stmt = $pdo->prepare("SELECT images FROM properties WHERE id=? AND owner_id=?");
    $stmt->execute([$prop_id, $owner_id]);
    $property = $stmt->fetch();
    if ($property) {
        // Remove images from filesystem
        $images = $property['images'] ? explode(',', $property['images']) : [];
        foreach ($images as $img) {
            $filePath = "uploads/properties/" . $img;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Delete property
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id=? AND owner_id=?");
        $stmt->execute([$prop_id, $owner_id]);
    }
}
header("Location: dashboard_owner.php");
exit;
