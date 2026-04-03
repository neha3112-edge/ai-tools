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

// ── DELETE ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $did = (int) $_POST['delete_id'];
  $used = $pdo->prepare("SELECT COUNT(*) FROM university_accreditations WHERE accreditation_id=?");
  $used->execute([$did]);
  if ($used->fetchColumn() > 0) {
    set_flash('error', 'Cannot delete — assigned to one or more universities.');
  } else {
    // Delete image file too
    $row = $pdo->prepare("SELECT image FROM accreditations WHERE id=?");
    $row->execute([$did]);
    $old_img = $row->fetchColumn();
    delete_file($old_img);
    $pdo->prepare("DELETE FROM accreditations WHERE id=?")->execute([$did]);
    set_flash('success', 'Accreditation deleted.');
  }
  redirect(ADMIN_URL . '/masters/accreditations.php');
}

// ── ADD ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
  $name = trim($_POST['name'] ?? '');
  if (!$name) {
    $errors['add_name'] = 'Name is required.';
  } else {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM accreditations WHERE name=?");
    $exists->execute([$name]);
    if ($exists->fetchColumn() > 0) {
      $errors['add_name'] = 'This accreditation already exists.';
    }
  }

  $img_path = null;
  if (empty($errors) && !empty($_FILES['image']['name'])) {
    $img_path = upload_file($_FILES['image'], 'accreditations', MAX_IMAGE_SIZE);
    if (!$img_path)
      $errors['add_image'] = 'Invalid image. JPG/PNG/WEBP under 2MB.';
  }

  if (empty($errors)) {
    $pdo->prepare("INSERT INTO accreditations (name, image) VALUES (?,?)")->execute([$name, $img_path]);
    set_flash('success', "Accreditation '{$name}' added.");
    redirect(ADMIN_URL . '/masters/accreditations.php');
  }
}

// ── EDIT ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
  $eid = (int) $_POST['edit_id'];
  $name = trim($_POST['name'] ?? '');

  // Fetch current record
  $cur_stmt = $pdo->prepare("SELECT * FROM accreditations WHERE id=?");
  $cur_stmt->execute([$eid]);
  $cur = $cur_stmt->fetch();

  if (!$name) {
    $errors['edit_name'] = 'Name is required.';
    $edit_item = $cur;
  } else {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM accreditations WHERE name=? AND id!=?");
    $exists->execute([$name, $eid]);
    if ($exists->fetchColumn() > 0) {
      $errors['edit_name'] = 'This name already exists.';
      $edit_item = $cur;
    }
  }

  // Handle image
  $img_path = $cur['image'] ?? null;
  if (empty($errors)) {
    // Remove image?
    if (!empty($_POST['remove_image'])) {
      delete_file($img_path);
      $img_path = null;
    }
    // New image upload?
    if (!empty($_FILES['image']['name'])) {
      $uploaded = upload_file($_FILES['image'], 'accreditations', MAX_IMAGE_SIZE);
      if ($uploaded) {
        delete_file($img_path);
        $img_path = $uploaded;
      } else {
        $errors['edit_image'] = 'Invalid image. JPG/PNG/WEBP under 2MB.';
        $edit_item = $cur;
      }
    }
  }

  if (empty($errors)) {
    $pdo->prepare("UPDATE accreditations SET name=?, image=? WHERE id=?")
      ->execute([$name, $img_path, $eid]);
    set_flash('success', "Accreditation updated to '{$name}'.");
    redirect(ADMIN_URL . '/masters/accreditations.php');
  }
}

// ── OPEN EDIT from GET ──
if (isset($_GET['edit'])) {
  $stmt = $pdo->prepare("SELECT * FROM accreditations WHERE id=?");
  $stmt->execute([(int) $_GET['edit']]);
  $edit_item = $stmt->fetch() ?: null;
}

// ── FETCH ALL ──
$all = $pdo->query(
  "SELECT a.*, COUNT(ua.university_id) as usage_count
     FROM accreditations a
     LEFT JOIN university_accreditations ua ON ua.accreditation_id = a.id
     GROUP BY a.id ORDER BY a.name ASC"
)->fetchAll();

$active_page = 'accreditations';
$page_title = 'Accreditations';
$page_subtitle = 'Manage accreditation & approval badges';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accreditations — SODE AI Tools</title>
<?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
<style>
  .master-wrap {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 1.5rem;
    align-items: start;
  }
  @media(max-width: 900px) { .master-wrap { grid-template-columns: 1fr; } }

  .usage-badge {
    display: inline-block; padding: 2px 8px; border-radius: 4px;
    font-size: 11px; font-weight: 600;
    background: rgba(79,110,247,0.12); color: var(--accent-h);
  }
  body.light .usage-badge { background: rgba(79,110,247,0.1); color: #3a57e8; }

  .edit-row td { background: rgba(79,110,247,0.05) !important; }

  /* Accr logo thumbnail in table */
  .accr-thumb {
    width: 36px; height: 36px; border-radius: 6px;
    object-fit: contain; border: 1px solid var(--border);
    background: var(--surface-h); padding: 2px;
  }
  .accr-thumb-placeholder {
    width: 36px; height: 36px; border-radius: 6px;
    border: 1px dashed var(--border);
    background: var(--surface-h);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-s); font-size: 10px;
  }

  /* Image upload preview in form */
  .img-upload-row { display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap; }
  .img-upload-preview {
    width: 72px; height: 72px; border-radius: var(--radius-sm);
    border: 1px dashed var(--border); overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface-h); flex-shrink: 0;
  }
  .img-upload-preview img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
  .remove-img-label {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: var(--danger); cursor: pointer;
    margin-top: 6px;
  }
  .remove-img-label input { accent-color: var(--danger); }
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
        <h3>Accreditations &amp; Approvals</h3>
        <p>These appear as selection options when adding/editing universities</p>
      </div>
    </div>

    <div class="master-wrap">

      <!-- ── LEFT: Form ── -->
      <div>
        <?php if (!$edit_item): ?>
          <!-- ADD FORM -->
          <div class="section-title">Add New Accreditation</div>
          <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="add">

              <div class="form-group" style="margin-bottom:1rem;">
                <label>Name <span class="req">*</span></label>
                <input type="text" name="name" class="form-control"
                       placeholder="e.g. UGC, NAAC, AICTE"
                       value="<?= e($_POST['name'] ?? '') ?>" autofocus>
                <?php if (isset($errors['add_name'])): ?>
                    <span class="form-hint" style="color:var(--danger)"><?= e($errors['add_name']) ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group" style="margin-bottom:1.25rem;">
                <label>Badge / Logo Image</label>
                <div class="img-upload-row">
                  <div class="img-upload-preview" id="add_preview">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".3"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                  <div style="flex:1;min-width:120px;">
                    <input type="file" id="add_image" name="image" accept="image/*" class="form-control">
                    <span class="form-hint">JPG, PNG, WEBP — max 2MB</span>
                    <?php if (isset($errors['add_image'])): ?>
                        <span class="form-hint" style="color:var(--danger)"><?= e($errors['add_image']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-primary" style="width:100%;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Accreditation
              </button>
            </form>
          </div>

        <?php else: ?>
          <!-- EDIT FORM -->
          <div class="section-title">Edit Accreditation</div>
          <div class="form-card" style="border-color:var(--accent);">
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="edit_id" value="<?= (int) $edit_item['id'] ?>">

              <div class="form-group" style="margin-bottom:1rem;">
                <label>Name <span class="req">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="<?= e($edit_item['name']) ?>" autofocus>
                <?php if (isset($errors['edit_name'])): ?>
                    <span class="form-hint" style="color:var(--danger)"><?= e($errors['edit_name']) ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group" style="margin-bottom:1.25rem;">
                <label>Badge / Logo Image</label>
                <div class="img-upload-row">
                  <div class="img-upload-preview" id="edit_preview">
                    <?php if (!empty($edit_item['image'])): ?>
                        <img id="edit_preview_img" src="<?= e($edit_item['image']) ?>" alt="">
                    <?php else: ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".3"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <?php endif; ?>
                  </div>
                  <div style="flex:1;min-width:120px;">
                    <input type="file" id="edit_image" name="image" accept="image/*" class="form-control">
                    <span class="form-hint">Upload new to replace. Max 2MB.</span>
                    <?php if (isset($errors['edit_image'])): ?>
                        <span class="form-hint" style="color:var(--danger)"><?= e($errors['edit_image']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($edit_item['image'])): ?>
                        <label class="remove-img-label" style="margin-top:8px;">
                          <input type="checkbox" name="remove_image" value="1"> Remove current image
                        </label>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div style="display:flex;gap:.75rem;">
                <a href="<?= ADMIN_URL ?>/masters/accreditations.php" class="btn btn-secondary" style="flex:1;justify-content:center;">
                  Cancel
                </a>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Update
                </button>
              </div>
            </form>
          </div>
        <?php endif; ?>

        <!-- Info note -->
        <div style="margin-top:1rem;padding:.875rem;background:var(--surface-h);border:1px solid var(--border);border-radius:var(--radius-sm);">
          <p style="font-size:12px;color:var(--text-s);line-height:1.6;">
            <strong style="color:var(--text-m);">Note:</strong>
            Accreditations assigned to universities cannot be deleted.
            Unassign them from universities first.
          </p>
        </div>
      </div>

      <!-- ── RIGHT: Table ── -->
      <div>
        <div class="section-title"><?= count($all) ?> Accreditations</div>
        <div class="panel">
          <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th style="width:44px;">#</th>
                <th style="width:52px;">Logo</th>
                <th>Name</th>
                <th>Universities</th>
                <th style="width:110px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all): ?>
                  <?php foreach ($all as $i => $a): ?>
                    <tr class="<?= ($edit_item && $edit_item['id'] == $a['id']) ? 'edit-row' : '' ?>">
                      <td data-label="#"> <?= $i + 1 ?> </td>
                      <td data-label="Logo">
                        <?php if (!empty($a['image'])): ?>
                            <img src="<?= e($a['image']) ?>" class="accr-thumb" alt="<?= e($a['name']) ?>">
                        <?php else: ?>
                            <div class="accr-thumb-placeholder" title="No image">—</div>
                        <?php endif; ?>
                      </td>
                      <td data-label="Name">
                        <span class="cell-name"><?= e($a['name']) ?></span>
                        <?php if ($edit_item && $edit_item['id'] == $a['id']): ?>
                            <span style="font-size:10px;color:var(--accent);margin-left:6px;font-weight:600;">EDITING</span>
                        <?php endif; ?>
                      </td>
                      <td data-label="Universities">
                        <?php if ($a['usage_count'] > 0): ?>
                            <span class="usage-badge"><?= $a['usage_count'] ?> uni<?= $a['usage_count'] > 1 ? 's' : '' ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-s);font-size:12px;">None</span>
                        <?php endif; ?>
                      </td>
                      <td data-label="Actions">
                        <div class="action-col">
                          <a href="?edit=<?= $a['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                          </a>
                          <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                            <button type="submit"
                              class="btn btn-danger btn-sm btn-icon"
                              title="<?= $a['usage_count'] > 0 ? 'Remove from universities first' : 'Delete' ?>"
                              <?= $a['usage_count'] > 0 ? 'disabled' : '' ?>
                              data-confirm="Delete '<?= e($a['name']) ?>'? This will also remove its image.">
                              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="5">
                    <div class="empty-state">
                      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                      <p>No accreditations yet. Add one on the left.</p>
                    </div>
                  </td></tr>
              <?php endif; ?>
            </tbody>
          </table>
          </div>
        </div>
      </div>

    </div><!-- /master-wrap -->
  </div>
</main>

<?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
<script>
// Image preview — Add form
bindImagePreview('add_image', 'add_preview');

// Image preview — Edit form
(function() {
  const inp = document.getElementById('edit_image');
  const pre = document.getElementById('edit_preview');
  if (!inp || !pre) return;
  inp.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      pre.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain;padding:4px;">';
    };
    reader.readAsDataURL(file);
  });

  // Remove image checkbox — grey out preview
  const removeChk = document.querySelector('input[name="remove_image"]');
  if (removeChk) {
    removeChk.addEventListener('change', function() {
      if (pre) pre.style.opacity = this.checked ? '0.3' : '1';
    });
  }
})();
</script>
</body>
</html>