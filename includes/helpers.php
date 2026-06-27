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

/**
 * Get dynamic sidebar modules list
 */
function get_sidebar_modules(): array {
    global $pdo;
    try {
        if (!isset($pdo)) {
            require_once __DIR__ . '/db.php';
        }
        $stmt = $pdo->query("
            SELECT module_key, name
            FROM sidebar_items
            WHERE is_active = 1
            ORDER BY sort_order ASC
        ");
        $modules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modules[$row['module_key']] = $row['name'];
        }
        return $modules;
    } catch (Exception $e) {
        return [
            'dashboard'        => 'Dashboard',
            'universities'     => 'Universities',
            'courses'          => 'Courses',
            'mappings'         => 'Course Mappings',
            'users'            => 'Manage Users',
            'teams'            => 'Manage Teams',
            'roles'            => 'Manage Roles',
            'sidebar'          => 'Sidebar Manager',
            'university_types' => 'University Types',
            'education_modes'  => 'Education Modes',
            'exam_modes'       => 'Exam Modes',
            'accreditations'   => 'Accreditations',
            'change_password'  => 'Change Password'
        ];
    }
}

/**
 * Render pagination controls
 */
function render_pagination(int $total_count, int $limit, int $current_page): string {
    if ($total_count <= 0) return '';
    $total_pages = (int)ceil($total_count / $limit);
    $offset = ($current_page - 1) * $limit;
    
    $html = '<div class="pagination" style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border); flex-wrap:wrap; gap:10px;">';
    $start_num = min($total_count, $offset + 1);
    $end_num = min($total_count, $offset + $limit);
    $html .= '<div style="font-size:13px; color:var(--text-s);">Showing ' . $start_num . ' to ' . $end_num . ' of ' . $total_count . ' entries</div>';
    
    if ($total_pages > 1) {
        $html .= '<div class="pagination-buttons" style="display:flex; gap:0.35rem; align-items:center;">';
        
        // Helper to build URL with page param
        $get_url = function($p) {
            $params = $_GET;
            $params['page'] = $p;
            return '?' . http_build_query($params);
        };
        
        // Prev button
        if ($current_page > 1) {
            $html .= '<a href="' . $get_url($current_page - 1) . '" class="btn btn-secondary btn-sm" style="padding:4px 8px;font-size:12px;">&laquo; Prev</a>';
        }
        
        // Pages
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);
        
        if ($start_page > 1) {
            $html .= '<a href="' . $get_url(1) . '" class="btn btn-secondary btn-sm" style="padding:4px 8px;font-size:12px;">1</a>';
            if ($start_page > 2) {
                $html .= '<span style="color:var(--text-s);padding:0 4px;">...</span>';
            }
        }
        
        for ($p = $start_page; $p <= $end_page; $p++) {
            $cls = ($p === $current_page) ? 'btn-primary' : 'btn-secondary';
            $html .= '<a href="' . $get_url($p) . '" class="btn ' . $cls . ' btn-sm" style="padding:4px 8px;font-size:12px;">' . $p . '</a>';
        }
        
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                $html .= '<span style="color:var(--text-s);padding:0 4px;">...</span>';
            }
            $html .= '<a href="' . $get_url($total_pages) . '" class="btn btn-secondary btn-sm" style="padding:4px 8px;font-size:12px;">' . $total_pages . '</a>';
        }
        
        // Next button
        if ($current_page < $total_pages) {
            $html .= '<a href="' . $get_url($current_page + 1) . '" class="btn btn-secondary btn-sm" style="padding:4px 8px;font-size:12px;">Next &raquo;</a>';
        }
        
        $html .= '</div>';
    }
    $html .= '</div>';
    
    return $html;
}

