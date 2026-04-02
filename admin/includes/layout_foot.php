<?php
/**
 * admin/includes/layout_foot.php
 * Include just before </body> — outputs shared JS
 */
?>
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

// ── CONFIRM DELETE ──
document.querySelectorAll('[data-confirm]').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    if (!confirm(this.dataset.confirm || 'Are you sure?')) {
      e.preventDefault();
    }
  });
});

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
</script>
