/* ── Sidebar Toggle ──────────────────────────── */
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');
const toggle   = document.getElementById('sidebarToggle');
const main     = document.querySelector('.main-content');

function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('show');
}

function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('show');
}

function isDesktop() { return window.innerWidth > 992; }

toggle?.addEventListener('click', () => {
    if (isDesktop()) {
        const collapsed = sidebar.style.transform === 'translateX(-100%)';
        sidebar.style.transform  = collapsed ? '' : 'translateX(-100%)';
        main.style.marginLeft    = collapsed ? 'var(--sidebar-w)' : '0';
    } else {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    }
});

overlay?.addEventListener('click', closeSidebar);

window.addEventListener('resize', () => {
    if (isDesktop()) closeSidebar();
});

/* ── Auto-dismiss alerts ─────────────────────── */
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(el)?.close(), 4000);
});

/* ── Confirm delete ──────────────────────────── */
document.querySelectorAll('.confirm-delete').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm('Are you sure you want to delete this record? This cannot be undone.')) {
            e.preventDefault();
        }
    });
});

/* ── Live table search ───────────────────────── */
document.getElementById('tableSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

/* ── Radio row highlight (attendance) ───────── */
document.querySelectorAll('input[type="radio"].att-radio')?.forEach(r => {
    r.addEventListener('change', () => {
        const row = r.closest('tr');
        if (!row) return;
        row.style.transition = 'background .2s';
        row.style.background  = r.value === 'Present'
            ? 'rgba(16,185,129,.05)'
            : r.value === 'Absent'
            ? 'rgba(239,68,68,.05)'
            : 'rgba(245,158,11,.05)';
    });
});
