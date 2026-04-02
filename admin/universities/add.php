<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME); session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';
require_login();

$edu_modes      = $pdo->query("SELECT * FROM education_modes ORDER BY id")->fetchAll();
$exam_modes_all = $pdo->query("SELECT * FROM exam_modes ORDER BY id")->fetchAll();
$accreditations = $pdo->query("SELECT * FROM accreditations ORDER BY name")->fetchAll();

$errors = [];
$old    = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if (!$name) $errors['name'] = 'University name is required.';

    $display_name = trim($_POST['display_name'] ?? '') ?: null;
    $slug_input   = trim($_POST['slug'] ?? '');
    $final_slug   = $slug_input ? make_slug($slug_input) : make_slug($name);

    if ($name && !is_slug_unique($pdo, 'universities', $final_slug)) {
        $errors['slug'] = 'This slug is already taken. Choose another.';
    }

    // Year validation
    $year_est = trim($_POST['year_of_establishment'] ?? '');
    if ($year_est !== '' && (!ctype_digit($year_est) || $year_est < 1700 || $year_est > date('Y'))) {
        $errors['year'] = 'Enter a valid year between 1700 and ' . date('Y') . '.';
    }

    $image = null;
    if (empty($errors) && !empty($_FILES['image']['name'])) {
        $image = upload_file($_FILES['image'], 'images', MAX_IMAGE_SIZE);
        if (!$image) $errors['image'] = 'Invalid image. Use JPG/PNG/WEBP under 2MB.';
    }

    $cert = null;
    if (empty($errors) && !empty($_FILES['sample_certificate']['name'])) {
        $cert = upload_file($_FILES['sample_certificate'], 'certificates', MAX_IMAGE_SIZE);
        if (!$cert) $errors['sample_certificate'] = 'Invalid file. Use JPG/PNG under 2MB.';
    }

    if (empty($errors)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO universities
                  (name,display_name,slug,image,sample_certificate,
                   rating,nirf_ranking,year_of_establishment,university_type,
                   campus_location,avg_placement_package,placement_assistance,
                   emi_facility,scholarship,key_advantages,view_university_link)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $name, $display_name, $final_slug, $image, $cert,
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
            ]);
            $uni_id = $pdo->lastInsertId();

            if (!empty($_POST['education_modes'])) {
                $ins = $pdo->prepare("INSERT INTO university_education_modes (university_id,education_mode_id) VALUES(?,?)");
                foreach ($_POST['education_modes'] as $mid) $ins->execute([$uni_id,(int)$mid]);
            }
            if (!empty($_POST['exam_modes'])) {
                $ins = $pdo->prepare("INSERT INTO university_exam_modes (university_id,exam_mode_id) VALUES(?,?)");
                foreach ($_POST['exam_modes'] as $eid) $ins->execute([$uni_id,(int)$eid]);
            }
            if (!empty($_POST['accreditations'])) {
                $ins = $pdo->prepare("INSERT INTO university_accreditations (university_id,accreditation_id) VALUES(?,?)");
                foreach ($_POST['accreditations'] as $aid) $ins->execute([$uni_id,(int)$aid]);
            }

            $pdo->commit();
            set_flash('success', "University '{$name}' added successfully.");
            redirect(ADMIN_URL . '/universities/index.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors['db'] = 'Database error: ' . $e->getMessage();
        }
    }
}

$active_page   = 'universities';
$page_title    = 'Add University';
$page_subtitle = 'Fill in the details below';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add University — SODE AI Tools</title>
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
      <div><h3>New University</h3><p>Fields marked <span style="color:var(--danger)">*</span> are required</p></div>
      <a href="<?= ADMIN_URL ?>/universities/index.php" class="btn btn-secondary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back
      </a>
    </div>

    <form method="POST" enctype="multipart/form-data" novalidate>

      <!-- IDENTITY -->
      <div class="section-title">Identity &amp; Display</div>
      <div class="form-card" style="margin-bottom:1.25rem;">
        <div class="form-grid">
          <div class="form-group">
            <label>University Name <span class="req">*</span></label>
            <input id="uni_name" name="name" type="text" class="form-control"
                   placeholder="e.g. Amity University" value="<?= e($old['name'] ?? '') ?>" required>
            <?php if (isset($errors['name'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['name']) ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>Display Name</label>
            <input name="display_name" type="text" class="form-control"
                   placeholder="Shown to users (blank = use main name)" value="<?= e($old['display_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Slug</label>
            <input id="uni_slug" name="slug" type="text" class="form-control"
                   placeholder="auto-generated from name" value="<?= e($old['slug'] ?? '') ?>">
            <span class="form-hint">Preview: <code id="uni_slug_preview" style="color:var(--accent);"><?= e($old['slug'] ?? '—') ?></code></span>
            <?php if (isset($errors['slug'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['slug']) ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label>University Type</label>
            <select name="university_type" class="form-control">
              <option value="">Select type</option>
              <?php foreach (['Government','Private','Deemed','Autonomous'] as $t): ?>
                <option value="<?= $t ?>" <?= ($old['university_type']??'')===$t?'selected':'' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Campus Location</label>
            <input name="campus_location" type="text" class="form-control"
                   placeholder="e.g. Noida, Uttar Pradesh" value="<?= e($old['campus_location'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Year of Establishment</label>
            <input name="year_of_establishment" type="text" inputmode="numeric"
                   pattern="[0-9]{4}" maxlength="4" class="form-control"
                   placeholder="e.g. 2006" value="<?= e($old['year_of_establishment'] ?? '') ?>">
            <?php if (isset($errors['year'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['year']) ?></span><?php endif; ?>
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
                   placeholder="e.g. 4.5" value="<?= e($old['rating'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>NIRF Ranking</label>
            <input name="nirf_ranking" type="number" min="1" class="form-control"
                   placeholder="e.g. 45" value="<?= e($old['nirf_ranking'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Avg. Placement Package</label>
            <input name="avg_placement_package" type="text" class="form-control"
                   placeholder="e.g. 12 LPA" value="<?= e($old['avg_placement_package'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>View University Link</label>
            <input name="view_university_link" type="url" class="form-control"
                   placeholder="https://..." value="<?= e($old['view_university_link'] ?? '') ?>">
          </div>
        </div>
        <hr class="section-divider">
        <div style="display:flex;flex-wrap:wrap;gap:1.25rem;">
          <label class="check-group"><input type="checkbox" name="placement_assistance" value="1" <?= !empty($old['placement_assistance'])?'checked':'' ?>><span>Placement Assistance</span></label>
          <label class="check-group"><input type="checkbox" name="emi_facility" value="1" <?= !empty($old['emi_facility'])?'checked':'' ?>><span>EMI Facility</span></label>
          <label class="check-group"><input type="checkbox" name="scholarship" value="1" <?= !empty($old['scholarship'])?'checked':'' ?>><span>Scholarship Available</span></label>
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
                    <?= in_array($m['id'],(array)($old['education_modes']??[]))?'checked':'' ?>>
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
                    <?= in_array($m['id'],(array)($old['exam_modes']??[]))?'checked':'' ?>>
                  <?= e($m['mode_name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- ACCREDITATIONS - select only, no image upload here -->
      <div class="section-title">Accreditations &amp; Approvals</div>
      <div class="form-card" style="margin-bottom:1.25rem;">
        <div class="form-group">
          <label>Select Accreditations</label>
          <span class="form-hint" style="margin-bottom:8px;display:block;">
            To add new accreditations, go to
            <a href="<?= ADMIN_URL ?>/masters/accreditations.php" style="color:var(--accent);" target="_blank">Settings → Accreditations ↗</a>
          </span>
          <?php if ($accreditations): ?>
          <div class="check-grid">
            <?php foreach ($accreditations as $a): ?>
              <label class="check-pill">
                <input type="checkbox" name="accreditations[]" value="<?= $a['id'] ?>"
                  <?= in_array($a['id'],(array)($old['accreditations']??[]))?'checked':'' ?>>
                <?= e($a['name']) ?>
              </label>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
            <p style="color:var(--text-s);font-size:13px;">
              No accreditations found.
              <a href="<?= ADMIN_URL ?>/masters/accreditations.php" style="color:var(--accent);">Add some first →</a>
            </p>
          <?php endif; ?>
        </div>
      </div>

      <!-- MEDIA -->
      <div class="section-title">Media</div>
      <div class="form-card" style="margin-bottom:1.25rem;">
        <div class="form-grid">
          <div class="form-group">
            <label>University Logo / Image</label>
            <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
              <div class="img-preview-wrap" id="img_preview_uni">
                <span style="font-size:11px;color:var(--text-s);text-align:center;padding:8px;">No image</span>
              </div>
              <div style="flex:1;min-width:160px;">
                <input type="file" id="image_input" name="image" accept="image/*" class="form-control">
                <span class="form-hint">JPG, PNG, WEBP — max 2MB</span>
                <?php if (isset($errors['image'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['image']) ?></span><?php endif; ?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Sample Certificate Image</label>
            <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
              <div class="img-preview-wrap" id="img_preview_cert">
                <span style="font-size:11px;color:var(--text-s);text-align:center;padding:8px;">No image</span>
              </div>
              <div style="flex:1;min-width:160px;">
                <input type="file" id="cert_input" name="sample_certificate" accept="image/*" class="form-control">
                <span class="form-hint">JPG, PNG, WEBP — max 2MB</span>
                <?php if (isset($errors['sample_certificate'])): ?><span class="form-hint" style="color:var(--danger)"><?= e($errors['sample_certificate']) ?></span><?php endif; ?>
              </div>
            </div>
          </div>
          <div class="form-group full">
            <label>Key Advantages</label>
            <textarea name="key_advantages" class="form-control" rows="4"
                      placeholder="List key advantages, one per line or as a paragraph…"><?= e($old['key_advantages'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;justify-content:flex-end;">
        <a href="<?= ADMIN_URL ?>/universities/index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          Save University
        </button>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
<script>
bindSlugGenerator('uni_name','uni_slug');
bindImagePreview('image_input','img_preview_uni');
bindImagePreview('cert_input','img_preview_cert');

// Year: allow only digits, max 4
document.querySelector('input[name="year_of_establishment"]').addEventListener('input', function(){
  this.value = this.value.replace(/\D/g,'').slice(0,4);
});
</script>
</body>
</html>
