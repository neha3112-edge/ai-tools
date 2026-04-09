<?php
// ============================================================
// includes/config.php
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'ai_tools');
define('DB_USER', 'root');        // change this
define('DB_PASS', '');            // change this
define('DB_CHARSET', 'utf8mb4');

// ── URL CONSTANTS ──
// Change 'ai-tools' if your project folder name is different
define('BASE_URL', '/ai-tools');
define('ADMIN_URL', '/ai-tools/admin');

// ── FILE PATHS ──
define('UPLOAD_DIR', dirname(__DIR__) . '/assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

// ── SESSION ──
define('ADMIN_SESSION_NAME', 'sode_admin');

// ── FILE SIZE LIMITS ──
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024);   // 2MB
define('MAX_BROCHURE_SIZE', 50 * 1024 * 1024);   // 50MB

