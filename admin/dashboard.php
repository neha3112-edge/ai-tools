<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_login();

// Stats
$stats['universities'] = $pdo->query("SELECT COUNT(*) FROM universities WHERE is_active=1")->fetchColumn();
$stats['courses'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE is_active=1")->fetchColumn();
$stats['mappings'] = $pdo->query("SELECT COUNT(*) FROM university_courses WHERE is_active=1")->fetchColumn();
$stats['admins'] = $pdo->query("SELECT COUNT(*) FROM admins WHERE is_active=1")->fetchColumn();

$recent_unis = $pdo->query("SELECT id,name,display_name,image,rating,created_at FROM universities WHERE is_active=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_courses = $pdo->query("SELECT id,name,course_level,created_at FROM courses WHERE is_active=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();

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
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-bottom: 1.75rem;
    }

    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.25rem;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      transition: border-color 0.2s, transform 0.2s;
    }

    .stat-card:hover {
      border-color: var(--border-h);
      transform: translateY(-2px);
    }

    .sc-label {
      font-size: 12px;
      color: var(--text-s);
      font-weight: 500;
      margin-bottom: 8px;
    }

    .sc-value {
      font-size: 28px;
      font-weight: 700;
      font-family: 'Space Grotesk', sans-serif;
      letter-spacing: -1px;
    }

    .sc-sub {
      font-size: 11px;
      color: var(--text-s);
      margin-top: 4px;
    }

    .sc-icon {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
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

    .quick-actions {
      display: flex;
      gap: .75rem;
      margin-bottom: 1.75rem;
      flex-wrap: wrap;
    }

    .two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .badge-ug {
      background: rgba(79, 110, 247, 0.15);
      color: #818cf8;
    }

    .badge-pg {
      background: rgba(124, 58, 237, 0.15);
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

    .empty-row td {
      text-align: center;
      color: var(--text-s);
      padding: 2rem;
    }

    @media(max-width:1024px) {
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .two-col {
        grid-template-columns: 1fr;
      }
    }

    @media(max-width:768px) {
      .sc-value {
        font-size: 22px;
      }

      .stat-card {
        padding: 1rem;
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
      <div class="stats-grid">
        <div class="stat-card">
          <div>
            <div class="sc-label">Total Universities</div>
            <div class="sc-value"><?= $stats['universities'] ?></div>
            <div class="sc-sub">Active records</div>
          </div>
          <div class="sc-icon blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="1.8" stroke-linecap="round">
              <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21V11h6v10" />
            </svg></div>
        </div>
        <div class="stat-card">
          <div>
            <div class="sc-label">Total Courses</div>
            <div class="sc-value"><?= $stats['courses'] ?></div>
            <div class="sc-sub">Active records</div>
          </div>
          <div class="sc-icon purple"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="1.8" stroke-linecap="round">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
            </svg></div>
        </div>
        <div class="stat-card">
          <div>
            <div class="sc-label">Course Mappings</div>
            <div class="sc-value"><?= $stats['mappings'] ?></div>
            <div class="sc-sub">University-course links</div>
          </div>
          <div class="sc-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="1.8" stroke-linecap="round">
              <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
            </svg></div>
        </div>
        <div class="stat-card">
          <div>
            <div class="sc-label">Admin Users</div>
            <div class="sc-value"><?= $stats['admins'] ?></div>
            <div class="sc-sub">Active admins</div>
          </div>
          <div class="sc-icon amber"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="1.8" stroke-linecap="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
            </svg></div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section-title">Quick Actions</div>
      <div class="quick-actions">
        <a href="<?= ADMIN_URL ?>/universities/add.php" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Add University
        </a>
        <a href="<?= ADMIN_URL ?>/courses/add.php" class="btn btn-secondary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Add Course
        </a>
        <a href="<?= ADMIN_URL ?>/mappings/add.php" class="btn btn-secondary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Map Course to University
        </a>
        <a href="<?= ADMIN_URL ?>/universities/index.php" class="btn btn-secondary">View All Universities</a>
        <a href="<?= ADMIN_URL ?>/courses/index.php" class="btn btn-secondary">View All Courses</a>
      </div>

      <!-- Recent Tables -->
      <div class="two-col">
        <div>
          <div class="section-title">Recent Universities</div>
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
        <div>
          <div class="section-title">Recent Courses</div>
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
      </div>

    </div>
  </main>

  <?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
</body>

</html>