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
  $stmt = $pdo->prepare("UPDATE courses SET is_active=0 WHERE id=?");
  $stmt->execute([(int) $_POST['delete_id']]);
  set_flash('success', 'Course deleted successfully.');
  redirect(ADMIN_URL . '/courses/index.php');
}

// Search + filter
$search = trim($_GET['search'] ?? '');
$level_filter = $_GET['level'] ?? '';

$where = ["is_active = 1"];
$params = [];

if ($search) {
  $where[] = "(name LIKE ? OR display_name LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}
if ($level_filter) {
  $where[] = "course_level = ?";
  $params[] = $level_filter;
}

$sql = "SELECT id, name, display_name, course_level, course_duration, created_at
        FROM courses
        WHERE " . implode(' AND ', $where) . "
        ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$active_page = 'courses';
$page_title = 'Courses';
$page_subtitle = 'Manage all master courses';
$base_path = '..';
$logout_path = '../logout.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Courses — SODE AI Tools</title>
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
          <h3>All Courses</h3>
          <p><?= count($courses) ?> record(s) found</p>
        </div>
        <a href="add.php" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Add Course
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
            <input type="text" name="search" placeholder="Search by course name…" value="<?= e($search) ?>">
          </div>
          <select name="level" class="form-control" style="width:auto;min-width:160px;">
            <option value="">All Levels</option>
            <option value="UG" <?= $level_filter === 'UG' ? 'selected' : '' ?>>UG - Undergraduate</option>
            <option value="PG" <?= $level_filter === 'PG' ? 'selected' : '' ?>>PG - Postgraduate</option>
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
          <table>
          <thead>
            <tr>
              <th style="width:50px;">#</th>
              <th>Course Name</th>
              <th>Level</th>
              <th>Duration</th>
              <th>Added</th>
              <th style="width:150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($courses): ?>
              <?php foreach ($courses as $i => $c): ?>
                <tr>
                  <td data-label="#"> <?= $i + 1 ?> </td>
                  <td data-label="Course Name">
                    <div>
                      <div class="cell-name"><?= e(get_display_name($c['name'], $c['display_name'])) ?></div>
                      <?php if ($c['display_name'] && $c['display_name'] !== $c['name']): ?>
                        <div style="font-size:11px;color:var(--text-s);"><?= e($c['name']) ?></div>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td data-label="Level">
                    <?php if ($c['course_level']): ?>
                      <?php $lc = strtolower($c['course_level']); ?>
                      <span class="badge" style="background:rgba(<?= $c['course_level'] === 'UG' ? '79,110,247' : '124,58,237' ?>,0.15);color:<?= $c['course_level'] === 'UG' ? '#818cf8' : '#a78bfa' ?>;">
                        <?= e($c['course_level']) ?>
                      </span>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>
                  <td data-label="Duration"><?= e($c['course_duration'] ?: '—') ?></td>
                  <td data-label="Added On"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                  <td data-label="Actions">
                    <div class="action-col">
                      <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="View Details">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" /><circle cx="12" cy="12" r="3" /></svg>
                      </a>
                      <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                      </a>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete" data-confirm="Delete '<?= e($c['name']) ?>'? This cannot be undone.">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /><path d="M10 11v6M14 11v6" /><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" /></svg>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr class="empty-row">
                <td colspan="6" style="text-align: center; color: var(--text-s); padding: 3rem;">
                  <div class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                      stroke-linecap="round" style="margin-bottom: 1rem; opacity: 0.5;">
                      <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                      <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                    <p>No courses found. <a href="add.php" style="color:var(--accent);">Add one now →</a></p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>
