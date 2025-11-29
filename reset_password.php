<?php
require_once 'includes/config.php';
require_once 'includes/csrf.php';

$token = $_GET['token'] ?? '';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf'] ?? '';
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!csrf_check($csrf)) {
        $error = 'Invalid CSRF token.';
    } else {
        // Find user by valid token and unexpired
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires >= NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            // Set new password, clear token
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
            $stmt->execute([$password_hash, $user['id']]);
            $success = 'Password reset! <a href="login.php">Login now</a>.';
        } else {
            $error = 'Invalid or expired reset link.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width:400px;">
    <h3>Reset Password</h3>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (!$success && $token): ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input required type="password" name="password" id="password" class="form-control" minlength="6" autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
