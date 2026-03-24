// js/admin.js — Admin Panel Enhancements

document.addEventListener('DOMContentLoaded', () => {

    // ── Mobile sidebar toggle ─────────────────────────────
    const sidebar    = document.querySelector('.admin-sidebar');
    const sidebarBtn = document.getElementById('sidebarToggle');
    if (sidebarBtn && sidebar) {
        sidebarBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
        document.addEventListener('click', e => {
            if (!sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    // ── Data table row highlight ──────────────────────────
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', () => row.style.background = '#F0FDF4');
        row.addEventListener('mouseleave', () => row.style.background = '');
    });

    // ── Inline status badge update feedback ──────────────
    document.querySelectorAll('a[href*="update_status"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const row = this.closest('tr');
            if (row) {
                const badge = row.querySelector('.badge');
                if (badge) {
                    badge.style.opacity = '0.5';
                    badge.textContent = 'Updating…';
                }
            }
        });
    });

    // ── Rich text character counter for description ───────
    const descArea = document.querySelector('textarea[name="description"]');
    if (descArea) {
        const counter = document.createElement('div');
        counter.style.cssText = 'font-size:.75rem;color:var(--clr-text-muted);text-align:right;margin-top:.25rem';
        descArea.parentElement.appendChild(counter);

        function updateCounter() {
            const len = descArea.value.length;
            counter.textContent = `${len} character${len !== 1 ? 's' : ''}`;
            counter.style.color = len > 1000 ? 'var(--clr-warning)' : 'var(--clr-text-muted)';
        }
        descArea.addEventListener('input', updateCounter);
        updateCounter();
    }

    // ── Amenities tag input enhancement ──────────────────
    const amenInput = document.querySelector('input[name="amenities"]');
    if (amenInput) {
        const preview = document.createElement('div');
        preview.style.cssText = 'display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.5rem';
        amenInput.parentElement.appendChild(preview);

        function renderTags() {
            const tags = amenInput.value.split(',').map(t => t.trim()).filter(Boolean);
            preview.innerHTML = tags.map(t =>
                `<span style="background:var(--clr-bg);border:1px solid var(--clr-border);border-radius:100px;padding:.2rem .65rem;font-size:.78rem;color:var(--clr-forest)">${t}</span>`
            ).join('');
        }
        amenInput.addEventListener('input', renderTags);
        renderTags();
    }

    // ── Confirm all data-confirm elements ─────────────────
    // (Duplicated here as insurance in case main.js isn't loaded in admin)
    document.querySelectorAll('[data-confirm]').forEach(el => {
        if (!el.dataset.confirmBound) {
            el.dataset.confirmBound = '1';
            el.addEventListener('click', e => {
                if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
            });
        }
    });
});
