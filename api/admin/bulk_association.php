<?php
/**
 * api/admin/bulk_association.php
 * Admin-only AJAX endpoint for bulk-syncing university ↔ master-item associations.
 */

// Must be first — before any output or includes that might send headers
session_name('sode_admin');   // hardcoded to avoid requiring config before session start
session_start();

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/db.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';

header('Content-Type: application/json');

// ── Require admin session ──────────────────────────────────────────────────
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorised']);
    exit;
}

// ── Module config ──────────────────────────────────────────────────────────
// type: 'junction' = many-to-many pivot table
//       'direct'   = single FK column on the universities row
$MODULES = [
    'education_modes' => [
        'type'    => 'junction',
        'table'   => 'university_education_modes',
        'fk_col'  => 'education_mode_id',
        'label'   => 'Education Mode',
    ],
    'exam_modes' => [
        'type'    => 'junction',
        'table'   => 'university_exam_modes',
        'fk_col'  => 'exam_mode_id',
        'label'   => 'Exam Mode',
    ],
    'accreditations' => [
        'type'    => 'junction',
        'table'   => 'university_accreditations',
        'fk_col'  => 'accreditation_id',
        'label'   => 'Accreditation',
    ],
    'university_types' => [
        'type'    => 'direct',
        'table'   => 'universities',
        'fk_col'  => 'university_type_id',
        'label'   => 'University Type',
    ],
];

// ── Parse request ──────────────────────────────────────────────────────────
$raw    = file_get_contents('php://input');
$body   = json_decode($raw, true) ?? [];
$action  = trim($body['action']  ?? ($_GET['action'] ?? ''));
$module  = trim($body['module']  ?? '');
$item_id = (int)($body['item_id'] ?? 0);

// Validate module
if (!array_key_exists($module, $MODULES)) {
    echo json_encode(['success' => false, 'error' => 'Invalid module: ' . $module]);
    exit;
}
if ($item_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid item_id']);
    exit;
}

$cfg = $MODULES[$module];

// ══════════════════════════════════════════════════════════════════
//  ACTION: get_universities
//  Returns all active universities with is_selected = 1 if linked
// ══════════════════════════════════════════════════════════════════
if ($action === 'get_universities') {

    if ($cfg['type'] === 'junction') {
        $stmt = $pdo->prepare("
            SELECT u.id,
                   COALESCE(NULLIF(u.display_name,''), u.name) AS uni_name,
                   u.image,
                   IF(j.university_id IS NOT NULL, 1, 0) AS is_selected
            FROM universities u
            LEFT JOIN {$cfg['table']} j
                   ON j.university_id = u.id AND j.{$cfg['fk_col']} = ?
            WHERE u.is_active = 1
            ORDER BY u.name ASC
        ");
        $stmt->execute([$item_id]);

    } else {
        // university_types — direct FK on universities table
        $stmt = $pdo->prepare("
            SELECT id,
                   COALESCE(NULLIF(display_name,''), name) AS uni_name,
                   image,
                   IF(university_type_id = ?, 1, 0) AS is_selected
            FROM universities
            WHERE is_active = 1
            ORDER BY name ASC
        ");
        $stmt->execute([$item_id]);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build clean response
    $unis = array_map(function ($r) {
        return [
            'id'          => (int)$r['id'],
            'uni_name'    => $r['uni_name'],
            'image'       => $r['image'] ? e($r['image']) : '',
            'is_selected' => (bool)$r['is_selected'],
        ];
    }, $rows);

    echo json_encode(['success' => true, 'data' => $unis]);
    exit;
}

// ══════════════════════════════════════════════════════════════════
//  ACTION: sync
//  Bulk replace university associations for a given item
// ══════════════════════════════════════════════════════════════════
if ($action === 'sync') {

    // selected_ids is an array of integer university IDs (can be empty = deselect all)
    $raw_ids      = $body['selected_ids'] ?? [];
    $selected_ids = array_unique(array_filter(array_map('intval', $raw_ids)));

    try {
        $pdo->beginTransaction();

        if ($cfg['type'] === 'junction') {
            // 1. Delete all existing associations for this item
            $pdo->prepare("DELETE FROM {$cfg['table']} WHERE {$cfg['fk_col']} = ?")
                ->execute([$item_id]);

            // 2. Insert new ones
            if (!empty($selected_ids)) {
                $placeholders = implode(',', array_fill(0, count($selected_ids), '(?,?)'));
                $values       = [];
                foreach ($selected_ids as $uid) {
                    $values[] = $uid;
                    $values[] = $item_id;
                }
                $pdo->prepare(
                    "INSERT INTO {$cfg['table']} (university_id, {$cfg['fk_col']}) VALUES {$placeholders}"
                )->execute($values);
            }

        } else {
            // university_types — direct FK (1-to-many)
            // 1. Unset this type from all universities that currently have it
            $pdo->prepare("UPDATE universities SET university_type_id = NULL WHERE university_type_id = ?")
                ->execute([$item_id]);

            // 2. Assign this type to selected universities
            if (!empty($selected_ids)) {
                $in = implode(',', $selected_ids); // safe: already cast to int
                $pdo->prepare("UPDATE universities SET university_type_id = ? WHERE id IN ({$in})")
                    ->execute([$item_id]);
            }
        }

        $pdo->commit();

        // Return the updated count so the frontend can refresh the badge in-place
        $count = count($selected_ids);
        echo json_encode([
            'success'   => true,
            'new_count' => $count,
            'message'   => $count . ' universit' . ($count === 1 ? 'y' : 'ies') . ' saved successfully.',
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'DB error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
exit;
