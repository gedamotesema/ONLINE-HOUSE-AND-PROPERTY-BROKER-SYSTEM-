<?php
require_once 'includes/config.php';
require_once 'includes/csrf.php';
require_once 'vendor/PHPMailer/PHPMailer.php';
require_once 'vendor/PHPMailer/SMTP.php';
require_once 'vendor/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $csrf = $_POST['csrf'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } elseif (!csrf_check($csrf)) {
        $message = 'Invalid CSRF token.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time()+3600); // 1 hour
            $stmt = $pdo->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
            $stmt->execute([$token, $expires, $email]);

            $site_url = "https://yourdomain.com/";   // CHANGE TO YOUR ACTUAL URL!
            $reset_link = $site_url . "reset_password.php?token=$token";

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.yourmailserver.com'; // e.g. smtp.gmail.com, smtp.office365.com
                $mail->SMTPAuth = true;
                $mail->Username = 'your@email.com';
                $mail->Password = 'your_app_password_or_real_password';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('no-reply@yourdomain.com', 'GNK Housing');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset - GNK Housing';
                $mail->Body = "Hi,\n\nYou requested a password reset. Click here or copy and paste this link:\n$reset_link\n\nThe link expires in 1 hour. If you didn't request this, ignore this message.";

                $mail->send();
                $message = "If the email is registered, a reset link has been sent.";
            } catch (Exception $e) {
                $message = "Could not send email. Please try again later.";
            }
        } else {
            $message = "If the email is registered, a reset link has been sent.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width:400px;">
    <h3>Forgot Password</h3>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input required type="email" name="email" id="email" class="form-control" autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        <div class="mt-2">
            <a href="login.php">Back to login</a>
        </div>
    </form>
</div>
</body>
</html>
