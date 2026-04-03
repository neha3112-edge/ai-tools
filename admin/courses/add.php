<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME); session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

$errors = [];
$old    = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if (!$name) $errors['name'] = 'Course name is required.';

    $display_name = trim($_POST['display_name'] ?? '') ?: null;
    $slug_input   = trim($_POST['slug'] ?? '');
    $final_slug   = $slug_input ? make_slug($slug_input) : make_slug($name);

    if ($name && !is_slug_unique($pdo, 'courses', $final_slug)) {
        $errors['slug'] = 'This slug is already taken. Choose another.';
    }

    $course_level = $_POST['course_level'] ?? '';
    if (!$course_level || !in_array($course_level, ['UG', 'PG'])) {
        $errors['course_level'] = 'Valid course level (UG or PG) is required.';
    }

    $program_eligibility = trim($_POST['program_eligibility'] ?? '') ?: null;
    $course_duration = trim($_POST['course_duration'] ?? '') ?: null;

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO courses 
                  (name, display_name, slug, course_level, program_eligibility, course_duration) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $name, 
                $display_name, 
                $final_slug, 
                $course_level, 
                $program_eligibility, 
                $course_duration
            ]);
            
            set_flash('success', "Course '{$name}' added successfully.");
            redirect(ADMIN_URL . '/courses/index.php');
        } catch (Exception $e) {
            $errors['db'] = 'Database error: ' . $e->getMessage();
        }
    }
}

$active_page   = 'courses';
$page_title    = 'Add Course';
$page_subtitle = 'Fill in the course details below';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Course — SODE AI Tools</title>
<?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
<style>
  .section-divider { border:none; border-top:1px solid var(--border); margin:1.5rem 0; }
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
        <svg width="15" height="15" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M10 5.5v5M10 13.5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        Please fix the errors highlighted below.
      </div>
    <?php endif; ?>

    <div class="page-header">
      <div><h3>New Course</h3><p>Fields marked <span style="color:var(--danger)">*</span> are required</p></div>
      <a href="<?= ADMIN_URL ?>/courses/index.php" class="btn btn-secondary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back
      </a>
    </div>

    <form method="POST" novalidate>
      <div class="section-title">Identity &amp; Details</div>
      <div class="form-card" style="margin-bottom:1.25rem;">
        <div class="form-grid">
          <div class="form-group">
            <label>Course Name <span class="req">*</span></label>
            <input id="course_name" name="name" type="text" class="form-control"
                   placeholder="e.g. Master of Business Administration" value="<?= e($old['name'] ?? '') ?>" required>
            <?php if (isset($errors['name'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['name']) ?></span><?php endif; ?>
          </div>
          
          <div class="form-group">
            <label>Display Name</label>
            <input name="display_name" type="text" class="form-control"
                   placeholder="e.g. MBA (blank = use main name)" value="<?= e($old['display_name'] ?? '') ?>">
          </div>
          
          <div class="form-group">
            <label>Slug</label>
            <input id="course_slug" name="slug" type="text" class="form-control"
                   placeholder="auto-generated from name" value="<?= e($old['slug'] ?? '') ?>">
            <span class="form-hint">Preview: <code id="course_slug_preview" style="color:var(--accent);"><?= e($old['slug'] ?? '—') ?></code></span>
            <?php if (isset($errors['slug'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['slug']) ?></span><?php endif; ?>
          </div>
          
          <div class="form-group">
            <label>Course Level <span class="req">*</span></label>
            <select name="course_level" class="form-control" required>
              <option value="">Select Level</option>
              <option value="UG" <?= ($old['course_level']??'') === 'UG' ? 'selected' : '' ?>>UG - Undergraduate</option>
              <option value="PG" <?= ($old['course_level']??'') === 'PG' ? 'selected' : '' ?>>PG - Postgraduate</option>
            </select>
            <?php if (isset($errors['course_level'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['course_level']) ?></span><?php endif; ?>
          </div>
          
          <div class="form-group">
            <label>Course Duration</label>
            <input name="course_duration" type="text" class="form-control"
                   placeholder="e.g. 2 Years, 6 Months" value="<?= e($old['course_duration'] ?? '') ?>">
          </div>

          <div class="form-group full">
            <label>Program Eligibility</label>
            <textarea name="program_eligibility" class="form-control" rows="3"
                      placeholder="e.g. Minimum 50% marks in Graduation from a recognized university."><?= e($old['program_eligibility'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;justify-content:flex-end;">
        <a href="<?= ADMIN_URL ?>/courses/index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          Save Course
        </button>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
<script>
bindSlugGenerator('course_name', 'course_slug');
</script>
</body>
</html>
