<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $code = $_POST['code'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($code) || empty($new_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (($pwd_check = validatePassword($new_password)) !== true) {
        $error = $pwd_check;
    } else {
        $stmt = $pdo->prepare("SELECT id, secret_code FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['secret_code'] && password_verify($code, $user['secret_code'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->execute([$hashed_password, $user['id']]);

                $stmtLog = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
                $stmtLog->execute([$user['id'], 'RESET_PASSWORD', 'Password reset via secret code', $_SERVER['REMOTE_ADDR']]);

                $success = "Password has been successfully reset. You can now login.";
            } else {
                $error = "Invalid secret recovery code.";
            }
        } else {
            $error = "If an account exists with this email, the password reset instructions would apply (Invalid Code or Email).";
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="row" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="glass-panel" style="padding: 2.5rem; width: 100%; max-width: 450px;">
        <h2 class="text-center" style="margin-bottom: 2rem;">Reset Password</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <div style="margin-top: 1rem; text-align: center;">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label>Secret Recovery Code (4 Digits)</label>
                    <input type="password" name="code" required placeholder="XXXX" maxlength="4"
                        style="letter-spacing: 5px; font-weight: bold; text-align: center;">
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required placeholder="New Password">
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm Password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
            </form>

            <p class="text-center mt-4">
                <a href="login.php" style="color: var(--text-secondary);">&larr; Back to Login</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>