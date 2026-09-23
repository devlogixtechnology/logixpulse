<?php
/**
 * navbar.php
 * Client Portal navbar with branding, navigation links,
 * and profile dropdown trigger.
 */
$userName = "Sarah Client";
$userEmail = "sarah@example.com";
$userInitials = "SC";
?>
<header class="portal-navbar">
    <div class="navbar-inner container-fluid">

        <div class="navbar-brand">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="2 12 8 12 10 6 14 18 16 12 22 12"></polyline>
                </svg>
            </span>
            <span class="brand-text">DevLogix <span class="brand-subtext">Client Portal</span></span>
        </div>

        <nav class="navbar-links" aria-label="Primary navigation">
            <a href="#dashboard" class="nav-link active" data-section="dashboard">Dashboard</a>
            <a href="#projects" class="nav-link" data-section="projects">Projects</a>
            <a href="#documents" class="nav-link" data-section="documents">Documents</a>
            <a href="#support" class="nav-link" data-section="support">Support</a>
        </nav>

        <div class="navbar-actions">
            <span class="workspace-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="7" width="18" height="13" rx="2"></rect>
                    <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Client Workspace
            </span>

            <button class="icon-btn" type="button" aria-label="Notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>

            <div class="profile-menu">
                <button
                    class="profile-trigger"
                    type="button"
                    id="profileMenuButton"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-controls="profileDropdown"
                >
                    <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars($userInitials); ?></span>
                    <span class="profile-name"><?php echo htmlspecialchars($userName); ?></span>
                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <div class="profile-dropdown" id="profileDropdown" role="menu" aria-labelledby="profileMenuButton">
                    <div class="dropdown-header">
                        <span class="avatar avatar-lg" aria-hidden="true"><?php echo htmlspecialchars($userInitials); ?></span>
                        <div class="dropdown-user-info">
                            <span class="dropdown-user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="dropdown-user-email"><?php echo htmlspecialchars($userEmail); ?></span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" role="menuitem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21v-1a7 7 0 0 1 14 0v1"></path></svg>
                        My Profile
                    </a>
                    <a href="#" class="dropdown-item" role="menuitem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Account Settings
                    </a>
                    <a href="#" class="dropdown-item" role="menuitem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        Help &amp; Support
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-item-danger" role="menuitem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Log Out
                    </a>
                </div>
            </div>

            <button class="mobile-toggle" id="mobileToggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileLinks">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <nav class="mobile-links" id="mobileLinks" aria-label="Mobile navigation">
        <a href="#dashboard" class="nav-link active" data-section="dashboard">Dashboard</a>
        <a href="#projects" class="nav-link" data-section="projects">Projects</a>
        <a href="#documents" class="nav-link" data-section="documents">Documents</a>
        <a href="#support" class="nav-link" data-section="support">Support</a>
    </nav>
</header>
