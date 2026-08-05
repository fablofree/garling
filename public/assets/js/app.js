/* ============================================================
   Garage A. Lingiah — Main JavaScript
   ============================================================ */

(function () {
    'use strict';

    // ── Sidebar toggle ──────────────────────────────────────
    const sidebar      = document.getElementById('sidebar');
    const mainWrapper  = document.getElementById('mainWrapper');
    const toggleBtn    = document.getElementById('sidebarToggle');
    const overlay      = document.getElementById('sidebarOverlay');

    function openSidebar() {
        if (sidebar)  sidebar.classList.add('open');
        if (overlay)  overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar)  sidebar.classList.remove('open');
        if (overlay)  overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar when a nav link is clicked on mobile
    if (sidebar) {
        sidebar.querySelectorAll('.nav-item').forEach(function (item) {
            item.addEventListener('click', function () {
                if (window.innerWidth < 769) closeSidebar();
            });
        });
    }

    // ── Auto-dismiss flash messages ─────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        });
    }, 5000);

    // ── Confirm delete buttons ──────────────────────────────
    document.querySelectorAll('form[onsubmit]').forEach(function (form) {
        // Already handled inline via onsubmit
    });

    // ── Table search (client-side quick filter) ─────────────
    const quickSearch = document.getElementById('quickSearch');
    if (quickSearch) {
        quickSearch.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.table tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    // ── Number formatting helper ─────────────────────────────
    window.formatNumber = function (n, decimals) {
        return parseFloat(n || 0).toFixed(decimals !== undefined ? decimals : 2);
    };

    // ── Highlight active nav based on URL ───────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(function (link) {
        const href = link.getAttribute('href');
        if (href && href !== '/' && currentPath.startsWith(href)) {
            link.classList.add('active');
        }
    });

    // ── Date inputs: set max to today where appropriate ─────
    document.querySelectorAll('input[type=date][name=payment_date]').forEach(function (el) {
        if (!el.value) el.value = new Date().toISOString().split('T')[0];
    });

    // ── Print shortcut ───────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            // Let browser handle; no override
        }
    });

})();
