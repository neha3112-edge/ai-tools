<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_permission('users.view');

$error = '';
$success = '';

// Handle Delete Action
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id === (int)$_SESSION['admin_id']) {
    set_flash('danger', 'You cannot delete your own user account.');
  } else {
    // Check if the target user is a superadmin
    $check_stmt = $pdo->prepare("SELECT is_superadmin FROM admins WHERE id = ?");
    $check_stmt->execute([$id]);
    $target_user = $check_stmt->fetch();
    
    if ($target_user && (int)$target_user['is_superadmin'] === 1 && empty($_SESSION['is_superadmin'])) {
      set_flash('danger', 'Only a Superadmin can delete another Superadmin.');
    } else {
      $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
      try {
        $stmt->execute([$id]);
        set_flash('success', 'User deleted successfully.');
      } catch (Exception $e) {
        set_flash('danger', 'Failed to delete user.');
      }
    }
  }
  redirect(ADMIN_URL . '/rbac/users.php');
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $team_id = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : null;
  $role_id = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
  $is_superadmin = isset($_POST['is_superadmin']) ? 1 : 0;
  if ($is_superadmin === 1 && empty($_SESSION['is_superadmin'])) {
    $is_superadmin = 0;
  }
  $is_active = isset($_POST['is_active']) ? 1 : 0;
  $country_code = trim($_POST['country_code'] ?? '+91');
  $phone = trim($_POST['phone'] ?? '');

  if (empty($name) || empty($email)) {
    $error = 'Name and Email are required.';
  } else {
    if ($action === 'create') {
      if (empty($password)) {
        $error = 'Password is required for new users.';
      } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, plain_password, team_id, role_id, is_superadmin, is_active, country_code, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        try {
          $stmt->execute([$name, $email, $hashed, $password, $team_id, $role_id, $is_superadmin, $is_active, $country_code, $phone]);
          set_flash('success', 'User created successfully.');
          redirect(ADMIN_URL . '/rbac/users.php');
        } catch (Exception $e) {
          $error = 'Email address already exists.';
        }
      }
    } elseif ($action === 'update') {
      $id = (int)$_POST['id'];
      
      // Check if target user is superadmin and caller is not
      $chk = $pdo->prepare("SELECT is_superadmin FROM admins WHERE id = ?");
      $chk->execute([$id]);
      $target = $chk->fetch();
      if ($target && (int)$target['is_superadmin'] === 1 && empty($_SESSION['is_superadmin'])) {
        set_flash('danger', 'Only a Superadmin can edit another Superadmin.');
        redirect(ADMIN_URL . '/rbac/users.php');
      }
      
      // Update core details
      if (!empty($password)) {
        // Password change requested
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ?, password = ?, plain_password = ?, team_id = ?, role_id = ?, is_superadmin = ?, is_active = ?, country_code = ?, phone = ? WHERE id = ?");
        $params = [$name, $email, $hashed, $password, $team_id, $role_id, $is_superadmin, $is_active, $country_code, $phone, $id];
      } else {
        // No password change
        $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ?, team_id = ?, role_id = ?, is_superadmin = ?, is_active = ?, country_code = ?, phone = ? WHERE id = ?");
        $params = [$name, $email, $team_id, $role_id, $is_superadmin, $is_active, $country_code, $phone, $id];
      }

      try {
        $stmt->execute($params);
        
        // If the updated user is the currently logged-in user, refresh their session details
        if ($id === (int)$_SESSION['admin_id']) {
          $_SESSION['admin_name'] = $name;
          $_SESSION['is_superadmin'] = $is_superadmin;
          resolve_user_permissions(); // reload permissions immediately
        }

        set_flash('success', 'User updated successfully.');
        redirect(ADMIN_URL . '/rbac/users.php');
      } catch (Exception $e) {
        $error = 'Email address already exists.';
      }
    }
  }
}

// Determine pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = (int)(($page - 1) * $limit);

$total_count = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
$total_pages = ceil($total_count / $limit);

$users_stmt = $pdo->prepare("
  SELECT a.id, a.name, a.email, a.plain_password, a.is_superadmin, a.is_active, a.country_code, a.phone, t.name as team_name, r.name as role_name 
  FROM admins a 
  LEFT JOIN teams t ON a.team_id = t.id 
  LEFT JOIN roles r ON a.role_id = r.id 
  ORDER BY a.name ASC
  LIMIT :limit OFFSET :offset
");
$users_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$users_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$users_stmt->execute();
$users = $users_stmt->fetchAll();

// Fetch all teams & roles for dropdowns
$teams = $pdo->query("SELECT id, name FROM teams ORDER BY name ASC")->fetchAll();
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name ASC")->fetchAll();

// Fetch user if editing
$edit_user = null;
if (isset($_GET['edit'])) {
  $edit_id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
  $stmt->execute([$edit_id]);
  $fetched_user = $stmt->fetch();
  if ($fetched_user) {
    if ((int)$fetched_user['is_superadmin'] === 1 && empty($_SESSION['is_superadmin'])) {
      set_flash('danger', 'Only a Superadmin can edit another Superadmin.');
      redirect(ADMIN_URL . '/rbac/users.php');
    } else {
      $edit_user = $fetched_user;
    }
  }
}

$active_page = 'users';
$page_title = 'User Management';
$page_subtitle = 'Manage administrative logins';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    /* Modal styles */
    .modal {
      display: none; 
      position: fixed; 
      z-index: 10000; 
      left: 0;
      top: 0;
      width: 100%; 
      height: 100%; 
      overflow: auto; 
      background-color: rgba(0,0,0,0.6); 
      backdrop-filter: blur(4px);
    }
    .modal-content {
      background-color: var(--surface);
      margin: 10% auto; 
      padding: 2rem;
      border: 1px solid var(--border);
      width: 90%;
      max-width: 500px;
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      position: relative;
    }
    .close-btn {
      position: absolute;
      right: 1.5rem;
      top: 1.5rem;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--text-s);
      transition: color 0.2s;
    }
    .close-btn:hover {
      color: var(--text);
    }
    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--border);
    }
    .detail-row:last-child {
      border-bottom: none;
    }
    .detail-label {
      color: var(--text-m);
      font-weight: 500;
    }
    .detail-value {
      color: var(--text);
      font-weight: 600;
    }

    .rbac-container {
      display: grid;
      grid-template-columns: 1.2fr 1.8fr;
      gap: 1.5rem;
    }
    @media (max-width: 1024px) {
      .rbac-container {
        grid-template-columns: 1fr;
      }
    }
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      box-shadow: var(--shadow-sm);
    }
    .form-group {
      margin-bottom: 1.25rem;
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
    .checkbox-item {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 0.5rem;
      cursor: pointer;
    }
    .checkbox-item input {
      cursor: pointer;
      width: 16px;
      height: 16px;
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
    .table-panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }
    th {
      color: var(--text-m);
      font-weight: 600;
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="rbac-container">
        <!-- Create / Edit Form -->
        <div class="card">
          <h3 style="margin-bottom: 1.25rem; font-family: 'Space Grotesk', sans-serif;">
            <?= $edit_user ? 'Edit User' : 'Create User' ?>
          </h3>
          <form method="POST">
            <input type="hidden" name="action" value="<?= $edit_user ? 'update' : 'create' ?>">
            <?php if ($edit_user): ?>
              <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="Full Name" value="<?= htmlspecialchars($edit_user ? $edit_user['name'] : '') ?>" required>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com" value="<?= htmlspecialchars($edit_user ? $edit_user['email'] : '') ?>" required autocomplete="username">
            </div>

            <div style="display: grid; grid-template-columns: 90px 1fr; gap: 1rem; margin-bottom: 1.25rem;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="country_code">Code</label>
                <input type="text" id="country_code" name="country_code" class="form-control" placeholder="+91" value="<?= htmlspecialchars($edit_user ? $edit_user['country_code'] : '+91') ?>">
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone Number" value="<?= htmlspecialchars($edit_user ? $edit_user['phone'] : '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="password">Password 
                <?php if ($edit_user): ?>
                  <span style="color:var(--text-s); font-size:0.8rem;">(Leave blank to keep current)</span>
                  <br>
                  <span style="font-size:0.85rem; color:var(--accent);">Current Password: <strong><?= htmlspecialchars($edit_user['plain_password'] ?: '—') ?></strong></span>
                <?php endif; ?>
              </label>
              <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="password" name="password" class="form-control" placeholder="Min 6 characters" <?= $edit_user ? '' : 'required' ?> autocomplete="new-password" style="padding-right: 45px;">
                <button type="button" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 10px; background: transparent; border: none; color: var(--text-s); cursor: pointer; display: flex; align-items: center; justify-content: center; height: 100%; width: 35px; padding: 0;" title="Toggle Password Visibility">
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>

            <div class="form-group">
              <label for="team_id">Team</label>
              <select id="team_id" name="team_id" class="form-control">
                <option value="">No Team</option>
                <?php foreach ($teams as $t): ?>
                  <option value="<?= $t['id'] ?>" <?= $edit_user && $edit_user['team_id'] == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="role_id">Role</label>
              <select id="role_id" name="role_id" class="form-control">
                <option value="">No Role (Access Denied by default)</option>
                <?php foreach ($roles as $r): ?>
                  <option value="<?= $r['id'] ?>" <?= $edit_user && $edit_user['role_id'] == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
              <?php if (!empty($_SESSION['is_superadmin'])): ?>
              <label class="checkbox-item">
                <input type="checkbox" name="is_superadmin" value="1" <?= $edit_user && $edit_user['is_superadmin'] ? 'checked' : '' ?>>
                <span style="font-size: 0.95rem; font-weight: 500;">Superadmin (Full access bypass)</span>
              </label>
              <?php endif; ?>

              <label class="checkbox-item" style="margin-top: 0.75rem;">
                <input type="checkbox" name="is_active" value="1" <?= !$edit_user || $edit_user['is_active'] ? 'checked' : '' ?>>
                <span style="font-size: 0.95rem; font-weight: 500;">Active Account</span>
              </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
              <?= $edit_user ? 'Update User' : 'Create User' ?>
            </button>
            <?php if ($edit_user): ?>
              <a href="users.php" class="btn btn-secondary" style="display: block; text-align: center; margin-top: 0.5rem; padding: 0.75rem;">Cancel Edit</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Users List -->
        <div class="table-panel">
          <h3 style="padding: 0.5rem 1rem 1rem 1rem; font-family: 'Space Grotesk', sans-serif;">All Users</h3>
          <table>
            <thead>
              <tr>
                <th>User</th>
                <th>Team</th>
                <th>Role</th>
                <th>Status</th>
                <th style="width: 120px; text-align: right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($u['name']) ?></strong>
                    <div style="font-size:0.8rem; color:var(--text-s);"><?= htmlspecialchars($u['email']) ?></div>
                  </td>
                  <td style="color: var(--text-m);"><?= htmlspecialchars($u['team_name'] ?: '—') ?></td>
                  <td>
                    <?php if ($u['is_superadmin']): ?>
                      <span class="badge badge-pg" style="background: rgba(79, 110, 247, 0.2); color: #818cf8;">Superadmin</span>
                    <?php else: ?>
                      <span class="badge badge-ug"><?= htmlspecialchars($u['role_name'] ?: 'No Role') ?></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge" style="background: <?= $u['is_active'] ? 'rgba(34,197,94,0.15); color:var(--success)' : 'rgba(239,68,68,0.15); color:var(--danger)' ?>;">
                      <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <?php 
                    $can_view = true;
                    $can_edit = true;
                    $can_delete = ($u['id'] !== (int)$_SESSION['admin_id']);
                    
                    if ((int)$u['is_superadmin'] === 1 && empty($_SESSION['is_superadmin'])) {
                      $can_view = false;
                      $can_edit = false;
                      $can_delete = false;
                    }
                    ?>
                    <?php if ($can_view): ?>
                      <button type="button" class="btn btn-secondary btn-sm" onclick="showUserDetails(<?= htmlspecialchars(json_encode($u)) ?>)" style="display: inline-flex; padding: 0.4rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border);" title="View Details">
                        👁️
                      </button>
                    <?php endif; ?>
                    <?php if ($can_edit): ?>
                      <a href="users.php?edit=<?= $u['id'] ?>" class="btn btn-secondary btn-sm" style="display: inline-flex; padding: 0.4rem;" title="Edit">
                        ✏️
                      </a>
                    <?php endif; ?>
                    <?php if ($can_delete): ?>
                      <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm" style="display: inline-flex; padding: 0.4rem; background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.2);" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete">
                        🗑️
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?= render_pagination($total_count, 10, $page) ?>
        </div>
      </div>
    </div>
  </main>

  <!-- User Details Modal -->
  <div id="detailsModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h3 style="margin-bottom: 1.5rem; font-family: 'Space Grotesk', sans-serif;">User Details</h3>
      <div id="modalDetailsBody">
        <!-- populated dynamically -->
      </div>
    </div>
  </div>

  <script>
    function showUserDetails(user) {
      const modal = document.getElementById('detailsModal');
      const body = document.getElementById('modalDetailsBody');
      
      const isSuper = user.is_superadmin == 1 ? '<span class="badge badge-pg" style="background: rgba(79, 110, 247, 0.2); color: #818cf8;">Yes</span>' : 'No';
      const isActive = user.is_active == 1 ? '<span class="badge" style="background: rgba(34,197,94,0.15); color:var(--success);">Active</span>' : '<span class="badge" style="background: rgba(239,68,68,0.15); color:var(--danger);">Inactive</span>';
      
      body.innerHTML = `
        <div class="detail-row">
          <span class="detail-label">Name</span>
          <span class="detail-value">${user.name}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Email</span>
          <span class="detail-value">${user.email}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Phone</span>
          <span class="detail-value">${user.country_code ? user.country_code + ' ' : ''}${user.phone || '—'}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Password (Plain)</span>
          <span class="detail-value" style="background: var(--bg); padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 0.95rem;">${user.plain_password || '—'}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Team</span>
          <span class="detail-value">${user.team_name || 'No Team'}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Role</span>
          <span class="detail-value">${user.role_name || 'No Role'}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Superadmin</span>
          <span class="detail-value">${isSuper}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="detail-value">${isActive}</span>
        </div>
      `;
      modal.style.display = 'block';
    }

    function closeModal() {
      document.getElementById('detailsModal').style.display = 'none';
    }

    window.onclick = function(event) {
      const modal = document.getElementById('detailsModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }

    function togglePasswordVisibility(fieldId, btn) {
      const field = document.getElementById(fieldId);
      if (!field) return;
      const isPrivate = field.type === 'password';
      field.type = isPrivate ? 'text' : 'password';
      btn.style.color = isPrivate ? 'var(--accent)' : 'var(--text-s)';
    }
  </script>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>
