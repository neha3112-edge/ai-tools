<?php
/**
 * admin/includes/sidebar.php
 * $active_page: dashboard, universities, courses, mappings, modes, exam_modes, accreditations
 */
if (!isset($active_page))
  $active_page = '';

function _nav_cls(string $page, string $cur): string
{
  return 'nav-item' . ($page === $cur ? ' active' : '');
}
$_logout_url = ADMIN_URL . '/logout.php';
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
  <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
      stroke-linecap="round">
      <line x1="18" y1="6" x2="6" y2="18" />
      <line x1="6" y1="6" x2="18" y2="18" />
    </svg>
  </button>
  <div class="sidebar-brand">
    <div class="logo-icon"><img style="width:100%;" src="<?= ADMIN_URL ?>/assets/images/favicon.png"></div>
    <span>SODE AI Tools</span>
  </div>
  <nav class="sidebar-nav">
    <?php
    global $pdo;
    if (!isset($pdo)) {
        require_once __DIR__ . '/../../includes/db.php';
    }

    $sidebar_items = [];
    try {
        $stmt = $pdo->query("SELECT * FROM sidebar_items WHERE is_active = 1 ORDER BY sort_order ASC");
        $sidebar_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback or empty if error
    }

    // Group items by section
    $sections = [];
    foreach ($sidebar_items as $item) {
        $sec = $item['section'];
        if (!isset($sections[$sec])) {
            $sections[$sec] = [];
        }
        $sections[$sec][] = $item;
    }

    // Define standard order for sections
    $section_order = ['Main', 'Manage', 'Access Control', 'Settings'];
    foreach (array_keys($sections) as $sec) {
        if (!in_array($sec, $section_order)) {
            $section_order[] = $sec;
        }
    }

    // Render each section dynamically
    foreach ($section_order as $sec) {
        if (!empty($sections[$sec])) {
            $visible_items = [];
            foreach ($sections[$sec] as $item) {
                $show_item = false;
                if (!empty($_SESSION['is_superadmin'])) {
                    $show_item = true;
                } else {
                    $show_item = has_module_access($item['module_key']);
                }
                if ($show_item) {
                    $visible_items[] = $item;
                }
            }

            if (!empty($visible_items)) {
                echo '<div class="nav-section">';
                echo '<div class="nav-section-label">' . htmlspecialchars($sec) . '</div>';
                foreach ($visible_items as $item) {
                    $url = strpos($item['link'], 'http') === 0 ? $item['link'] : ADMIN_URL . '/' . $item['link'];
                    echo '<a href="' . $url . '" class="' . _nav_cls($item['active_key'], $active_page) . '">';
                    echo $item['icon']; // Output raw SVG icon
                    echo htmlspecialchars($item['name']);
                    echo '</a>';
                }
                echo '</div>';
            }
        }
    }
    ?>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar"><?= strtoupper(substr($_SESSION['admin_name'], 0, 2)) ?></div>
      <div class="user-meta">
        <span class="uname"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
        <span class="urole"><?= htmlspecialchars($_SESSION['admin_role']) ?></span>
      </div>
    </div>
    <form method="POST" action="<?= $_logout_url ?>">
      <button type="submit" class="btn-logout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
        </svg>
        Sign out
      </button>
    </form>
  </div>
</aside>