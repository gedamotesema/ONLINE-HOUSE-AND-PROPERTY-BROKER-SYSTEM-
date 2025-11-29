<?php
// login.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf'] ?? '';
    // Basic validations
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password too short.';
    } elseif (!csrf_check($csrf)) {
        $error = 'Invalid CSRF token.';
    } else {
        if (login($email, $password)) {
            // Set username in session
            $stmt = $pdo->prepare("SELECT username, role FROM users WHERE email=?");
            $stmt->execute([$email]);
            $u = $stmt->fetch();
            $_SESSION['username'] = $u['username'];
            $_SESSION['role'] = $u['role'];
            // Redirect by role
            if ($u['role'] == 'renter') header("Location: dashboard_renter.php");
            elseif ($u['role'] == 'owner') header("Location: dashboard_owner.php");
            elseif ($u['role'] == 'admin') header("Location: admin.php");
            else header("Location: index.php");
            exit;
        } else {
            $error = 'Incorrect email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Rental Broker</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'views/shared/header.php'; ?>
    <main class="container mt-5" style="max-width:400px;">
        <h3>Login</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input required type="email" name="email" id="email" class="form-control" autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input required type="password" name="password" id="password" class="form-control" minlength="6">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="mt-2">
                <a href="register.php">Don't have an account? Register</a>
            </div>
        </form>
    </main>
    <?php include 'views/shared/footer.php'; ?>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
