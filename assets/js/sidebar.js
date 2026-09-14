/**
 * LogixPulse — Sidebar JS  (assets/js/sidebar.js)
 * -------------------------------------------------------
 * Handles:
 *   • Desktop collapse / expand toggle
 *   • Mobile open / close via overlay
 *   • Escape key closes mobile sidebar
 *
 * Vanilla JS only — no jQuery, no frameworks.
 * All IDs use the #lp- prefix to avoid conflicts.
 */
(function () {
    'use strict';

    var sidebar     = document.getElementById('lp-sidebar');
    var collapseBtn = document.getElementById('lp-sb-collapse-btn');
    var overlay     = document.getElementById('lp-sidebar-overlay');
    var menuBtn     = document.getElementById('lp-menu-toggle');

    if (!sidebar) return;

    var MOBILE_BREAKPOINT = 992;   /* px — matches CSS media query */
    var COLLAPSED_CLASS   = 'lp-sb-collapsed';
    var OPEN_CLASS        = 'open';
    var OVERLAY_CLASS     = 'active';

    /* ── Helpers ── */
    function isMobile() {
        return window.innerWidth < MOBILE_BREAKPOINT;
    }

    /* ── Desktop: collapse / expand ── */
    function toggleCollapse() {
        if (isMobile()) return;   /* mobile uses open/close instead */
        sidebar.classList.toggle(COLLAPSED_CLASS);
        var isCollapsed = sidebar.classList.contains(COLLAPSED_CLASS);
        if (collapseBtn) {
            collapseBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
        }
    }

    /* ── Mobile: open ── */
    function openSidebar() {
        sidebar.classList.add(OPEN_CLASS);
        if (overlay) {
            overlay.classList.add(OVERLAY_CLASS);
            overlay.setAttribute('aria-hidden', 'false');
        }
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
    }

    /* ── Mobile: close ── */
    function closeSidebar() {
        sidebar.classList.remove(OPEN_CLASS);
        if (overlay) {
            overlay.classList.remove(OVERLAY_CLASS);
            overlay.setAttribute('aria-hidden', 'true');
        }
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
    }

    /* ── Collapse button ── */
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            if (isMobile()) {
                closeSidebar();
            } else {
                toggleCollapse();
            }
        });
    }

    /* ── Hamburger (menu toggle in navbar) ── */
    if (menuBtn) {
        menuBtn.addEventListener('click', function () {
            sidebar.classList.contains(OPEN_CLASS) ? closeSidebar() : openSidebar();
        });
    }

    /* ── Overlay tap to close ── */
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    /* ── Escape key ── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isMobile() && sidebar.classList.contains(OPEN_CLASS)) {
            closeSidebar();
        }
    });

}());
