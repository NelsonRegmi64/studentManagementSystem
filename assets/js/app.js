function initSidebar() {
  const toggle = document.getElementById('menuToggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!toggle || !sidebar) return;
  function openSidebar() {
    sidebar.classList.add('open');
    if (overlay) {
      overlay.classList.add('show');
      overlay.style.display = 'block';
    }
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar.classList.remove('open');
    if (overlay) {
      overlay.classList.remove('show');
      setTimeout(() => { overlay.style.display = 'none'; }, 250);
    }
    document.body.style.overflow = '';
  }
  toggle.addEventListener('click', () => {
    if (sidebar.classList.contains('open')) closeSidebar();
    else openSidebar();
  });
  if (overlay) overlay.addEventListener('click', closeSidebar);
  sidebar.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 900) closeSidebar();
    });
  });
}
function showToast(message, type) {
  type = type || 'success';
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = 'toast' + (type === 'error' ? ' error' : type === 'info' ? ' info' : '');
  toast.innerHTML = '<span class="toast-msg">' + message + '</span><button class="toast-close" aria-label="Close">&times;</button>';
  container.appendChild(toast);
  const remove = function () {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(40px)';
    toast.style.transition = '0.25s ease';
    setTimeout(function () { toast.remove(); }, 250);
  };
  toast.querySelector('.toast-close').addEventListener('click', remove);
  setTimeout(remove, 4000);
}
function initDeleteModals() {
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var href = this.getAttribute('href');
      var msg = this.getAttribute('data-confirm') || 'Are you sure you want to delete this?';
      showConfirmModal(msg, function () { window.location.href = href; });
    });
  });
}
function showConfirmModal(message, onConfirm) {
  var old = document.getElementById('confirmModal');
  if (old) old.remove();
  var overlay = document.createElement('div');
  overlay.className = 'modal-overlay show';
  overlay.id = 'confirmModal';
  overlay.innerHTML = '<div class="modal"><h3>Confirm Delete</h3><p>' + message + '</p><div class="modal-actions"><button class="btn-secondary" id="modalCancel">Cancel</button><button class="btn-danger" id="modalConfirm">Delete</button></div></div>';
  document.body.appendChild(overlay);
  document.getElementById('modalCancel').addEventListener('click', function () { overlay.remove(); });
  document.getElementById('modalConfirm').addEventListener('click', function () {
    overlay.remove();
    if (onConfirm) onConfirm();
  });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) overlay.remove();
  });
}
function initForms() {
  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (btn && !btn.disabled) {
        btn.dataset.originalText = btn.textContent;
        btn.textContent = 'Please wait...';
        btn.disabled = true;
        setTimeout(function () {
          btn.disabled = false;
          btn.textContent = btn.dataset.originalText || 'Submit';
        }, 5000);
      }
    });
  });
}
document.addEventListener('DOMContentLoaded', function () {
  initSidebar();
  initDeleteModals();
  initForms();
});
