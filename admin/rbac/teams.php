<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_permission('teams.view');

$error = '';
$success = '';

// Handle Delete Action
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  
  // Check if any admin user is assigned to this team
  $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE team_id = ?");
  $check_stmt->execute([$id]);
  $assigned_users = (int)$check_stmt->fetchColumn();
  
  if ($assigned_users > 0) {
    set_flash('danger', 'Cannot delete team. One or more users are assigned to it.');
  } else {
    $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ?");
    try {
      $stmt->execute([$id]);
      set_flash('success', 'Team deleted successfully.');
    } catch (Exception $e) {
      set_flash('danger', 'Failed to delete team.');
    }
  }
  redirect(ADMIN_URL . '/rbac/teams.php');
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');

  if (empty($name)) {
    $error = 'Team name is required.';
  } else {
    if ($action === 'create') {
      $stmt = $pdo->prepare("INSERT INTO teams (name, description) VALUES (?, ?)");
      try {
        $stmt->execute([$name, $description]);
        set_flash('success', 'Team created successfully.');
        redirect(ADMIN_URL . '/rbac/teams.php');
      } catch (Exception $e) {
        $error = 'Team name already exists.';
      }
    } elseif ($action === 'update') {
      $id = (int)$_POST['id'];
      $stmt = $pdo->prepare("UPDATE teams SET name = ?, description = ? WHERE id = ?");
      try {
        $stmt->execute([$name, $description, $id]);
        set_flash('success', 'Team updated successfully.');
        redirect(ADMIN_URL . '/rbac/teams.php');
      } catch (Exception $e) {
        $error = 'Team name already exists.';
      }
    }
  }
}

// Determine pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = (int)(($page - 1) * $limit);

$total_count = (int)$pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn();
$total_pages = ceil($total_count / $limit);

$teams_stmt = $pdo->prepare("
  SELECT t.id, t.name, t.description, COUNT(a.id) as user_count 
  FROM teams t 
  LEFT JOIN admins a ON a.team_id = t.id 
  GROUP BY t.id 
  ORDER BY t.name ASC
  LIMIT :limit OFFSET :offset
");
$teams_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$teams_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$teams_stmt->execute();
$teams = $teams_stmt->fetchAll();

// Fetch team if editing
$edit_team = null;
if (isset($_GET['edit'])) {
  $edit_id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
  $stmt->execute([$edit_id]);
  $edit_team = $stmt->fetch();
}

$active_page = 'teams';
$page_title = 'Team Management';
$page_subtitle = 'Manage user groups and teams';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Teams — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    .rbac-container {
      display: grid;
      grid-template-columns: 1fr 2fr;
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
            <?= $edit_team ? 'Edit Team' : 'Create Team' ?>
          </h3>
          <form method="POST">
            <input type="hidden" name="action" value="<?= $edit_team ? 'update' : 'create' ?>">
            <?php if ($edit_team): ?>
              <input type="hidden" name="id" value="<?= $edit_team['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
              <label for="name">Team Name</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Sales Team" value="<?= htmlspecialchars($edit_team ? $edit_team['name'] : '') ?>" required>
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" name="description" class="form-control" style="height: 100px;" placeholder="Brief details about the team"><?= htmlspecialchars($edit_team ? $edit_team['description'] : '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
              <?= $edit_team ? 'Update Team' : 'Create Team' ?>
            </button>
            <?php if ($edit_team): ?>
              <a href="teams.php" class="btn btn-secondary" style="display: block; text-align: center; margin-top: 0.5rem; padding: 0.75rem;">Cancel Edit</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Teams List -->
        <div class="table-panel">
          <h3 style="padding: 0.5rem 1rem 1rem 1rem; font-family: 'Space Grotesk', sans-serif;">All Teams</h3>
          <table>
            <thead>
              <tr>
                <th>Team</th>
                <th>Description</th>
                <th>Members</th>
                <th style="width: 120px; text-align: right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($teams)): ?>
                <tr>
                  <td colspan="4" style="text-align: center; color: var(--text-s); padding: 2rem;">No teams created yet.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($teams as $t): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                    <td style="color: var(--text-m);"><?= htmlspecialchars($t['description'] ?: '—') ?></td>
                    <td><span class="badge badge-ug"><?= $t['user_count'] ?></span></td>
                    <td style="text-align: right;">
                      <a href="teams.php?edit=<?= $t['id'] ?>" class="btn btn-secondary btn-sm" style="display: inline-flex; padding: 0.4rem;" title="Edit">
                        ✏️
                      </a>
                      <a href="teams.php?delete=<?= $t['id'] ?>" class="btn btn-danger btn-sm" style="display: inline-flex; padding: 0.4rem; background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.2);" onclick="return confirm('Are you sure you want to delete this team?')" title="Delete">
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

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>
</body>

</html>
