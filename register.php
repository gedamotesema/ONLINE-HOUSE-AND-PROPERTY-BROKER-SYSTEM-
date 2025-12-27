<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'tenant';

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!in_array($role, ['tenant', 'landlord'])) {
        $error = "Invalid role selected.";
    } elseif (($pwd_check = validatePassword($password)) !== true) {
        $error = $pwd_check;
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $secret_code = rand(1000, 9999);
            $hashed_code = password_hash((string) $secret_code, PASSWORD_DEFAULT);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, secret_code) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role, $hashed_code]);

                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = $role;
                $_SESSION['user_avatar'] = null;

                $stmtLog = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
                $stmtLog->execute([$user_id, 'REGISTER', "New $role registered", $_SERVER['REMOTE_ADDR']]);

                $success_view = true;
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="row" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="glass-panel" style="padding: 2.5rem; width: 100%; max-width: 500px;">

        <?php if (isset($success_view) && $success_view): ?>
            <div style="text-align: center;">
                <div style="font-size: 3rem; color: var(--success-color); margin-bottom: 1rem;"><i
                        class="fas fa-check-circle"></i></div>
                <h2 style="margin-bottom: 1rem;">Account Created!</h2>
                <p>Welcome to GNK housing, <strong><?php echo htmlspecialchars($name); ?></strong>.</p>

                <div class="secret-code-container"
                    style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--accent-color); margin: 2rem 0; padding: 2rem;">
                    <h4
                        style="color: var(--accent-color); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px;">
                        Important: Secret Recovery Code</h4>
                    <p style="margin-bottom: 1rem;">Please save this 4-digit code. It is the <strong>ONLY</strong> way to
                        reset your password if you forget it.</p>
                    <div
                        style="font-size: 3rem; font-weight: bold; letter-spacing: 5px; color: #fff; text-shadow: 0 0 10px var(--accent-color);">
                        <?php echo $secret_code; ?>
                    </div>
                </div>

                <a href="dashboard.php" class="btn btn-primary" style="width: 100%;">I have saved my code, Continue</a>
            </div>

        <?php else: ?>
            <h2 class="text-center" style="margin-bottom: 2rem;">Create Account</h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required
                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary);">I want to:</label>
                    <select name="role">
                        <option value="tenant">Find a place to rent (Tenant)</option>
                        <option value="landlord">List my properties (Landlord)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Sign Up</button>
            </form>
        <?php endif; ?>

        <p class="text-center mt-4" style="color: var(--text-secondary);">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>