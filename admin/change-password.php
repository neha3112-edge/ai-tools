<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

require_login();
require_permission('change_password.view');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current_password = $_POST['current_password'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $error = 'All fields are required.';
  } elseif ($new_password !== $confirm_password) {
    $error = 'New password and confirm password do not match.';
  } elseif (strlen($new_password) < 6) {
    $error = 'New password must be at least 6 characters.';
  } else {
    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['password'])) {
      $hashed = password_hash($new_password, PASSWORD_BCRYPT);
      $update = $pdo->prepare("UPDATE admins SET password = ?, plain_password = ? WHERE id = ?");
      if ($update->execute([$hashed, $new_password, $_SESSION['admin_id']])) {
        $success = 'Password changed successfully.';
      } else {
        $error = 'Database update failed. Please try again.';
      }
    } else {
      $error = 'Current password is incorrect.';
    }
  }
}

$active_page = 'change-password';
$page_title = 'Change Password';
$page_subtitle = 'Manage your account password';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password — SODE AI Tools</title>
  <?php require_once __DIR__ . '/includes/layout_head.php'; ?>
  <style>
    .form-panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 2rem;
      max-width: 500px;
      margin: 2rem auto;
      box-shadow: var(--shadow-sm);
    }
    .form-group {
      margin-bottom: 1.5rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text);
    }
    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font-size: 0.95rem;
    }
    .form-control:focus {
      border-color: var(--accent);
      outline: none;
    }
    .alert {
      padding: 1rem;
      border-radius: var(--radius-sm);
      margin-bottom: 1.5rem;
      font-weight: 500;
    }
    .alert-danger {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.25);
      color: var(--danger);
    }
    .alert-success {
      background: rgba(34, 197, 94, 0.15);
      border: 1px solid rgba(34, 197, 94, 0.25);
      color: var(--success);
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="content">
      <div class="form-panel">
        <h2 style="margin-bottom: 0.5rem; font-family: 'Space Grotesk', sans-serif;">Change Password</h2>
        <p style="color: var(--text-m); margin-bottom: 1.5rem; font-size: 0.9rem;">Change your login credentials. Keep it secure.</p>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label for="current_password">Current Password</label>
            <div style="position: relative; display: flex; align-items: center;">
              <input type="password" id="current_password" name="current_password" class="form-control" required autocomplete="current-password" style="padding-right: 45px;">
              <button type="button" onclick="togglePasswordVisibility('current_password', this)" style="position: absolute; right: 10px; background: transparent; border: none; color: var(--text-s); cursor: pointer; display: flex; align-items: center; justify-content: center; height: 100%; width: 35px; padding: 0;" title="Toggle Password Visibility">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="form-group">
            <label for="new_password">New Password</label>
            <div style="position: relative; display: flex; align-items: center;">
              <input type="password" id="new_password" name="new_password" class="form-control" required autocomplete="new-password" style="padding-right: 45px;">
              <button type="button" onclick="togglePasswordVisibility('new_password', this)" style="position: absolute; right: 10px; background: transparent; border: none; color: var(--text-s); cursor: pointer; display: flex; align-items: center; justify-content: center; height: 100%; width: 35px; padding: 0;" title="Toggle Password Visibility">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <div style="position: relative; display: flex; align-items: center;">
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" required autocomplete="new-password" style="padding-right: 45px;">
              <button type="button" onclick="togglePasswordVisibility('confirm_password', this)" style="position: absolute; right: 10px; background: transparent; border: none; color: var(--text-s); cursor: pointer; display: flex; align-items: center; justify-content: center; height: 100%; width: 35px; padding: 0;" title="Toggle Password Visibility">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">Update Password</button>
        </form>

  <script>
    function togglePasswordVisibility(fieldId, btn) {
      const field = document.getElementById(fieldId);
      if (!field) return;
      const isPrivate = field.type === 'password';
      field.type = isPrivate ? 'text' : 'password';
      btn.style.color = isPrivate ? 'var(--accent)' : 'var(--text-s)';
    }
  </script>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/layout_foot.php'; ?>
</body>

</html>
