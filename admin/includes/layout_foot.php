<?php
/**
 * admin/includes/layout_foot.php
 * Include just before </body> — outputs shared JS
 */
?>
<!-- GLOBAL DELETE MODAL -->
<div id="systemDeleteModal" class="modal-overlay">
  <div class="modal-dialog">
    <div class="modal-header">
       <div class="modal-icon">
         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
       </div>
       <h3>Confirm Deletion</h3>
    </div>
    <div class="modal-body">
       <p id="deleteModalSummary" style="margin-bottom:0;"></p>
       <div class="modal-warning">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span>Warning: This action cannot be rolled back or undone. Associated data will drop.</span>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
       <button type="button" class="btn btn-danger" id="deleteModalConfirmBtn">
         <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
         Confirm Delete
       </button>
    </div>
  </div>
</div>

<!-- GLOBAL LIGHTBOX -->
<div id="systemLightboxModal" class="modal-overlay" style="z-index:10000; padding:1.5rem;" onclick="closeLightbox(event)">
  <div style="position:relative; max-width:90%; max-height:90%; display:flex; align-items:center; justify-content:center;">
    <button type="button" onclick="closeLightbox(event)" style="position:absolute; top:-40px; right:-20px; background:none; border:none; color:#fff; font-size:32px; cursor:pointer; padding:10px; line-height:1;">&times;</button>
    <img id="lightboxImage" src="" alt="" style="max-width:100%; max-height:85vh; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,0.5); object-fit:contain; border:2px solid rgba(255,255,255,0.1); background:#000;">
  </div>
</div>

<script>
// ── SIDEBAR TOGGLE ──
const sidebar    = document.getElementById('sidebar');
const overlay    = document.getElementById('sidebarOverlay');
const hamburger  = document.getElementById('hamburgerBtn');
const sidebarClose = document.getElementById('sidebarClose');

function openSidebar() {
  sidebar.classList.add('open');
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeSidebar() {
  sidebar.classList.remove('open');
  overlay.classList.remove('active');
  document.body.style.overflow = '';
}

if (hamburger)    hamburger.addEventListener('click', openSidebar);
if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
if (overlay)      overlay.addEventListener('click', closeSidebar);

document.querySelectorAll('.nav-item').forEach(function(link) {
  link.addEventListener('click', function() {
    if (window.innerWidth <= 768) closeSidebar();
  });
});
window.addEventListener('resize', function() {
  if (window.innerWidth > 768) {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  }
});

// ── THEME TOGGLE ──
const themeToggle = document.getElementById('themeToggle');
const THEME_KEY   = 'sode_theme';

// Apply on load
(function() {
  if (localStorage.getItem(THEME_KEY) === 'light') {
    document.body.classList.add('light');
  }
})();

if (themeToggle) {
  themeToggle.addEventListener('click', function() {
    const isLight = document.body.classList.toggle('light');
    localStorage.setItem(THEME_KEY, isLight ? 'light' : 'dark');
  });
}

// ── AUTO-DISMISS ALERTS ──
document.querySelectorAll('.alert').forEach(function(el) {
  setTimeout(function() {
    el.style.transition = 'opacity 0.4s';
    el.style.opacity = '0';
    setTimeout(function() { el.remove(); }, 400);
  }, 4000);
});

// ── CONFIRM DELETE MODAL ──
let currentDeleteForm = null;

document.querySelectorAll('[data-confirm]').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    if(this.closest('form')) {
        e.preventDefault();
        currentDeleteForm = this.closest('form');
        const summary = this.dataset.confirm || 'Are you sure you want to delete this record?';
        document.getElementById('deleteModalSummary').textContent = summary;
        document.getElementById('systemDeleteModal').classList.add('active');
    } else {
        // Fallback for non-form buttons if any
        if (!confirm(this.dataset.confirm || 'Are you sure?')) {
          e.preventDefault();
        }
    }
  });
});

function closeDeleteModal() {
  document.getElementById('systemDeleteModal').classList.remove('active');
  currentDeleteForm = null;
}

const confirmBtn = document.getElementById('deleteModalConfirmBtn');
if (confirmBtn) {
    confirmBtn.addEventListener('click', function() {
        if (currentDeleteForm) {
            currentDeleteForm.submit();
        }
    });
}

// ── SLUG AUTO-GENERATE ──
function bindSlugGenerator(nameId, slugId) {
  const nameEl = document.getElementById(nameId);
  const slugEl = document.getElementById(slugId);
  if (!nameEl || !slugEl) return;

  let manualSlug = slugEl.value.trim() !== '';

  nameEl.addEventListener('input', function() {
    if (manualSlug) return;
    slugEl.value = nameEl.value
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/[\s]+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
    updateSlugPreview(slugId);
  });

  slugEl.addEventListener('input', function() {
    manualSlug = slugEl.value.trim() !== '';
    updateSlugPreview(slugId);
  });

  slugEl.addEventListener('blur', function() {
    // Clean up slug on blur
    this.value = this.value
      .toLowerCase()
      .replace(/[^a-z0-9-]/g, '')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
    updateSlugPreview(slugId);
  });
}

function updateSlugPreview(slugId) {
  const previewEl = document.getElementById(slugId + '_preview');
  const slugEl    = document.getElementById(slugId);
  if (previewEl && slugEl) {
    previewEl.textContent = slugEl.value || '—';
  }
}

// ── IMAGE PREVIEW ──
function bindImagePreview(inputId, previewId) {
  const input   = document.getElementById(inputId);
  const preview = document.getElementById(previewId);
  if (!input || !preview) return;

  input.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
    };
    reader.readAsDataURL(file);
  });
}

// ── GLOBAL LIGHTBOX ──
document.querySelectorAll('a[data-lightbox]').forEach(function(link) {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const href = this.getAttribute('href');
    if (href) {
      document.getElementById('lightboxImage').src = href;
      document.getElementById('systemLightboxModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  });
});

window.closeLightbox = function(e) {
  if (e && e.target !== e.currentTarget && e.target.tagName === 'IMG') {
    // Prevent closing if they clicked precisely on the image
    return;
  }
  document.getElementById('systemLightboxModal').classList.remove('active');
  document.body.style.overflow = '';
  setTimeout(() => { document.getElementById('lightboxImage').src = ''; }, 300);
};

// ── BULK SELECT & DELETE ──
(function() {
  const selectAll = document.getElementById('bulkSelectAll');
  const bulkBar = document.getElementById('bulkBar');
  const bulkForm = document.getElementById('bulkDeleteForm');
  if (!selectAll || !bulkBar || !bulkForm) return;

  const bulkCountEl = document.getElementById('bulkCount');
  const bulkIdsInput = document.getElementById('bulkDeleteIds');

  function getRowCheckboxes() {
    return document.querySelectorAll('.bulk-row-cb');
  }

  function updateBulkState() {
    const cbs = getRowCheckboxes();
    let checked = 0;
    let ids = [];
    cbs.forEach(cb => {
      if (cb.checked) {
        checked++;
        ids.push(cb.value);
        cb.closest('tr').classList.add('bulk-selected');
      } else {
        cb.closest('tr').classList.remove('bulk-selected');
      }
    });

    // Update select-all state
    selectAll.checked = cbs.length > 0 && checked === cbs.length;
    selectAll.indeterminate = checked > 0 && checked < cbs.length;

    // Update floating bar
    if (checked > 0) {
      bulkBar.classList.add('visible');
      bulkCountEl.innerHTML = '<span>' + checked + '</span> selected';
      bulkIdsInput.value = ids.join(',');
    } else {
      bulkBar.classList.remove('visible');
      bulkIdsInput.value = '';
    }
  }

  selectAll.addEventListener('change', function() {
    const cbs = getRowCheckboxes();
    cbs.forEach(cb => { cb.checked = selectAll.checked; });
    updateBulkState();
  });

  document.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('bulk-row-cb')) {
      updateBulkState();
    }
  });

  // Bulk delete button triggers modal
  const bulkDeleteTrigger = document.getElementById('bulkDeleteTrigger');
  if (bulkDeleteTrigger) {
    bulkDeleteTrigger.addEventListener('click', function() {
      const cbs = getRowCheckboxes();
      let checked = 0;
      cbs.forEach(cb => { if (cb.checked) checked++; });
      if (checked === 0) return;

      document.getElementById('deleteModalSummary').textContent =
        'Delete ' + checked + ' selected record(s)? This action cannot be undone.';
      currentDeleteForm = bulkForm;
      document.getElementById('systemDeleteModal').classList.add('active');
    });
  }

  // Deselect all button
  const bulkDeselectBtn = document.getElementById('bulkDeselectBtn');
  if (bulkDeselectBtn) {
    bulkDeselectBtn.addEventListener('click', function() {
      selectAll.checked = false;
      getRowCheckboxes().forEach(cb => { cb.checked = false; });
      updateBulkState();
    });
  }
})();
</script>

