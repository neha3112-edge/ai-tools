<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) {
  set_flash('error', 'No lead ID provided.');
  redirect(ADMIN_URL . '/counseling_leads.php');
}

try {
  $stmt = $pdo->prepare("SELECT * FROM counseling_leads WHERE id = ?");
  $stmt->execute([$id]);
  $lead = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$lead) {
    set_flash('error', 'counseling Lead not found.');
    redirect(ADMIN_URL . '/counseling_leads.php');
  }
} catch (PDOException $e) {
  set_flash('error', 'Database error: ' . $e->getMessage());
  redirect(ADMIN_URL . '/counseling_leads.php');
}

$active_page = 'counseling_leads';
$page_title = 'View counseling Lead';
$page_subtitle = 'Details for ' . e($lead['name']);
$base_path = '.';
$logout_path = 'logout.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View counseling Lead — SODE AI Tools</title>
  <?php require_once __DIR__ . '/includes/layout_head.php'; ?>
  <style>
    .lead-details-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    @media (min-width: 768px) {
      .lead-details-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .detail-card {
      background: var(--bg-secondary);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 1.5rem;
    }

    .detail-card h3 {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      color: var(--accent-blue);
      border-bottom: 1px solid var(--border);
      padding-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 8px;
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
      font-size: 0.8rem;
      color: var(--text-m);
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 0.25rem;
    }

    .detail-value {
      font-size: 1rem;
      color: var(--text);
      font-weight: 500;
    }

    .detail-value.highlight {
      color: var(--accent-blue);
      font-weight: 600;
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="content">
      <div class="page-header" style="margin-bottom: 1.5rem;">
        <div>
          <h3>Lead Details</h3>
        </div>
        <div class="page-actions">
          <a href="counseling_leads.php" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Leads
          </a>
        </div>
      </div>

      <div class="panel">
        <div class="lead-details-grid">
          <!-- User Information -->
          <div class="detail-card">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              Contact Information
            </h3>
            <div class="detail-row">
              <span class="detail-label">Full Name</span>
              <span class="detail-value"><?= e($lead['name']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Email Address</span>
              <span class="detail-value highlight">
                <a href="mailto:<?= e($lead['email']) ?>"
                  style="color:inherit; text-decoration:none;"><?= e($lead['email']) ?></a>
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Phone Number</span>
              <span class="detail-value highlight">
                <a href="tel:<?= e($lead['country_code']) . e($lead['phone']) ?>"
                  style="color:inherit; text-decoration:none;">
                  <?= e($lead['country_code']) ?> <?= e($lead['phone']) ?>
                </a>
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">State</span>
              <span class="detail-value"><?= e($lead['state']) ?></span>
            </div>
          </div>

          <!-- counseling Context -->
          <div class="detail-card">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 12 20 22 4 22 4 12"></polyline>
                <rect x="2" y="7" width="20" height="5"></rect>
                <line x1="12" y1="22" x2="12" y2="7"></line>
                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
              </svg>
              counseling Request Interest
            </h3>
            <div class="detail-row">
              <span class="detail-label">Target University</span>
              <span class="detail-value"
                style="color:var(--success); font-weight:700;"><?= e($lead['uni_name'] ?: 'N/A') ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Target Course</span>
              <span class="detail-value">
                <span class="badge"
                  style="background:rgba(37,99,235,0.1); color:#2563eb; font-size:0.9rem; padding:6px 12px;">
                  <?= e($lead['course']) ?>
                </span>
              </span>
            </div>
            <div class="detail-row" style="margin-top:1.5rem;">
              <span class="detail-label">Requested On</span>
              <span class="detail-value"><?= date('l, d F Y, h:i A', strtotime($lead['created_at'])) ?></span>
            </div>
          </div>

          <!-- Analytics Context -->
          <div class="detail-card" style="grid-column: 1 / -1;">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                </path>
              </svg>
              Technical Context
            </h3>
            <div class="detail-row">
              <span class="detail-label">Submission Page URL</span>
              <span class="detail-value"><a href="<?= e($lead['page_url']) ?>" target="_blank"
                  style="color:var(--accent-blue);"><?= e($lead['page_url']) ?></a></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">User IP Address</span>
              <span class="detail-value"><code
                  style="background:#e2e8f0; color:#000; padding:2px 6px; border-radius:4px;"><?= e($lead['user_ip']) ?></code></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
</body>

</html>
