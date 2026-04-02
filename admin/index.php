<?php
// admin/index.php
// When someone visits /ai-tools/admin/ or /ai-tools/admin/index.php
// redirect them to login (or dashboard if already logged in)

require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();

if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
} else {
    header('Location: ' . ADMIN_URL . '/login.php');
}
exit;
