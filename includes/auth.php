<?php
// includes/auth.php

function login($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash']) && $user['status'] === 'active') {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function require_login() {
    if (!logged_in()) {
        header("Location: login.php");
        exit;
    }
}

function is_admin() {
    return logged_in() && $_SESSION['role'] === 'admin';
}

function is_owner() {
    return logged_in() && $_SESSION['role'] === 'owner';
}

function is_renter() {
    return logged_in() && $_SESSION['role'] === 'renter';
}

// For logging out
function logout() {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
