<?php
/**
 * admin/includes/bulk_assoc_modal.php
 *
 * Drop-in include for all master pages that need bulk university association.
 * Place before </body>. Provides:
 *   - Modal HTML
 *   - All CSS (scoped)
 *   - openBulkModal(module, itemId, itemLabel)  — JS function
 *   - closeBulkModal()
 */
?>

<!-- ═══════════════════════════════════════════════════════
     BULK ASSOCIATION MODAL
════════════════════════════════════════════════════════ -->
<div id="bulkAssocOverlay" class="ba-overlay" role="dialog" aria-modal="true" aria-labelledby="baModalTitle">
  <div class="ba-dialog">

    <!-- Header -->
    <div class="ba-header">
      <div class="ba-header-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="ba-header-text">
        <h3 id="baModalTitle">Manage Universities</h3>
        <p id="baModalSubtitle">Assign or remove universities</p>
      </div>
      <button class="ba-close-btn" onclick="closeBulkModal()" title="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <!-- Search + Select All bar -->
    <div class="ba-toolbar">
      <div class="ba-search-wrap">
        <svg class="ba-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="baSearchInput" class="ba-search" placeholder="Search universities…" oninput="baFilter()" autocomplete="off">
      </div>
      <label class="ba-select-all-label">
        <input type="checkbox" id="baSelectAll" onchange="baToggleAll(this.checked)">
        <span id="baSelectAllText">Select All</span>
      </label>
    </div>

    <!-- Counter strip -->
    <div class="ba-counter-strip">
      <span id="baCounter">0 selected</span>
      <span id="baFilterNotice" style="display:none; color:var(--warning); font-size:11px;">Showing filtered results</span>
    </div>

    <!-- University list -->
    <div class="ba-list-wrap" id="baListWrap">
      <div class="ba-loading" id="baLoading">
        <div class="ba-spinner"></div>
        <span>Loading universities…</span>
      </div>
      <div id="baList" style="display:none;"></div>
      <div id="baEmpty" class="ba-empty" style="display:none;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".4">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <p>No universities match your search.</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="ba-footer">
      <button class="btn btn-secondary" onclick="closeBulkModal()">Cancel</button>
      <button class="btn btn-primary" id="baSaveBtn" onclick="baSave()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        <span id="baSaveBtnText">Save Changes</span>
      </button>
    </div>

  </div><!-- /.ba-dialog -->
</div><!-- /#bulkAssocOverlay -->

<!-- Toast notification -->
<div id="baToast" class="ba-toast" role="alert"></div>

<style>
/* ── Overlay ─────────────────────────────────────────── */
.ba-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.65);
  backdrop-filter: blur(5px);
  z-index: 99999;
  display: flex; align-items: center; justify-content: center;
  padding: 1rem;
  opacity: 0; visibility: hidden;
  transition: opacity .25s ease, visibility .25s ease;
}
.ba-overlay.active { opacity: 1; visibility: visible; }

/* ── Dialog ──────────────────────────────────────────── */
.ba-dialog {
  background: var(--surface);
  border: 1px solid var(--border-h, rgba(255,255,255,.13));
  border-radius: 18px;
  width: 100%; max-width: 520px;
  max-height: 88vh;
  display: flex; flex-direction: column;
  box-shadow: 0 24px 60px rgba(0,0,0,.5);
  transform: translateY(24px) scale(.97);
  transition: transform .3s cubic-bezier(.175,.885,.32,1.275);
  overflow: hidden;
}
.ba-overlay.active .ba-dialog { transform: translateY(0) scale(1); }

/* ── Header ──────────────────────────────────────────── */
.ba-header {
  display: flex; align-items: center; gap: 14px;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.ba-header-icon {
  width: 42px; height: 42px; border-radius: 12px;
  background: rgba(79,110,247,.15);
  color: var(--accent);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.ba-header-text { flex: 1; min-width: 0; }
.ba-header-text h3 { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.ba-header-text p  { font-size: 12px; color: var(--text-s); margin: 2px 0 0; }
.ba-close-btn {
  width: 32px; height: 32px; border-radius: 8px;
  border: 1px solid var(--border); background: none;
  color: var(--text-s); cursor: pointer; display: flex;
  align-items: center; justify-content: center;
  transition: background .15s, color .15s; flex-shrink: 0;
}
.ba-close-btn:hover { background: var(--surface-h); color: var(--text); }

/* ── Toolbar ─────────────────────────────────────────── */
.ba-toolbar {
  display: flex; gap: 12px; align-items: center;
  padding: .875rem 1.5rem;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.ba-search-wrap {
  flex: 1; position: relative;
}
.ba-search-icon {
  position: absolute; left: 10px; top: 50%;
  transform: translateY(-50%); color: var(--text-s); pointer-events: none;
}
.ba-search {
  width: 100%;
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .55rem .75rem .55rem 2rem;
  font-size: 13px; color: var(--text);
  font-family: inherit; outline: none;
  transition: border-color .2s, box-shadow .2s;
}
.ba-search::placeholder { color: var(--text-s); }
.ba-search:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,110,247,.15); }

body.light .ba-search { background: var(--bg); }

.ba-select-all-label {
  display: flex; align-items: center; gap: 7px;
  font-size: 12.5px; font-weight: 600; color: var(--text-m);
  cursor: pointer; user-select: none; white-space: nowrap;
  flex-shrink: 0;
}
.ba-select-all-label input { accent-color: var(--accent); width: 15px; height: 15px; cursor: pointer; }

/* ── Counter ─────────────────────────────────────────── */
.ba-counter-strip {
  padding: .4rem 1.5rem;
  font-size: 11.5px; color: var(--text-s);
  display: flex; align-items: center; gap: 10px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

/* ── List wrap ───────────────────────────────────────── */
.ba-list-wrap {
  flex: 1; overflow-y: auto; padding: .75rem 1rem;
  min-height: 200px;
}
.ba-list-wrap::-webkit-scrollbar { width: 5px; }
.ba-list-wrap::-webkit-scrollbar-track { background: transparent; }
.ba-list-wrap::-webkit-scrollbar-thumb { background: var(--border-h); border-radius: 4px; }

/* ── Loading ─────────────────────────────────────────── */
.ba-loading {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 12px; padding: 2.5rem 0; color: var(--text-s); font-size: 13px;
}
.ba-spinner {
  width: 26px; height: 26px;
  border: 2.5px solid var(--border-h);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: baSpin .7s linear infinite;
}
@keyframes baSpin { to { transform: rotate(360deg); } }

/* ── Empty ───────────────────────────────────────────── */
.ba-empty {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 10px; padding: 2rem 0;
  color: var(--text-s); font-size: 13px;
}

/* ── University row in list ──────────────────────────── */
.ba-uni-row {
  display: flex; align-items: center; gap: 12px;
  padding: .65rem .75rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background .15s;
}
.ba-uni-row:hover { background: var(--surface-h); }
.ba-uni-row.ba--checked { background: rgba(79,110,247,.08); }

.ba-uni-row input[type=checkbox] {
  accent-color: var(--accent);
  width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;
}
.ba-uni-thumb {
  width: 36px; height: 36px; border-radius: 8px;
  border: 1px solid var(--border);
  object-fit: contain; background: var(--surface-h);
  padding: 2px; flex-shrink: 0;
}
.ba-uni-initials {
  width: 36px; height: 36px; border-radius: 8px;
  background: rgba(79,110,247,.15); color: var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.ba-uni-name {
  flex: 1; font-size: 13.5px; font-weight: 500; color: var(--text);
  min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ba-uni-badge-new {
  font-size: 10px; font-weight: 700; color: var(--success);
  background: rgba(34,197,94,.1); border-radius: 10px;
  padding: 1px 7px; flex-shrink: 0;
}

/* ── Footer ──────────────────────────────────────────── */
.ba-footer {
  display: flex; gap: 10px; justify-content: flex-end;
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
}
.ba-footer .btn { min-width: 120px; justify-content: center; }

/* ── Toast ───────────────────────────────────────────── */
.ba-toast {
  position: fixed; bottom: 24px; right: 24px;
  background: var(--surface);
  border: 1px solid var(--border-h);
  border-radius: 10px;
  padding: .7rem 1.1rem;
  font-size: 13px; font-weight: 500; color: var(--text);
  box-shadow: 0 8px 24px rgba(0,0,0,.35);
  opacity: 0; transform: translateY(8px);
  transition: all .25s ease;
  pointer-events: none; z-index: 999999;
  display: flex; align-items: center; gap: 8px;
}
.ba-toast.show { opacity: 1; transform: translateY(0); }
.ba-toast.success { border-color: rgba(34,197,94,.4); color: #4ade80; }
.ba-toast.error   { border-color: rgba(239,68,68,.4);  color: #fca5a5; }
</style>

<script>
(function () {
  /* ── State ───────────────────────────────────────────── */
  let _module    = '';
  let _itemId    = 0;
  let _allUnis   = [];   // full dataset from server
  let _filtered  = [];   // currently visible subset
  const API_URL  = '<?= rtrim(BASE_URL, "/") ?>/api/admin/bulk_association.php';

  /* ── Open ────────────────────────────────────────────── */
  window.openBulkModal = function (module, itemId, itemLabel) {
    _module  = module;
    _itemId  = itemId;
    _allUnis = [];
    _filtered = [];

    document.getElementById('baModalSubtitle').textContent =
      'Assign / remove universities for: ' + itemLabel;
    document.getElementById('baSearchInput').value = '';
    document.getElementById('baList').style.display       = 'none';
    document.getElementById('baEmpty').style.display      = 'none';
    document.getElementById('baLoading').style.display    = 'flex';
    document.getElementById('baFilterNotice').style.display = 'none';
    document.getElementById('baSelectAll').checked        = false;
    updateCounter(0, 0);

    document.getElementById('bulkAssocOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';

    fetchUniversities();
  };

  /* ── Close ───────────────────────────────────────────── */
  window.closeBulkModal = function () {
    document.getElementById('bulkAssocOverlay').classList.remove('active');
    document.body.style.overflow = '';
  };

  // Close on overlay click
  document.getElementById('bulkAssocOverlay').addEventListener('click', function (e) {
    if (e.target === this) closeBulkModal();
  });

  // ESC key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeBulkModal();
  });

  /* ── Fetch universities from server ──────────────────── */
  function fetchUniversities() {
    fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'get_universities', module: _module, item_id: _itemId })
    })
    .then(r => r.json())
    .then(json => {
      if (!json.success) throw new Error(json.error || 'Failed to load');
      _allUnis = json.data;
      _filtered = _allUnis;
      renderList(_allUnis);
    })
    .catch(err => {
      document.getElementById('baLoading').innerHTML =
        '<p style="color:var(--danger);font-size:13px;">⚠ ' + err.message + '</p>';
    });
  }

  /* ── Render list ─────────────────────────────────────── */
  function renderList(unis) {
    const list = document.getElementById('baList');
    const empty = document.getElementById('baEmpty');
    document.getElementById('baLoading').style.display = 'none';

    if (!unis.length) {
      list.style.display  = 'none';
      empty.style.display = 'flex';
      updateCounter(0, 0);
      return;
    }

    empty.style.display = 'none';
    list.style.display  = 'block';

    list.innerHTML = unis.map(u => {
      const thumb = u.image
        ? `<img class="ba-uni-thumb" src="${u.image}" alt="${escHtml(u.uni_name)}">`
        : `<div class="ba-uni-initials">${escHtml(u.uni_name.substring(0, 2).toUpperCase())}</div>`;

      return `
        <label class="ba-uni-row${u.is_selected ? ' ba--checked' : ''}" data-id="${u.id}">
          <input type="checkbox" value="${u.id}" ${u.is_selected ? 'checked' : ''}
                 onchange="baOnCheck(this)">
          ${thumb}
          <span class="ba-uni-name">${escHtml(u.uni_name)}</span>
        </label>`;
    }).join('');

    refreshSelectAll();
    updateCounter(
      unis.filter(u => u.is_selected).length,
      _allUnis.length
    );
  }

  /* ── Filter (search) ─────────────────────────────────── */
  window.baFilter = function () {
    const q = document.getElementById('baSearchInput').value.toLowerCase().trim();
    _filtered = q ? _allUnis.filter(u => u.uni_name.toLowerCase().includes(q)) : _allUnis;

    const notice = document.getElementById('baFilterNotice');
    notice.style.display = q ? 'inline' : 'none';

    renderList(_filtered);
  };

  /* ── Checkbox change ─────────────────────────────────── */
  window.baOnCheck = function (cb) {
    const id  = parseInt(cb.value);
    const row = cb.closest('.ba-uni-row');
    const uData = _allUnis.find(u => u.id === id);
    if (uData) uData.is_selected = cb.checked;

    row.classList.toggle('ba--checked', cb.checked);
    refreshSelectAll();
    updateCounter(
      _allUnis.filter(u => u.is_selected).length,
      _allUnis.length
    );
  };

  /* ── Toggle all (only visible filtered rows) ─────────── */
  window.baToggleAll = function (checked) {
    // Update only the currently filtered set
    _filtered.forEach(u => { u.is_selected = checked; });

    // Re-render (preserves full _allUnis state for others)
    renderList(_filtered);

    updateCounter(
      _allUnis.filter(u => u.is_selected).length,
      _allUnis.length
    );
  };

  /* ── Refresh Select All state ────────────────────────── */
  function refreshSelectAll() {
    const chk = document.getElementById('baSelectAll');
    const visChecked  = _filtered.filter(u => u.is_selected).length;
    const visTotal    = _filtered.length;
    chk.indeterminate = visChecked > 0 && visChecked < visTotal;
    chk.checked       = visTotal > 0 && visChecked === visTotal;
    document.getElementById('baSelectAllText').textContent =
      chk.checked ? 'Deselect All' : 'Select All';
  }

  /* ── Counter ─────────────────────────────────────────── */
  function updateCounter(selected, total) {
    document.getElementById('baCounter').textContent =
      selected + ' of ' + total + ' universit' + (total === 1 ? 'y' : 'ies') + ' selected';
  }

  /* ── Save ────────────────────────────────────────────── */
  window.baSave = function () {
    const selectedIds = _allUnis.filter(u => u.is_selected).map(u => u.id);
    const btn     = document.getElementById('baSaveBtn');
    const btnText = document.getElementById('baSaveBtnText');

    btn.disabled  = true;
    btnText.textContent = 'Saving…';

    fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'sync',
        module: _module,
        item_id: _itemId,
        selected_ids: selectedIds
      })
    })
    .then(r => r.json())
    .then(json => {
      if (!json.success) throw new Error(json.error || 'Save failed');

      // Update the badge in the table row that triggered the modal
      const triggerRow = document.querySelector(`[data-ba-row-id="${_itemId}"][data-ba-module="${_module}"]`);
      if (triggerRow) {
        const badge = triggerRow.querySelector('.ba-usage-badge');
        if (badge) {
          badge.textContent = json.new_count + ' uni' + (json.new_count !== 1 ? 's' : '');
          badge.style.display = json.new_count > 0 ? '' : 'none';
        }
        const noneText = triggerRow.querySelector('.ba-none-text');
        if (noneText) {
          noneText.style.display = json.new_count === 0 ? '' : 'none';
        }
      }

      showToast('✓ ' + json.message, 'success');
      closeBulkModal();
    })
    .catch(err => {
      showToast('⚠ ' + err.message, 'error');
    })
    .finally(() => {
      btn.disabled = false;
      btnText.textContent = 'Save Changes';
    });
  };

  /* ── Toast helper ────────────────────────────────────── */
  function showToast(msg, type) {
    const t = document.getElementById('baToast');
    t.textContent = msg;
    t.className   = 'ba-toast ' + (type || '');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
  }

  /* ── HTML escape helper ──────────────────────────────── */
  function escHtml(s) {
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

})();
</script>
