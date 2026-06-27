<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_permission('roles.view');

$error = '';
$success = '';

// Available Sidebar Modules - dynamically retrieved from helper
$modules_list = get_sidebar_modules();

// Available CRUD actions
$actions_list = ['Read', 'Create', 'Update', 'Delete', 'Write'];

// Fetch all teams
$teams = $pdo->query("SELECT id, name FROM teams ORDER BY name ASC")->fetchAll();

// Handle Delete Action
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  
  // Check if any admin user is assigned to this role
  $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE role_id = ?");
  $check_stmt->execute([$id]);
  $assigned_users = (int)$check_stmt->fetchColumn();
  
  if ($assigned_users > 0) {
    set_flash('danger', 'Cannot delete role. One or more users are assigned to it.');
  } else {
    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
    try {
      $stmt->execute([$id]);
      set_flash('success', 'Role deleted successfully.');
    } catch (Exception $e) {
      set_flash('danger', 'Failed to delete role.');
    }
  }
  redirect(ADMIN_URL . '/rbac/roles.php');
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $name = trim($_POST['name'] ?? '');
  
  $modules_posted = $_POST['modules'] ?? [];
  $team_perms_posted = $_POST['team_perms'] ?? []; // format: [team_id => [action1, action2, ...]]

  $modules_json = json_encode(array_values($modules_posted));
  
  $clean_team_perms = [];
  foreach ($team_perms_posted as $tid => $acts) {
    if (is_array($acts)) {
      $clean_team_perms[(int)$tid] = array_values(array_filter($acts));
    }
  }
  $team_perms_json = json_encode($clean_team_perms);

  if (empty($name)) {
    $error = 'Role name is required.';
  } else {
    if ($action === 'create') {
      $stmt = $pdo->prepare("INSERT INTO roles (name, module_access, team_permissions, permissions) VALUES (?, ?, ?, '')");
      try {
        $stmt->execute([$name, $modules_json, $team_perms_json]);
        set_flash('success', 'Role created successfully.');
        redirect(ADMIN_URL . '/rbac/roles.php');
      } catch (Exception $e) {
        $error = 'Role name already exists.';
      }
    } elseif ($action === 'update') {
      $id = (int)$_POST['id'];
      $stmt = $pdo->prepare("UPDATE roles SET name = ?, module_access = ?, team_permissions = ? WHERE id = ?");
      try {
        $stmt->execute([$name, $modules_json, $team_perms_json, $id]);
        set_flash('success', 'Role updated successfully.');
        redirect(ADMIN_URL . '/rbac/roles.php');
      } catch (Exception $e) {
        $error = 'Role name already exists.';
      }
    }
  }
}

// Determine pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = (int)(($page - 1) * $limit);

$total_count = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
$total_pages = ceil($total_count / $limit);

$roles_stmt = $pdo->prepare("
  SELECT r.id, r.name, r.module_access, r.team_permissions, COUNT(a.id) as user_count 
  FROM roles r 
  LEFT JOIN admins a ON a.role_id = r.id 
  GROUP BY r.id 
  ORDER BY r.name ASC
  LIMIT :limit OFFSET :offset
");
$roles_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$roles_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$roles_stmt->execute();
$roles = $roles_stmt->fetchAll();

// Fetch role if editing
$edit_role = null;
$edit_modules = [];
$edit_team_perms = [];
if (isset($_GET['edit'])) {
  $edit_id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
  $stmt->execute([$edit_id]);
  $edit_role = $stmt->fetch();
  if ($edit_role) {
    $edit_modules = json_decode($edit_role['module_access'], true) ?: [];
    $edit_team_perms = json_decode($edit_role['team_permissions'], true) ?: [];
  }
}

$active_page = 'roles';
$page_title = 'Role Management';
$page_subtitle = 'Define roles and permissions';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Roles — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
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
            <?= $edit_role ? 'Edit Role' : 'Create Role' ?>
          </h3>
          <form method="POST">
            <input type="hidden" name="action" value="<?= $edit_role ? 'update' : 'create' ?>">
            <?php if ($edit_role): ?>
              <input type="hidden" name="id" value="<?= $edit_role['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
              <label for="name">Role Name</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Manager" value="<?= htmlspecialchars($edit_role ? $edit_role['name'] : '') ?>" required>
            </div>



            <!-- Sidebar Modules Access -->
            <div class="form-group">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                <label style="font-weight:600; margin:0;">Sidebar Module Access</label>
                <label style="font-size:0.8rem; color:var(--text-m); cursor:pointer; display:flex; align-items:center; gap:4px; margin:0;" title="Select/deselect all modules">
                  <input type="checkbox" id="selectAllModules" style="width:13px; height:13px; cursor:pointer;">
                  <span>All</span>
                </label>
              </div>
              <p style="font-size:0.8rem; color:var(--text-s); margin-bottom:0.75rem;">Select modules this role can access and view in the sidebar.</p>
              <div style="background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px 16px;">
                <?php foreach ($modules_list as $modKey => $modLabel): ?>
                  <label class="checkbox-item" style="margin-bottom:0;">
                    <input type="checkbox" name="modules[]" value="<?= $modKey ?>" <?= in_array($modKey, $edit_modules, true) ? 'checked' : '' ?>>
                    <span style="font-size: 0.9rem; color: var(--text);"><?= htmlspecialchars($modLabel) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Team Actions Permissions Matrix -->
            <div class="form-group">
              <label style="margin-bottom: 0.5rem; font-weight:600;">Team Permissions Matrix</label>
              <p style="font-size:0.8rem; color:var(--text-s); margin-bottom:0.75rem;">Define what actions users assigned to this role can perform for each team's context.</p>
              <div style="background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1rem; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 400px;">
                  <thead>
                    <tr style="border-bottom: 1px solid var(--border);">
                      <th style="padding: 0.5rem 0.25rem; font-size: 0.8rem; color: var(--text-m); text-align: left;">Team</th>
                      <?php foreach ($actions_list as $actionName): ?>
                        <th style="padding: 0.5rem 0.25rem; font-size: 0.8rem; color: var(--text-m); text-align: center; font-weight: normal;"><?= $actionName ?></th>
                      <?php endforeach; ?>
                      <th style="padding: 0.5rem 0.25rem; font-size: 0.8rem; color: var(--text-m); text-align: center; font-weight: normal;">All</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($teams)): ?>
                      <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-s); padding: 1rem; font-size: 0.85rem;">No teams created yet.</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($teams as $t): ?>
                        <?php 
                          $tid = (int)$t['id'];
                          $current_acts = $edit_team_perms[$tid] ?? [];
                        ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);" class="team-row">
                          <td style="padding: 0.75rem 0.25rem; font-size: 0.85rem; font-weight: 500;"><?= htmlspecialchars($t['name']) ?></td>
                          <?php foreach ($actions_list as $actionName): ?>
                            <td style="padding: 0.75rem 0.25rem; text-align: center;">
                              <input type="checkbox" name="team_perms[<?= $tid ?>][]" value="<?= $actionName ?>" <?= in_array($actionName, $current_acts, true) ? 'checked' : '' ?> style="cursor: pointer; width: 14px; height: 14px;" class="perm-chk">
                            </td>
                          <?php endforeach; ?>
                          <td style="padding: 0.75rem 0.25rem; text-align: center;">
                            <input type="checkbox" class="row-select-all" style="cursor: pointer; width: 14px; height: 14px;" title="Select/deselect all actions for this team">
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem; font-weight: 600;">
              <?= $edit_role ? 'Update Role' : 'Create Role' ?>
            </button>
            <?php if ($edit_role): ?>
              <a href="roles.php" class="btn btn-secondary" style="display: block; text-align: center; margin-top: 0.5rem; padding: 0.75rem;">Cancel Edit</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Roles List -->
        <div class="table-panel">
          <h3 style="padding: 0.5rem 1rem 1rem 1rem; font-family: 'Space Grotesk', sans-serif;">All Roles</h3>
          <table>
            <thead>
              <tr>
                <th>Role</th>
                <th>Modules Access</th>
                <th>Users Assigned</th>
                <th style="width: 120px; text-align: right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($roles)): ?>
                <tr>
                  <td colspan="4" style="text-align: center; color: var(--text-s); padding: 2rem;">No roles created yet.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($roles as $r): ?>
                  <?php 
                    $mod_access_arr = json_decode($r['module_access'], true) ?: [];
                    $t_perms_arr = json_decode($r['team_permissions'], true) ?: [];
                    
                    $mod_labels = [];
                    foreach ($mod_access_arr as $mKey) {
                      if (isset($modules_list[$mKey])) {
                        $mod_labels[] = str_replace(' Management', '', $modules_list[$mKey]);
                      }
                    }
                    $modules_str = !empty($mod_labels) ? implode(', ', $mod_labels) : 'None';
                  ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($r['name']) ?></strong>
                      <div style="font-size:0.75rem; color:var(--text-s); margin-top:4px;">
                        Teams: <?= count($t_perms_arr) ?> assigned
                      </div>
                    </td>
                    <td>
                      <span style="font-size:0.85rem; color:var(--text-m);"><?= htmlspecialchars($modules_str) ?></span>
                    </td>
                    <td><span class="badge badge-ug"><?= $r['user_count'] ?> users</span></td>
                    <td style="text-align: right;">
                      <a href="roles.php?edit=<?= $r['id'] ?>" class="btn btn-secondary btn-sm" style="display: inline-flex; padding: 0.4rem;" title="Edit">
                        ✏️
                      </a>
                      <a href="roles.php?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm" style="display: inline-flex; padding: 0.4rem; background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.2);" onclick="return confirm('Are you sure you want to delete this role?')" title="Delete">
                        🗑️
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <?= render_pagination($total_count, 10, $page) ?>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Select/Deselect All in Sidebar Modules
    document.getElementById('selectAllModules').addEventListener('change', function() {
      const isChecked = this.checked;
      document.querySelectorAll('input[name="modules[]"]').forEach(function(chk) {
        chk.checked = isChecked;
      });
    });

    (function() {
      const allModules = document.querySelectorAll('input[name="modules[]"]');
      const checkedModules = document.querySelectorAll('input[name="modules[]"]:checked');
      const allChk = document.getElementById('selectAllModules');
      if (allModules.length > 0) {
        allChk.checked = (allModules.length === checkedModules.length);
        allChk.indeterminate = (checkedModules.length > 0 && checkedModules.length < allModules.length);
      }
      
      allModules.forEach(function(chk) {
        chk.addEventListener('change', function() {
          const total = allModules.length;
          const checked = document.querySelectorAll('input[name="modules[]"]:checked').length;
          allChk.checked = (checked === total);
          allChk.indeterminate = (checked > 0 && checked < total);
        });
      });
    })();

    // Select/Deselect All in Team Row
    document.querySelectorAll('.row-select-all').forEach(function(allChk) {
      const row = allChk.closest('.team-row');
      const items = row.querySelectorAll('.perm-chk');

      items.forEach(function(item) {
        item.addEventListener('change', function() {
          const total = items.length;
          const checked = row.querySelectorAll('.perm-chk:checked').length;
          allChk.checked = (checked === total);
          allChk.indeterminate = (checked > 0 && checked < total);
        });
      });

      (function() {
        const total = items.length;
        const checked = row.querySelectorAll('.perm-chk:checked').length;
        allChk.checked = (checked === total && total > 0);
        allChk.indeterminate = (checked > 0 && checked < total);
      })();

      allChk.addEventListener('change', function() {
        items.forEach(function(item) {
          item.checked = allChk.checked;
        });
      });
    });
  </script>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>
