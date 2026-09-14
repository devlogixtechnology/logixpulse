<?php
/**
 * LogixPulse — Reusable Sidebar Partial  (includes/sidebar.php)
 * -------------------------------------------------------
 * HOW TO USE ON ANY PAGE:
 *   <?php include 'includes/sidebar.php'; ?>   (from root)
 *   <?php include '../includes/sidebar.php'; ?> (from sub-folder)
 *
 * ACTIVE LINK HIGHLIGHTING:
 *   Define $lp_active_page before including, e.g.:
 *   <?php $lp_active_page = 'leadboard'; include 'includes/sidebar.php'; ?>
 *
 *   Accepted values match the 'data-page' on each <li>:
 *   dashboard | leadboard | clients | team |
 *   reports | rbac | auditlogs | admin
 * -------------------------------------------------------
 */

/* Default: no page is active */
$lp_active_page = $lp_active_page ?? 'dashboard';

/* ── User info defaults (override before including) ── */
$sb_user_name  = $sb_user_name  ?? 'Alex Lawson';
$sb_user_role  = $sb_user_role  ?? 'Super Admin';

$sb_parts    = explode(' ', trim($sb_user_name));
$sb_initials = strtoupper(substr($sb_parts[0], 0, 1) . (isset($sb_parts[1]) ? substr($sb_parts[1], 0, 1) : ''));

$sb_user_name  = htmlspecialchars($sb_user_name,  ENT_QUOTES, 'UTF-8');
$sb_user_role  = htmlspecialchars($sb_user_role,  ENT_QUOTES, 'UTF-8');
$sb_initials   = htmlspecialchars($sb_initials,   ENT_QUOTES, 'UTF-8');

/**
 * Helper — returns ' active' class string if the page key matches.
 */
function lp_sidebar_active(string $page, string $current): string {
    return $page === $current ? ' lp-sb-active' : '';
}
?>

<!-- ═══════════════════════════════════════════════════════════
     LogixPulse · Left Sidebar
     ═══════════════════════════════════════════════════════════ -->
<aside id="lp-sidebar" aria-label="Primary navigation">

    <!-- ── Sidebar top: brand + collapse toggle ── -->
    <div class="lp-sb-top">
        <!-- Brand mark (icon only visible in rail, full text in expanded) -->
        <a href="index.php" class="lp-sb-brand" aria-label="LogixPulse home">
            <svg class="lp-sb-brand-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <rect width="32" height="32" rx="8" fill="#4f46e5"/>
                <polyline points="5,18 10,12 15,20 20,8 25,15 27,15"
                          stroke="#fff" stroke-width="2.2"
                          stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="lp-sb-brand-text">Logix<span>Pulse</span></span>
        </a>

        <!-- Collapse / expand toggle -->
        <button id="lp-sb-collapse-btn" type="button"
                aria-label="Toggle sidebar" aria-expanded="true"
                aria-controls="lp-sidebar">
            <!-- chevrons-left icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <polyline points="11 17 6 12 11 7"/>
                <polyline points="18 17 13 12 18 7"/>
            </svg>
        </button>
    </div>

    <!-- ── Sidebar scroll area ── -->
    <nav class="lp-sb-inner" aria-label="Sidebar navigation">

        <!-- ══ CORE OPERATIONS ══ -->
        <div class="lp-sb-section">
            <span class="lp-sb-label">Core Operations</span>
            <ul class="lp-sb-list" role="list">

                <li data-page="dashboard">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('dashboard', $lp_active_page) ?>"
                       id="lp-nav-dashboard" aria-label="Dashboard">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <span class="lp-sb-text">Dashboard</span>
                    </a>
                </li>

                <li data-page="leadboard">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('leadboard', $lp_active_page) ?>"
                       id="lp-nav-leadboard" aria-label="Lead Board">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                        </svg>
                        <span class="lp-sb-text">Lead Board</span>
                        <span class="lp-sb-badge">128</span>
                    </a>
                </li>

                <li data-page="clients">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('clients', $lp_active_page) ?>"
                       id="lp-nav-clients" aria-label="Client Accounts">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span class="lp-sb-text">Client Accounts</span>
                    </a>
                </li>

                <li data-page="team">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('team', $lp_active_page) ?>"
                       id="lp-nav-team" aria-label="Team Roster">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                        </svg>
                        <span class="lp-sb-text">Team Roster</span>
                    </a>
                </li>

            </ul>
        </div><!-- /.lp-sb-section -->

        <!-- ══ EXECUTIVE & GOVERNANCE ══ -->
        <div class="lp-sb-section">
            <span class="lp-sb-label">Executive &amp; Governance</span>
            <ul class="lp-sb-list" role="list">

                <li data-page="reports">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('reports', $lp_active_page) ?>"
                       id="lp-nav-reports" aria-label="Executive Reports">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6"  y1="20" x2="6"  y2="14"/>
                            <line x1="2"  y1="20" x2="22" y2="20"/>
                        </svg>
                        <span class="lp-sb-text">Executive Reports</span>
                    </a>
                </li>

                <li data-page="rbac">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('rbac', $lp_active_page) ?>"
                       id="lp-nav-rbac" aria-label="User RBAC">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span class="lp-sb-text">User RBAC</span>
                    </a>
                </li>

                <li data-page="auditlogs">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('auditlogs', $lp_active_page) ?>"
                       id="lp-nav-auditlogs" aria-label="Audit Logs">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        <span class="lp-sb-text">Audit Logs</span>
                    </a>
                </li>

            </ul>
        </div><!-- /.lp-sb-section -->

        <!-- ══ SETTINGS ══ -->
        <div class="lp-sb-section">
            <span class="lp-sb-label">Settings</span>
            <ul class="lp-sb-list" role="list">

                <li data-page="admin">
                    <a href="#"
                       class="lp-sb-link<?= lp_sidebar_active('admin', $lp_active_page) ?>"
                       id="lp-nav-admin" aria-label="Administration">
                        <svg class="lp-sb-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        <span class="lp-sb-text">Administration</span>
                    </a>
                </li>

            </ul>
        </div><!-- /.lp-sb-section -->

    </nav><!-- /.lp-sb-inner -->

    <!-- ── Sidebar footer: user info ── -->
    <div class="lp-sb-footer">
        <div class="lp-sb-footer-avatar" aria-hidden="true"><?= $sb_initials ?></div>
        <div class="lp-sb-footer-info">
            <span class="lp-sb-footer-name"><?= $sb_user_name ?></span>
            <span class="lp-sb-footer-role">
                <span class="lp-sb-online-dot" aria-hidden="true"></span>
                <?= $sb_user_role ?>
            </span>
        </div>
        <!-- Sign-out icon -->
        <button class="lp-sb-footer-signout" type="button" aria-label="Sign out">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </button>
    </div>

</aside>
<!-- ═══════════════════════════════════════════════════════ end #lp-sidebar ═ -->
