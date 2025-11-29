<?php
// views/shared/header.php
$user_logged_in = isset($_SESSION['user_id']);
$user_role = $user_logged_in ? $_SESSION['role'] : 'guest';
$username = $user_logged_in ? $_SESSION['username'] ?? '' : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Rental Broker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if ($user_logged_in && $user_role == 'renter'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_renter.php">My Dashboard</a>
                    </li>
                <?php endif; ?>
                <?php if ($user_logged_in && $user_role == 'owner'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_owner.php">Owner Dashboard</a>
                    </li>
                <?php endif; ?>
                <?php if ($user_logged_in && $user_role == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin Dashboard</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if ($user_logged_in): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-2">👤 <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($user_role) ?>)</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
