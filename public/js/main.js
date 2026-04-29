// ─── ReelProof Main JS ────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {

  // ── Auto-dismiss flash messages ───────────────────────────────────────────
  document.querySelectorAll('.flash').forEach(flash => {
    setTimeout(() => {
      flash.style.transition = 'opacity 0.5s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500);
    }, 4000);
  });

  // ── Mobile menu toggle ────────────────────────────────────────────────────
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const navLinks  = document.querySelector('.navbar-links');
  if (mobileBtn && navLinks) {
    mobileBtn.addEventListener('click', () => {
      const isVisible = navLinks.classList.toggle('mobile-open');
      mobileBtn.textContent = isVisible ? '✕' : '☰';
    });
  }

  // ── Password visibility toggle ────────────────────────────────────────────
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);
      if (target) {
        target.type = target.type === 'password' ? 'text' : 'password';
        btn.textContent = target.type === 'password' ? '👁' : '🙈';
      }
    });
  });

  // ── Confirm delete ────────────────────────────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });

  // ── Upload zone click passthrough ─────────────────────────────────────────
  document.querySelectorAll('.upload-zone').forEach(zone => {
    zone.addEventListener('click', () => {
      zone.querySelector('input[type="file"]')?.click();
    });
    zone.addEventListener('dragover', e => {
      e.preventDefault();
      zone.style.borderColor = 'var(--accent)';
    });
    zone.addEventListener('dragleave', () => {
      zone.style.borderColor = '';
    });
    zone.addEventListener('drop', e => {
      e.preventDefault();
      zone.style.borderColor = '';
      const input = zone.querySelector('input[type="file"]');
      if (input && e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  });

  // ── Textarea auto-resize ──────────────────────────────────────────────────
  document.querySelectorAll('textarea').forEach(ta => {
    ta.addEventListener('input', () => {
      ta.style.height = 'auto';
      ta.style.height = ta.scrollHeight + 'px';
    });
  });

});
