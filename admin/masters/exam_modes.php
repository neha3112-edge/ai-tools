<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_permission('exam_modes.view');

$errors = [];
$edit_item = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  if (!has_team_action('Delete')) {
    set_flash('error', 'You do not have permission to delete records.');
    redirect(ADMIN_URL . '/masters/exam_modes.php');
  }
  $did = (int) $_POST['delete_id'];
  $used = $pdo->prepare("SELECT COUNT(*) FROM university_exam_modes WHERE exam_mode_id=?");
  $used->execute([$did]);
  if ($used->fetchColumn() > 0) {
    set_flash('error', 'Cannot delete — mode is assigned to universities.');
  } else {
    $pdo->prepare("DELETE FROM exam_modes WHERE id=?")->execute([$did]);
    set_flash('success', 'Exam mode deleted.');
  }
  redirect(ADMIN_URL . '/masters/exam_modes.php');
}

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_delete_ids'])) {
  if (!has_team_action('Delete')) {
    set_flash('error', 'You do not have permission to delete records.');
    redirect(ADMIN_URL . '/masters/exam_modes.php');
  }
  $ids = array_filter(array_map('intval', explode(',', $_POST['bulk_delete_ids'])));
  if ($ids) {
    $deleted = 0; $skipped = 0;
    foreach ($ids as $did) {
      $used = $pdo->prepare("SELECT COUNT(*) FROM university_exam_modes WHERE exam_mode_id=?");
      $used->execute([$did]);
      if ($used->fetchColumn() > 0) { $skipped++; continue; }
      $pdo->prepare("DELETE FROM exam_modes WHERE id=?")->execute([$did]);
      $deleted++;
    }
    if ($deleted > 0) set_flash('success', $deleted . ' mode(s) deleted.' . ($skipped ? " $skipped skipped (in use)." : ''));
    elseif ($skipped > 0) set_flash('error', 'All selected modes are assigned to universities. Cannot delete.');
  }
  redirect(ADMIN_URL . '/masters/exam_modes.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
  if (!has_team_action('Create')) {
    set_flash('error', 'You do not have permission to add master items.');
    redirect(ADMIN_URL . '/masters/exam_modes.php');
  }
  $name = trim($_POST['name'] ?? '');
  if (!$name) {
    $errors['add_name'] = 'Name is required.';
  } else {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM exam_modes WHERE mode_name=?");
    $exists->execute([$name]);
    if ($exists->fetchColumn() > 0) {
      $errors['add_name'] = 'Already exists.';
    } else {
      $pdo->prepare("INSERT INTO exam_modes (mode_name) VALUES(?)")->execute([$name]);
      set_flash('success', "'{$name}' added.");
      redirect(ADMIN_URL . '/masters/exam_modes.php');
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
  if (!has_team_action('Update')) {
    set_flash('error', 'You do not have permission to edit master items.');
    redirect(ADMIN_URL . '/masters/exam_modes.php');
  }
  $eid = (int) $_POST['edit_id'];
  $name = trim($_POST['name'] ?? '');
  if (!$name) {
    $errors['edit_name'] = 'Name is required.';
    $edit_item = ['id' => $eid, 'mode_name' => ''];
  } else {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM exam_modes WHERE mode_name=? AND id!=?");
    $exists->execute([$name, $eid]);
    if ($exists->fetchColumn() > 0) {
      $errors['edit_name'] = 'Already exists.';
      $edit_item = ['id' => $eid, 'mode_name' => $name];
    } else {
      $pdo->prepare("UPDATE exam_modes SET mode_name=? WHERE id=?")->execute([$name, $eid]);
      set_flash('success', "Updated to '{$name}'.");
      redirect(ADMIN_URL . '/masters/exam_modes.php');
    }
  }
}

if (isset($_GET['edit'])) {
  if (!has_team_action('Update')) {
    set_flash('error', 'You do not have permission to edit master items.');
    redirect(ADMIN_URL . '/masters/exam_modes.php');
  }
  $stmt = $pdo->prepare("SELECT * FROM exam_modes WHERE id=?");
  $stmt->execute([(int) $_GET['edit']]);
  $edit_item = $stmt->fetch() ?: null;
}

// Determine pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = (int)(($page - 1) * $limit);

$total_count = (int)$pdo->query("SELECT COUNT(*) FROM exam_modes")->fetchColumn();
$total_pages = ceil($total_count / $limit);

$stmt = $pdo->prepare("SELECT m.*, COUNT(um.university_id) as usage_count
    FROM exam_modes m LEFT JOIN university_exam_modes um ON um.exam_mode_id=m.id
    GROUP BY m.id ORDER BY m.mode_name ASC
    LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$all = $stmt->fetchAll();

$active_page = 'exam_modes';
$page_title = 'Exam Modes';
$page_subtitle = 'Online, Offline, Proctored etc.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exam Modes — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    .master-wrap {
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 1.5rem;
      align-items: start;
    }

    @media(max-width:768px) {
      .master-wrap {
        grid-template-columns: 1fr;
      }
    }

    .usage-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
      background: rgba(79, 110, 247, 0.12);
      color: var(--accent-h);
    }
    .ba-trigger-cell { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .ba-manage-btn { font-size:11px !important; gap:5px; }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
    <div class="content">
      <?= render_flash() ?>
      <div class="page-header">
        <div>
          <h3>Exam Modes</h3>
          <p>Manage available exam modes for universities</p>
        </div>
      </div>
      <?php 
      $can_create = has_team_action('Create');
      $can_update = has_team_action('Update');
      ?>
      <div class="master-wrap" style="<?= (!$can_create && !$can_update) ? 'grid-template-columns: 1fr;' : '' ?>">
        <?php if ($can_create || $can_update): ?>
        <div>
          <?php if (!$edit_item && $can_create): ?>
            <div class="section-title">Add New</div>
            <div class="form-card">
              <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group" style="margin-bottom:1rem;">
                  <label>Mode Name <span class="req">*</span></label>
                  <input type="text" name="name" class="form-control" placeholder="e.g. Proctored Online"
                    value="<?= e($_POST['name'] ?? '') ?>" autofocus>
                  <?php if (isset($errors['add_name'])): ?><span class="form-hint"
                      style="color:var(--danger)"><?= e($errors['add_name']) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Add Exam Mode</button>
              </form>
            </div>
          <?php elseif ($edit_item && $can_update): ?>
            <div class="section-title">Edit Mode</div>
            <div class="form-card" style="border-color:var(--accent);">
              <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_id" value="<?= (int) $edit_item['id'] ?>">
                <div class="form-group" style="margin-bottom:1rem;">
                  <label>Mode Name <span class="req">*</span></label>
                  <input type="text" name="name" class="form-control" value="<?= e($edit_item['mode_name']) ?>" autofocus>
                  <?php if (isset($errors['edit_name'])): ?><span class="form-hint"
                      style="color:var(--danger)"><?= e($errors['edit_name']) ?></span><?php endif; ?>
                </div>
                <div style="display:flex;gap:.75rem;">
                  <a href="<?= ADMIN_URL ?>/masters/exam_modes.php" class="btn btn-secondary"
                    style="flex:1;justify-content:center;">Cancel</a>
                  <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Update</button>
                </div>
              </form>
            </div>
          <?php else: ?>
            <div class="section-title">Access Restricted</div>
            <div class="form-card">You do not have permission to perform this action.</div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <div>
          <div class="section-title"><?= count($all) ?> Exam Modes</div>
          <div class="panel">
            <table>
              <thead>
                <tr>
                  <?php if (has_team_action('Delete')): ?>
                  <th style="width:40px;"><input type="checkbox" class="bulk-cb" id="bulkSelectAll" title="Select All"></th>
                  <?php endif; ?>
                  <th>#</th>
                  <th>Mode Name</th>
                  <th>Universities</th>
                  <th style="width:100px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($all):
                  foreach ($all as $i => $m): ?>
                    <tr data-ba-row-id="<?= $m['id'] ?>" data-ba-module="exam_modes">
                      <?php if (has_team_action('Delete')): ?>
                      <td><input type="checkbox" class="bulk-cb bulk-row-cb" value="<?= $m['id'] ?>"></td>
                      <?php endif; ?>
                      <td style="color:var(--text-s);"><?= $i + 1 ?></td>
                      <td><span class="cell-name"><?= e($m['mode_name']) ?></span></td>
                      <td>
                        <div class="ba-trigger-cell">
                          <?php if ($m['usage_count'] > 0): ?>
                            <span class="usage-badge ba-usage-badge"><?= $m['usage_count'] ?> uni<?= $m['usage_count'] > 1 ? 's' : '' ?></span>
                          <?php else: ?>
                            <span style="color:var(--text-s);font-size:12px;" class="ba-none-text">None</span>
                          <?php endif; ?>
                          <?php if (has_team_action('Update')): ?>
                          <button type="button" class="btn btn-secondary btn-sm ba-manage-btn"
                            onclick="openBulkModal('exam_modes', <?= $m['id'] ?>, <?= htmlspecialchars(json_encode($m['mode_name']), ENT_QUOTES) ?>)"
                            title="Manage universities">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Manage
                          </button>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td>
                        <div class="action-col">
                          <?php if (has_team_action('Update')): ?>
                          <a href="?edit=<?= $m['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2" stroke-linecap="round">
                              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                          </a>
                          <?php endif; ?>
                          <?php if (has_team_action('Delete')): ?>
                          <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
                            <button type="button" class="btn btn-danger btn-sm btn-icon" title="Delete"
                              data-confirm="Delete '<?= e($m['mode_name']) ?>'?">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                <path d="M10 11v6M14 11v6" />
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                              </svg>
                            </button>
                          </form>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; else: ?>
                  <tr>
                    <td colspan="<?= has_team_action('Delete') ? 5 : 4 ?>">
                      <div class="empty-state">
                        <p>No exam modes yet.</p>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <?= render_pagination($total_count, 10, $page) ?>
        </div>
      </div>
    </div>

    <?php if (has_team_action('Delete')): ?>
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
    <?php endif; ?>
  </main>
  <?php require_once __DIR__ . '/../includes/bulk_assoc_modal.php'; ?>
  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>