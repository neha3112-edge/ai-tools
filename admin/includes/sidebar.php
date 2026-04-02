<?php
/**
 * admin/includes/sidebar.php
 * $active_page: dashboard, universities, courses, mappings, modes, exam_modes, accreditations
 */
if (!isset($active_page)) $active_page = '';

function _nav_cls(string $page, string $cur): string {
    return 'nav-item' . ($page === $cur ? ' active' : '');
}
$_logout_url = ADMIN_URL . '/logout.php';
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
  <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <div class="sidebar-brand">
    <div class="logo-icon">&#9881;</div>
    <span>SODE AI Tools</span>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-section-label">Main</div>
      <a href="<?= ADMIN_URL ?>/dashboard.php" class="<?= _nav_cls('dashboard',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
        Dashboard
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Manage</div>
      <a href="<?= ADMIN_URL ?>/universities/index.php" class="<?= _nav_cls('universities',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21V11h6v10"/></svg>
        Universities
      </a>
      <a href="<?= ADMIN_URL ?>/courses/index.php" class="<?= _nav_cls('courses',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        Courses
      </a>
      <a href="<?= ADMIN_URL ?>/mappings/index.php" class="<?= _nav_cls('mappings',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
        Course Mappings
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Settings</div>
      <a href="<?= ADMIN_URL ?>/masters/modes.php" class="<?= _nav_cls('modes',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        Education Modes
      </a>
      <a href="<?= ADMIN_URL ?>/masters/exam_modes.php" class="<?= _nav_cls('exam_modes',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Exam Modes
      </a>
      <a href="<?= ADMIN_URL ?>/masters/accreditations.php" class="<?= _nav_cls('accreditations',$active_page) ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Accreditations
      </a>
    </div>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar"><?= strtoupper(substr($_SESSION['admin_name'],0,2)) ?></div>
      <div class="user-meta">
        <span class="uname"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
        <span class="urole"><?= htmlspecialchars($_SESSION['admin_role']) ?></span>
      </div>
    </div>
    <form method="POST" action="<?= $_logout_url ?>">
      <button type="submit" class="btn-logout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Sign out
      </button>
    </form>
  </div>
</aside>
