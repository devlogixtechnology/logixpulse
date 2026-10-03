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

    /* ----------------------------------------------------------------
       Milestone Feed (REC-FEB-03)
       Loads the logged-in client's milestones from api/client_timeline.php
       and renders them inside the Project Timeline card.
       All database text is inserted with textContent (never innerHTML),
       so it is always displayed as plain text.
       ---------------------------------------------------------------- */
    var milestoneFeed = document.getElementById("milestoneFeed");
    var milestoneBody = document.getElementById("milestoneFeedBody");
    var milestoneEndpoint = milestoneFeed ? milestoneFeed.getAttribute("data-endpoint") : "";
    var milestoneLoading = false;
    var milestoneLoadedAt = 0;
    var milestoneHasList = false;
    var milestoneExpanded = {}; // remembers which milestones are open across refreshes

    var MILESTONE_STATUS = {
        completed: { label: "Completed", cssClass: "is-completed" },
        in_progress: { label: "In Progress", cssClass: "is-in-progress" },
        upcoming: { label: "Upcoming", cssClass: "is-upcoming" }
    };

    var ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
    var ICON_CLOCK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 14"></polyline></svg>';
    var ICON_FILE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
    var ICON_CHEVRON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
    // Empty-state illustration: a vertical timeline with no events yet (fixed markup, no database text)
    var ILLUSTRATION_EMPTY_TIMELINE = '<svg viewBox="0 0 160 120" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">'
        + '<ellipse cx="80" cy="112" rx="46" ry="5" fill="#E5E7EE"></ellipse>'
        + '<line x1="44" y1="18" x2="44" y2="98" stroke="#C7CBF5" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 7"></line>'
        + '<circle cx="44" cy="22" r="9" fill="#EEF0FF" stroke="#4F46E5" stroke-width="2.5"></circle>'
        + '<circle cx="44" cy="58" r="9" fill="#FFFFFF" stroke="#C7CBF5" stroke-width="2.5" stroke-dasharray="3 3"></circle>'
        + '<circle cx="44" cy="94" r="9" fill="#FFFFFF" stroke="#C7CBF5" stroke-width="2.5" stroke-dasharray="3 3"></circle>'
        + '<rect x="64" y="12" width="70" height="20" rx="6" fill="#FFFFFF" stroke="#C7CBF5" stroke-width="2"></rect>'
        + '<rect x="72" y="19" width="36" height="5" rx="2.5" fill="#C7CBF5"></rect>'
        + '<rect x="72" y="26" width="22" height="3" rx="1.5" fill="#E5E7EE"></rect>'
        + '<rect x="64" y="48" width="70" height="20" rx="6" fill="#F6F7FB" stroke="#E5E7EE" stroke-width="2" stroke-dasharray="4 3"></rect>'
        + '<rect x="64" y="84" width="70" height="20" rx="6" fill="#F6F7FB" stroke="#E5E7EE" stroke-width="2" stroke-dasharray="4 3"></rect>'
        + '</svg>';

    if (milestoneFeed && milestoneBody && milestoneEndpoint) {
        milestoneBody.addEventListener("click", handleMilestoneClick);

        // Fetch again when the client returns to this tab, so status
        // changes made in the backend show up without a full page reload.
        document.addEventListener("visibilitychange", function () {
            var thirtySeconds = 30000;
            if (!document.hidden && Date.now() - milestoneLoadedAt > thirtySeconds) {
                loadMilestoneFeed(true);
            }
        });

        loadMilestoneFeed(false);
    }

    /**
     * Fetches the milestones. isRefresh = true means a list is already on
     * screen, so we update it quietly (no loading flash) and keep it if the
     * refresh fails.
     */
    function loadMilestoneFeed(isRefresh) {
        if (milestoneLoading) {
            return;
        }
        milestoneLoading = true;

        if (!isRefresh) {
            showMilestoneLoading();
        }

        fetch(milestoneEndpoint, {
            method: "GET",
            credentials: "same-origin",
            cache: "no-store",
            headers: { "Accept": "application/json" }
        })
            .then(function (response) {
                if (response.status === 401) {
                    var authError = new Error("unauthorized");
                    authError.code = 401;
                    throw authError;
                }
                if (!response.ok) {
                    throw new Error("HTTP " + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                if (!data || data.success !== true || !Array.isArray(data.milestones)) {
                    throw new Error("Unexpected response");
                }
                milestoneLoadedAt = Date.now();
                renderMilestones(data.milestones);
            })
            .catch(function (error) {
                if (isRefresh && milestoneHasList && error.code !== 401) {
                    return; // keep showing the last good data
                }
                showMilestoneError(error.code === 401);
            })
            .then(function () {
                milestoneLoading = false;
            });
    }

    function renderMilestones(milestones) {
        milestoneBody.setAttribute("aria-busy", "false");
        milestoneBody.textContent = "";

        // An empty list is a normal state for a newly started project.
        if (milestones.length === 0) {
            milestoneHasList = false;
            milestoneBody.appendChild(buildMilestoneEmpty());
            return;
        }

        var list = createEl("ol", "milestone-list");
        milestones.forEach(function (milestone) {
            list.appendChild(buildMilestoneItem(milestone));
        });
        milestoneBody.appendChild(list);
        milestoneHasList = true;
    }

    function buildMilestoneItem(milestone) {
        // Unknown status values are treated as Upcoming, never as Completed.
        var status = MILESTONE_STATUS[milestone.status] ? milestone.status : "upcoming";
        var config = MILESTONE_STATUS[status];
        var id = String(milestone.id);
        var notes = milestone.notes ? String(milestone.notes) : "";
        var attachments = Array.isArray(milestone.attachments) ? milestone.attachments : [];
        var hasDetails = notes !== "" || attachments.length > 0;
        var isOpen = hasDetails && milestoneExpanded[id] === true;

        var item = createEl("li", "milestone-item " + config.cssClass);

        // Timeline marker (icon)
        var marker = createEl("span", "milestone-marker");
        marker.setAttribute("aria-hidden", "true");
        if (status === "completed") {
            marker.innerHTML = ICON_CHECK; // fixed icon markup, no database text
        } else if (status === "in_progress") {
            marker.appendChild(createEl("span", "milestone-marker-pulse"));
        } else {
            marker.innerHTML = ICON_CLOCK;
        }
        item.appendChild(marker);

        // Content card
        var content = createEl("div", "milestone-content");

        var head = createEl("div", "milestone-head");
        head.appendChild(createEl("h4", "milestone-name", milestone.title || "Untitled milestone"));
        head.appendChild(createEl("span", "milestone-status", milestone.status_label || config.label));
        content.appendChild(head);

        var metaText = buildMilestoneMeta(milestone, status);
        if (metaText) {
            content.appendChild(createEl("p", "milestone-meta", metaText));
        }

        if (hasDetails) {
            var panelId = "milestone-details-" + id;

            var toggle = createEl("button", "milestone-toggle");
            toggle.type = "button";
            toggle.setAttribute("data-milestone-id", id);
            toggle.setAttribute("aria-controls", panelId);
            toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
            toggle.appendChild(createEl("span", "milestone-toggle-label", isOpen ? "Hide details" : "Show details"));
            var chevron = createEl("span", "milestone-toggle-icon");
            chevron.setAttribute("aria-hidden", "true");
            chevron.innerHTML = ICON_CHEVRON;
            toggle.appendChild(chevron);
            content.appendChild(toggle);

            var panel = createEl("div", "milestone-details");
            panel.id = panelId;
            panel.hidden = !isOpen;

            if (notes !== "") {
                panel.appendChild(createEl("p", "milestone-notes", notes));
            }
            if (attachments.length > 0) {
                panel.appendChild(createEl("p", "milestone-attachments-label", "Attachments"));
                var files = createEl("ul", "milestone-attachments");
                attachments.forEach(function (file) {
                    files.appendChild(buildMilestoneAttachment(file));
                });
                panel.appendChild(files);
            }
            content.appendChild(panel);
        }

        item.appendChild(content);
        return item;
    }

    // Attachments use the same look as "Recent Documents": icon, file name, size.
    // The backend only provides name and size, so they are shown as plain
    // entries (there is no file download for milestone attachments).
    function buildMilestoneAttachment(file) {
        var row = createEl("li", "milestone-attachment");

        var icon = createEl("span", "simple-list-icon chip-blue");
        icon.setAttribute("aria-hidden", "true");
        icon.innerHTML = ICON_FILE;
        row.appendChild(icon);

        var body = createEl("div", "simple-list-body");
        body.appendChild(createEl("p", "simple-list-title milestone-file-name", file.name || "Attachment"));
        if (file.size_label) {
            body.appendChild(createEl("span", "simple-list-meta", file.size_label));
        }
        row.appendChild(body);

        return row;
    }

    // Builds the one-line summary under the title, using only what is available.
    function buildMilestoneMeta(milestone, status) {
        var parts = [];

        if (milestone.project_name) {
            parts.push(String(milestone.project_name));
        }

        if (status === "completed") {
            if (milestone.completed_label) {
                parts.push("Completed " + milestone.completed_label);
            }
        } else if (status === "in_progress") {
            if (milestone.started_label) {
                parts.push("Started " + milestone.started_label);
            }
            if (milestone.due_label) {
                parts.push("Due " + milestone.due_label);
            }
        } else if (milestone.due_label) {
            parts.push("Planned for " + milestone.due_label);
        }

        return parts.join(" \u00B7 ");
    }

    function handleMilestoneClick(event) {
        var retry = event.target.closest(".milestone-retry");
        if (retry) {
            loadMilestoneFeed(false);
            return;
        }

        var toggle = event.target.closest(".milestone-toggle");
        if (!toggle) {
            return;
        }

        var panel = document.getElementById(toggle.getAttribute("aria-controls"));
        if (!panel) {
            return;
        }

        var willOpen = toggle.getAttribute("aria-expanded") !== "true";
        toggle.setAttribute("aria-expanded", willOpen ? "true" : "false");
        toggle.querySelector(".milestone-toggle-label").textContent = willOpen ? "Hide details" : "Show details";
        panel.hidden = !willOpen;
        milestoneExpanded[toggle.getAttribute("data-milestone-id")] = willOpen;
    }

    function showMilestoneLoading() {
        milestoneBody.setAttribute("aria-busy", "true");
        milestoneBody.textContent = "";

        var loading = createEl("div", "milestone-loading");
        loading.setAttribute("role", "status");
        var spinner = createEl("span", "milestone-spinner");
        spinner.setAttribute("aria-hidden", "true");
        loading.appendChild(spinner);
        loading.appendChild(createEl("span", "", "Loading your project timeline\u2026"));
        milestoneBody.appendChild(loading);
    }

    function buildMilestoneEmpty() {
        var empty = createEl("div", "card-empty milestone-empty");

        var art = createEl("span", "milestone-empty-art");
        art.setAttribute("aria-hidden", "true");
        art.innerHTML = ILLUSTRATION_EMPTY_TIMELINE; // fixed illustration markup, no database text
        empty.appendChild(art);

        empty.appendChild(createEl("p", "", "No project updates yet"));
        empty.appendChild(createEl("span", "card-empty-hint", "Project activity and milestone updates will appear here as work progresses."));
        return empty;
    }

    function showMilestoneError(isSessionExpired) {
        milestoneHasList = false;
        milestoneBody.setAttribute("aria-busy", "false");
        milestoneBody.textContent = "";

        var box = createEl("div", "card-empty milestone-error");
        box.setAttribute("role", "alert");

        if (isSessionExpired) {
            box.appendChild(createEl("p", "", "Your session has expired"));
            box.appendChild(createEl("span", "card-empty-hint", "Please sign in again to see your project timeline."));
            var link = createEl("a", "milestone-retry", "Go to sign in");
            link.href = "../index.html";
            box.appendChild(link);
        } else {
            box.appendChild(createEl("p", "", "We couldn\u2019t load your project timeline"));
            box.appendChild(createEl("span", "card-empty-hint", "Please check your connection and try again."));
            var button = createEl("button", "milestone-retry", "Try again");
            button.type = "button";
            box.appendChild(button);
        }

        milestoneBody.appendChild(box);
    }

    // Small helper: creates an element and sets its text safely.
    function createEl(tag, className, text) {
        var el = document.createElement(tag);
        if (className) {
            el.className = className;
        }
        if (text !== undefined) {
            el.textContent = text;
        }
        return el;
    }
});