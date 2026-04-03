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
    set_flash('error', 'Invalid mapping.');
    redirect(ADMIN_URL . '/mappings/index.php');
}

$sql = "
    SELECT uc.*, 
           u.name as u_name, u.display_name as u_disp,
           c.name as c_name, c.display_name as c_disp, c.course_level,
           m.mode_name
    FROM university_courses uc
    JOIN universities u ON uc.university_id = u.id
    JOIN courses c ON uc.course_id = c.id
    JOIN education_modes m ON uc.education_mode_id = m.id
    WHERE uc.id = ? AND uc.is_active = 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$mapping = $stmt->fetch();

if (!$mapping) {
    set_flash('error', 'Mapping not found.');
    redirect(ADMIN_URL . '/mappings/index.php');
}

$active_page = 'mappings';
$page_title = 'View Mapping';
$page_subtitle = 'Course-to-University link details';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mapping — SODE AI Tools</title>
    <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .detail-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 12px;
            color: var(--text-s);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
        <div class="content">
            <?= render_flash() ?>

            <div class="page-header" style="flex-wrap: wrap;">
                <div>
                    <h3>Mapping Details</h3>
                    <p>Created on <?= date('d M Y', strtotime($mapping['created_at'])) ?></p>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="<?= ADMIN_URL ?>/mappings/index.php" class="btn btn-secondary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <line x1="19" y1="12" x2="5" y2="12" />
                            <polyline points="12 19 5 12 12 5" />
                        </svg> Back
                    </a>
                    <a href="edit.php?id=<?= $mapping['id'] ?>" class="btn btn-primary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg> Edit Mapping
                    </a>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-card">
                    <div class="section-title" style="margin-bottom:1rem;color:var(--accent);">Entity Details</div>
                    <div class="detail-row">
                        <div class="detail-label">University</div>
                        <div class="detail-value">
                            <a href="<?= ADMIN_URL ?>/universities/view.php?id=<?= $mapping['university_id'] ?>"
                                style="color:var(--accent);text-decoration:none;font-weight:600;">
                                <?= e(get_display_name($mapping['u_name'], $mapping['u_disp'])) ?>
                            </a>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Course</div>
                        <div class="detail-value">
                            <a href="<?= ADMIN_URL ?>/courses/view.php?id=<?= $mapping['course_id'] ?>"
                                style="color:var(--accent);text-decoration:none;font-weight:600;">
                                <?= e(get_display_name($mapping['c_name'], $mapping['c_disp'])) ?>
                            </a>
                            <?= $mapping['course_level'] ? '<span class="badge" style="background:var(--surface-h);">' . e($mapping['course_level']) . '</span>' : '' ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Education Mode</div>
                        <div class="detail-value">
                            <span class="badge"
                                style="background:rgba(79,110,247,0.1);color:var(--accent-h);"><?= e($mapping['mode_name']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <div class="section-title" style="margin-bottom:1rem;color:var(--accent);">Fees & Feedback</div>
                    <div class="detail-row">
                        <div class="detail-label">Academic Fees</div>
                        <div class="detail-value">
                            <?= $mapping['academic_fees'] ? '₹' . number_format($mapping['academic_fees'], 2) : '—' ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fees Discount</div>
                        <div class="detail-value">
                            <?php if ($mapping['fees_discount']): ?>
                                <span class="discount-tag"
                                    style="background:rgba(16,185,129,0.15);color:#10b981;padding:2px 8px;border-radius:10px;font-size:12px;font-weight:600;">₹<?= number_format($mapping['fees_discount'], 2) ?>
                                    off</span>
                            <?php else: ?>—<?php endif; ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Course Rating</div>
                        <div class="detail-value">
                            <?= $mapping['course_rating'] ? '⭐ ' . e($mapping['course_rating']) . ' / 5' : '—' ?>
                        </div>
                    </div>
                </div>

                <div class="detail-card" style="grid-column:1/-1;">
                    <div class="section-title" style="margin-bottom:1rem;color:var(--accent);">Additional Information
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
                        <div class="detail-row">
                            <div class="detail-label">Course Specializations</div>
                            <div class="detail-value" style="white-space:pre-line;">
                                <?= $mapping['course_specializations'] ? e($mapping['course_specializations']) : '—' ?>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Course Brochure</div>
                            <div class="detail-value">
                                <?php if ($mapping['brochure_file']): ?>
                                    <a href="<?= e($mapping['brochure_file']) ?>" target="_blank"
                                        class="btn btn-secondary btn-sm"
                                        style="display:inline-flex;align-items:center;gap:6px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" y1="15" x2="12" y2="3" />
                                        </svg>
                                        View / Download PDF
                                    </a>
                                <?php else: ?>
                                    <span style="color:var(--text-s);">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>