<?php
function sanitize($input)
{
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatPrice($price)
{
    return 'ETB ' . number_format($price, 2);
}

function flash($name, $message = '', $class = 'success')
{
    if (!empty($message)) {
        $_SESSION['flash'][$name] = [
            'message' => $message,
            'class' => $class
        ];
    } elseif (isset($_SESSION['flash'][$name])) {
        $flash = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        return "<div class='alert alert-{$flash['class']}'>{$flash['message']}</div>";
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function hasRole($role)
{
    return isLoggedIn() && $_SESSION['user_role'] === $role;
}

function isAdmin()
{
    return hasRole('admin');
}

function validatePassword($password)
{
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number.";
    }
    if (!preg_match('/[\W]/', $password)) { // \W matches any non-word character (special characters)
        return "Password must contain at least one special character.";
    }
    return true;
}

function redirect($url)
{
    header("Location: $url");
    exit;
}
?>