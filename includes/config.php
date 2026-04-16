<?php
// ============================================================
// includes/config.php
// ============================================================
define('DB_HOST', 'localhost');

// define('DB_NAME', 'u120175788_ai_tools_db');
// define('DB_USER', 'u120175788_ai_tools_db');
// define('DB_PASS', 'AI5484@(*de3DE@174');

define('DB_NAME', 'ai_tools');
define('DB_USER', 'root');
define('DB_PASS', '');

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

