<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GNK | Premium Rental Broker</title>
    <meta name="csrf-token" content="<?php echo generateCsrfToken(); ?>">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="main.js" defer></script>
</head>

<body>

    <nav class="glass-panel nav-bar">
        <div class="logo">GNK housing</div>
        <button class="nav-toggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="nav-links">
            <a href="index.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="properties.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'properties.php' ? 'active' : ''; ?>">Properties</a>
            <a href="about.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About Us</a>

            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
                <a href="messages.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">Messages</a>
                <?php if (isAdmin()): ?>
                    <a href="admin_dashboard.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">Admin</a>
                <?php endif; ?>

                <a href="profile.php" style="display: flex; align-items: center; gap: 8px;">
                    <?php if (!empty($_SESSION['user_avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>"
                            style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-color);">
                    <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size: 1.5rem;"></i>
                    <?php endif; ?>
                    <span>My Profile</span>
                </a>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container main-content">
        <?php echo flash('global'); ?>