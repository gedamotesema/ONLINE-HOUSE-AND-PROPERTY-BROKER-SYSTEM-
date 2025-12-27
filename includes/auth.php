<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function generateCsrfToken()
{
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireRole($role)
{
    requireLogin();
    if ($_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'admin') {
        redirect('dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('CSRF validation failed.');
    }
}
?>