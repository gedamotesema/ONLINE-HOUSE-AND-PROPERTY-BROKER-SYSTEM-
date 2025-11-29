<?php
// renter_reply.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_renter()) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inquiry_id = $_POST['inquiry_id'] ?? null;
    $message = trim($_POST['message'] ?? '');

    if ($inquiry_id && strlen($message) >= 5) {
        $renter_id = $_SESSION['user_id'];

        // Insert renter's follow-up message as a reply
        $stmt = $pdo->prepare("INSERT INTO inquiry_replies (inquiry_id, owner_id, reply, created_at) 
                               SELECT ?, owner_id, ?, NOW() FROM inquiries WHERE id = ? AND sender_id = ?");
        $stmt->execute([$inquiry_id, $message, $inquiry_id, $renter_id]);

        // Update status back to replied or keep it
        $pdo->prepare("UPDATE inquiries SET status = 'replied' WHERE id = ?")->execute([$inquiry_id]);
    }
}
header("Location: dashboard_renter.php");
exit;