<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
session_unset();
session_destroy();
header('Location: ' . ADMIN_URL . '/login.php');
exit;
