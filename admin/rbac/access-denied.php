<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_login();

$active_page = '';
$page_title = 'Access Denied';
$page_subtitle = 'Insufficient Permissions';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Denied — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    .denied-panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 3rem 2rem;
      max-width: 600px;
      margin: 4rem auto;
      text-align: center;
      box-shadow: var(--shadow-sm);
    }
    .denied-icon {
      font-size: 4rem;
      color: var(--danger);
      margin-bottom: 1.5rem;
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <div class="denied-panel">
        <div class="denied-icon">🛑</div>
        <h2 style="font-family: 'Space Grotesk', sans-serif; margin-bottom: 1rem;">Access Denied</h2>
        <p style="color: var(--text-m); margin-bottom: 2rem; font-size: 1.05rem; line-height: 1.6;">
          You do not have the required permissions to access this page. Please contact the main administrator if you believe this is an error.
        </p>
        <a href="<?= ADMIN_URL ?>/index.php" class="btn btn-primary" style="display: inline-flex; justify-content: center; padding: 0.75rem 2rem;">
          Return to panel
        </a>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>
