<?php
// admin/index.php
// When someone visits /ai-tools/admin/ or /ai-tools/admin/index.php
// redirect them to login (or dashboard if already logged in)

require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (is_logged_in()) {
    resolve_user_permissions();
    if (has_permission('dashboard.view')) {
        header('Location: ' . ADMIN_URL . '/dashboard.php');
    } else {
        if (has_permission('universities.view')) {
            header('Location: ' . ADMIN_URL . '/universities/index.php');
        } elseif (has_permission('courses.view')) {
            header('Location: ' . ADMIN_URL . '/courses/index.php');
        } elseif (has_permission('mappings.view')) {
            header('Location: ' . ADMIN_URL . '/mappings/index.php');
        } elseif (has_permission('leads.view')) {
            header('Location: ' . ADMIN_URL . '/leads.php');
        } else {
            header('Location: ' . ADMIN_URL . '/rbac/access-denied.php');
        }
    }
} else {
    header('Location: ' . ADMIN_URL . '/login.php');
}
exit;
