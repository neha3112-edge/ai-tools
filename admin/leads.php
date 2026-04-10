<?php
require_once '../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_login();

// Handle soft-delete or hard-delete? Since it's a simple leads table, we can just hard-delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  try {
      $stmt = $pdo->prepare("DELETE FROM brochure_leads WHERE id=?");
      $stmt->execute([(int) $_POST['delete_id']]);
      set_flash('success', 'Lead record deleted successfully.');
  } catch (PDOException $e) {
      set_flash('error', 'Could not delete the lead record. Please check table existence.');
  }
  redirect(ADMIN_URL . '/leads.php');
}

// Search + filter
$search = trim($_GET['search'] ?? '');

$where = ["1 = 1"];
$params = [];

if ($search) {
  $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

$sql = "SELECT id, name, email, country_code, phone, course, state, page_url, user_ip, created_at
        FROM brochure_leads
        WHERE " . implode(' AND ', $where) . "
        ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If table doesn't exist yet
    $leads = [];
    if($e->getCode() == '42S02') {
        $table_missing = true;
    }
}

$active_page = 'leads';
$page_title = 'Brochure Leads';
$page_subtitle = 'Manage brochure download requests';
$base_path = '.';
$logout_path = 'logout.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brochure Leads — SODE AI Tools</title>
  <?php require_once __DIR__ . '/includes/layout_head.php'; ?>
</head>

<body>
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>
      
      <?php if(isset($table_missing)): ?>
          <div style="background:var(--danger); color:#fff; padding:1rem; border-radius:var(--radius-sm); margin-bottom:2rem;">
            <strong>Warning:</strong> The `brochure_leads` database table is missing! Please execute the SQL table creation script to capture leads.
          </div>
      <?php endif; ?>

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h3>All Brochure Leads</h3>
          <p><?= count($leads) ?> record(s) found</p>
        </div>
      </div>

      <!-- Search & Filter -->
      <div class="search-bar">
        <form method="GET" style="display:contents;">
          <div class="search-input-wrap" style="flex:1;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" name="search" placeholder="Search by name, email, or phone…" value="<?= e($search) ?>" style="width:100%;">
          </div>
          <button type="submit" class="btn btn-secondary">Filter</button>
          <?php if ($search): ?>
            <a href="leads.php" class="btn btn-secondary">Clear</a>
          <?php endif; ?>
        </form>
      </div>

      <!-- Table -->
      <div class="panel">
        <div class="table-responsive">
          <table>
          <thead>
            <tr>
              <th style="width:50px;">#</th>
              <th>Name</th>
              <th>Contact Info</th>
              <th>Course Selected</th>
              <th>State</th>
              <th>Requested On</th>
              <th style="width:80px; text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($leads): ?>
              <?php foreach ($leads as $i => $l): ?>
                <tr>
                  <td data-label="#"> <?= $i + 1 ?> </td>
                  <td data-label="Name">
                      <div class="cell-name"><?= e($l['name']) ?></div>
                  </td>
                  <td data-label="Contact">
                      <div style="font-size:0.9rem;">
                          <div style="color:var(--accent);"><?= e($l['email']) ?></div>
                          <div style="color:var(--text-m); font-weight:600;"><?= e($l['country_code']) ?> <?= e($l['phone']) ?></div>
                      </div>
                  </td>
                  <td data-label="Course">
                      <span class="badge" style="background:rgba(37,99,235,0.1); color:#2563eb;">
                          <?= e($l['course']) ?>
                      </span>
                  </td>
                  <td data-label="State"><?= e($l['state']) ?></td>
                  <td data-label="Requested On"><?= date('d M Y, h:i A', strtotime($l['created_at'])) ?></td>
                  <td data-label="Action">
                    <div class="action-col" style="justify-content:center;">
                      <a href="lead_view.php?id=<?= $l['id'] ?>" class="btn btn-secondary btn-sm btn-icon" title="View Details">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" /><circle cx="12" cy="12" r="3" /></svg>
                      </a>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $l['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete Lead" data-confirm="Delete lead from '<?= e($l['name']) ?>'? This cannot be undone.">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /><path d="M10 11v6M14 11v6" /><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" /></svg>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="empty-state">
                  <div class="empty-icon">📝</div>
                  <h4>No leads found</h4>
                  <p>When users download brochures, their details will appear here.</p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
  
  <script>
    // Confirmation dialog for deletions
    document.querySelectorAll('[data-confirm]').forEach(btn => {
      btn.addEventListener('click', e => {
        if (!confirm(btn.getAttribute('data-confirm'))) e.preventDefault();
      });
    });
  </script>
  <script src="<?= ADMIN_URL ?>/assets/js/admin.js"></script>
</body>
</html>
