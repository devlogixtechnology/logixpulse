/**
 * script.js
 * Handles the profile dropdown menu, mobile nav toggle,
 * and same-page section switching for the Client Portal navbar.
 */

document.addEventListener("DOMContentLoaded", function () {
    var profileButton = document.getElementById("profileMenuButton");
    var profileDropdown = document.getElementById("profileDropdown");
    var mobileToggle = document.getElementById("mobileToggle");
    var mobileLinks = document.getElementById("mobileLinks");
    var navLinks = document.querySelectorAll(".nav-link[data-section]");
    var sections = document.querySelectorAll(".app-section");

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
});
