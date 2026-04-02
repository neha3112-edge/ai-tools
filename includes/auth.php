<?php
// ============================================================
// includes/auth.php
// ============================================================

function is_logged_in(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function require_superadmin(): void {
    require_login();
    if ($_SESSION['admin_role'] !== 'superadmin') {
        header('Location: ' . ADMIN_URL . '/dashboard.php');
        exit;
    }
}
