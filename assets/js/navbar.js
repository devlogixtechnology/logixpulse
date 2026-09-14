/**
 * LogixPulse — Navbar Dropdown JS  (assets/js/navbar.js)
 * -------------------------------------------------------
 * Handles:
 *   • Profile/account dropdown open / close / toggle
 *   • Outside-click to close
 *   • Escape key to close
 *   • Keyboard activation (Enter / Space) on the trigger
 *
 * Vanilla JS only — no jQuery, no frameworks.
 * All IDs use the #lp- prefix to avoid conflicts.
 */
(function () {
    'use strict';

    var trigger  = document.getElementById('lp-profile-btn');
    var dropdown = document.getElementById('lp-account-dropdown');

    if (!trigger || !dropdown) return;

    /* ── Helpers ── */
    function openDropdown() {
        dropdown.classList.add('lp-open');
        trigger.classList.add('lp-active');
        trigger.setAttribute('aria-expanded', 'true');
    }

    function closeDropdown() {
        dropdown.classList.remove('lp-open');
        trigger.classList.remove('lp-active');
        trigger.setAttribute('aria-expanded', 'false');
    }

    function toggleDropdown() {
        dropdown.classList.contains('lp-open') ? closeDropdown() : openDropdown();
    }

    /* ── Profile button click ── */
    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleDropdown();
    });

    /* ── Keyboard activation ── */
    trigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleDropdown();
        }
        if (e.key === 'ArrowDown' && dropdown.classList.contains('lp-open')) {
            /* Move focus to first menu item */
            var first = dropdown.querySelector('.lp-drop-item');
            if (first) first.focus();
            e.preventDefault();
        }
    });

    /* ── Outside-click to close ── */
    document.addEventListener('click', function (e) {
        if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });

    /* ── Escape to close ── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && dropdown.classList.contains('lp-open')) {
            closeDropdown();
            trigger.focus();
        }
    });

}());
