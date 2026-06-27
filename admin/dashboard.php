<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_permission('dashboard.view');

// Stats and views conditional checks
$show_uni_stats = has_permission('universities.view');
$show_course_stats = has_permission('courses.view');
$show_mapping_stats = has_permission('mappings.view');
$show_admin_stats = !empty($_SESSION['is_superadmin']);

$stats = [
    'universities' => 0,
    'courses'      => 0,
    'mappings'     => 0,
    'admins'       => 0
];
$recent_unis = [];
$recent_courses = [];
$recent_mappings = [];
$recent_types = [];
$recent_modes = [];
$recent_exam_modes = [];
$recent_accreditations = [];

if ($show_uni_stats) {
    $stats['universities'] = $pdo->query("SELECT COUNT(*) FROM universities WHERE is_active=1")->fetchColumn();
    $recent_unis = $pdo->query("SELECT id,name,display_name,image,rating,created_at FROM universities WHERE is_active=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
}
if ($show_course_stats) {
    $stats['courses'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE is_active=1")->fetchColumn();
    $recent_courses = $pdo->query("SELECT id,name,course_level,created_at FROM courses WHERE is_active=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
}
if ($show_mapping_stats) {
    $stats['mappings'] = $pdo->query("SELECT COUNT(*) FROM university_courses WHERE is_active=1")->fetchColumn();
    $recent_mappings = $pdo->query("
        SELECT uc.id, uc.academic_fees, uc.created_at,
               u.name as u_name, u.display_name as u_disp, u.image as u_image,
               c.name as c_name, c.display_name as c_disp,
               m.mode_name
        FROM university_courses uc
        JOIN universities u ON uc.university_id = u.id
        JOIN courses c ON uc.course_id = c.id
        JOIN education_modes m ON uc.education_mode_id = m.id
        WHERE uc.is_active = 1
        ORDER BY uc.created_at DESC
        LIMIT 5
    ")->fetchAll();
}
$show_recent_types = has_permission('university_types.view');
$show_recent_modes = has_permission('education_modes.view');
$show_recent_exam_modes = has_permission('exam_modes.view');
$show_recent_accreds = has_permission('accreditations.view');

if ($show_recent_types) {
    $recent_types = $pdo->query("SELECT id, type_name FROM university_types WHERE is_active=1 ORDER BY id DESC LIMIT 5")->fetchAll();
}
if ($show_recent_modes) {
    $recent_modes = $pdo->query("SELECT id, mode_name FROM education_modes ORDER BY id DESC LIMIT 5")->fetchAll();
}
if ($show_recent_exam_modes) {
    $recent_exam_modes = $pdo->query("SELECT id, mode_name FROM exam_modes ORDER BY id DESC LIMIT 5")->fetchAll();
}
if ($show_recent_accreds) {
    $recent_accreditations = $pdo->query("SELECT id, name, image FROM accreditations ORDER BY id DESC LIMIT 5")->fetchAll();
}
if ($show_admin_stats) {
    $stats['admins'] = $pdo->query("SELECT COUNT(*) FROM admins WHERE is_active=1")->fetchColumn();
}

$active_page = 'dashboard';
$page_title = 'Dashboard';
$page_subtitle = 'Welcome back, ' . $_SESSION['admin_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — SODE AI Tools</title>
  <?php require_once __DIR__ . '/includes/layout_head.php'; ?>
  <style>
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: linear-gradient(135deg, var(--surface) 0%, rgba(255, 255, 255, 0.01) 100%);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: var(--accent);
      opacity: 0;
      transition: opacity 0.3s;
    }

    .stat-card:hover::before {
      opacity: 1;
    }

    .stat-card:hover {
      border-color: var(--accent);
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(79, 110, 247, 0.15);
    }

    .sc-label {
      font-size: 11px;
      color: var(--text-s);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }

    .sc-value {
      font-size: 32px;
      font-weight: 700;
      font-family: 'Space Grotesk', sans-serif;
      letter-spacing: -1px;
      color: var(--text);
    }

    .sc-sub {
      font-size: 11px;
      color: var(--text-s);
      margin-top: 4px;
    }

    .sc-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .sc-icon.blue {
      background: rgba(79, 110, 247, 0.15);
      color: var(--accent);
    }

    .sc-icon.purple {
      background: rgba(124, 58, 237, 0.15);
      color: #a78bfa;
    }

    .sc-icon.green {
      background: rgba(34, 197, 94, 0.15);
      color: var(--success);
    }

    .sc-icon.amber {
      background: rgba(245, 158, 11, 0.15);
      color: var(--warning);
    }

    body.light .sc-icon.blue {
      background: rgba(79, 110, 247, 0.1);
    }

    body.light .sc-icon.purple {
      background: rgba(124, 58, 237, 0.1);
    }

    body.light .sc-icon.green {
      background: rgba(34, 197, 94, 0.1);
    }

    body.light .sc-icon.amber {
      background: rgba(217, 119, 6, 0.1);
    }

    /* Quick Actions */
    .quick-actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2rem;
    }

    .quick-action-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      text-decoration: none;
      color: var(--text);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-sm);
    }

    .quick-action-card:hover {
      border-color: var(--accent);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(79, 110, 247, 0.12);
      background: rgba(79, 110, 247, 0.02);
    }

    .qa-icon-wrap {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: all 0.3s;
    }
    
    .qa-icon-wrap.blue {
      background: rgba(79, 110, 247, 0.1);
      color: var(--accent);
    }

    .qa-icon-wrap.purple {
      background: rgba(124, 58, 237, 0.1);
      color: #a78bfa;
    }

    .qa-icon-wrap.green {
      background: rgba(34, 197, 94, 0.1);
      color: var(--success);
    }

    .quick-action-card:hover .qa-icon-wrap {
      background: var(--accent);
      color: #fff;
      transform: scale(1.05);
    }

    .qa-content {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .qa-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
    }

    .qa-desc {
      font-size: 11px;
      color: var(--text-s);
    }

    /* Panels & Tables */
    .panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin-bottom: 1.5rem;
      transition: border-color 0.2s;
    }

    .panel:hover {
      border-color: rgba(255,255,255,0.06);
    }

    .panel-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(255, 255, 255, 0.01);
    }

    .panel-header h4 {
      margin: 0;
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
      font-family: 'Space Grotesk', sans-serif;
    }

    .panel-header a {
      font-size: 12px;
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s;
    }

    .panel-header a:hover {
      color: var(--accent-g);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background: rgba(0, 0, 0, 0.08);
      color: var(--text-m);
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
      padding: 0.85rem 1.25rem;
      border-bottom: 1px solid var(--border);
      text-align: left;
    }

    td {
      padding: 0.95rem 1.25rem;
      font-size: 13px;
      color: var(--text);
      border-bottom: 1px solid var(--border);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover td {
      background: rgba(255, 255, 255, 0.01);
    }

    .badge-ug {
      background: rgba(79, 110, 247, 0.15);
      color: #818cf8;
      font-size: 11px;
      padding: 3px 8px;
      border-radius: 6px;
      font-weight: 600;
    }

    .badge-pg {
      background: rgba(124, 58, 237, 0.15);
      color: #a78bfa;
      font-size: 11px;
      padding: 3px 8px;
      border-radius: 6px;
      font-weight: 600;
    }

    body.light .badge-ug {
      background: rgba(79, 110, 247, 0.1);
      color: #3a57e8;
    }

    body.light .badge-pg {
      background: rgba(124, 58, 237, 0.1);
      color: #6d28d9;
    }

    /* Tabs Selector */
    .panel-tabs {
      display: flex;
      gap: 6px;
      border-bottom: 1px solid var(--border);
      padding: 0.6rem 1rem;
      background: rgba(0, 0, 0, 0.05);
      overflow-x: auto;
    }

    .tab-btn {
      background: transparent;
      border: 1px solid transparent;
      color: var(--text-m);
      padding: 0.45rem 0.95rem;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      border-radius: 20px;
      font-family: inherit;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      white-space: nowrap;
    }

    .tab-btn:hover {
      color: var(--text);
      background: rgba(255, 255, 255, 0.03);
    }

    .tab-btn.active {
      background: var(--accent);
      color: #fff;
      border-color: var(--accent);
      box-shadow: 0 4px 12px rgba(79, 110, 247, 0.25);
    }

    .empty-row td {
      text-align: center;
      color: var(--text-s);
      padding: 2rem;
    }

    @media(max-width:1024px) {
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media(max-width:768px) {
      .sc-value {
        font-size: 26px;
      }

      .stat-card {
        padding: 1.25rem;
      }
    }

    @media(max-width:480px) {
      .sc-icon {
        display: none;
      }
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>

      <!-- Stats -->
      <?php
      $visible_cards = [];

      if ($show_uni_stats) {
          $visible_cards[] = '
            <div class="stat-card">
              <div>
                <div class="sc-label">Total Universities</div>
                <div class="sc-value">' . $stats['universities'] . '</div>
                <div class="sc-sub">Active records</div>
              </div>
              <div class="sc-icon blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="1.8" stroke-linecap="round">
                  <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21V11h6v10" />
                </svg></div>
            </div>';
      }

      if ($show_course_stats) {
          $visible_cards[] = '
            <div class="stat-card">
              <div>
                <div class="sc-label">Total Courses</div>
                <div class="sc-value">' . $stats['courses'] . '</div>
                <div class="sc-sub">Active records</div>
              </div>
              <div class="sc-icon purple"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="1.8" stroke-linecap="round">
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg></div>
            </div>';
      }

      if ($show_mapping_stats) {
          $visible_cards[] = '
            <div class="stat-card">
              <div>
                <div class="sc-label">Course Mappings</div>
                <div class="sc-value">' . $stats['mappings'] . '</div>
                <div class="sc-sub">University-course links</div>
              </div>
              <div class="sc-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="1.8" stroke-linecap="round">
                  <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                </svg></div>
            </div>';
      }

      if ($show_admin_stats) {
          $visible_cards[] = '
            <div class="stat-card">
              <div>
                <div class="sc-label">Admin Users</div>
                <div class="sc-value">' . $stats['admins'] . '</div>
                <div class="sc-sub">Active admins</div>
              </div>
              <div class="sc-icon amber"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="1.8" stroke-linecap="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg></div>
            </div>';
      }

      $visible_count = count($visible_cards);
      if ($visible_count > 0):
      ?>
      <div class="stats-grid" style="grid-template-columns: repeat(<?= $visible_count ?>, 1fr);">
          <?php foreach ($visible_cards as $card): ?>
              <?= $card ?>
          <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Quick Actions -->
      <?php
      $quick_actions = [];
      
      if (has_permission('universities.add')) {
          $quick_actions[] = '
            <a href="' . ADMIN_URL . '/universities/add.php" class="quick-action-card">
              <div class="qa-icon-wrap blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                  <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21V11h6v10" />
                </svg>
              </div>
              <div class="qa-content">
                <span class="qa-title">Add University</span>
                <span class="qa-desc">Create university record</span>
              </div>
            </a>';
      }
      
      if (has_permission('courses.add')) {
          $quick_actions[] = '
            <a href="' . ADMIN_URL . '/courses/add.php" class="quick-action-card">
              <div class="qa-icon-wrap purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
              </div>
              <div class="qa-content">
                <span class="qa-title">Add Course</span>
                <span class="qa-desc">Create course profile</span>
              </div>
            </a>';
      }
      
      if (has_permission('mappings.add')) {
          $quick_actions[] = '
            <a href="' . ADMIN_URL . '/mappings/add.php" class="quick-action-card">
              <div class="qa-icon-wrap green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                  <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                </svg>
              </div>
              <div class="qa-content">
                <span class="qa-title">Map Course</span>
                <span class="qa-desc">Link course to university</span>
              </div>
            </a>';
      }
      
      if (has_permission('universities.view')) {
          $quick_actions[] = '
            <a href="' . ADMIN_URL . '/universities/index.php" class="quick-action-card">
              <div class="qa-icon-wrap blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </div>
              <div class="qa-content">
                <span class="qa-title">All Universities</span>
                <span class="qa-desc">Manage existing profiles</span>
              </div>
            </a>';
      }
      
      if (has_permission('courses.view')) {
          $quick_actions[] = '
            <a href="' . ADMIN_URL . '/courses/index.php" class="quick-action-card">
              <div class="qa-icon-wrap purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
              </div>
              <div class="qa-content">
                <span class="qa-title">All Courses</span>
                <span class="qa-desc">Manage course configurations</span>
              </div>
            </a>';
      }
      
      if (!empty($quick_actions)):
      ?>
      <div class="section-title">Quick Actions</div>
      <div class="quick-actions-grid">
        <?php foreach ($quick_actions as $action): ?>
            <?= $action ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Recent Tables -->
      <?php
      $show_recent_unis = has_permission('universities.view');
      $show_recent_courses = has_permission('courses.view');
      $show_recent_mappings = has_permission('mappings.view');
      $show_recent_masters = has_permission('masters.view');

      $total_panels = ($show_recent_unis ? 1 : 0) + ($show_recent_courses ? 1 : 0) + ($show_recent_mappings ? 1 : 0) + ($show_recent_masters ? 1 : 0);

      if ($total_panels > 0):
      ?>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        
        <?php if ($show_recent_unis): ?>
        <!-- Recent Universities -->
        <div>
          <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="color: var(--accent);"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21V11h6v10" /></svg>
            <span>Recent Universities</span>
          </div>
          <div class="panel">
            <div class="panel-header"><span>Latest Added</span><a href="<?= ADMIN_URL ?>/universities/index.php">View
                all &rarr;</a></div>
            <table>
              <thead>
                <tr>
                  <th>University</th>
                  <th>Rating</th>
                  <th>Added</th>
                  <th style="width:50px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recent_unis):
                  foreach ($recent_unis as $u): ?>
                    <tr>
                      <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                          <?php if ($u['image']): ?>
                            <img src="<?= e($u['image']) ?>"
                              style="width:30px;height:30px;border-radius:6px;object-fit:cover;border:1px solid var(--border);flex-shrink:0;"
                              alt="">
                          <?php else: ?>
                            <div
                              style="width:30px;height:30px;border-radius:6px;background:rgba(79,110,247,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--accent);flex-shrink:0;">
                              <?= strtoupper(substr($u['name'], 0, 1)) ?>
                            </div>
                          <?php endif; ?>
                          <span class="cell-name"
                            style="font-size:13px;"><?= e(get_display_name($u['name'], $u['display_name'])) ?></span>
                        </div>
                      </td>
                      <td><?= $u['rating'] ? '⭐ ' . e($u['rating']) : '—' ?></td>
                      <td><?= date('d M', strtotime($u['created_at'])) ?></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/universities/view.php?id=<?= $u['id'] ?>"
                          class="btn btn-secondary btn-sm btn-icon" title="View Details">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                          </svg>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; else: ?>
                  <tr class="empty-row">
                    <td colspan="4">No universities yet. <a href="<?= ADMIN_URL ?>/universities/add.php"
                        style="color:var(--accent)">Add one →</a></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($show_recent_courses): ?>
        <!-- Recent Courses -->
        <div>
          <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="color: #a78bfa;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V3.5A2.5 2.5 0 0 1 6.5 1M20 1v21" /></svg>
            <span>Recent Courses</span>
          </div>
          <div class="panel">
            <div class="panel-header"><span>Latest Added</span><a href="<?= ADMIN_URL ?>/courses/index.php">View all
                &rarr;</a></div>
            <table>
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Level</th>
                  <th>Added</th>
                  <th style="width:50px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recent_courses):
                  foreach ($recent_courses as $c): ?>
                    <tr>
                      <td><span class="cell-name"><?= e($c['name']) ?></span></td>
                      <td><span class="badge badge-<?= strtolower($c['course_level']) ?>"><?= $c['course_level'] ?></span>
                      </td>
                      <td><?= date('d M', strtotime($c['created_at'])) ?></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/courses/view.php?id=<?= $c['id'] ?>"
                          class="btn btn-secondary btn-sm btn-icon" title="View Details">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                          </svg>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; else: ?>
                  <tr class="empty-row">
                    <td colspan="3">No courses yet. <a href="<?= ADMIN_URL ?>/courses/add.php"
                        style="color:var(--accent)">Add one →</a></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($show_recent_mappings): ?>
        <!-- Recent Mappings -->
        <div>
          <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="color: var(--success);"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" /><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" /></svg>
            <span>Recent Mappings</span>
          </div>
          <div class="panel">
            <div class="panel-header"><span>Latest Mappings</span><a href="<?= ADMIN_URL ?>/mappings/index.php">View all &rarr;</a></div>
            <table>
              <thead>
                <tr>
                  <th>University</th>
                  <th>Course</th>
                  <th>Mode</th>
                  <th>Fees</th>
                  <th style="width:50px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recent_mappings):
                  foreach ($recent_mappings as $m): ?>
                    <tr>
                      <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                          <?php if ($m['u_image']): ?>
                            <img src="<?= e($m['u_image']) ?>"
                              style="width:30px;height:30px;border-radius:6px;object-fit:cover;border:1px solid var(--border);flex-shrink:0;"
                              alt="">
                          <?php else: ?>
                            <div
                              style="width:30px;height:30px;border-radius:6px;background:rgba(79,110,247,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--accent);flex-shrink:0;">
                              <?= strtoupper(substr($m['u_name'], 0, 1)) ?>
                            </div>
                          <?php endif; ?>
                          <span class="cell-name" style="font-size:13px;"><?= e(get_display_name($m['u_name'], $m['u_disp'])) ?></span>
                        </div>
                      </td>
                      <td>
                        <span class="cell-name" style="font-size:13px;"><?= e(get_display_name($m['c_name'], $m['c_disp'])) ?></span>
                      </td>
                      <td><span class="badge badge-ug"><?= e($m['mode_name']) ?></span></td>
                      <td><?= $m['academic_fees'] ? '₹' . number_format($m['academic_fees'], 2) : '—' ?></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/mappings/view.php?id=<?= $m['id'] ?>"
                          class="btn btn-secondary btn-sm btn-icon" title="View Details">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                          </svg>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; else: ?>
                  <tr class="empty-row">
                    <td colspan="5">No mappings yet. <a href="<?= ADMIN_URL ?>/mappings/add.php" style="color:var(--accent)">Add one →</a></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($show_recent_masters): ?>
        <!-- Master Settings -->
        <div>
          <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="color: var(--warning);"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" /></svg>
            <span>Master Settings</span>
          </div>
          <div class="panel" style="padding: 0;">
            <div class="panel-header" style="padding: 1rem 1rem 0.5rem 1rem;">
              <span>Latest Configurations</span>
            </div>
            
            <!-- Tabs Header -->
            <div class="panel-tabs" style="display: flex; gap: 4px; border-bottom: 1px solid var(--border); padding: 0.5rem 1rem; background: rgba(255,255,255,0.01); flex-wrap: wrap;">
              <?php 
              $first_active = '';
              if ($show_recent_types) { $first_active = 'tab-types'; }
              elseif ($show_recent_modes) { $first_active = 'tab-modes'; }
              elseif ($show_recent_exam_modes) { $first_active = 'tab-exams'; }
              elseif ($show_recent_accreds) { $first_active = 'tab-accreds'; }
              ?>

              <?php if ($show_recent_types): ?>
              <button class="tab-btn <?= $first_active === 'tab-types' ? 'active' : '' ?>" onclick="switchSettingTab(event, 'tab-types')" style="<?= $first_active === 'tab-types' ? 'background: rgba(79, 110, 247, 0.15); border: none; color: var(--text);' : 'background: none; border: none; color: var(--text-m);' ?> padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); font-family: inherit;">Uni Types</button>
              <?php endif; ?>

              <?php if ($show_recent_modes): ?>
              <button class="tab-btn <?= $first_active === 'tab-modes' ? 'active' : '' ?>" onclick="switchSettingTab(event, 'tab-modes')" style="<?= $first_active === 'tab-modes' ? 'background: rgba(79, 110, 247, 0.15); border: none; color: var(--text);' : 'background: none; border: none; color: var(--text-m);' ?> padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); font-family: inherit;">Edu Modes</button>
              <?php endif; ?>

              <?php if ($show_recent_exam_modes): ?>
              <button class="tab-btn <?= $first_active === 'tab-exams' ? 'active' : '' ?>" onclick="switchSettingTab(event, 'tab-exams')" style="<?= $first_active === 'tab-exams' ? 'background: rgba(79, 110, 247, 0.15); border: none; color: var(--text);' : 'background: none; border: none; color: var(--text-m);' ?> padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); font-family: inherit;">Exam Modes</button>
              <?php endif; ?>

              <?php if ($show_recent_accreds): ?>
              <button class="tab-btn <?= $first_active === 'tab-accreds' ? 'active' : '' ?>" onclick="switchSettingTab(event, 'tab-accreds')" style="<?= $first_active === 'tab-accreds' ? 'background: rgba(79, 110, 247, 0.15); border: none; color: var(--text);' : 'background: none; border: none; color: var(--text-m);' ?> padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); font-family: inherit;">Accreds</button>
              <?php endif; ?>
            </div>

            <!-- Tab Panes -->
            <div style="padding: 0 1rem 1rem 1rem;">
              
              <!-- University Types Pane -->
              <?php if ($show_recent_types): ?>
              <div id="tab-types" class="tab-pane" style="display: <?= $first_active === 'tab-types' ? 'block' : 'none' ?>;">
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Type Name</th>
                      <th style="width: 50px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($recent_types): foreach ($recent_types as $rt): ?>
                    <tr>
                      <td>#<?= $rt['id'] ?></td>
                      <td><span class="cell-name" style="font-size:13px;"><?= e($rt['type_name']) ?></span></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/masters/university_types.php" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 11px;">Manage</a>
                      </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr class="empty-row"><td colspan="3">No university types found.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>

              <!-- Education Modes Pane -->
              <?php if ($show_recent_modes): ?>
              <div id="tab-modes" class="tab-pane" style="display: <?= $first_active === 'tab-modes' ? 'block' : 'none' ?>;">
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Mode Name</th>
                      <th style="width: 50px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($recent_modes): foreach ($recent_modes as $rm): ?>
                    <tr>
                      <td>#<?= $rm['id'] ?></td>
                      <td><span class="cell-name" style="font-size:13px;"><?= e($rm['mode_name']) ?></span></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/masters/modes.php" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 11px;">Manage</a>
                      </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr class="empty-row"><td colspan="3">No education modes found.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>

              <!-- Exam Modes Pane -->
              <?php if ($show_recent_exam_modes): ?>
              <div id="tab-exams" class="tab-pane" style="display: <?= $first_active === 'tab-exams' ? 'block' : 'none' ?>;">
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Exam Mode</th>
                      <th style="width: 50px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($recent_exam_modes): foreach ($recent_exam_modes as $re): ?>
                    <tr>
                      <td>#<?= $re['id'] ?></td>
                      <td><span class="cell-name" style="font-size:13px;"><?= e($re['mode_name']) ?></span></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/masters/exam_modes.php" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 11px;">Manage</a>
                      </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr class="empty-row"><td colspan="3">No exam modes found.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>

              <!-- Accreditations Pane -->
              <?php if ($show_recent_accreds): ?>
              <div id="tab-accreds" class="tab-pane" style="display: <?= $first_active === 'tab-accreds' ? 'block' : 'none' ?>;">
                <table>
                  <thead>
                    <tr>
                      <th>Logo</th>
                      <th>Accreditation</th>
                      <th style="width: 50px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($recent_accreditations): foreach ($recent_accreditations as $ra): ?>
                    <tr>
                      <td>
                        <?php if ($ra['image']): ?>
                          <img src="<?= e($ra['image']) ?>" style="width: 25px; height: 25px; object-fit: contain; background: #fff; border-radius: 4px; padding: 2px; border: 1px solid var(--border);">
                        <?php else: ?>
                          <span style="font-size: 11px; color: var(--text-s);">None</span>
                        <?php endif; ?>
                      </td>
                      <td><span class="cell-name" style="font-size:13px;"><?= e($ra['name']) ?></span></td>
                      <td>
                        <a href="<?= ADMIN_URL ?>/masters/accreditations.php" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 11px;">Manage</a>
                      </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr class="empty-row"><td colspan="3">No accreditations found.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>

            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>
      <?php endif; ?>

    </div>
  </main>

  <script>
  function switchSettingTab(evt, tabId) {
    const card = evt.target.closest('.panel');
    card.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
    
    card.querySelectorAll('.tab-btn').forEach(b => {
      b.classList.remove('active');
      b.style.color = 'var(--text-m)';
      b.style.background = 'none';
    });
    
    card.querySelector('#' + tabId).style.display = 'block';
    
    evt.target.classList.add('active');
    evt.target.style.color = 'var(--text)';
    evt.target.style.background = 'rgba(79, 110, 247, 0.15)';
  }
  </script>

  <?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
</body>

</html>