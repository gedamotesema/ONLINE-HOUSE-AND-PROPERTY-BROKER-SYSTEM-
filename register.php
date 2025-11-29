<?php
// register.php
require_once 'includes/config.php';
require_once 'includes/csrf.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'renter';
    $csrf = $_POST['csrf'] ?? '';

    // Validation
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 characters (letters/numbers/underscore).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['renter','owner'])) {
        $error = "Invalid role selected.";
    } elseif (!csrf_check($csrf)) {
        $error = "Invalid CSRF token.";
    } else {
        // Check email uniqueness
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email is already registered.";
        } else {
            // Store hashed password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, created_at, status) VALUES (?,?,?,?,NOW(),'active')");
            $stmt->execute([$username, $email, $password_hash, $role]);
            $success = "Registration successful! You can login now.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Rental Broker</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'views/shared/header.php'; ?>
    <main class="container mt-5" style="max-width:500px;">
        <h3>Register</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input required type="text" name="username" id="username" class="form-control" minlength="3" maxlength="20">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input required type="email" name="email" id="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input required type="password" name="password" id="password" class="form-control" minlength="6">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Register as</label>
                <select name="role" id="role" class="form-select">
                    <option value="renter" selected>Renter/Customer</option>
                    <option value="owner">Property Owner</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <div class="mt-2">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </form>
    </main>
    <?php include 'views/shared/footer.php'; ?>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
