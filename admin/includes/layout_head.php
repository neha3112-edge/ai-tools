<?php
/**
 * admin/includes/layout_head.php
 * Include inside <head> — outputs all shared CSS
 * Usage: <?php require 'includes/layout_head.php'; ?>
 */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link
  href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap"
  rel="stylesheet">
<style>
  *,
  *::before,
  *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  :root {
    color-scheme: dark;
    /* Native browser controls follow dark mode */
    --bg: #0a0d14;
    --sidebar: #0e1120;
    --surface: #111520;
    --surface-h: #161b2e;
    --border: rgba(255, 255, 255, 0.07);
    --border-h: rgba(255, 255, 255, 0.13);
    --accent: #4f6ef7;
    --accent-h: #6b85ff;
    --accent-g: #7c3aed;
    --text: #f1f3f9;
    --text-m: #8b92a8;
    --text-s: #545c72;
    --success: #22c55e;
    --warning: #f59e0b;
    --danger: #ef4444;
    --radius: 12px;
    --radius-sm: 8px;
    --sidebar-w: 240px;
  }

  body.light {
    color-scheme: light;
    --bg: #f4f6fb;
    --sidebar: #ffffff;
    --surface: #ffffff;
    --surface-h: #f0f2f8;
    --border: rgba(0, 0, 0, 0.08);
    --border-h: rgba(0, 0, 0, 0.15);
    --accent: #4f6ef7;
    --accent-h: #3a57e8;
    --accent-g: #7c3aed;
    --text: #0f1523;
    --text-m: #4b5572;
    --text-s: #9198ae;
    --success: #16a34a;
    --warning: #d97706;
    --danger: #dc2626;
  }

  body.light table tr:hover td {
    background: rgba(0, 0, 0, 0.025);
  }

  body.light table th {
    background: rgba(0, 0, 0, 0.025);
  }

  body.light .sidebar-overlay {
    background: rgba(0, 0, 0, 0.35);
  }

  select option {
    background: var(--surface);
    color: var(--text);
  }

  body.light select option {
    background: var(--surface);
    color: var(--text);
  }

  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
    min-height: 100vh;
    font-size: 14px;
    transition: background 0.2s, color 0.2s;
    overflow-x: hidden;
  }

  /* ── SIDEBAR ── */
  .sidebar {
    width: var(--sidebar-w);
    background: var(--sidebar);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 100;
  }

  .sidebar-brand {
    padding: 1.25rem 1.25rem 1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .sidebar-brand .logo-icon {
    width: 34px;
    height: 34px;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-g) 100%);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .sidebar-brand span {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.2px;
  }

  .sidebar-nav {
    flex: 1;
    padding: 1rem 0.75rem;
    overflow-y: auto;
  }

  .nav-section {
    margin-bottom: 1.5rem;
  }

  .nav-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-s);
    padding: 0 0.5rem;
    margin-bottom: 6px;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.55rem 0.65rem;
    border-radius: var(--radius-sm);
    color: var(--text-m);
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 500;
    transition: background 0.15s, color 0.15s;
    margin-bottom: 2px;
  }

  .nav-item:hover {
    background: var(--surface-h);
    color: var(--text);
  }

  .nav-item.active {
    background: rgba(79, 110, 247, 0.15);
    color: var(--accent-h);
  }

  .nav-item svg {
    flex-shrink: 0;
    opacity: 0.7;
  }

  .nav-item.active svg {
    opacity: 1;
  }

  .sidebar-footer {
    padding: 0.75rem;
    border-top: 1px solid var(--border);
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.6rem;
    border-radius: var(--radius-sm);
    background: var(--surface);
    margin-bottom: 0.5rem;
    border: 1px solid var(--border);
  }

  .avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-g) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
  }

  .user-meta span {
    display: block;
  }

  .user-meta .uname {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text);
  }

  .user-meta .urole {
    font-size: 11px;
    color: var(--text-s);
    text-transform: capitalize;
  }

  .btn-logout {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 0.55rem 0.65rem;
    border-radius: var(--radius-sm);
    border: none;
    background: none;
    color: var(--text-s);
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
  }

  .btn-logout:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #f87171;
  }

  /* ── MAIN ── */
  .main {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    min-width: 0;
  }

  .topbar {
    height: 60px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.75rem;
    background: var(--sidebar);
    position: sticky;
    top: 0;
    z-index: 50;
  }

  .topbar-left-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .topbar-left h2 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 17px;
    font-weight: 700;
    letter-spacing: -0.3px;
  }

  .topbar-left p {
    font-size: 12px;
    color: var(--text-s);
    margin-top: 1px;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .badge-date {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 12px;
    color: var(--text-m);
  }

  .btn-hamburger {
    display: none;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    color: var(--text-m);
    transition: background 0.15s, color 0.15s;
    flex-shrink: 0;
  }

  .btn-hamburger:hover {
    background: var(--surface-h);
    color: var(--text);
  }

  .btn-theme-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    color: var(--text-m);
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    flex-shrink: 0;
  }

  .btn-theme-toggle:hover {
    background: var(--surface-h);
    color: var(--text);
    border-color: var(--border-h);
  }

  body .icon-light {
    display: none;
  }

  body .icon-dark {
    display: flex;
  }

  body.light .icon-light {
    display: flex;
  }

  body.light .icon-dark {
    display: none;
  }

  /* ── CONTENT ── */
  .content {
    padding: 1.75rem;
    flex: 1;
    min-width: 0;
    max-width: 100%;
    overflow-x: hidden;
  }

  /* ── COMMON COMPONENTS ── */
  .section-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.875rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  .panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
  }

  .panel-header {
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .panel-header span {
    font-size: 13.5px;
    font-weight: 600;
  }

  .panel-header a {
    font-size: 12px;
    color: var(--accent);
    text-decoration: none;
  }

  .panel-header a:hover {
    text-decoration: underline;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  table th {
    padding: 0.625rem 1.25rem;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text-s);
    text-align: left;
    border-bottom: 1px solid var(--border);
    background: rgba(255, 255, 255, 0.02);
  }

  /* ── GLOBAL MODALS ── */
  .modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; visibility: hidden;
    transition: all 0.3s ease;
  }
  .modal-overlay.active { opacity: 1; visibility: visible; }
  .modal-dialog {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: calc(var(--radius) * 1.5);
    width: 90%; max-width: 440px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  .modal-overlay.active .modal-dialog { transform: translateY(0) scale(1); }
  .modal-header { padding: 1.5rem 1.5rem 0.5rem; text-align: center; }
  .modal-icon {
    width: 52px; height: 52px; border-radius: 50%;
    background: rgba(239, 68, 68, 0.1); color: var(--danger);
    display: inline-flex; align-items: center; justify-content: center;
    margin: 0 auto 15px;
  }
  .modal-header h3 { margin: 0; font-size: 18px; color: var(--text); font-weight: 600; }
  .modal-body { padding: 1rem 1.5rem; text-align: center; color: var(--text-m); font-size: 14.5px; line-height: 1.5; }
  .modal-warning {
    margin-top: 18px; padding: 12px; background: rgba(239, 68, 68, 0.05);
    border: 1px dashed rgba(239, 68, 68, 0.2); border-radius: var(--radius);
    display: flex; gap: 10px; text-align: left; font-size: 13px; color: var(--danger);
    align-items: flex-start; line-height: 1.4;
  }
  .modal-footer {
    padding: 1rem 1.5rem 1.5rem; display: flex; gap: 10px;
  }
  .modal-footer .btn { flex: 1; justify-content: center; padding: 0.6rem 1rem; font-size: 14px; display:inline-flex; align-items:center; gap:6px; }
  
  body.light .modal-icon { background: rgba(239, 68, 68, 0.08); }
  body.light .modal-warning { background: rgba(239, 68, 68, 0.04); }

  table td {
    padding: 0.75rem 1.25rem;
    font-size: 13px;
    border-bottom: 1px solid var(--border);
    color: var(--text-m);
  }

  table tr:last-child td {
    border-bottom: none;
  }

  table tr:hover td {
    background: rgba(255, 255, 255, 0.02);
  }

  table td .cell-name {
    color: var(--text);
    font-weight: 500;
  }

  .table-responsive,
  .table-scroll {
    width: 100%;
    margin-bottom: 1rem;
    border-radius: var(--radius-sm);
  }

  .table-responsive table,
  .table-scroll table {
    min-width: 100%;
  }

  .table-responsive table th,
  .table-scroll table th,
  .table-responsive table td,
  .table-scroll table td {
    white-space: normal;
  }

  .badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
  }

  .badge-ug {
    background: rgba(79, 110, 247, 0.15);
    color: #818cf8;
  }

  .badge-pg {
    background: rgba(124, 58, 237, 0.15);
    color: #a78bfa;
  }

  .badge-active {
    background: rgba(34, 197, 94, 0.12);
    color: #4ade80;
  }

  .badge-inactive {
    background: rgba(239, 68, 68, 0.12);
    color: #f87171;
  }

  /* Alert flash messages */
  .alert {
    padding: 0.75rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 13px;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .alert-success {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.25);
    color: #4ade80;
  }

  .alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.25);
    color: #fca5a5;
  }

  .alert-warning {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.25);
    color: #fcd34d;
  }

  body.light .alert-success {
    color: #15803d;
  }

  body.light .alert-error {
    color: #b91c1c;
  }

  body.light .alert-warning {
    color: #92400e;
  }

  /* Form elements */
  .form-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.75rem;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
  }

  .form-grid.three {
    grid-template-columns: 1fr 1fr 1fr;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .form-group.full {
    grid-column: 1 / -1;
  }

  .form-group label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-m);
  }

  .form-group label span.req {
    color: var(--danger);
    margin-left: 2px;
  }

  .form-control {
    width: 100%;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.65rem 0.875rem;
    font-size: 13.5px;
    font-family: inherit;
    color: var(--text);
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
  }

  body.light .form-control {
    background: var(--bg);
  }

  .form-control::placeholder {
    color: var(--text-s);
  }

  .form-control:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(79, 110, 247, 0.15);
  }

  .form-control:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  select.form-control {
    cursor: pointer;
  }

  textarea.form-control {
    resize: vertical;
    min-height: 90px;
  }

  .form-hint {
    font-size: 11.5px;
    color: var(--text-s);
    margin-top: 2px;
  }

  /* Checkbox / toggle */
  .check-group {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.5rem 0;
  }

  .check-group input[type=checkbox] {
    width: 16px;
    height: 16px;
    accent-color: var(--accent);
    cursor: pointer;
  }

  .check-group label {
    font-size: 13.5px;
    color: var(--text-m);
    cursor: pointer;
    margin: 0;
  }

  /* Multi-checkbox grid */
  .check-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .check-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0.4rem 0.875rem;
    border-radius: 20px;
    border: 1px solid var(--border);
    background: var(--surface-h);
    font-size: 12.5px;
    color: var(--text-m);
    cursor: pointer;
    transition: border-color 0.15s, color 0.15s, background 0.15s;
    user-select: none;
  }

  .check-pill input[type=checkbox] {
    display: none;
  }

  .check-pill:has(input:checked) {
    border-color: var(--accent);
    color: var(--accent-h);
    background: rgba(79, 110, 247, 0.1);
  }

  /* Image preview */
  .img-preview-wrap {
    width: 100px;
    height: 100px;
    border-radius: var(--radius-sm);
    border: 1px dashed var(--border);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-s);
    font-size: 11px;
    background: var(--surface-h);
  }

  .img-preview-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Buttons */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 0.6rem 1.1rem;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    font-family: inherit;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s, background 0.15s;
    text-decoration: none;
    border: 1px solid transparent;
  }

  .btn:active {
    transform: scale(0.98);
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-g) 100%);
    color: #fff;
    border-color: transparent;
  }

  .btn-primary:hover {
    opacity: 0.88;
  }

  .btn-secondary {
    background: var(--surface-h);
    color: var(--text);
    border-color: var(--border);
  }

  .btn-secondary:hover {
    border-color: var(--border-h);
    background: var(--surface);
  }

  .btn-danger {
    background: rgba(239, 68, 68, 0.12);
    color: #f87171;
    border-color: rgba(239, 68, 68, 0.25);
  }

  .btn-danger:hover {
    background: rgba(239, 68, 68, 0.2);
  }

  .btn-sm {
    padding: 0.35rem 0.7rem;
    font-size: 12px;
  }

  .btn-icon {
    padding: 0.4rem;
    width: 30px;
    height: 30px;
    justify-content: center;
  }

  /* Page top bar (inside content) */
  .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .page-header h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px;
    font-weight: 700;
  }

  .page-header p {
    font-size: 13px;
    color: var(--text-s);
    margin-top: 3px;
  }

  /* Search bar */
  .search-bar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    align-items: center;
  }

  .search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
  }

  .search-input-wrap svg {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-s);
    pointer-events: none;
  }

  .search-input-wrap input {
    width: 100%;
    padding: 0.6rem 0.875rem 0.6rem 2.25rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    color: var(--text);
    outline: none;
    transition: border-color 0.2s;
  }

  .search-input-wrap input:focus {
    border-color: var(--accent);
  }

  .search-input-wrap input::placeholder {
    color: var(--text-s);
  }

  /* Empty state */
  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-s);
  }

  .empty-state svg {
    margin: 0 auto 1rem;
    display: block;
    opacity: 0.3;
  }

  .empty-state p {
    font-size: 14px;
  }

  /* Action col */
  .action-col {
    display: flex;
    gap: 6px;
    align-items: center;
  }

  /* Sidebar mobile */
  .sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 99;
    opacity: 0;
    transition: opacity 0.25s;
    pointer-events: none;
  }

  .sidebar-overlay.active {
    opacity: 1;
    pointer-events: auto;
  }

  .sidebar-close {
    display: none;
    position: absolute;
    top: 14px;
    right: 12px;
    width: 28px;
    height: 28px;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid var(--border);
    border-radius: 6px;
    cursor: pointer;
    color: var(--text-m);
    transition: color 0.15s, background 0.15s;
  }

  .sidebar-close:hover {
    color: var(--text);
    background: rgba(255, 255, 255, 0.1);
  }

  @media (max-width: 768px) {
    .sidebar {
      transform: translateX(-100%);
      transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 200;
    }

    .sidebar.open {
      transform: translateX(0);
    }

    .sidebar-overlay {
      display: block;
    }

    .sidebar-close {
      display: flex;
    }

    .main {
      margin-left: 0;
    }

    .btn-hamburger {
      display: flex;
    }

    .topbar {
      padding: 0 1rem;
    }

    .topbar-left h2 {
      font-size: 15px;
    }

    .topbar-left p {
      display: none;
    }

    .content {
      padding: 1rem;
    }

    .form-grid,
    .form-grid.three {
      grid-template-columns: 1fr;
    }

    table th,
    table td {
      padding: 0.6rem 0.875rem;
      font-size: 12px;
    }

    .badge-date {
      display: none;
    }

    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem;
    }

    .page-header .btn {
      align-self: flex-start;
    }

    .search-bar {
      flex-direction: column;
      align-items: stretch;
    }

    .search-bar select {
      width: 100% !important;
      min-width: unset !important;
    }

    .hide-mobile {
      display: none !important;
    }

    /* TABLE TO CARDS */
    .table-responsive table,
    .table-scroll table {
      min-width: 100% !important;
      border: none;
    }

    .table-responsive thead,
    .table-scroll thead {
      display: none;
    }

    .table-responsive tbody,
    .table-scroll tbody {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      padding: 10px;
    }

    .table-responsive tr,
    .table-scroll tr {
      display: flex;
      flex-direction: column;
      background: var(--surface-h);
      border-radius: var(--radius-sm);
      padding: 0.5rem;
      border: 1px solid var(--border);
    }

    .table-responsive tr:hover td,
    .table-scroll tr:hover td {
      background: transparent;
    }

    .table-responsive td,
    .table-scroll td {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      padding: 0.75rem 0.5rem;
      white-space: normal !important;
      text-align: right;
      flex-wrap: wrap;
      gap: 10px;
    }

    .table-responsive td:last-child,
    .table-scroll td:last-child {
      border-bottom: none;
    }

    .table-responsive td::before,
    .table-scroll td::before {
      content: attr(data-label);
      font-size: 11px;
      font-weight: 700;
      color: var(--text-s);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .action-col {
      width: 100%;
      justify-content: flex-end;
      margin-top: 4px;
    }
  }

  @media (max-width: 480px) {
    .action-col {
      gap: 4px;
    }

    .stats-grid {
      grid-template-columns: 1fr 1fr;
    }
  }
</style>