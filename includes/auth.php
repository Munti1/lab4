<?php
// includes/auth.php — Session helpers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'pages/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function currentUser() {
    return [
        'id'        => $_SESSION['user_id']   ?? null,
        'full_name' => $_SESSION['full_name'] ?? '',
        'email'     => $_SESSION['email']     ?? '',
    ];
}

function logout() {
    session_unset();
    session_destroy();
}
