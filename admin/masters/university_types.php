<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

$errors = [];
$edit_item = null;

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $did = (int) $_POST['delete_id'];
  $used = $pdo->prepare("SELECT COUNT(*) FROM universities WHERE university_type_id=?");
  $used->execute([$did]);
  if ($used->fetchColumn() > 0) {
    set_flash('error', 'Cannot delete — type is assigned to one or more universities.');
  } else {
    $pdo->prepare("DELETE FROM university_types WHERE id=?")->execute([$did]);
    set_flash('success', 'University Type deleted.');
  }
  redirect(ADMIN_URL . '/masters/university_types.php');
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
  $name = trim($_POST['name'] ?? '');
  if (!$name) {
    $errors['add_name'] = 'Name is required.';
  } else {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM university_types WHERE type_name=?");
    $exists->execute([$name]);
    if ($exists->fetchColumn() > 0) {
      $errors['add_name'] = 'Already exists.';
    } else {
      $pdo->prepare("INSERT INTO university_types (type_name) VALUES(?)")->execute([$name]);
      set_flash('success', "Type '{$name}' added.");
      redirect(ADMIN_URL . '/masters/university_types.php');
    }
  }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
  $eid = (int) $_POST['edit_id'];
  $name = trim($_POST['name'] ?? '');
  if (!$name) {
    $errors['edit_name'] = 'Name is required.';
    $edit_item = ['id' => $eid, 'type_name' => ''];
  } else {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM university_types WHERE type_name=? AND id!=?");
    $exists->execute([$name, $eid]);
    if ($exists->fetchColumn() > 0) {
      $errors['edit_name'] = 'Already exists.';
      $edit_item = ['id' => $eid, 'type_name' => $name];
    } else {
      $pdo->prepare("UPDATE university_types SET type_name=? WHERE id=?")->execute([$name, $eid]);
      set_flash('success', "Type updated to '{$name}'.");
      redirect(ADMIN_URL . '/masters/university_types.php');
    }
  }
}

// Populate edit item if requested
if (isset($_GET['edit'])) {
  $stmt = $pdo->prepare("SELECT * FROM university_types WHERE id=?");
  $stmt->execute([(int) $_GET['edit']]);
  $edit_item = $stmt->fetch();
}

$types = $pdo->query("
    SELECT t.*, (SELECT COUNT(*) FROM universities u WHERE u.university_type_id = t.id) as used_count
    FROM university_types t
    ORDER BY t.type_name ASC
")->fetchAll();

$active_page = 'university_types';
$page_title = 'University Types';
$page_subtitle = 'Manage system-wide university types (e.g. Government, Private)';
$base_path = '../..';
$logout_path = '../../logout.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>University Types — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    .split-layout { display: flex; gap: 2rem; align-items: flex-start; }
    .split-form { flex: 0 0 350px; }
    .split-table { flex: 1; min-width: 0; }
    @media(max-width:800px) {
      .split-layout { flex-direction: column; }
      .split-form, .split-table { flex: 1 1 auto; width: 100%; }
    }
    .panel { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem; }
    .panel-header { font-weight:700; color:var(--text); margin-bottom:1.5rem; font-size:1.1rem; border-bottom:1px solid var(--border); padding-bottom:0.75rem; }
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
          <h3>University Types</h3>
          <p><?= count($types) ?> record(s) found</p>
        </div>
      </div>
      <div class="split-layout">
        <div class="split-form">
          <div class="panel">
            <?php if ($edit_item): ?>
              <div class="panel-header" style="color:var(--accent);">Edit Type</div>
              <form method="POST" action="university_types.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_id" value="<?= $edit_item['id'] ?>">
                <div class="form-group">
                  <label>Type Name <span class="required">*</span></label>
                  <input type="text" name="name" class="form-control" value="<?= e($edit_item['type_name'] ?? '') ?>">
                  <?php if (isset($errors['edit_name'])): ?><span class="helper-text" style="color:var(--danger);"><?= $errors['edit_name'] ?></span><?php endif; ?>
                </div>
                <div style="display:flex;gap:0.5rem;margin-top:1.5rem;">
                  <button type="submit" class="btn btn-primary" style="flex:1;">Update</button>
                  <a href="university_types.php" class="btn btn-secondary" style="flex:1;text-align:center;">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <div class="panel-header">Add New Type</div>
              <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                  <label>Type Name <span class="required">*</span></label>
                  <input type="text" name="name" class="form-control" placeholder="e.g. Deemed, Autonomous" value="<?= e($_POST['name'] ?? '') ?>">
                  <?php if (isset($errors['add_name'])): ?><span class="helper-text" style="color:var(--danger);"><?= $errors['add_name'] ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:0.5rem;">Add Type</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
        <div class="split-table">
          <div class="panel" style="padding:0;">
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th style="width:50px;">#</th>
                    <th>Type Name</th>
                    <th>Usage Count</th>
                    <th style="width:100px;text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($types): ?>
                    <?php foreach ($types as $i => $t): ?>
                      <tr <?= $edit_item && $edit_item['id'] == $t['id'] ? 'style="background:rgba(37,99,235,0.05);"' : '' ?>>
                        <td data-label="#"> <?= $i + 1 ?> </td>
                        <td data-label="Type Name" style="font-weight:600;color:var(--text);"><?= e($t['type_name']) ?></td>
                        <td data-label="Usage Count">
                          <?php if ($t['used_count'] > 0): ?>
                            <span class="badge" style="background:rgba(37,99,235,0.1);color:#2563eb;"><?= $t['used_count'] ?> Universities</span>
                          <?php else: ?>
                            <span class="badge">Unused</span>
                          <?php endif; ?>
                        </td>
                        <td data-label="Actions" style="text-align:right;">
                          <div class="action-col" style="justify-content:flex-end;">
                            <a href="?edit=<?= $t['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" style="display:inline;">
                              <input type="hidden" name="delete_id" value="<?= $t['id'] ?>">
                              <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete" <?= $t['used_count'] > 0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?> data-confirm="Delete '<?= e($t['type_name']) ?>'?">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="4" class="empty-state">No types found. Add your first university type.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  
  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>
</html>
