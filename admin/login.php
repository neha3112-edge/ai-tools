<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email=? AND is_active=1 LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: ' . ADMIN_URL . '/dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — SODE AI Tools</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0a0d14; --surface: #111520; --border: rgba(255,255,255,0.08);
    --accent: #4f6ef7; --accent-g: #7c3aed; --text: #f1f3f9;
    --text-m: #8b92a8; --text-s: #545c72; --danger: #ef4444;
    --radius: 14px; --radius-sm: 8px;
  }
  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--bg); min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    color: var(--text); padding: 1rem;
  }
  body::before {
    content: ''; position: fixed; top: -30%; left: -20%;
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(79,110,247,0.12) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
  }
  body::after {
    content: ''; position: fixed; bottom: -30%; right: -20%;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(124,58,237,0.10) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
  }
  .login-wrap { position: relative; z-index: 1; width: 100%; max-width: 420px; }
  .brand { text-align: center; margin-bottom: 2rem; }
  .brand-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-g) 100%);
    border-radius: 16px; display: inline-flex; align-items: center;
    justify-content: center; margin-bottom: 1rem; font-size: 22px;
  }
  .brand h1 { font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
  .brand p  { font-size: 13px; color: var(--text-m); margin-top: 4px; }
  .card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 2rem;
  }
  .card-title { font-size: 17px; font-weight: 600; margin-bottom: 1.5rem; }
  .alert-error {
    background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
    border-radius: var(--radius-sm); padding: .75rem 1rem;
    font-size: 13px; color: #fca5a5; margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: 8px;
  }
  .form-group { margin-bottom: 1.1rem; }
  .form-group label { display: block; font-size: 13px; font-weight: 500; color: var(--text-m); margin-bottom: 6px; }
  .form-group input {
    width: 100%; background: rgba(255,255,255,0.04);
    border: 1px solid var(--border); border-radius: var(--radius-sm);
    padding: .7rem .9rem; font-size: 14px; font-family: inherit;
    color: var(--text); transition: border-color .2s, box-shadow .2s; outline: none;
  }
  .form-group input::placeholder { color: var(--text-s); }
  .form-group input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,110,247,0.15); }
  .pw-wrap { position: relative; }
  .pw-wrap input { padding-right: 2.8rem; }
  .pw-toggle {
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: var(--text-s);
    display: flex; align-items: center; padding: 0; transition: color .2s;
  }
  .pw-toggle:hover { color: var(--text-m); }
  .btn-login {
    width: 100%; padding: .8rem;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-g) 100%);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-size: 14px; font-weight: 600; font-family: inherit;
    cursor: pointer; margin-top: .5rem;
    transition: opacity .2s, transform .15s; letter-spacing: .2px;
  }
  .btn-login:hover  { opacity: .92; }
  .btn-login:active { transform: scale(.99); }
  .footer-note { text-align: center; margin-top: 1.5rem; font-size: 12px; color: var(--text-s); }
</style>
</head>
<body>
<div class="login-wrap">
  <div class="brand">
    <div class="brand-icon">&#9881;</div>
    <h1>SODE AI Tools</h1>
    <p>Admin Panel &mdash; Authorized Access Only</p>
  </div>
  <div class="card">
    <div class="card-title">Sign in to your account</div>
    <?php if ($error): ?>
    <div class="alert-error">
      <svg width="15" height="15" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="#f87171" stroke-width="1.5"/><path d="M10 5.5v5M10 13.5v.5" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" placeholder="admin@sode.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="pw-wrap">
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
          <button type="button" class="pw-toggle" onclick="togglePw()" aria-label="Toggle password">
            <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-login">Sign In &rarr;</button>
    </form>
  </div>
  <p class="footer-note">&copy; <?= date('Y') ?> SODE AI Tools. Internal use only.</p>
</div>
<script>
function togglePw() {
  const inp = document.getElementById('password');
  inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
