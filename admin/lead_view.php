<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM brochure_leads WHERE id = ?");
$stmt->execute([$id]);
$lead = $stmt->fetch();

if (!$lead) {
  set_flash('error', 'Lead record not found.');
  redirect('leads.php');
}

$active_page = 'leads';
$page_title = 'View Lead Info';
$page_subtitle = 'Details collected from brochure download';
$base_path = '.';
$logout_path = 'logout.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Lead Details — SODE AI Tools</title>
  <?php require_once __DIR__ . '/includes/layout_head.php'; ?>
  <style>
    .detail-card {
        background: var(--surface);
        padding: 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        max-width: 700px;
        margin: 2rem auto;
    }
    .detail-row {
        display: flex;
        border-bottom: 1px solid var(--border);
        padding: 1rem 0;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        flex: 0 0 150px;
        color: var(--text-m);
        font-weight: 600;
        font-size: 0.95rem;
    }
    .detail-value {
        flex: 1;
        color: var(--text);
        font-size: 1rem;
        word-break: break-all;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="content">
      <div class="page-header" style="max-width: 700px; margin: 0 auto; margin-top:2rem;">
        <div>
          <h3>Lead #<?= str_pad($lead['id'], 4, '0', STR_PAD_LEFT) ?> Details</h3>
          <p>Requested on <?= date('d M Y, h:i A', strtotime($lead['created_at'])) ?></p>
        </div>
        <a href="leads.php" class="btn btn-secondary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
          Back to List
        </a>
      </div>

      <div class="detail-card">
          <div class="detail-row">
              <div class="detail-label">Full Name</div>
              <div class="detail-value" style="font-weight:700; font-size:1.1rem; color:var(--accent);"><?= e($lead['name']) ?></div>
          </div>
          <div class="detail-row">
              <div class="detail-label">Email Address</div>
              <div class="detail-value"><?= e($lead['email']) ?></div>
          </div>
          <div class="detail-row">
              <div class="detail-label">Phone Number</div>
              <div class="detail-value"><?= e($lead['country_code']) ?> <?= e($lead['phone']) ?></div>
          </div>
          <div class="detail-row">
              <div class="detail-label">Selected Course</div>
              <div class="detail-value">
                  <span class="badge" style="background:rgba(37,99,235,0.1); color:#2563eb; font-size:0.9rem;">
                      <?= e($lead['course']) ?>
                  </span>
              </div>
          </div>
          <div class="detail-row">
              <div class="detail-label">State / Region</div>
              <div class="detail-value"><?= e($lead['state']) ?></div>
          </div>
          <div class="detail-row">
              <div class="detail-label">Source URL</div>
              <div class="detail-value">
                  <?php if($lead['page_url']): ?>
                      <a href="<?= e($lead['page_url']) ?>" target="_blank" style="color:var(--accent);"><?= e($lead['page_url']) ?></a>
                  <?php else: ?>
                      <span style="color:var(--text-s);">Not Captured</span>
                  <?php endif; ?>
              </div>
          </div>
          <div class="detail-row">
              <div class="detail-label">User IP Address</div>
              <div class="detail-value">
                  <?php if($lead['user_ip']): ?>
                      <code><?= e($lead['user_ip']) ?></code>
                  <?php else: ?>
                      <span style="color:var(--text-s);">Not Captured</span>
                  <?php endif; ?>
              </div>
          </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
</body>
</html>
