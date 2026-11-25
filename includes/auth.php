<?php
// includes/auth.php - Authentication & session guard for CMS

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return !empty($_SESSION['hua_user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['hua_user_id'],
        'username' => $_SESSION['hua_username'] ?? 'Medewerker',
        'role' => $_SESSION['hua_role'] ?? 'admin'
    ];
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
