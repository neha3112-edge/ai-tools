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

// Fetch university
$stmt = $pdo->prepare("
  SELECT u.*, ut.type_name as university_type 
  FROM universities u
  LEFT JOIN university_types ut ON u.university_type_id = ut.id
  WHERE u.id = ? AND u.is_active=1
");
$stmt->execute([$id]);
$uni = $stmt->fetch();
if (!$uni) {
  set_flash('error', 'University not found.');
  redirect(ADMIN_URL . '/universities/index.php');
}

// Education modes
$edu = $pdo->prepare("SELECT m.mode_name FROM university_education_modes um
    JOIN education_modes m ON m.id=um.education_mode_id WHERE um.university_id=?");
$edu->execute([$id]);
$edu_modes = array_column($edu->fetchAll(), 'mode_name');

// Exam modes
$exam = $pdo->prepare("SELECT m.mode_name FROM university_exam_modes um
    JOIN exam_modes m ON m.id=um.exam_mode_id WHERE um.university_id=?");
$exam->execute([$id]);
$exam_modes = array_column($exam->fetchAll(), 'mode_name');

// Accreditations
$accr = $pdo->prepare("SELECT a.name, a.image FROM university_accreditations ua
    JOIN accreditations a ON a.id=ua.accreditation_id WHERE ua.university_id=? ORDER BY a.name");
$accr->execute([$id]);
$accreditations = $accr->fetchAll();

// Courses this university offers (via mappings)
$courses_stmt = $pdo->prepare("
    SELECT c.id, c.name, c.display_name, c.course_level, c.course_duration,
           uc.academic_fees, uc.fees_discount, uc.course_rating, uc.brochure_file,
           em.mode_name as education_mode, uc.id as mapping_id
    FROM university_courses uc
    JOIN courses c ON c.id = uc.course_id
    JOIN education_modes em ON em.id = uc.education_mode_id
    WHERE uc.university_id=? AND uc.is_active=1 AND c.is_active=1
    ORDER BY c.course_level ASC, c.name ASC
");
$courses_stmt->execute([$id]);
$courses = $courses_stmt->fetchAll();

$active_page = 'universities';
$page_title = 'View University';
$page_subtitle = get_display_name($uni['name'], $uni['display_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(get_display_name($uni['name'], $uni['display_name'])) ?> — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    /* ── HERO CARD ── */
    .uni-hero {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.75rem;
      margin-bottom: 1.25rem;
      display: flex;
      gap: 1.5rem;
      align-items: flex-start;
      flex-wrap: wrap;
    }

    .uni-hero-img {
      width: 96px;
      height: 96px;
      border-radius: var(--radius);
      object-fit: cover;
      border: 1px solid var(--border);
      flex-shrink: 0;
    }

    .uni-hero-placeholder {
      width: 96px;
      height: 96px;
      border-radius: var(--radius);
      background: linear-gradient(135deg, rgba(79, 110, 247, 0.15), rgba(124, 58, 237, 0.1));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      font-weight: 700;
      color: var(--accent);
      flex-shrink: 0;
      border: 1px solid var(--border);
      font-family: 'Space Grotesk', sans-serif;
    }

    .uni-hero-info {
      flex: 1;
      min-width: 200px;
    }

    .uni-hero-info h2 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -0.3px;
      margin-bottom: 4px;
    }

    .uni-hero-info .sub-name {
      font-size: 13px;
      color: var(--text-s);
      margin-bottom: 10px;
    }

    .uni-meta-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
      margin-top: 10px;
    }

    .meta-pill {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 10px;
      border-radius: 20px;
      background: var(--surface-h);
      border: 1px solid var(--border);
      font-size: 12px;
      color: var(--text-m);
    }

    .meta-pill strong {
      color: var(--text);
    }

    .meta-pill.green {
      background: rgba(34, 197, 94, 0.08);
      border-color: rgba(34, 197, 94, 0.2);
      color: #4ade80;
    }

    .meta-pill.blue {
      background: rgba(79, 110, 247, 0.08);
      border-color: rgba(79, 110, 247, 0.2);
      color: var(--accent-h);
    }

    .meta-pill.amber {
      background: rgba(245, 158, 11, 0.08);
      border-color: rgba(245, 158, 11, 0.2);
      color: #fcd34d;
    }

    body.light .meta-pill.green {
      color: #15803d;
    }

    body.light .meta-pill.blue {
      color: #3a57e8;
    }

    body.light .meta-pill.amber {
      color: #92400e;
    }

    .uni-hero-actions {
      display: flex;
      flex-direction: column;
      gap: 8px;
      flex-shrink: 0;
    }

    /* ── INFO GRID ── */
    .info-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    @media(max-width:900px) {
      .info-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media(max-width:600px) {
      .info-grid {
        grid-template-columns: 1fr;
      }
    }

    .info-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.25rem;
    }

    .info-card-title {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.6px;
      text-transform: uppercase;
      color: var(--text-s);
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .info-card-title svg {
      opacity: 0.5;
      flex-shrink: 0;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 6px 0;
      border-bottom: 1px solid var(--border);
      font-size: 13px;
      gap: 8px;
    }

    .info-row:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .info-row .lbl {
      color: var(--text-s);
      flex-shrink: 0;
    }

    .info-row .val {
      color: var(--text);
      font-weight: 500;
      text-align: right;
    }

    .yes-badge {
      color: #4ade80;
      font-weight: 600;
    }

    .no-badge {
      color: var(--text-s);
    }

    body.light .yes-badge {
      color: #15803d;
    }

    /* ── MODES & ACCREDITATIONS ── */
    .tags-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .mode-tag {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      background: rgba(79, 110, 247, 0.1);
      color: var(--accent-h);
      border: 1px solid rgba(79, 110, 247, 0.2);
    }

    body.light .mode-tag {
      background: rgba(79, 110, 247, 0.08);
      color: #3a57e8;
    }

    .accr-badge-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .accr-badge {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 5px;
      padding: 8px 12px;
      background: var(--surface-h);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 11px;
      font-weight: 600;
      color: var(--text-m);
      min-width: 60px;
      text-align: center;
    }

    .accr-badge img {
      width: 36px;
      height: 36px;
      object-fit: contain;
      border-radius: 4px;
      background: #fff;
      padding: 2px;
    }

    .accr-badge .accr-initial {
      width: 36px;
      height: 36px;
      border-radius: 4px;
      background: rgba(79, 110, 247, 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 700;
      color: var(--accent);
    }

    /* ── KEY ADVANTAGES ── */
    .advantages-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .advantages-list li {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      padding: 6px 0;
      font-size: 13px;
      color: var(--text-m);
      border-bottom: 1px solid var(--border);
    }

    .advantages-list li:last-child {
      border-bottom: none;
    }

    .advantages-list li::before {
      content: '';
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--accent);
      margin-top: 5px;
      flex-shrink: 0;
    }

    /* ── CERTIFICATE IMAGE ── */
    .cert-img {
      max-width: 100%;
      height: 160px;
      object-fit: contain;
      background: var(--surface-h);
      border-radius: var(--radius-sm);
      border: 1px solid var(--border);
      display: block;
      margin: 0 auto;
      cursor: zoom-in;
    }

    /* ── COURSES TABLE ── */
    .courses-section {
      margin-top: 1.5rem;
    }

    .course-level-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
    }

    .badge-ug {
      background: rgba(79, 110, 247, 0.12);
      color: #818cf8;
    }

    .badge-pg {
      background: rgba(124, 58, 237, 0.12);
      color: #a78bfa;
    }

    body.light .badge-ug {
      background: rgba(79, 110, 247, 0.1);
      color: #3a57e8;
    }

    body.light .badge-pg {
      background: rgba(124, 58, 237, 0.1);
      color: #6d28d9;
    }

    .rating-star {
      color: #f59e0b;
      font-size: 13px;
    }

    .discount-tag {
      display: inline-block;
      padding: 1px 6px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
      background: rgba(34, 197, 94, 0.1);
      color: #4ade80;
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    body.light .discount-tag {
      color: #15803d;
    }

    /* Responsive table scroll */
    .table-scroll {
      overflow-x: auto;
    }

    @media(max-width:768px) {
      .uni-hero {
        flex-direction: column;
      }

      .uni-hero-actions {
        flex-direction: row;
        flex-wrap: wrap;
        width: 100%;
      }

      .uni-hero-img,
      .uni-hero-placeholder {
        width: 72px;
        height: 72px;
      }
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>

      <!-- Breadcrumb -->
      <div
        style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-s);margin-bottom:1.25rem;flex-wrap:wrap;">
        <a href="<?= ADMIN_URL ?>/universities/index.php"
          style="color:var(--accent);text-decoration:none;">Universities</a>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round">
          <polyline points="9 18 15 12 9 6" />
        </svg>
        <span><?= e(get_display_name($uni['name'], $uni['display_name'])) ?></span>
      </div>

      <!-- ── HERO ── -->
      <div class="uni-hero">
        <?php if ($uni['image']): ?>
          <img src="<?= e($uni['image']) ?>" class="uni-hero-img" alt="<?= e($uni['name']) ?>">
        <?php else: ?>
          <div class="uni-hero-placeholder"><?= strtoupper(substr($uni['name'], 0, 1)) ?></div>
        <?php endif; ?>

        <div class="uni-hero-info">
          <h2><?= e(get_display_name($uni['name'], $uni['display_name'])) ?></h2>
          <?php if ($uni['display_name'] && $uni['display_name'] !== $uni['name']): ?>
            <div class="sub-name"><?= e($uni['name']) ?></div>
          <?php endif; ?>

          <div class="uni-meta-pills">
            <?php if ($uni['rating']): ?>
              <span class="meta-pill amber">⭐ <strong><?= e($uni['rating']) ?></strong> / 5</span>
            <?php endif; ?>
            <?php if ($uni['nirf_ranking']): ?>
              <span class="meta-pill blue">NIRF <strong>#<?= e($uni['nirf_ranking']) ?></strong></span>
            <?php endif; ?>
            <?php if ($uni['university_type']): ?>
              <span class="meta-pill"><?= e($uni['university_type']) ?></span>
            <?php endif; ?>
            <?php if ($uni['campus_location']): ?>
              <span class="meta-pill">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <?= e($uni['campus_location']) ?>
              </span>
            <?php endif; ?>
            <?php if ($uni['year_of_establishment']): ?>
              <span class="meta-pill">Est. <?= e($uni['year_of_establishment']) ?></span>
            <?php endif; ?>
            <?php if ($edu_modes): ?>
              <?php foreach ($edu_modes as $m): ?>
                <span class="meta-pill green"><?= e($m) ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="uni-hero-actions">
          <a href="<?= ADMIN_URL ?>/universities/edit.php?id=<?= $id ?>" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            Edit University
          </a>
          <a href="<?= ADMIN_URL ?>/universities/index.php" class="btn btn-secondary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round">
              <line x1="19" y1="12" x2="5" y2="12" />
              <polyline points="12 19 5 12 12 5" />
            </svg>
            Back to List
          </a>
          <?php if ($uni['view_university_link']): ?>
            <a href="<?= e($uni['view_university_link']) ?>" target="_blank" class="btn btn-secondary">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                <polyline points="15 3 21 3 21 9" />
                <line x1="10" y1="14" x2="21" y2="3" />
              </svg>
              Visit Website
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── INFO GRID ── -->
      <div class="info-grid">

        <!-- Placement & Fees -->
        <div class="info-card">
          <div class="info-card-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z" />
            </svg>
            Placement & Fees
          </div>
          <div class="info-row">
            <span class="lbl">Avg. Package</span>
            <span class="val"><?= $uni['avg_placement_package'] ? e($uni['avg_placement_package']) : '—' ?></span>
          </div>
          <div class="info-row">
            <span class="lbl">Placement Support</span>
            <span class="val <?= $uni['placement_assistance'] ? 'yes-badge' : 'no-badge' ?>">
              <?= $uni['placement_assistance'] ? '✓ Yes' : '✗ No' ?>
            </span>
          </div>
          <div class="info-row">
            <span class="lbl">EMI Facility</span>
            <span class="val <?= $uni['emi_facility'] ? 'yes-badge' : 'no-badge' ?>">
              <?= $uni['emi_facility'] ? '✓ Yes' : '✗ No' ?>
            </span>
          </div>
          <div class="info-row">
            <span class="lbl">Scholarship</span>
            <span class="val <?= $uni['scholarship'] ? 'yes-badge' : 'no-badge' ?>">
              <?= $uni['scholarship'] ? '✓ Available' : '✗ No' ?>
            </span>
          </div>
        </div>

        <!-- Modes -->
        <div class="info-card">
          <div class="info-card-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round">
              <circle cx="12" cy="12" r="3" />
              <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
            </svg>
            Education & Exam Modes
          </div>
          <div class="info-row" style="flex-direction:column;align-items:flex-start;gap:8px;">
            <span class="lbl">Education Modes</span>
            <?php if ($edu_modes): ?>
              <div class="tags-wrap">
                <?php foreach ($edu_modes as $m): ?>
                  <span class="mode-tag"><?= e($m) ?></span>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <span style="color:var(--text-s);font-size:12px;">None set</span>
            <?php endif; ?>
          </div>
          <div class="info-row" style="flex-direction:column;align-items:flex-start;gap:8px;">
            <span class="lbl">Exam Modes</span>
            <?php if ($exam_modes): ?>
              <div class="tags-wrap">
                <?php foreach ($exam_modes as $m): ?>
                  <span class="mode-tag"
                    style="background:rgba(124,58,237,0.1);color:#a78bfa;border-color:rgba(124,58,237,0.2);"><?= e($m) ?></span>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <span style="color:var(--text-s);font-size:12px;">None set</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Accreditations -->
        <div class="info-card">
          <div class="info-card-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            Accreditations (<?= count($accreditations) ?>)
          </div>
          <?php if ($accreditations): ?>
            <div class="accr-badge-wrap">
              <?php foreach ($accreditations as $a): ?>
                <div class="accr-badge">
                  <?php if ($a['image']): ?>
                    <img src="<?= e($a['image']) ?>" alt="<?= e($a['name']) ?>">
                  <?php else: ?>
                    <div class="accr-initial"><?= strtoupper(substr($a['name'], 0, 2)) ?></div>
                  <?php endif; ?>
                  <?= e($a['name']) ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <span style="color:var(--text-s);font-size:12px;">No accreditations assigned</span>
          <?php endif; ?>
        </div>

      </div>

      <!-- ── TWO COL: Key Advantages + Certificate ── -->
      <?php if ($uni['key_advantages'] || $uni['sample_certificate']): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem;margin-bottom:1.25rem;">
          <?php if ($uni['key_advantages']): ?>
            <div class="info-card">
              <div class="info-card-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                  stroke-linecap="round">
                  <polyline points="9 11 12 14 22 4" />
                  <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
                Key Advantages
              </div>
              <ul class="advantages-list">
                <?php
                $advs = array_filter(array_map('trim', explode("\n", $uni['key_advantages'])));
                if ($advs):
                  foreach ($advs as $adv): ?>
                    <li><?= e($adv) ?></li>
                  <?php endforeach;
                else: ?>
                  <li><?= e($uni['key_advantages']) ?></li>
                <?php endif; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if ($uni['sample_certificate']): ?>
            <div class="info-card">
              <div class="info-card-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                  stroke-linecap="round">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14 2 14 8 20 8" />
                </svg>
                Sample Certificate
              </div>
              <a href="<?= e($uni['sample_certificate']) ?>" data-lightbox="cert">
                <img src="<?= e($uni['sample_certificate']) ?>" class="cert-img" alt="Sample Certificate">
              </a>
              <span style="font-size:11px;color:var(--text-s);margin-top:6px;display:block;">Click to view full size</span>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- ── COURSES SECTION ── -->
      <div class="courses-section">
        <div class="section-title">
          Courses Offered
          <span style="font-size:12px;font-weight:400;color:var(--text-s);margin-left:4px;"><?= count($courses) ?>
            course<?= count($courses) != 1 ? 's' : '' ?></span>
        </div>

        <?php if ($courses): ?>
          <div class="panel">
            <div class="panel-header">
              <span>All Courses at <?= e(get_display_name($uni['name'], $uni['display_name'])) ?></span>
              <a href="<?= ADMIN_URL ?>/mappings/add.php?university_id=<?= $id ?>"
                style="color:var(--accent);text-decoration:none;font-size:12px;display:flex;align-items:center;gap:4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                  stroke-linecap="round">
                  <line x1="12" y1="5" x2="12" y2="19" />
                  <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Course Mapping
              </a>
            </div>
            <div class="table-scroll">
              <table>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Course</th>
                    <th>Level</th>
                    <th>Mode</th>
                    <th>Duration</th>
                    <th>Fees</th>
                    <th>Discount</th>
                    <th>Rating</th>
                    <th>Brochure</th>
                    <th style="width:90px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($courses as $i => $c): ?>
                    <tr>
                      <td data-label="#"> <?= $i + 1 ?> </td>
                      <td data-label="Course">
                        <div style="text-align: right;">
                          <span class="cell-name"><?= e(get_display_name($c['name'], $c['display_name'])) ?></span>
                          <?php if ($c['display_name'] && $c['display_name'] !== $c['name']): ?>
                            <div style="font-size:11px;color:var(--text-s);"><?= e($c['name']) ?></div>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td data-label="Level"><span class="course-level-badge badge-<?= strtolower($c['course_level']) ?>"><?= e($c['course_level']) ?></span></td>
                      <td data-label="Mode">
                        <span style="font-size:12px;background:rgba(79,110,247,0.1);color:var(--accent-h);padding:2px 8px;border-radius:10px;">
                          <?= e($c['education_mode']) ?>
                        </span>
                      </td>
                      <td data-label="Duration" style="font-size:12px;"><?= $c['course_duration'] ? e($c['course_duration']) : '—' ?></td>
                      <td data-label="Fees" style="font-size:13px;font-weight:500;">
                        <?= $c['academic_fees'] ? '₹' . number_format($c['academic_fees'], 0) : '—' ?>
                      </td>
                      <td data-label="Discount">
                        <?php if ($c['fees_discount']): ?>
                          <span class="discount-tag">₹<?= number_format($c['fees_discount'], 0) ?> off</span>
                        <?php else: ?>
                          <span style="color:var(--text-s);">—</span>
                        <?php endif; ?>
                      </td>
                      <td data-label="Rating">
                        <?php if ($c['course_rating']): ?>
                          <span class="rating-star">★</span> <?= e($c['course_rating']) ?>
                        <?php else: ?>—<?php endif; ?>
                      </td>
                      <td data-label="Brochure">
                        <?php if ($c['brochure_file']): ?>
                          <a href="<?= e($c['brochure_file']) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Download Brochure">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="7 10 12 15 17 10" /><line x1="12" y1="15" x2="12" y2="3" /></svg> PDF
                          </a>
                        <?php else: ?>
                          <span style="color:var(--text-s);font-size:12px;">—</span>
                        <?php endif; ?>
                      </td>
                      <td data-label="Actions">
                        <div class="action-col">
                          <a href="<?= ADMIN_URL ?>/courses/view.php?id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="View Course Details">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" /><circle cx="12" cy="12" r="3" /></svg>
                          </a>
                          <a href="<?= ADMIN_URL ?>/mappings/edit.php?id=<?= $c['mapping_id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="Edit mapping">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

        <?php else: ?>
          <div class="panel">
            <div class="empty-state" style="padding:2.5rem;">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                stroke-linecap="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
              </svg>
              <p>No courses mapped yet.</p>
              <a href="<?= ADMIN_URL ?>/mappings/add.php?university_id=<?= $id ?>" class="btn btn-primary"
                style="margin-top:1rem;display:inline-flex;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                  stroke-linecap="round">
                  <line x1="12" y1="5" x2="12" y2="19" />
                  <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Course Mapping
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>