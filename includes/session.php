<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: /index.php');
        exit;
    }
}

function currentUser() {
    return [
        'id'    => $_SESSION['user_id']   ?? null,
        'name'  => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['role']      ?? '',
    ];
}
?>