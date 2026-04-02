<?php
// ============================================================
// includes/helpers.php
// ============================================================

/**
 * Generate URL-safe slug from a string
 */
function make_slug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s\-]/', '', $text); // remove special chars
    $text = preg_replace('/[\s\-]+/', '-', $text);       // spaces to hyphens
    return trim($text, '-');
}

/**
 * Get display name — falls back to main name if null/empty
 */
function get_display_name(string $name, ?string $display_name): string {
    return (!empty($display_name)) ? $display_name : $name;
}

/**
 * Get slug — auto-generates from name if null/empty
 */
function get_slug(string $name, ?string $slug): string {
    return (!empty($slug)) ? $slug : make_slug($name);
}

/**
 * Check slug uniqueness in a table
 * @param PDO    $pdo
 * @param string $table   'universities' or 'courses'
 * @param string $slug
 * @param int    $exclude_id  pass 0 when adding new
 */
function is_slug_unique(PDO $pdo, string $table, string $slug, int $exclude_id = 0): bool {
    $allowed = ['universities', 'courses'];
    if (!in_array($table, $allowed)) return false;

    $sql = "SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id != ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slug, $exclude_id]);
    return (int)$stmt->fetchColumn() === 0;
}

/**
 * Upload a file and return relative path or false on failure
 * @param array  $file     $_FILES['field']
 * @param string $subdir   'images' | 'brochures' | 'certificates' | 'accreditations'
 * @param int    $max_size bytes
 */
function upload_file(array $file, string $subdir, int $max_size): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK)   return false;
    if ($file['size'] > $max_size)          return false;

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_img = ['jpg','jpeg','png','webp','gif'];
    $allowed_doc = ['pdf'];

    if ($subdir === 'brochures') {
        if (!in_array($ext, $allowed_doc)) return false;
    } else {
        if (!in_array($ext, $allowed_img)) return false;
    }

    $dir = UPLOAD_DIR . $subdir . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename  = uniqid('', true) . '_' . time() . '.' . $ext;
    $dest      = $dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return UPLOAD_URL . $subdir . '/' . $filename;
}

/**
 * Delete a file from uploads
 */
function delete_file(?string $path): void {
    if (!$path) return;
    $full = $_SERVER['DOCUMENT_ROOT'] . $path;
    if (file_exists($full)) unlink($full);
}

/**
 * Sanitize string for output
 */
function e(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

/**
 * Format money
 */
function format_money(mixed $amount): string {
    if ($amount === null || $amount === '') return '—';
    return '₹' . number_format((float)$amount, 0, '.', ',');
}

/**
 * Redirect helper
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Flash message (set)
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Flash message (get + clear)
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash HTML
 */
function render_flash(): string {
    $flash = get_flash();
    if (!$flash) return '';
    $type  = $flash['type']; // success | error | warning
    $msg   = e($flash['message']);
    return "<div class=\"alert alert-{$type}\">{$msg}</div>";
}
