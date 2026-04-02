<?php
/**
 * admin/includes/topbar.php
 *
 * Variables to set before including:
 *   $page_title    — e.g. "Dashboard"
 *   $page_subtitle — e.g. "Welcome back"  (optional)
 */
if (!isset($page_title))    $page_title    = 'Admin';
if (!isset($page_subtitle)) $page_subtitle = '';
?>
<div class="topbar">
  <div class="topbar-left-wrap">
    <button class="btn-hamburger" id="hamburgerBtn" aria-label="Open menu">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>
    <div class="topbar-left">
      <h2><?= htmlspecialchars($page_title) ?></h2>
      <?php if ($page_subtitle): ?>
        <p><?= htmlspecialchars($page_subtitle) ?></p>
      <?php endif; ?>
    </div>
  </div>
  <div class="topbar-right">
    <div class="badge-date"><?= date('D, d M Y') ?></div>
    <button class="btn-theme-toggle" id="themeToggle" aria-label="Toggle theme" title="Toggle light/dark mode">
      <span class="icon-dark">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </span>
      <span class="icon-light">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
          <circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
          <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
      </span>
    </button>
  </div>
</div>
