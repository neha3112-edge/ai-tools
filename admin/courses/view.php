<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
  set_flash('error', 'Invalid course.');
  redirect(ADMIN_URL . '/courses/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM courses WHERE id=?");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
  set_flash('error', 'Course not found.');
  redirect(ADMIN_URL . '/courses/index.php');
}

$active_page = 'courses';
$page_title = 'View Course';
$page_subtitle = get_display_name($course['name'], $course['display_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Course — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    .detail-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 1.5rem;
    }
    .detail-group {
      margin-bottom: 1.25rem;
    }
    .detail-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-s);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }
    .detail-value {
      font-size: 15px;
      color: var(--text);
    }
    .detail-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>

      <div class="page-header" style="flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3><?= e(get_display_name($course['name'], $course['display_name'])) ?></h3>
          <p>
            ID #<?= $id ?> &middot; 
            <span class="<?= $course['is_active'] ? 'text-success' : 'text-danger' ?>">
              <?= $course['is_active'] ? 'Active' : 'Inactive' ?>
            </span>
          </p>
        </div>
        <div style="display:flex;gap:.75rem; flex-wrap: wrap;">
          <a href="<?= ADMIN_URL ?>/courses/index.php" class="btn btn-secondary">Back</a>
          <a href="<?= ADMIN_URL ?>/courses/edit.php?id=<?= $id ?>" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Course
          </a>
        </div>
      </div>

      <div class="detail-card">
        <div class="section-title">Identity &amp; Classification</div>
        <div class="detail-grid">
          <div class="detail-group">
            <div class="detail-label">Master Name</div>
            <div class="detail-value"><?= e($course['name']) ?></div>
          </div>
          <div class="detail-group">
            <div class="detail-label">Display Name</div>
            <div class="detail-value"><?= e($course['display_name'] ?: '—') ?></div>
          </div>
          <div class="detail-group">
            <div class="detail-label">Slug (URL)</div>
            <div class="detail-value"><code style="color:var(--accent);"><?= e($course['slug']) ?></code></div>
          </div>
          <div class="detail-group">
            <div class="detail-label">Course Level</div>
            <div class="detail-value">
              <?php if ($course['course_level']): ?>
                <span class="badge" style="background:rgba(<?= $course['course_level'] === 'UG' ? '79,110,247' : '124,58,237' ?>,0.15);color:<?= $course['course_level'] === 'UG' ? '#818cf8' : '#a78bfa' ?>;">
                  <?= e($course['course_level']) ?>
                </span>
              <?php else: ?>
                —
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="detail-card">
        <div class="section-title">Course Information</div>
        <div class="detail-grid">
          <div class="detail-group">
            <div class="detail-label">Course Duration</div>
            <div class="detail-value"><?= e($course['course_duration'] ?: '—') ?></div>
          </div>
          <div class="detail-group" style="grid-column: 1 / -1;">
            <div class="detail-label">Program Eligibility</div>
            <div class="detail-value" style="white-space: pre-wrap; line-height: 1.6;"><?= e($course['program_eligibility'] ?: '—') ?></div>
          </div>
        </div>
      </div>

      <div class="detail-card">
        <div class="section-title">System Info</div>
        <div class="detail-grid">
          <div class="detail-group">
            <div class="detail-label">Added On</div>
            <div class="detail-value"><?= date('d F Y, g:i A', strtotime($course['created_at'])) ?></div>
          </div>
          <div class="detail-group">
            <div class="detail-label">Last Updated</div>
            <div class="detail-value"><?= date('d F Y, g:i A', strtotime($course['updated_at'])) ?></div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>
</html>
