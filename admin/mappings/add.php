<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

$errors = [];
$old = $_POST;

$universities = $pdo->query("SELECT id, name, display_name FROM universities WHERE is_active=1 ORDER BY name")->fetchAll();
$courses = $pdo->query("SELECT id, name, display_name, course_level FROM courses WHERE is_active=1 ORDER BY name")->fetchAll();
$modes = $pdo->query("SELECT * FROM education_modes ORDER BY id")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uni_id = (int) ($_POST['university_id'] ?? 0);
  $crs_id = (int) ($_POST['course_id'] ?? 0);
  $mode_id = (int) ($_POST['education_mode_id'] ?? 0);

  if (!$uni_id)
    $errors['university_id'] = 'Please select a university.';
  if (!$crs_id)
    $errors['course_id'] = 'Please select a course.';
  if (!$mode_id)
    $errors['education_mode_id'] = 'Please select an education mode.';

  $acad_fees = trim($_POST['academic_fees'] ?? '');
  if ($acad_fees !== '' && !is_numeric($acad_fees))
    $errors['academic_fees'] = 'Must be a valid number.';

  $discount = trim($_POST['fees_discount'] ?? '');
  if ($discount !== '' && !is_numeric($discount))
    $errors['fees_discount'] = 'Must be a valid number.';

  $rating = trim($_POST['course_rating'] ?? '');
  if ($rating !== '' && (!is_numeric($rating) || $rating < 0 || $rating > 5)) {
    $errors['course_rating'] = 'Must be a valid rating between 0 and 5.';
  }

  $specializations = trim($_POST['course_specializations'] ?? '') ?: null;

  // Check unique constraint manually
  if ($uni_id && $crs_id && $mode_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM university_courses WHERE university_id=? AND course_id=? AND education_mode_id=?");
    $stmt->execute([$uni_id, $crs_id, $mode_id]);
    if ($stmt->fetchColumn() > 0) {
      $errors['duplicate'] = 'This combination of University, Course, and Mode is already mapped.';
    }
  }

  $brochure = null;
  if (empty($errors) && !empty($_FILES['brochure_file']['name'])) {
    $brochure = upload_file($_FILES['brochure_file'], 'brochures', MAX_BROCHURE_SIZE);
    if (!$brochure)
      $errors['brochure_file'] = 'Invalid brochure file. Must be a PDF, JPG, PNG or WEBP under 50MB.';
  }

  if (empty($errors)) {
    try {
      $stmt = $pdo->prepare("
                INSERT INTO university_courses 
                  (university_id, course_id, education_mode_id, academic_fees, fees_discount, course_rating, course_specializations, brochure_file) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
      $stmt->execute([
        $uni_id,
        $crs_id,
        $mode_id,
        $acad_fees === '' ? null : $acad_fees,
        $discount === '' ? null : $discount,
        $rating === '' ? null : $rating,
        $specializations,
        $brochure
      ]);

      set_flash('success', "Course successfully mapped to University.");
      redirect(ADMIN_URL . '/mappings/index.php');
    } catch (Exception $e) {
      // Re-check for duplicate if uniqueness failed at DB level
      if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
        $errors['duplicate'] = 'This combination of University, Course, and Mode is already mapped.';
      } else {
        $errors['db'] = 'Database error: ' . $e->getMessage();
      }
    }
  }
}

$active_page = 'mappings';
$page_title = 'Map Course';
$page_subtitle = 'Link a course to a university';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Map Course — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
  <style>
    .section-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 1.5rem 0;
    }

    /* TomSelect Dark Theme Overrides */
    .ts-wrapper.form-control {
      padding: 0 !important;
      background: transparent !important;
      border: none !important;
      height: auto !important;
      box-shadow: none !important;
    }

    .ts-control {
      background: rgba(255, 255, 255, 0.04) !important;
      border: 1px solid var(--border) !important;
      color: var(--text) !important;
      border-radius: var(--radius-sm) !important;
      padding: 0.65rem 0.875rem !important;
      min-height: 40px !important;
      box-shadow: none !important;
      font-size: 13.5px !important;
      display: flex !important;
      align-items: center !important;
    }

    .ts-control.focus {
      border-color: var(--accent) !important;
      box-shadow: 0 0 0 3px rgba(79, 110, 247, 0.15) !important;
      background: rgba(255, 255, 255, 0.04) !important;
    }

    .ts-control>input {
      color: var(--text) !important;
      font-family: inherit !important;
      font-size: 13.5px !important;
    }

    .ts-dropdown {
      background: var(--surface) !important;
      border: 1px solid var(--border) !important;
      color: var(--text) !important;
      border-radius: var(--radius-sm) !important;
      margin-top: 4px !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
      z-index: 1000 !important;
    }

    .ts-dropdown .option {
      padding: 8px 12px !important;
      font-size: 13.5px !important;
      color: var(--text) !important;
    }

    .ts-dropdown .option.active,
    .ts-dropdown .option:hover {
      background: var(--surface-h) !important;
      color: var(--accent-h) !important;
    }

    body.light .ts-control {
      background: var(--bg) !important;
    }

    body.light .ts-dropdown {
      background: var(--surface) !important;
    }

    .ts-wrapper.single .ts-control:after {
      border-color: var(--text-m) transparent transparent transparent !important;
    }

    .ts-wrapper.focus .ts-control {
      border-color: var(--accent) !important;
    }

    .form-control.ts-hidden-accessible {
      position: absolute !important;
      clip: rect(0 0 0 0) !important;
      width: 1px !important;
      height: 1px !important;
      margin: -1px !important;
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
        <div class="alert alert-error">
          <svg width="15" height="15" viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5" />
            <path d="M10 5.5v5M10 13.5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
          </svg>
          <?php
          if (isset($errors['duplicate']))
            echo e($errors['duplicate']);
          else
            echo 'Please fix the errors highlighted below.';
          ?>
        </div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <h3>New Mapping</h3>
          <p>Fields marked <span style="color:var(--danger)">*</span> are required</p>
        </div>
        <a href="<?= ADMIN_URL ?>/mappings/index.php" class="btn btn-secondary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <line x1="19" y1="12" x2="5" y2="12" />
            <polyline points="12 19 5 12 12 5" />
          </svg>
          Back
        </a>
      </div>

      <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="section-title">Mapping Configuration</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="form-grid">
            <div class="form-group">
              <label>Select University <span class="req">*</span></label>
              <select name="university_id" id="university_id" class="form-control select-search" required>
                <option value="">-- Choose University --</option>
                <?php foreach ($universities as $u): ?>
                  <option value="<?= $u['id'] ?>" <?= ($old['university_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                    <?= e(get_display_name($u['name'], $u['display_name'])) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['university_id'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['university_id']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
              <label>Select Course <span class="req">*</span></label>
              <select name="course_id" id="course_id" class="form-control select-search" required>
                <option value="">-- Choose Course --</option>
                <?php foreach ($courses as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= ($old['course_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                    <?= e(get_display_name($c['name'], $c['display_name'])) ?>
                    <?= $c['course_level'] ? '(' . e($c['course_level']) . ')' : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['course_id'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['course_id']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
              <label>Education Mode <span class="req">*</span></label>
              <select name="education_mode_id" class="form-control" required>
                <option value="">-- Select Mode --</option>
                <?php foreach ($modes as $m): ?>
                  <option value="<?= $m['id'] ?>" <?= ($old['education_mode_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                    <?= e($m['mode_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['education_mode_id'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['education_mode_id']) ?></span><?php endif; ?>
            </div>
          </div>
        </div>

        <div class="section-title">Course Specific Details</div>
        <div class="form-card" style="margin-bottom:1.25rem;">
          <div class="form-grid">
            <div class="form-group">
              <label>Academic Fees (₹)</label>
              <input name="academic_fees" type="number" step="0.01" class="form-control" placeholder="e.g. 50000"
                value="<?= e($old['academic_fees'] ?? '') ?>">
              <?php if (isset($errors['academic_fees'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['academic_fees']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
              <label>Fees Discount (₹)</label>
              <input name="fees_discount" type="number" step="1" class="form-control" placeholder="e.g. 5000"
                value="<?= e($old['fees_discount'] ?? '') ?>">
              <?php if (isset($errors['fees_discount'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['fees_discount']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
              <label>Course Rating (Out of 5)</label>
              <input name="course_rating" type="number" step="0.1" min="0" max="5" class="form-control"
                placeholder="e.g. 4.5" value="<?= e($old['course_rating'] ?? '') ?>">
              <?php if (isset($errors['course_rating'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['course_rating']) ?></span><?php endif; ?>
            </div>

            <div class="form-group full">
              <label>Course Specializations</label>
              <textarea name="course_specializations" class="form-control" rows="4"
                placeholder="Enter specializations (e.g. Finance, Marketing) as list or paragraph..."><?= e($old['course_specializations'] ?? '') ?></textarea>
            </div>

            <div class="form-group full">
              <label>Course Brochure (PDF)</label>
              <input type="file" name="brochure_file" accept=".pdf,image/*" class="form-control">
              <span class="form-hint">Max size: 50MB. Allowed: PDF, JPG, PNG, WEBP</span>
              <?php if (isset($errors['brochure_file'])): ?><span class="form-hint"
                  style="color:var(--danger)"><?= e($errors['brochure_file']) ?></span><?php endif; ?>
            </div>
          </div>
        </div>

        <div style="display:flex;gap:.75rem;justify-content:flex-end;">
          <a href="<?= ADMIN_URL ?>/mappings/index.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round">
              <polyline points="20 6 9 17 4 12" />
            </svg>
            Save Mapping
          </button>
        </div>
      </form>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
  <script>
    document.querySelectorAll('.select-search').forEach((el) => {
      new TomSelect(el, {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: el.getAttribute('placeholder') || "Search & Select..."
      });
    });
  </script>
</body>

</html>