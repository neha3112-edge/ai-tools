<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

// Handle single hard-delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $did = (int) $_POST['delete_id'];
  // Cleanup uploaded files before deleting
  $row = $pdo->prepare("SELECT image, sample_certificate FROM universities WHERE id=?");
  $row->execute([$did]);
  $uni = $row->fetch();
  if ($uni) {
    delete_file($uni['image']);
    delete_file($uni['sample_certificate']);
    // Cleanup brochure files from associated mappings
    $bro_stmt = $pdo->prepare("SELECT brochure_file FROM university_courses WHERE university_id=? AND brochure_file IS NOT NULL AND brochure_file != ''");
    $bro_stmt->execute([$did]);
    foreach ($bro_stmt->fetchAll(PDO::FETCH_COLUMN) as $bf) { delete_file($bf); }
  }
  $pdo->prepare("DELETE FROM universities WHERE id=?")->execute([$did]);
  set_flash('success', 'University deleted permanently.');
  redirect(ADMIN_URL . '/universities/index.php');
}

// Handle bulk hard-delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_delete_ids'])) {
  $ids = array_filter(array_map('intval', explode(',', $_POST['bulk_delete_ids'])));
  if ($ids) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    // Cleanup files
    $rows = $pdo->prepare("SELECT id, image, sample_certificate FROM universities WHERE id IN ($placeholders)");
    $rows->execute($ids);
    foreach ($rows->fetchAll() as $uni) {
      delete_file($uni['image']);
      delete_file($uni['sample_certificate']);
      $bro_stmt = $pdo->prepare("SELECT brochure_file FROM university_courses WHERE university_id=? AND brochure_file IS NOT NULL AND brochure_file != ''");
      $bro_stmt->execute([$uni['id']]);
      foreach ($bro_stmt->fetchAll(PDO::FETCH_COLUMN) as $bf) { delete_file($bf); }
    }
    $pdo->prepare("DELETE FROM universities WHERE id IN ($placeholders)")->execute($ids);
    set_flash('success', count($ids) . ' university(ies) deleted permanently.');
  }
  redirect(ADMIN_URL . '/universities/index.php');
}

// Search + filter
$search = trim($_GET['search'] ?? '');
$level_filter = $_GET['type'] ?? '';
$university_types = $pdo->query("SELECT type_name FROM university_types WHERE is_active=1 ORDER BY type_name")->fetchAll(PDO::FETCH_COLUMN);

$where = ["u.is_active = 1"];
$params = [];

if ($search) {
  $where[] = "(u.name LIKE ? OR u.campus_location LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}
if ($level_filter) {
  $where[] = "ut.type_name = ?";
  $params[] = $level_filter;
}

$sql = "SELECT u.id, u.name, u.display_name, u.image, u.rating, u.nirf_ranking,
               ut.type_name as university_type, u.campus_location, u.created_at
        FROM universities u
        LEFT JOIN university_types ut ON u.university_type_id = ut.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY u.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$universities = $stmt->fetchAll();

$active_page = 'universities';
$page_title = 'Universities';
$page_subtitle = 'Manage all universities';
$base_path = '..';
$logout_path = '../logout.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Universities — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h3>All Universities</h3>
          <p><?= count($universities) ?> record(s) found</p>
        </div>
        <a href="add.php" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Add University
        </a>
      </div>

      <!-- Search & Filter -->
      <div class="search-bar">
        <form method="GET" style="display:contents;">
          <div class="search-input-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" name="search" placeholder="Search by name or location…" value="<?= e($search) ?>">
          </div>
          <select name="type" class="form-control" style="width:auto;min-width:160px;">
            <option value="">All Types</option>
            <?php foreach($university_types as $t): ?>
              <option value="<?= e($t) ?>" <?= $level_filter === $t ? 'selected' : '' ?>><?= e($t) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-secondary">Filter</button>
          <?php if ($search || $level_filter): ?>
            <a href="index.php" class="btn btn-secondary">Clear</a>
          <?php endif; ?>
        </form>
      </div>

      <!-- Table -->
      <div class="panel">
        <div class="table-responsive">
          <table data-paginate>
            <thead>
              <tr>
                <th style="width:40px;"><input type="checkbox" class="bulk-cb" id="bulkSelectAll" title="Select All"></th>
                <th style="width:50px;">#</th>
                <th>University</th>
                <th>Type</th>
                <th>Location</th>
                <th>Rating</th>
                <th>NIRF</th>
                <th>Added</th>
                <th style="width:150px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($universities): ?>
                <?php foreach ($universities as $i => $u): ?>
                  <tr>
                    <td><input type="checkbox" class="bulk-cb bulk-row-cb" value="<?= $u['id'] ?>"></td>
                    <td data-label="#"> <?= $i + 1 ?> </td>
                    <td data-label="University">
                      <div style="display:flex;align-items:center;gap:10px;">
                        <?php if ($u['image']): ?>
                          <img src="<?= e($u['image']) ?>" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid var(--border);" alt="">
                        <?php else: ?>
                          <div style="width:36px;height:36px;border-radius:8px;background:rgba(79,110,247,0.1);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--accent);">
                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                          </div>
                        <?php endif; ?>
                        <div style="text-align: left;">
                          <div class="cell-name"><?= e(get_display_name($u['name'], $u['display_name'])) ?></div>
                          <?php if ($u['display_name'] && $u['display_name'] !== $u['name']): ?>
                            <div style="font-size:11px;color:var(--text-s);"><?= e($u['name']) ?></div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td data-label="Type">
                      <?= $u['university_type'] ? '<span class="badge" style="background:rgba(79,110,247,0.1);color:var(--accent-h);">' . e($u['university_type']) . '</span>' : '—' ?>
                    </td>
                    <td data-label="Location"><?= e($u['campus_location'] ?: '—') ?></td>
                    <td data-label="Rating"><?= $u['rating'] ? '⭐ ' . e($u['rating']) : '—' ?></td>
                    <td data-label="NIRF Ranking"><?= $u['nirf_ranking'] ? '#' . e($u['nirf_ranking']) : '—' ?></td>
                    <td data-label="Added On"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td data-label="Actions">
                      <div class="action-col">
                        <a href="view.php?id=<?= $u['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="View Details">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" /><circle cx="12" cy="12" r="3" /></svg>
                        </a>
                        <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                        </a>
                        <form method="POST" style="display:inline;">
                          <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                          <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete" data-confirm="Delete '<?= e($u['name']) ?>'? This cannot be undone.">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /><path d="M10 11v6M14 11v6" /><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" /></svg>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9">
                    <div class="empty-state">
                      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21V11h6v10" />
                      </svg>
                      <p>No universities found. <a href="add.php" style="color:var(--accent);">Add one now →</a></p>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination-wrap" id="paginationWrap" data-perpage="10">
        <span class="pagination-info" id="paginationInfo"></span>
        <div class="pagination-controls" id="paginationControls"></div>
        <div class="pg-perpage-wrap">
          <select class="pg-perpage-select" id="pgPerPage">
            <option value="10">10 / page</option>
            <option value="25">25 / page</option>
            <option value="50">50 / page</option>
            <option value="100">100 / page</option>
          </select>
        </div>
      </div>

      <!-- Bulk Delete Bar -->
      <div class="bulk-bar" id="bulkBar">
        <div class="bulk-count" id="bulkCount"><span>0</span> selected</div>
        <button type="button" class="btn btn-secondary btn-sm" id="bulkDeselectBtn">Deselect</button>
        <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteTrigger">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
          Delete Selected
        </button>
      </div>
      <form method="POST" id="bulkDeleteForm" style="display:none;">
        <input type="hidden" name="bulk_delete_ids" id="bulkDeleteIds" value="">
      </form>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>