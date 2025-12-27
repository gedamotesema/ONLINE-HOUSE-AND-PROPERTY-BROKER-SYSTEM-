<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['profile_picture'];

            $stmtLog = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmtLog->execute([$user['id'], 'LOGIN', 'User logged in', $_SERVER['REMOTE_ADDR']]);

            redirect('dashboard.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="row" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="glass-panel" style="padding: 2.5rem; width: 100%; max-width: 400px;">
        <h2 class="text-center" style="margin-bottom: 2rem;">Welcome Back</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
                <div style="text-align: right; margin-top: 5px;">
                    <a href="reset_password.php" style="font-size: 0.85rem; color: var(--accent-color);">Forgot
                        Password?</a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <p class="text-center mt-4" style="color: var(--text-secondary);">
            Don't have an account? <a href="register.php">Sign up</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>