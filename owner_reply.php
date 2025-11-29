<?php
// owner_reply.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_owner()) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inquiry_id = $_POST['inquiry_id'] ?? null;
    $reply = trim($_POST['reply'] ?? '');

    if ($inquiry_id && strlen($reply) >= 5) {
        $owner_id = $_SESSION['user_id'];

        // Insert reply
        $stmt = $pdo->prepare("INSERT INTO inquiry_replies (inquiry_id, owner_id, reply) VALUES (?, ?, ?)");
        $stmt->execute([$inquiry_id, $owner_id, $reply]);

        // Mark inquiry as replied
        $stmt = $pdo->prepare("UPDATE inquiries SET status = 'replied' WHERE id = ? AND owner_id = ?");
        $stmt->execute([$inquiry_id, $owner_id]);
    }
}

header("Location: dashboard_owner.php");
exit;