<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME); session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

$errors = [];
$edit_item = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $did = (int)$_POST['delete_id'];
    $used = $pdo->prepare("SELECT COUNT(*) FROM university_education_modes WHERE education_mode_id=?");
    $used->execute([$did]);
    if ($used->fetchColumn() > 0) {
        set_flash('error','Cannot delete — mode is assigned to one or more universities.');
    } else {
        $pdo->prepare("DELETE FROM education_modes WHERE id=?")->execute([$did]);
        set_flash('success','Education mode deleted.');
    }
    redirect(ADMIN_URL.'/masters/modes.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    if (!$name) { $errors['add_name'] = 'Name is required.'; }
    else {
        $exists = $pdo->prepare("SELECT COUNT(*) FROM education_modes WHERE mode_name=?");
        $exists->execute([$name]);
        if ($exists->fetchColumn() > 0) { $errors['add_name'] = 'Already exists.'; }
        else {
            $pdo->prepare("INSERT INTO education_modes (mode_name) VALUES(?)")->execute([$name]);
            set_flash('success',"Mode '{$name}' added.");
            redirect(ADMIN_URL.'/masters/modes.php');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $eid = (int)$_POST['edit_id'];
    $name = trim($_POST['name'] ?? '');
    if (!$name) { $errors['edit_name'] = 'Name is required.'; $edit_item = ['id'=>$eid,'mode_name'=>''];  }
    else {
        $exists = $pdo->prepare("SELECT COUNT(*) FROM education_modes WHERE mode_name=? AND id!=?");
        $exists->execute([$name,$eid]);
        if ($exists->fetchColumn() > 0) { $errors['edit_name'] = 'Already exists.'; $edit_item = ['id'=>$eid,'mode_name'=>$name]; }
        else {
            $pdo->prepare("UPDATE education_modes SET mode_name=? WHERE id=?")->execute([$name,$eid]);
            set_flash('success',"Mode updated to '{$name}'.");
            redirect(ADMIN_URL.'/masters/modes.php');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM education_modes WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_item = $stmt->fetch() ?: null;
}

$all = $pdo->query("SELECT m.*, COUNT(um.university_id) as usage_count
    FROM education_modes m
    LEFT JOIN university_education_modes um ON um.education_mode_id=m.id
    GROUP BY m.id ORDER BY m.mode_name ASC")->fetchAll();

$active_page   = 'modes';
$page_title    = 'Education Modes';
$page_subtitle = 'Distance, Online, Distance & Online etc.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Education Modes — SODE AI Tools</title>
<?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
<style>
  .master-wrap { display:grid; grid-template-columns:360px 1fr; gap:1.5rem; align-items:start; }
  @media(max-width:768px) { .master-wrap { grid-template-columns:1fr; } }
  .usage-badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; background:rgba(79,110,247,0.12); color:var(--accent-h); }
  body.light .usage-badge { background:rgba(79,110,247,0.1); color:#3a57e8; }
  .edit-row { background:rgba(79,110,247,0.06) !important; }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main">
  <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
  <div class="content">
    <?= render_flash() ?>
    <div class="page-header">
      <div><h3>Education Modes</h3><p>Manage available education modes for universities</p></div>
    </div>
    <div class="master-wrap">
      <div>
        <?php if (!$edit_item): ?>
        <div class="section-title">Add New</div>
        <div class="form-card">
          <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group" style="margin-bottom:1rem;">
              <label>Mode Name <span class="req">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Distance & Online" value="<?= e($_POST['name'] ?? '') ?>" autofocus>
              <?php if (isset($errors['add_name'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['add_name']) ?></span><?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Mode
            </button>
          </form>
        </div>
        <?php else: ?>
        <div class="section-title">Edit Mode</div>
        <div class="form-card" style="border-color:var(--accent);">
          <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="edit_id" value="<?= (int)$edit_item['id'] ?>">
            <div class="form-group" style="margin-bottom:1rem;">
              <label>Mode Name <span class="req">*</span></label>
              <input type="text" name="name" class="form-control" value="<?= e($edit_item['mode_name']) ?>" autofocus>
              <?php if (isset($errors['edit_name'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['edit_name']) ?></span><?php endif; ?>
            </div>
            <div style="display:flex;gap:.75rem;">
              <a href="<?= ADMIN_URL ?>/masters/modes.php" class="btn btn-secondary" style="flex:1;justify-content:center;">Cancel</a>
              <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Update</button>
            </div>
          </form>
        </div>
        <?php endif; ?>
      </div>
      <div>
        <div class="section-title"><?= count($all) ?> Modes</div>
        <div class="panel">
          <table>
            <thead><tr><th>#</th><th>Mode Name</th><th>Universities</th><th style="width:100px;">Actions</th></tr></thead>
            <tbody>
              <?php if ($all): foreach ($all as $i => $m): ?>
              <tr class="<?= ($edit_item && $edit_item['id']==$m['id']) ? 'edit-row' : '' ?>">
                <td style="color:var(--text-s);"><?= $i+1 ?></td>
                <td><span class="cell-name"><?= e($m['mode_name']) ?></span></td>
                <td><?= $m['usage_count'] > 0 ? '<span class="usage-badge">'.$m['usage_count'].' uni'.($m['usage_count']>1?'s':'').'</span>' : '<span style="color:var(--text-s);font-size:12px;">None</span>' ?></td>
                <td>
                  <div class="action-col">
                    <a href="?edit=<?= $m['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete"
                        data-confirm="Delete '<?= e($m['mode_name']) ?>'?"
                        <?= $m['usage_count'] > 0 ? 'disabled' : '' ?>>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="4"><div class="empty-state"><p>No modes yet.</p></div></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>
</html>
