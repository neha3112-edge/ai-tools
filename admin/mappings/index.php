<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

// Handle soft-delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $stmt = $pdo->prepare("UPDATE university_courses SET is_active=0 WHERE id=?");
  $stmt->execute([(int) $_POST['delete_id']]);
  set_flash('success', 'Mapping deleted successfully.');
  redirect(ADMIN_URL . '/mappings/index.php');
}

// Search + filter
$search = trim($_GET['search'] ?? '');
$mode_filter = $_GET['mode'] ?? '';

$where = ["uc.is_active = 1"];
$params = [];

if ($search) {
  $where[] = "(u.name LIKE ? OR c.name LIKE ? OR u.display_name LIKE ? OR c.display_name LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
  $params[] = "%$search%";
  $params[] = "%$search%";
}
if ($mode_filter) {
  $where[] = "uc.education_mode_id = ?";
  $params[] = $mode_filter;
}

$sql = "SELECT uc.id, uc.academic_fees, uc.fees_discount, uc.course_rating, uc.created_at,
               u.name as u_name, u.display_name as u_disp,
               c.name as c_name, c.display_name as c_disp, c.course_level,
               m.mode_name
        FROM university_courses uc
        JOIN universities u ON uc.university_id = u.id
        JOIN courses c ON uc.course_id = c.id
        JOIN education_modes m ON uc.education_mode_id = m.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY uc.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mappings = $stmt->fetchAll();

$modes = $pdo->query("SELECT * FROM education_modes ORDER BY id")->fetchAll();

$active_page = 'mappings';
$page_title = 'Course Mappings';
$page_subtitle = 'Manage University and Course relationships';
$base_path = '..';
$logout_path = '../logout.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mappings — SODE AI Tools</title>
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
          <h3>Course Mappings</h3>
          <p><?= count($mappings) ?> record(s) found</p>
        </div>
        <a href="add.php" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Map Course
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
            <input type="text" name="search" placeholder="Search by University or Course…" value="<?= e($search) ?>">
          </div>
          <select name="mode" class="form-control" style="width:auto;min-width:160px;">
            <option value="">All Modes</option>
            <?php foreach($modes as $m): ?>
              <option value="<?= $m['id'] ?>" <?= $mode_filter == $m['id'] ? 'selected' : '' ?>><?= e($m['mode_name']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-secondary">Filter</button>
          <?php if ($search || $mode_filter): ?>
            <a href="index.php" class="btn btn-secondary">Clear</a>
          <?php endif; ?>
        </form>
      </div>

      <!-- Table -->
      <div class="panel">
        <table>
          <thead>
            <tr>
              <th style="width:50px;">#</th>
              <th>University</th>
              <th>Course</th>
              <th>Mode</th>
              <th>Fees / Rating</th>
              <th>Added</th>
              <th style="width:100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($mappings): ?>
              <?php foreach ($mappings as $i => $m): ?>
                <tr>
                  <td style="color:var(--text-s);"><?= $i + 1 ?></td>
                  <td>
                    <div class="cell-name"><?= e(get_display_name($m['u_name'], $m['u_disp'])) ?></div>
                  </td>
                  <td>
                    <div class="cell-name"><?= e(get_display_name($m['c_name'], $m['c_disp'])) ?></div>
                    <?php if ($m['course_level']): ?>
                      <div style="font-size:11px;color:var(--accent);"><?= e($m['course_level']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td><span class="badge" style="background:var(--surface-h);color:var(--text);border:1px solid var(--border);"><?= e($m['mode_name']) ?></span></td>
                  <td>
                    <div style="font-weight:600;font-size:13px;">
                      <?= $m['academic_fees'] ? '₹' . number_format($m['academic_fees'], 2) : '—' ?>
                    </div>
                    <div style="font-size:11px;color:var(--text-s);">
                       Rating: <?= $m['course_rating'] ? '⭐ ' . e($m['course_rating']) : '—' ?>
                    </div>
                  </td>
                  <td><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                  <td>
                    <div class="action-col">
                      <a href="edit.php?id=<?= $m['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                      </a>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete"
                          data-confirm="Delete mapping?">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                            <path d="M10 11v6M14 11v6" />
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                          </svg>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr class="empty-row">
                <td colspan="7" style="text-align: center; color: var(--text-s); padding: 3rem;">
                  <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                      stroke-linecap="round" style="margin-bottom: 1rem; opacity: 0.5;">
                      <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    <p>No mappings found. <a href="add.php" style="color:var(--accent);">Map a university to a course now →</a></p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>
