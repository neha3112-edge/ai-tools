<?php
require_once '../../includes/config.php';
session_name(ADMIN_SESSION_NAME);
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_permission('sidebar.view');

$error = '';

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $chk = $pdo->prepare("SELECT link FROM sidebar_items WHERE id = ?");
    $chk->execute([$id]);
    $chk_item = $chk->fetch();
    if ($chk_item && strpos($chk_item['link'], 'sidebar_manager.php') !== false) {
        set_flash('danger', 'You cannot delete the Sidebar Manager itself.');
    } else {
        $stmt = $pdo->prepare("DELETE FROM sidebar_items WHERE id = ?");
        try {
            $stmt->execute([$id]);
            set_flash('success', 'Sidebar item deleted successfully.');
        } catch (Exception $e) {
            set_flash('danger', 'Failed to delete sidebar item.');
        }
    }
    redirect(ADMIN_URL . '/rbac/sidebar_manager.php');
}

// Handle Add/Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action       = $_POST['action'] ?? '';
    $name         = trim($_POST['name'] ?? '');
    $link         = trim($_POST['link'] ?? '');
    $active_key   = trim($_POST['active_key'] ?? '');
    $icon         = trim($_POST['icon'] ?? '');
    $module_key   = trim($_POST['module_key'] ?? '');
    $section      = trim($_POST['section'] ?? 'Manage');
    $sort_order   = (int)($_POST['sort_order'] ?? 0);
    $is_superadmin_only = isset($_POST['is_superadmin_only']) ? 1 : 0;
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($link) || empty($active_key) || empty($icon) || empty($module_key)) {
        $error = 'All required fields must be filled.';
    } else {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO sidebar_items (name, link, active_key, icon, module_key, section, is_superadmin_only, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $link, $active_key, $icon, $module_key, $section, $is_superadmin_only, $sort_order, $is_active]);
                set_flash('success', 'Sidebar item "' . $name . '" created successfully.');
                redirect(ADMIN_URL . '/rbac/sidebar_manager.php');
            } catch (Exception $e) {
                $error = 'Failed to create item: ' . $e->getMessage();
            }
        } elseif ($action === 'update') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE sidebar_items SET name=?, link=?, active_key=?, icon=?, module_key=?, section=?, is_superadmin_only=?, sort_order=?, is_active=? WHERE id=?");
            try {
                $stmt->execute([$name, $link, $active_key, $icon, $module_key, $section, $is_superadmin_only, $sort_order, $is_active, $id]);
                set_flash('success', 'Sidebar item updated successfully.');
                redirect(ADMIN_URL . '/rbac/sidebar_manager.php');
            } catch (Exception $e) {
                $error = 'Failed to update item: ' . $e->getMessage();
            }
        }
    }
}

// Pagination
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 12;
$offset = ($page - 1) * $limit;

$total_count = (int)$pdo->query("SELECT COUNT(*) FROM sidebar_items")->fetchColumn();

$items_stmt = $pdo->prepare("SELECT * FROM sidebar_items ORDER BY section ASC, sort_order ASC LIMIT :limit OFFSET :offset");
$items_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$items_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$items_stmt->execute();
$items = $items_stmt->fetchAll();

// Fetch all distinct sections from DB for the datalist
$all_sections = $pdo->query("SELECT DISTINCT section FROM sidebar_items ORDER BY section ASC")->fetchAll(PDO::FETCH_COLUMN);

// Edit mode
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM sidebar_items WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}

$active_page  = 'sidebar';
$page_title   = 'Sidebar Menu Manager';
$page_subtitle = 'Dynamically configure navigation tabs, access rules, and icons';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar Manager — SODE AI Tools</title>
  <?php require_once __DIR__ . '/../includes/layout_head.php'; ?>
  <style>
    /* ── Layout ── */
    .sm-grid {
      display: grid;
      grid-template-columns: 380px 1fr;
      gap: 1.5rem;
      align-items: start;
    }
    @media (max-width: 1100px) { .sm-grid { grid-template-columns: 1fr; } }

    /* ── Card ── */
    .sm-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
    }
    .sm-card-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sm-card-header h3 {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 15px;
      font-weight: 600;
      color: var(--text);
      flex: 1;
    }
    .sm-card-body { padding: 1.5rem; }

    /* ── Form ── */
    .fg { margin-bottom: 1.15rem; }
    .fg label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-m);
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .fg input, .fg select, .fg textarea {
      width: 100%;
      padding: 0.65rem 0.9rem;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 10px;
      color: var(--text);
      font-size: 13.5px;
      font-family: inherit;
      transition: border-color 0.18s, box-shadow 0.18s;
    }
    .fg input:focus, .fg select:focus, .fg textarea:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(79,110,247,0.12);
    }
    .fg textarea { resize: vertical; min-height: 76px; font-family: 'Fira Code', 'Cascadia Code', monospace; font-size: 12px; }
    .fg small { display: block; margin-top: 5px; font-size: 11px; color: var(--text-s); line-height: 1.4; }
    .fg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .fg-check {
      display: flex;
      gap: 1.5rem;
      margin-top: 0.5rem;
    }
    .check-pill {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      cursor: pointer;
      font-size: 13px;
      transition: border-color 0.15s;
    }
    .check-pill:hover { border-color: var(--border-h); }
    .check-pill input[type="checkbox"] { accent-color: var(--accent); width: 15px; height: 15px; cursor: pointer; }

    /* ── Code snippet ── */
    .code-block {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 1rem 1.15rem;
      font-family: 'Fira Code', monospace;
      font-size: 11.5px;
      line-height: 1.7;
      color: var(--text-m);
      overflow-x: auto;
      margin-top: 0.5rem;
      position: relative;
    }
    .code-block .kw  { color: #7c85f7; }
    .code-block .fn  { color: #50d0e0; }
    .code-block .str { color: #f0a070; }
    .code-block .dyn { color: var(--accent); font-weight: 700; }
    .code-block .cm  { color: var(--text-s); }

    /* ── Table panel ── */
    .sm-table { width: 100%; border-collapse: collapse; }
    .sm-table th {
      padding: 0.7rem 1rem;
      text-align: left;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--text-s);
      background: var(--bg);
      border-bottom: 1px solid var(--border);
    }
    .sm-table td {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
      font-size: 13.5px;
    }
    .sm-table tbody tr { transition: background 0.12s; }
    .sm-table tbody tr:hover td { background: var(--surface-h); }
    .sm-table tbody tr:last-child td { border-bottom: none; }

    /* ── Icon preview ── */
    .icon-wrap {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      background: rgba(79,110,247,0.1);
      border: 1px solid rgba(79,110,247,0.2);
      border-radius: 9px;
      color: var(--accent);
      flex-shrink: 0;
    }
    .icon-wrap svg { width: 16px; height: 16px; }

    /* ── Badges ── */
    .tag {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.04em;
    }
    .tag-red   { background: rgba(239,68,68,0.14);  color: #f87171; }
    .tag-gray  { background: rgba(100,116,139,0.14); color: #94a3b8; }
    .tag-green { background: rgba(34,197,94,0.14);  color: #4ade80; }
    .tag-blue  { background: rgba(79,110,247,0.14); color: #818cf8; }

    /* ── Mono code inline ── */
    .mono {
      font-family: 'Fira Code', monospace;
      font-size: 11.5px;
      padding: 2px 7px;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 5px;
      color: var(--text-m);
    }
    .mono-accent { color: var(--accent); }
    .mono-green  { color: var(--success); }

    /* ── Section pill ── */
    .sec-pill {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11.5px;
      color: var(--text-s);
    }
    .sec-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .sec-main     .sec-dot { background: #818cf8; }
    .sec-manage   .sec-dot { background: #34d399; }
    .sec-access   .sec-dot { background: #f59e0b; }
    .sec-settings .sec-dot { background: #60a5fa; }
    .sec-other    .sec-dot { background: var(--text-s); }

    /* ── Action buttons ── */
    .act-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      padding: 5px 12px;
      border-radius: 7px;
      font-size: 12px;
      font-weight: 500;
      border: 1px solid transparent;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.15s;
    }
    .act-edit {
      background: rgba(79,110,247,0.1);
      border-color: rgba(79,110,247,0.25);
      color: var(--accent);
    }
    .act-edit:hover { background: rgba(79,110,247,0.2); border-color: var(--accent); }
    .act-del {
      background: rgba(239,68,68,0.1);
      border-color: rgba(239,68,68,0.2);
      color: var(--danger);
    }
    .act-del:hover { background: rgba(239,68,68,0.2); border-color: var(--danger); }

    /* ── Header icon ── */
    .hdr-icon {
      width: 32px; height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 9px;
      flex-shrink: 0;
    }
    .hdr-icon-purple { background: rgba(124,58,237,0.15); color: #a78bfa; }
    .hdr-icon-blue   { background: rgba(79,110,247,0.15); color: var(--accent); }

    /* ── Submit button ── */
    .submit-btn {
      width: 100%;
      padding: 0.75rem;
      justify-content: center;
      font-size: 14px;
      border-radius: 10px;
      margin-top: 0.5rem;
      font-weight: 600;
      letter-spacing: 0.01em;
    }
    .cancel-btn {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.65rem;
      border-radius: 10px;
      margin-top: 0.5rem;
      font-size: 13px;
      font-weight: 500;
      text-decoration: none;
      border: 1px solid var(--border);
      color: var(--text-m);
      background: transparent;
      transition: all 0.15s;
    }
    .cancel-btn:hover { background: var(--surface-h); border-color: var(--border-h); color: var(--text); }

    /* ── Empty state ── */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 3rem 1rem;
      color: var(--text-s);
      text-align: center;
    }
    .empty-state svg { color: var(--text-s); opacity: 0.4; margin-bottom: 6px; }

    /* ── Custom section combobox ── */
    .sec-combo {
      position: relative;
    }
    .sec-combo input {
      padding-right: 2.4rem; /* room for arrow button */
    }
    .sec-combo-arrow {
      position: absolute;
      right: 1px;
      top: 1px;
      bottom: 1px;
      width: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      border: none;
      border-left: 1px solid var(--border);
      border-radius: 0 9px 9px 0;
      color: var(--text-s);
      cursor: pointer;
      transition: color 0.15s, background 0.15s;
    }
    .sec-combo-arrow:hover { background: var(--surface-h); color: var(--text); }
    .sec-combo-arrow svg { transition: transform 0.2s; }
    .sec-combo-arrow.open svg { transform: rotate(180deg); }

    .sec-combo-list {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      left: 0; right: 0;
      background: var(--surface);
      border: 1px solid var(--border-h);
      border-radius: 10px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.45);
      list-style: none;
      padding: 5px;
      z-index: 999;
      max-height: 220px;
      overflow-y: auto;
    }
    .sec-combo-list.open { display: block; }

    .sec-combo-opt, .sec-combo-new {
      padding: 9px 12px;
      border-radius: 7px;
      font-size: 13px;
      cursor: pointer;
      color: var(--text);
      transition: background 0.12s;
    }
    .sec-combo-opt:hover { background: var(--surface-h); }
    .sec-combo-new {
      color: var(--accent);
      font-weight: 500;
      font-size: 12.5px;
    }
    .sec-combo-new:hover { background: rgba(79,110,247,0.1); }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="main">
    <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

    <div class="content">
      <?= render_flash() ?>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="sm-grid">

        <!-- ── LEFT: Form ── -->
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

          <!-- Form Card -->
          <div class="sm-card">
            <div class="sm-card-header">
              <div class="hdr-icon hdr-icon-purple">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <?php if ($edit_item): ?>
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  <?php else: ?>
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                  <?php endif; ?>
                </svg>
              </div>
              <h3><?= $edit_item ? 'Edit Sidebar Tab' : 'Add New Tab' ?></h3>
              <?php if ($edit_item): ?>
                <span class="tag tag-blue">Editing</span>
              <?php endif; ?>
            </div>
            <div class="sm-card-body">
              <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_item ? 'update' : 'create' ?>">
                <?php if ($edit_item): ?>
                  <input type="hidden" name="id" value="<?= (int)$edit_item['id'] ?>">
                <?php endif; ?>

                <div class="fg">
                  <label>Display Name <span style="color:var(--danger)">*</span></label>
                  <input type="text" name="name" id="inp-name" placeholder="e.g. My Reports" value="<?= htmlspecialchars($edit_item['name'] ?? '') ?>" required>
                </div>

                <div class="fg-row">
                  <div class="fg">
                    <label>Page Route / Link <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="link" id="inp-link" placeholder="e.g. reports/index.php" value="<?= htmlspecialchars($edit_item['link'] ?? '') ?>" required>
                  </div>
                  <div class="fg">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" id="inp-sort" placeholder="e.g. 15" value="<?= (int)($edit_item['sort_order'] ?? 0) ?>">
                  </div>
                </div>

                <div class="fg-row">
                  <div class="fg">
                    <label>Active Page Key <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="active_key" id="inp-active-key" placeholder="e.g. reports" value="<?= htmlspecialchars($edit_item['active_key'] ?? '') ?>" required>
                    <small>Matches <code style="background:var(--bg);padding:1px 5px;border-radius:4px;font-size:10px;">$active_page</code> on target page</small>
                  </div>
                  <div class="fg">
                    <label>RBAC Module Key <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="module_key" id="inp-module-key" placeholder="e.g. reports" value="<?= htmlspecialchars($edit_item['module_key'] ?? '') ?>" required>
                    <small>Used in role permission toggles</small>
                  </div>
                </div>

                <div class="fg">
                  <label>Menu Section <span style="color:var(--text-s); font-size:10px; font-weight:400; text-transform:none;">(type new or pick existing)</span></label>
                  <!-- Custom combobox: real input submits, dropdown is JS-driven -->
                  <div class="sec-combo" id="sec-combo">
                    <input type="text" name="section" id="inp-section"
                      placeholder="e.g. Reports, Tools …"
                      value="<?= htmlspecialchars($edit_item['section'] ?? 'Manage') ?>"
                      autocomplete="off" required>
                    <button type="button" class="sec-combo-arrow" id="sec-combo-arrow" tabindex="-1" aria-label="Toggle sections">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <ul class="sec-combo-list" id="sec-combo-list" role="listbox">
                      <?php foreach ($all_sections as $sec): ?>
                      <li class="sec-combo-opt" data-value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></li>
                      <?php endforeach; ?>
                      <li class="sec-combo-new" id="sec-combo-new" style="display:none;"></li>
                    </ul>
                  </div>
                  <small>Click the arrow or start typing to see / create a section.</small>
                </div>

                <div class="fg">
                  <label>Icon — Raw SVG <span style="color:var(--danger)">*</span></label>
                  <textarea name="icon" id="inp-icon" placeholder="<svg width=&quot;16&quot; height=&quot;16&quot; ...>...</svg>" required><?= htmlspecialchars($edit_item['icon'] ?? '') ?></textarea>
                </div>

                <div class="fg">
                  <label>Visibility &amp; Status</label>
                  <div class="fg-check">
                    <label class="check-pill">
                      <input type="checkbox" name="is_superadmin_only" value="1" <?= !empty($edit_item['is_superadmin_only']) ? 'checked' : '' ?>>
                      Superadmin Only
                    </label>
                    <label class="check-pill">
                      <input type="checkbox" name="is_active" value="1" <?= !isset($edit_item['is_active']) || $edit_item['is_active'] ? 'checked' : '' ?>>
                      Active Tab
                    </label>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary submit-btn">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <?php if ($edit_item): ?>
                      <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    <?php else: ?>
                      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    <?php endif; ?>
                  </svg>
                  <?= $edit_item ? 'Save Changes' : 'Add Sidebar Tab' ?>
                </button>
                <?php if ($edit_item): ?>
                  <a href="sidebar_manager.php" class="cancel-btn">Cancel &amp; go back</a>
                <?php endif; ?>
              </form>
            </div>
          </div>

          <!-- Code Snippet Card -->
          <div class="sm-card">
            <div class="sm-card-header">
              <div class="hdr-icon hdr-icon-blue">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                </svg>
              </div>
              <h3>PHP Integration Snippet</h3>
            </div>
            <div class="sm-card-body">
              <p style="font-size:12px; color:var(--text-s); margin-bottom:0.75rem; line-height:1.5;">
                Add this block to the <strong style="color:var(--text-m);">top of your new page file</strong> to enforce RBAC access. Update <code style="background:var(--bg);padding:1px 5px;border-radius:4px;font-size:10.5px;color:var(--accent);">module_key</code> to match what you set in the form.
              </p>
              <div class="code-block">
<span class="kw">&lt;?php</span><br>
<span class="fn">require_once</span> <span class="str">'../../includes/config.php'</span>;<br>
<span class="fn">session_name</span>(ADMIN_SESSION_NAME);<br>
<span class="fn">session_start</span>();<br>
<span class="fn">require_once</span> <span class="str">'../../includes/db.php'</span>;<br>
<span class="fn">require_once</span> <span class="str">'../../includes/auth.php'</span>;<br>
<span class="fn">require_once</span> <span class="str">'../../includes/helpers.php'</span>;<br>
<br>
<span class="cm">// ── Access guard for this module ──</span><br>
<span class="fn">require_module_access</span>(<span class="str">'</span><span class="dyn" id="dyn-module-key"><?= htmlspecialchars($edit_item['module_key'] ?? 'module_key') ?></span><span class="str">'</span>);<br>
<br>
<span class="cm">// ── Set page active state ──</span><br>
<span class="kw">$active_page</span> = <span class="str">'<span id="dyn-active-key"><?= htmlspecialchars($edit_item['active_key'] ?? 'active_key') ?></span>'</span>;<br>
<span class="kw">?&gt;</span>
              </div>
            </div>
          </div>

        </div>

        <!-- ── RIGHT: Table ── -->
        <div class="sm-card" style="overflow:hidden;">
          <div class="sm-card-header">
            <div class="hdr-icon hdr-icon-blue">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
              </svg>
            </div>
            <h3>All Sidebar Items</h3>
            <span class="tag tag-blue" style="margin-left:auto;"><?= $total_count ?> items</span>
          </div>

          <div style="overflow-x:auto;">
            <table class="sm-table">
              <thead>
                <tr>
                  <th style="width:44px;">Icon</th>
                  <th>Name</th>
                  <th>Section</th>
                  <th>Route &amp; Keys</th>
                  <th style="width:50px; text-align:center;">Sort</th>
                  <th style="text-align:right; padding-right:1.25rem;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($items)): ?>
                  <tr>
                    <td colspan="6">
                      <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
                        <strong style="color:var(--text-m);">No sidebar items yet</strong>
                        <span style="font-size:12px;">Add your first tab using the form on the left.</span>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($items as $row): ?>
                    <tr>
                      <td>
                        <div class="icon-wrap"><?= $row['icon'] ?></div>
                      </td>
                      <td>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                          <strong style="font-size:13.5px;"><?= htmlspecialchars($row['name']) ?></strong>
                          <div style="display:flex; gap:4px; flex-wrap:wrap;">
                            <?php if ($row['is_superadmin_only']): ?>
                              <span class="tag tag-red">Superadmin</span>
                            <?php endif; ?>
                            <?php if (!$row['is_active']): ?>
                              <span class="tag tag-gray">Inactive</span>
                            <?php else: ?>
                              <span class="tag tag-green">Active</span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </td>
                      <td>
                        <?php
                          $sec = $row['section'];
                          $smap = ['Main'=>'sec-main','Manage'=>'sec-manage','Access Control'=>'sec-access','Settings'=>'sec-settings'];
                          $cls = $smap[$sec] ?? 'sec-other';
                        ?>
                        <div class="sec-pill <?= $cls ?>">
                          <div class="sec-dot"></div>
                          <?= htmlspecialchars($sec) ?>
                        </div>
                      </td>
                      <td>
                        <div style="display:flex; flex-direction:column; gap:5px;">
                          <span class="mono" style="font-size:11px;"><?= htmlspecialchars($row['link']) ?></span>
                          <div style="display:flex; gap:5px;">
                            <span class="mono mono-accent" title="Module Key"><?= htmlspecialchars($row['module_key']) ?></span>
                            <span class="mono mono-green" title="Active Key"><?= htmlspecialchars($row['active_key']) ?></span>
                          </div>
                        </div>
                      </td>
                      <td style="text-align:center;">
                        <span style="font-size:13px; font-weight:600; color:var(--text-m);"><?= (int)$row['sort_order'] ?></span>
                      </td>
                      <td style="text-align:right; padding-right:1.1rem;">
                        <div style="display:inline-flex; gap:6px; align-items:center;">
                          <a href="sidebar_manager.php?edit=<?= (int)$row['id'] ?>&page=<?= $page ?>" class="act-btn act-edit">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                          </a>
                          <a href="sidebar_manager.php?delete=<?= (int)$row['id'] ?>" class="act-btn act-del" onclick="return confirm('Delete \'<?= addslashes(htmlspecialchars($row['name'])) ?>\'? This will remove it from the sidebar immediately.')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                            Delete
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border);">
            <?= render_pagination($total_count, $limit, $page) ?>
          </div>
        </div>

      </div><!-- /.sm-grid -->
    </div><!-- /.content -->
  </main>

  <?php require_once __DIR__ . '/../includes/layout_foot.php'; ?>

  <script>
    /* ── Code snippet live update ── */
    const mKeyInp = document.getElementById('inp-module-key');
    const aKeyInp = document.getElementById('inp-active-key');
    const dynMod  = document.getElementById('dyn-module-key');
    const dynAct  = document.getElementById('dyn-active-key');
    if (mKeyInp && dynMod) mKeyInp.addEventListener('input', () => { dynMod.textContent = mKeyInp.value.trim() || 'module_key'; });
    if (aKeyInp && dynAct) aKeyInp.addEventListener('input', () => { dynAct.textContent = aKeyInp.value.trim() || 'active_key'; });

    /* ── Custom section combobox ── */
    (function () {
      const combo    = document.getElementById('sec-combo');
      const inp      = document.getElementById('inp-section');
      const arrow    = document.getElementById('sec-combo-arrow');
      const list     = document.getElementById('sec-combo-list');
      const newItem  = document.getElementById('sec-combo-new');
      const allOpts  = Array.from(list.querySelectorAll('.sec-combo-opt'));

      if (!combo) return;

      function openList() {
        filterOpts(inp.value);
        list.classList.add('open');
        arrow.classList.add('open');
      }
      function closeList() {
        list.classList.remove('open');
        arrow.classList.remove('open');
      }
      function filterOpts(q) {
        const term = q.trim().toLowerCase();
        let anyVisible = false;
        allOpts.forEach(o => {
          const match = o.dataset.value.toLowerCase().includes(term);
          o.style.display = match ? '' : 'none';
          if (match) anyVisible = true;
        });
        // "Create new" hint
        if (term && !allOpts.some(o => o.dataset.value.toLowerCase() === term)) {
          newItem.textContent = '✚ Create "' + q.trim() + '"';
          newItem.dataset.value = q.trim();
          newItem.style.display = '';
        } else {
          newItem.style.display = 'none';
        }
      }

      inp.addEventListener('focus', () => openList());
      inp.addEventListener('input', () => { filterOpts(inp.value); list.classList.add('open'); arrow.classList.add('open'); });
      arrow.addEventListener('mousedown', e => { e.preventDefault(); list.classList.contains('open') ? closeList() : openList(); inp.focus(); });

      list.addEventListener('mousedown', e => {
        const opt = e.target.closest('.sec-combo-opt, .sec-combo-new');
        if (!opt) return;
        e.preventDefault();
        inp.value = opt.dataset.value;
        closeList();
        inp.focus();
      });

      document.addEventListener('click', e => { if (!combo.contains(e.target)) closeList(); });
      document.addEventListener('keydown', e => { if (e.key === 'Escape') closeList(); });
    })();
  </script>
</body>
</html>
