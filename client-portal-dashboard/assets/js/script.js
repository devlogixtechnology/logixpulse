/**
 * script.js
 * Handles the profile dropdown menu, mobile nav toggle,
 * same-page section switching, and the invoice table
 * search/filter behavior for the Client Portal.
 */

document.addEventListener("DOMContentLoaded", function () {
    var profileButton = document.getElementById("profileMenuButton");
    var profileDropdown = document.getElementById("profileDropdown");
    var mobileToggle = document.getElementById("mobileToggle");
    var mobileLinks = document.getElementById("mobileLinks");
    var navLinks = document.querySelectorAll(".nav-link[data-section]");
    var sections = document.querySelectorAll(".app-section");
    var invoiceSearch = document.getElementById("invoiceSearch");
    var invoiceFilters = document.querySelectorAll(".filter-chip[data-filter]");
    var invoiceRows = document.querySelectorAll("#invoiceTable tbody tr");
    var invoiceEmptyState = document.getElementById("invoiceEmptyState");
    var invoiceCount = document.getElementById("invoiceCount");
    var activeInvoiceFilter = "all";

    if (profileButton && profileDropdown) {
        profileButton.addEventListener("click", function (event) {
            event.stopPropagation();
            toggleDropdown();
        });

        profileDropdown.addEventListener("click", function (event) {
            event.stopPropagation();
        });

        document.addEventListener("click", function () {
            closeDropdown();
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closeDropdown();
                profileButton.focus();
            }
        });
    }

    if (mobileToggle && mobileLinks) {
        mobileToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            var isOpen = mobileLinks.classList.toggle("open");
            mobileToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });
    }

    if (navLinks.length && sections.length) {
        navLinks.forEach(function (link) {
            link.addEventListener("click", function (event) {
                event.preventDefault();
                var targetSection = link.getAttribute("data-section");
                showSection(targetSection);

                if (mobileLinks && mobileLinks.classList.contains("open")) {
                    mobileLinks.classList.remove("open");
                    mobileToggle.setAttribute("aria-expanded", "false");
                }
            });
        });

        var initialSection = window.location.hash
            ? window.location.hash.replace("#", "")
            : "dashboard";
        showSection(initialSection);
    }

    function showSection(sectionId) {
        var sectionExists = document.getElementById(sectionId);
        if (!sectionExists) {
            sectionId = "dashboard";
        }

        sections.forEach(function (section) {
            section.classList.toggle("active", section.id === sectionId);
        });

        navLinks.forEach(function (link) {
            var isActive = link.getAttribute("data-section") === sectionId;
            link.classList.toggle("active", isActive);
        });

        if (window.location.hash !== "#" + sectionId) {
            history.replaceState(null, "", "#" + sectionId);
        }
    }

    function toggleDropdown() {
        var isOpen = profileDropdown.classList.contains("open");
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    function openDropdown() {
        profileDropdown.classList.add("open");
        profileButton.setAttribute("aria-expanded", "true");
    }

    function closeDropdown() {
        profileDropdown.classList.remove("open");
        profileButton.setAttribute("aria-expanded", "false");
    }

    if (invoiceRows.length) {
        if (invoiceFilters.length) {
            invoiceFilters.forEach(function (chip) {
                chip.addEventListener("click", function () {
                    invoiceFilters.forEach(function (btn) {
                        btn.classList.remove("is-active");
                    });
                    chip.classList.add("is-active");
                    activeInvoiceFilter = chip.getAttribute("data-filter");
                    applyInvoiceFilters();
                });
            });
        }

        if (invoiceSearch) {
            invoiceSearch.addEventListener("input", function () {
                applyInvoiceFilters();
            });
        }

        applyInvoiceFilters();
    }

    function applyInvoiceFilters() {
        var query = invoiceSearch ? invoiceSearch.value.trim().toLowerCase() : "";
        var visibleCount = 0;

        invoiceRows.forEach(function (row) {
            var status = row.getAttribute("data-status");
            var matchesFilter = activeInvoiceFilter === "all" || status === activeInvoiceFilter;
            var matchesSearch = !query || row.textContent.toLowerCase().indexOf(query) !== -1;
            var isVisible = matchesFilter && matchesSearch;

            row.classList.toggle("is-hidden", !isVisible);
            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (invoiceEmptyState) {
            invoiceEmptyState.classList.toggle("is-visible", visibleCount === 0);
            invoiceEmptyState.hidden = visibleCount !== 0 ? true : false;
        }

        if (invoiceCount) {
            invoiceCount.textContent = "Showing " + visibleCount + " of " + invoiceRows.length + " invoices";
        }
    }
});
