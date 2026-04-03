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
  set_flash('error', 'Invalid university.');
  redirect(ADMIN_URL . '/universities/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM universities WHERE id=? AND is_active=1");
$stmt->execute([$id]);
$uni = $stmt->fetch();
if (!$uni) {
  set_flash('error', 'University not found.');
  redirect(ADMIN_URL . '/universities/index.php');
}

// ── QUICK ADD ACCREDITATION (AJAX) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'quick_add_accr') {
  header('Content-Type: application/json');
  $name = trim($_POST['accr_name'] ?? '');
  if (!$name) {
    echo json_encode(['success' => false, 'error' => 'Name is required.']);
    exit;
  }
  $exists = $pdo->prepare("SELECT COUNT(*) FROM accreditations WHERE name=?");
  $exists->execute([$name]);
  if ($exists->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'error' => 'Already exists.']);
    exit;
  }

  $img_path = null;
  if (!empty($_FILES['accr_image']['name'])) {
    $img_path = upload_file($_FILES['accr_image'], 'accreditations', MAX_IMAGE_SIZE);
  }
  $pdo->prepare("INSERT INTO accreditations (name,image) VALUES(?,?)")->execute([$name, $img_path]);
  $new_id = $pdo->lastInsertId();
  echo json_encode(['success' => true, 'id' => $new_id, 'name' => $name, 'image' => $img_path]);
  exit;
}

// ── QUICK EDIT ACCREDITATION (AJAX) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'quick_edit_accr') {
  header('Content-Type: application/json');
  $aid = (int) ($_POST['accr_id'] ?? 0);
  $name = trim($_POST['accr_name'] ?? '');
  if (!$aid || !$name) {
    echo json_encode(['success' => false, 'error' => 'Invalid data.']);
    exit;
  }

  $exists = $pdo->prepare("SELECT COUNT(*) FROM accreditations WHERE name=? AND id!=?");
  $exists->execute([$name, $aid]);
  if ($exists->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'error' => 'Name already exists.']);
    exit;
  }

  $cur_img_stmt = $pdo->prepare("SELECT image FROM accreditations WHERE id=?");
  $cur_img_stmt->execute([$aid]);
  $cur_img = $cur_img_stmt->fetchColumn();

  $img_path = $cur_img;
  if (!empty($_POST['remove_image'])) {
    delete_file($img_path);
    $img_path = null;
  }
  if (!empty($_FILES['accr_image']['name'])) {
    $uploaded = upload_file($_FILES['accr_image'], 'accreditations', MAX_IMAGE_SIZE);
    if ($uploaded) {
      delete_file($img_path);
      $img_path = $uploaded;
    }
  }

  $pdo->prepare("UPDATE accreditations SET name=?,image=? WHERE id=?")->execute([$name, $img_path, $aid]);
  echo json_encode(['success' => true, 'id' => $aid, 'name' => $name, 'image' => $img_path]);
  exit;
}

// ── FETCH LOOKUPS ──
$edu_modes = $pdo->query("SELECT * FROM education_modes ORDER BY id")->fetchAll();
$exam_modes_all = $pdo->query("SELECT * FROM exam_modes ORDER BY id")->fetchAll();
$accreditations = $pdo->query("SELECT * FROM accreditations ORDER BY name")->fetchAll();

$sel_edu = $pdo->prepare("SELECT education_mode_id FROM university_education_modes WHERE university_id=?");
$sel_edu->execute([$id]);
$cur_edu = array_column($sel_edu->fetchAll(), 'education_mode_id');

$sel_exam = $pdo->prepare("SELECT exam_mode_id FROM university_exam_modes WHERE university_id=?");
$sel_exam->execute([$id]);
$cur_exam = array_column($sel_exam->fetchAll(), 'exam_mode_id');

$sel_accr = $pdo->prepare("SELECT accreditation_id FROM university_accreditations WHERE university_id=?");
$sel_accr->execute([$id]);
$cur_accr = array_column($sel_accr->fetchAll(), 'accreditation_id');

// ── MAIN SAVE ──
$errors = [];
$old = $uni;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($_POST['action'] ?? '', ['quick_add_accr', 'quick_edit_accr'])) {
  $old = $_POST;
  $name = trim($_POST['name'] ?? '');
  if (!$name)
    $errors['name'] = 'University name is required.';

  $display_name = trim($_POST['display_name'] ?? '') ?: null;
  $slug_input = trim($_POST['slug'] ?? '');
  $final_slug = $slug_input ? make_slug($slug_input) : make_slug($name);

  if ($name && !is_slug_unique($pdo, 'universities', $final_slug, $id))
    $errors['slug'] = 'This slug is already taken.';

  $year_est = trim($_POST['year_of_establishment'] ?? '');
  if ($year_est !== '' && (!ctype_digit($year_est) || $year_est < 1700 || $year_est > date('Y')))
    $errors['year'] = 'Enter a valid year between 1700 and ' . date('Y') . '.';

  $image = $uni['image'];
  if (empty($errors) && !empty($_FILES['image']['name'])) {
    $uploaded = upload_file($_FILES['image'], 'images', MAX_IMAGE_SIZE);
    if ($uploaded) {
      delete_file($uni['image']);
      $image = $uploaded;
    } else
      $errors['image'] = 'Invalid image file.';
  }

  $cert = $uni['sample_certificate'];
  if (empty($errors) && !empty($_FILES['sample_certificate']['name'])) {
    $uploaded = upload_file($_FILES['sample_certificate'], 'certificates', MAX_IMAGE_SIZE);
    if ($uploaded) {
      delete_file($uni['sample_certificate']);
      $cert = $uploaded;
    } else
      $errors['sample_certificate'] = 'Invalid file.';
  }

  if (empty($errors)) {
    $pdo->beginTransaction();
    try {
      $pdo->prepare("
                UPDATE universities SET
                  name=?,display_name=?,slug=?,image=?,sample_certificate=?,
                  rating=?,nirf_ranking=?,year_of_establishment=?,university_type=?,
                  campus_location=?,avg_placement_package=?,placement_assistance=?,
                  emi_facility=?,scholarship=?,key_advantages=?,view_university_link=?
                WHERE id=?
            ")->execute([
            $name,
            $display_name,
            $final_slug,
            $image,
            $cert,
            $_POST['rating'] ?: null,
            $_POST['nirf_ranking'] ?: null,
            $year_est ?: null,
            $_POST['university_type'] ?: null,
            trim($_POST['campus_location'] ?? '') ?: null,
            trim($_POST['avg_placement_package'] ?? '') ?: null,
            isset($_POST['placement_assistance']) ? 1 : 0,
            isset($_POST['emi_facility']) ? 1 : 0,
            isset($_POST['scholarship']) ? 1 : 0,
            trim($_POST['key_advantages'] ?? '') ?: null,
            trim($_POST['view_university_link'] ?? '') ?: null,
            $id,
          ]);

      $pdo->prepare("DELETE FROM university_education_modes WHERE university_id=?")->execute([$id]);
      if (!empty($_POST['education_modes'])) {
        $ins = $pdo->prepare("INSERT INTO university_education_modes (university_id,education_mode_id) VALUES(?,?)");
        foreach ($_POST['education_modes'] as $mid)
          $ins->execute([$id, (int) $mid]);
      }

      $pdo->prepare("DELETE FROM university_exam_modes WHERE university_id=?")->execute([$id]);
      if (!empty($_POST['exam_modes'])) {
        $ins = $pdo->prepare("INSERT INTO university_exam_modes (university_id,exam_mode_id) VALUES(?,?)");
        foreach ($_POST['exam_modes'] as $eid)
          $ins->execute([$id, (int) $eid]);
      }

      $pdo->prepare("DELETE FROM university_accreditations WHERE university_id=?")->execute([$id]);
      if (!empty($_POST['accreditations'])) {
        $ins = $pdo->prepare("INSERT INTO university_accreditations (university_id,accreditation_id) VALUES(?,?)");
        foreach ($_POST['accreditations'] as $aid)
          $ins->execute([$id, (int) $aid]);
      }

      $pdo->commit();
      set_flash('success', "University '{$name}' updated.");
      redirect(ADMIN_URL . '/universities/index.php');
    } catch (Exception $e) {
      $pdo->rollBack();
      $errors['db'] = 'Database error: ' . $e->getMessage();
    }
  }

  $cur_edu = (array) ($_POST['education_modes'] ?? []);
  $cur_exam = (array) ($_POST['exam_modes'] ?? []);
  $cur_accr = (array) ($_POST['accreditations'] ?? []);
}

$active_page = 'universities';
$page_title = 'Edit University';
$page_subtitle = $uni['name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit University — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    .section-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 1.5rem 0;
    }

    /* ── ACCREDITATION PANEL ── */
    .accr-section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 0.875rem;
    }

    .accr-section-header label {
      font-size: 13px;
      font-weight: 500;
      color: var(--text-m);
    }

    .accr-section-header .accr-actions {
      display: flex;
      gap: 6px;
    }

    /* Pill grid with images */
    .accr-pill-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 1rem;
    }

    .accr-pill {
      display: flex;
      align-items: center;
      gap: 7px;
      padding: 6px 12px 6px 8px;
      border-radius: 20px;
      border: 1px solid var(--border);
      background: var(--surface-h);
      cursor: pointer;
      transition: border-color .15s, background .15s;
      user-select: none;
      font-size: 13px;
      color: var(--text-m);
    }

    .accr-pill input[type=checkbox] {
      display: none;
    }

    .accr-pill img {
      width: 22px;
      height: 22px;
      border-radius: 4px;
      object-fit: contain;
      background: #fff;
      padding: 1px;
      flex-shrink: 0;
    }

    .accr-pill .accr-pill-icon {
      width: 22px;
      height: 22px;
      border-radius: 4px;
      background: rgba(79, 110, 247, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: 700;
      color: var(--accent);
      flex-shrink: 0;
    }

    .accr-pill:has(input:checked) {
      border-color: var(--accent);
      color: var(--accent-h);
      background: rgba(79, 110, 247, 0.1);
    }

    .accr-pill .edit-accr-btn {
      display: none;
      align-items: center;
      justify-content: center;
      width: 18px;
      height: 18px;
      border-radius: 3px;
      margin-left: 2px;
      background: rgba(79, 110, 247, 0.15);
      color: var(--accent);
      font-size: 10px;
      cursor: pointer;
      flex-shrink: 0;
      transition: background .15s;
    }

    .accr-pill:hover .edit-accr-btn {
      display: flex;
    }

    .accr-pill .edit-accr-btn:hover {
      background: rgba(79, 110, 247, 0.3);
    }

    /* ── ACCR MODAL ── */
    .modal-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.55);
      z-index: 500;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .modal-backdrop.open {
      display: flex;
    }

    .modal {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      width: 100%;
      max-width: 420px;
      position: relative;
    }

    .modal-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-close {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--text-s);
      display: flex;
      align-items: center;
      padding: 4px;
      border-radius: 4px;
      transition: color .15s;
    }

    .modal-close:hover {
      color: var(--text);
    }

    .modal-img-row {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      flex-wrap: wrap;
    }

    .modal-img-preview {
      width: 60px;
      height: 60px;
      border-radius: var(--radius-sm);
      border: 1px dashed var(--border);
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--surface-h);
      flex-shrink: 0;
    }

    .modal-img-preview img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 3px;
    }

    .modal-error {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.25);
      border-radius: 6px;
      padding: .5rem .75rem;
      font-size: 12px;
      color: #fca5a5;
      margin-bottom: .75rem;
      display: none;
    }

    .remove-img-label {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: var(--danger);
      cursor: pointer;
      margin-top: 6px;
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">Please fix the errors highlighted below.</div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <h3>Edit: <?= e(get_display_name($uni['name'], $uni['display_name'])) ?></h3>
          <p>ID #<?= $id ?></p>
        </div>
        <a href="<?= ADMIN_URL ?>/universities/index.php" class="btn btn-secondary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <line x1="19" y1="12" x2="5" y2="12" />
            <polyline points="12 19 5 12 12 5" />
          </svg>
          Back
        </a>
      </div>

      <form id="mainForm" method="POST" enctype="multipart/form-data" novalidate>

        <!-- IDENTITY -->
        <div class="section-title">Identity &amp; Display</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="form-grid">
            <div class="form-group">
              <label>University Name <span class="req">*</span></label>
              <input id="uni_name" name="name" type="text" class="form-control" value="<?= e($old['name']) ?>" required>
              <?php if (isset($errors['name'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['name']) ?></span><?php endif; ?>
            </div>
            <div class="form-group">
              <label>Display Name</label>
              <input name="display_name" type="text" class="form-control" placeholder="Leave blank to use main name"
                value="<?= e($old['display_name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Slug</label>
              <input id="uni_slug" name="slug" type="text" class="form-control" value="<?= e($old['slug'] ?? '') ?>">
              <span class="form-hint">Preview: <code id="uni_slug_preview"
                  style="color:var(--accent);"><?= e(get_slug($old['name'], $old['slug'] ?? null)) ?></code></span>
              <?php if (isset($errors['slug'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['slug']) ?></span><?php endif; ?>
              <span class="form-hint" style="color:var(--warning);">⚠ Changing slug may break links.</span>
            </div>
            <div class="form-group">
              <label>University Type</label>
              <select name="university_type" class="form-control">
                <option value="">Select type</option>
                <?php foreach (['Government', 'Private', 'Deemed', 'Autonomous'] as $t): ?>
                  <option value="<?= $t ?>" <?= ($old['university_type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Campus Location</label>
              <input name="campus_location" type="text" class="form-control"
                value="<?= e($old['campus_location'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Year of Establishment</label>
              <input name="year_of_establishment" type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4"
                class="form-control" placeholder="e.g. 2006" value="<?= e($old['year_of_establishment'] ?? '') ?>">
              <?php if (isset($errors['year'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['year']) ?></span><?php endif; ?>
            </div>
          </div>
        </div>

        <!-- RANKINGS -->
        <div class="section-title">Rankings &amp; Features</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="form-grid">
            <div class="form-group">
              <label>Rating <span style="color:var(--text-s);font-weight:400">(out of 5)</span></label>
              <input name="rating" type="number" step="0.1" min="0" max="5" class="form-control"
                value="<?= e($old['rating'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>NIRF Ranking</label>
              <input name="nirf_ranking" type="number" min="1" class="form-control"
                value="<?= e($old['nirf_ranking'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Avg. Placement Package</label>
              <input name="avg_placement_package" type="text" class="form-control"
                value="<?= e($old['avg_placement_package'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>View University Link</label>
              <input name="view_university_link" type="url" class="form-control"
                value="<?= e($old['view_university_link'] ?? '') ?>">
            </div>
          </div>
          <hr class="section-divider">
          <div style="display:flex;flex-wrap:wrap;gap:1.25rem;">
            <label class="check-group"><input type="checkbox" name="placement_assistance" value="1"
                <?= !empty($old['placement_assistance']) ? 'checked' : '' ?>><span>Placement Assistance</span></label>
            <label class="check-group"><input type="checkbox" name="emi_facility" value="1"
                <?= !empty($old['emi_facility']) ? 'checked' : '' ?>><span>EMI Facility</span></label>
            <label class="check-group"><input type="checkbox" name="scholarship" value="1"
                <?= !empty($old['scholarship']) ? 'checked' : '' ?>><span>Scholarship Available</span></label>
          </div>
        </div>

        <!-- MODES -->
        <div class="section-title">Education &amp; Exam Modes</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="form-grid">
            <div class="form-group">
              <label>Education Modes</label>
              <div class="check-grid">
                <?php foreach ($edu_modes as $m): ?>
                  <label class="check-pill">
                    <input type="checkbox" name="education_modes[]" value="<?= $m['id'] ?>"
                      <?= in_array($m['id'], $cur_edu) ? 'checked' : '' ?>>
                    <?= e($m['mode_name']) ?>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="form-group">
              <label>Exam Modes</label>
              <div class="check-grid">
                <?php foreach ($exam_modes_all as $m): ?>
                  <label class="check-pill">
                    <input type="checkbox" name="exam_modes[]" value="<?= $m['id'] ?>"
                      <?= in_array($m['id'], $cur_exam) ? 'checked' : '' ?>>
                    <?= e($m['mode_name']) ?>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- ACCREDITATIONS with Quick Add/Edit -->
        <div class="section-title">Accreditations &amp; Approvals</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="accr-section-header">
            <label>Select Accreditations</label>
            <div class="accr-actions">
              <button type="button" class="btn btn-secondary btn-sm" onclick="openAddAccrModal()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                  stroke-linecap="round">
                  <line x1="12" y1="5" x2="12" y2="19" />
                  <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add New
              </button>
              <a href="<?= ADMIN_URL ?>/masters/accreditations.php" target="_blank" class="btn btn-secondary btn-sm"
                title="Manage all accreditations">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                  stroke-linecap="round">
                  <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                  <polyline points="15 3 21 3 21 9" />
                  <line x1="10" y1="14" x2="21" y2="3" />
                </svg>
                Manage All
              </a>
            </div>
          </div>

          <!-- Accreditation pills with images -->
          <div class="accr-pill-grid" id="accr_pill_grid">
            <?php if ($accreditations): ?>
              <?php foreach ($accreditations as $a): ?>
                <label class="accr-pill" id="accr_pill_<?= $a['id'] ?>">
                  <input type="checkbox" name="accreditations[]" value="<?= $a['id'] ?>"
                    <?= in_array($a['id'], $cur_accr) ? 'checked' : '' ?>>
                  <?php if (!empty($a['image'])): ?>
                    <img src="<?= e($a['image']) ?>" alt="<?= e($a['name']) ?>" id="accr_img_<?= $a['id'] ?>">
                  <?php else: ?>
                    <span class="accr-pill-icon"
                      id="accr_icon_<?= $a['id'] ?>"><?= strtoupper(substr($a['name'], 0, 2)) ?></span>
                  <?php endif; ?>
                  <span id="accr_label_<?= $a['id'] ?>"><?= e($a['name']) ?></span>
                  <span class="edit-accr-btn"
                    onclick="openEditAccrModal(event, <?= $a['id'] ?>, '<?= addslashes($a['name']) ?>', '<?= e($a['image'] ?? '') ?>')"
                    title="Edit accreditation">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                      stroke-linecap="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                  </span>
                </label>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="color:var(--text-s);font-size:13px;">No accreditations found. Add one using the button above.</p>
            <?php endif; ?>
          </div>
          <span class="form-hint">Click a badge to select it. Hover to edit.</span>
        </div>

        <!-- MEDIA -->
        <div class="section-title">Media</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="form-grid">
            <div class="form-group">
              <label>University Logo / Image</label>
              <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                <div class="img-preview-wrap" id="img_preview_uni">
                  <?php if ($uni['image']): ?><img src="<?= e($uni['image']) ?>"
                      style="width:100%;height:100%;object-fit:cover;"><?php else: ?><span
                      style="font-size:11px;color:var(--text-s);text-align:center;padding:8px;">No
                      image</span><?php endif; ?>
                </div>
                <div style="flex:1;min-width:160px;">
                  <input type="file" id="image_input" name="image" accept="image/*" class="form-control">
                  <span class="form-hint">Leave blank to keep current. Max 2MB.</span>
                  <?php if (isset($errors['image'])): ?><span class="form-hint"
                      style="color:var(--danger)"><?= e($errors['image']) ?></span><?php endif; ?>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Sample Certificate Image</label>
              <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                <div class="img-preview-wrap" id="img_preview_cert">
                  <?php if ($uni['sample_certificate']): ?><img src="<?= e($uni['sample_certificate']) ?>"
                      style="width:100%;height:100%;object-fit:cover;"><?php else: ?><span
                      style="font-size:11px;color:var(--text-s);text-align:center;padding:8px;">No
                      image</span><?php endif; ?>
                </div>
                <div style="flex:1;min-width:160px;">
                  <input type="file" id="cert_input" name="sample_certificate" accept="image/*" class="form-control">
                  <span class="form-hint">Leave blank to keep current. Max 2MB.</span>
                  <?php if (isset($errors['sample_certificate'])): ?><span class="form-hint"
                      style="color:var(--danger)"><?= e($errors['sample_certificate']) ?></span><?php endif; ?>
                </div>
              </div>
            </div>
            <div class="form-group full">
              <label>Key Advantages</label>
              <textarea name="key_advantages" class="form-control"
                rows="4"><?= e($old['key_advantages'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <div style="display:flex;gap:.75rem;justify-content:flex-end;">
          <a href="<?= ADMIN_URL ?>/universities/index.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round">
              <polyline points="20 6 9 17 4 12" />
            </svg>
            Update University
          </button>
        </div>
      </form>
    </div>
  </main>

  <!-- ══ ADD ACCREDITATION MODAL ══ -->
  <div class="modal-backdrop" id="addAccrModal">
    <div class="modal">
      <div class="modal-title">
        Add New Accreditation
        <button class="modal-close" onclick="closeModal('addAccrModal')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>
      <div class="modal-error" id="addAccrError"></div>
      <div class="form-group" style="margin-bottom:1rem;">
        <label>Name <span class="req">*</span></label>
        <input type="text" id="addAccrName" class="form-control" placeholder="e.g. UGC, NAAC">
      </div>
      <div class="form-group" style="margin-bottom:1.25rem;">
        <label>Badge / Logo Image</label>
        <div class="modal-img-row">
          <div class="modal-img-preview" id="addAccrImgPreview">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
              opacity=".3">
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <circle cx="8.5" cy="8.5" r="1.5" />
              <polyline points="21 15 16 10 5 21" />
            </svg>
          </div>
          <div style="flex:1;">
            <input type="file" id="addAccrImg" accept="image/*" class="form-control">
            <span class="form-hint">Optional. JPG/PNG/WEBP, max 2MB.</span>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:.75rem;">
        <button type="button" class="btn btn-secondary" style="flex:1;justify-content:center;"
          onclick="closeModal('addAccrModal')">Cancel</button>
        <button type="button" class="btn btn-primary" style="flex:1;justify-content:center;" id="addAccrSubmit"
          onclick="submitAddAccr()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Add
        </button>
      </div>
    </div>
  </div>

  <!-- ══ EDIT ACCREDITATION MODAL ══ -->
  <div class="modal-backdrop" id="editAccrModal">
    <div class="modal">
      <div class="modal-title">
        Edit Accreditation
        <button class="modal-close" onclick="closeModal('editAccrModal')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>
      <div class="modal-error" id="editAccrError"></div>
      <input type="hidden" id="editAccrId">
      <div class="form-group" style="margin-bottom:1rem;">
        <label>Name <span class="req">*</span></label>
        <input type="text" id="editAccrName" class="form-control">
      </div>
      <div class="form-group" style="margin-bottom:1.25rem;">
        <label>Badge / Logo Image</label>
        <div class="modal-img-row">
          <div class="modal-img-preview" id="editAccrImgPreview"></div>
          <div style="flex:1;">
            <input type="file" id="editAccrImg" accept="image/*" class="form-control">
            <span class="form-hint">Upload new to replace. Max 2MB.</span>
            <label class="remove-img-label" id="editRemoveImgLabel" style="display:none;">
              <input type="checkbox" id="editRemoveImg"> Remove current image
            </label>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:.75rem;">
        <button type="button" class="btn btn-secondary" style="flex:1;justify-content:center;"
          onclick="closeModal('editAccrModal')">Cancel</button>
        <button type="button" class="btn btn-primary" style="flex:1;justify-content:center;" onclick="submitEditAccr()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          Update
        </button>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
  <script>
    bindSlugGenerator('uni_name', 'uni_slug');
    bindImagePreview('image_input', 'img_preview_uni');
    bindImagePreview('cert_input', 'img_preview_cert');

    document.querySelector('input[name="year_of_establishment"]').addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });

    // ── MODAL HELPERS ──
    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }

    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(function (bd) {
      bd.addEventListener('click', function (e) {
        if (e.target === bd) closeModal(bd.id);
      });
    });

    // ── ADD ACCREDITATION MODAL ──
    function openAddAccrModal() {
      document.getElementById('addAccrName').value = '';
      document.getElementById('addAccrImg').value = '';
      document.getElementById('addAccrImgPreview').innerHTML =
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
      document.getElementById('addAccrError').style.display = 'none';
      openModal('addAccrModal');
      setTimeout(function () { document.getElementById('addAccrName').focus(); }, 100);
    }

    // Live preview for add modal
    document.getElementById('addAccrImg').addEventListener('change', function () {
      const file = this.files[0]; if (!file) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById('addAccrImgPreview').innerHTML =
          '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain;padding:3px;">';
      };
      reader.readAsDataURL(file);
    });

    function submitAddAccr() {
      const name = document.getElementById('addAccrName').value.trim();
      const errEl = document.getElementById('addAccrError');
      if (!name) { errEl.textContent = 'Name is required.'; errEl.style.display = 'block'; return; }

      const btn = document.getElementById('addAccrSubmit');
      btn.disabled = true; btn.textContent = 'Saving…';

      const fd = new FormData();
      fd.append('action', 'quick_add_accr');
      fd.append('accr_name', name);
      const imgFile = document.getElementById('addAccrImg').files[0];
      if (imgFile) fd.append('accr_image', imgFile);

      fetch('edit.php?id=<?= $id ?>', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          btn.disabled = false; btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add';
          if (!data.success) { errEl.textContent = data.error; errEl.style.display = 'block'; return; }
          appendAccrPill(data.id, data.name, data.image, true);
          closeModal('addAccrModal');
        })
        .catch(function () {
          btn.disabled = false;
          errEl.textContent = 'Network error. Try again.'; errEl.style.display = 'block';
        });
    }

    function appendAccrPill(id, name, image, checked) {
      const grid = document.getElementById('accr_pill_grid');
      // Remove "no accreditations" text if present
      const noText = grid.querySelector('p');
      if (noText) noText.remove();

      const label = document.createElement('label');
      label.className = 'accr-pill';
      label.id = 'accr_pill_' + id;

      const imgHtml = image
        ? '<img src="' + image + '" alt="' + name + '" id="accr_img_' + id + '">'
        : '<span class="accr-pill-icon" id="accr_icon_' + id + '">' + name.substring(0, 2).toUpperCase() + '</span>';

      label.innerHTML =
        '<input type="checkbox" name="accreditations[]" value="' + id + '"' + (checked ? ' checked' : '') + '>' +
        imgHtml +
        '<span id="accr_label_' + id + '">' + name + '</span>' +
        '<span class="edit-accr-btn" onclick="openEditAccrModal(event,' + id + ',\'' + name.replace(/'/g, "\\'") + '\',\'' + (image || '') + '\')" title="Edit">' +
        '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>' +
        '</span>';
      grid.appendChild(label);
    }

    // ── EDIT ACCREDITATION MODAL ──
    function openEditAccrModal(e, id, name, image) {
      e.preventDefault(); e.stopPropagation();
      document.getElementById('editAccrId').value = id;
      document.getElementById('editAccrName').value = name;
      document.getElementById('editAccrImg').value = '';
      document.getElementById('editAccrError').style.display = 'none';
      document.getElementById('editRemoveImg').checked = false;

      const preview = document.getElementById('editAccrImgPreview');
      const removeLabel = document.getElementById('editRemoveImgLabel');

      if (image) {
        preview.innerHTML = '<img src="' + image + '" style="width:100%;height:100%;object-fit:contain;padding:3px;">';
        removeLabel.style.display = 'inline-flex';
      } else {
        preview.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
        removeLabel.style.display = 'none';
      }
      openModal('editAccrModal');
      setTimeout(function () { document.getElementById('editAccrName').focus(); }, 100);
    }

    document.getElementById('editAccrImg').addEventListener('change', function () {
      const file = this.files[0]; if (!file) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById('editAccrImgPreview').innerHTML =
          '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain;padding:3px;">';
        document.getElementById('editRemoveImgLabel').style.display = 'inline-flex';
      };
      reader.readAsDataURL(file);
    });

    function submitEditAccr() {
      const id = document.getElementById('editAccrId').value;
      const name = document.getElementById('editAccrName').value.trim();
      const errEl = document.getElementById('editAccrError');
      if (!name) { errEl.textContent = 'Name is required.'; errEl.style.display = 'block'; return; }

      const fd = new FormData();
      fd.append('action', 'quick_edit_accr');
      fd.append('accr_id', id);
      fd.append('accr_name', name);
      if (document.getElementById('editRemoveImg').checked) fd.append('remove_image', '1');
      const imgFile = document.getElementById('editAccrImg').files[0];
      if (imgFile) fd.append('accr_image', imgFile);

      fetch('edit.php?id=<?= $id ?>', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (!data.success) { errEl.textContent = data.error; errEl.style.display = 'block'; return; }

          // Update pill in DOM
          const labelEl = document.getElementById('accr_label_' + data.id);
          if (labelEl) labelEl.textContent = data.name;

          // Update image in pill
          const imgEl = document.getElementById('accr_img_' + data.id);
          const iconEl = document.getElementById('accr_icon_' + data.id);
          const pill = document.getElementById('accr_pill_' + data.id);

          if (data.image) {
            if (imgEl) { imgEl.src = data.image; }
            else if (iconEl && pill) {
              const newImg = document.createElement('img');
              newImg.src = data.image; newImg.alt = data.name;
              newImg.id = 'accr_img_' + data.id;
              pill.replaceChild(newImg, iconEl);
            }
          } else {
            if (imgEl && pill) {
              const newIcon = document.createElement('span');
              newIcon.className = 'accr-pill-icon';
              newIcon.id = 'accr_icon_' + data.id;
              newIcon.textContent = data.name.substring(0, 2).toUpperCase();
              pill.replaceChild(newIcon, imgEl);
            }
          }

          closeModal('editAccrModal');
        })
        .catch(function () {
          errEl.textContent = 'Network error. Try again.'; errEl.style.display = 'block';
        });
    }
  </script>
</body>

</html>