<?php
/**
 * LogixPulse — Client Navbar Partial  (includes/navbar.php)
 * -------------------------------------------------------
 * Include on every page:
 *   <?php include 'includes/navbar.php'; ?>
 *
 * Override defaults before including:
 *   $lp_user_name    = 'Marcus Vance';
 *   $lp_user_email   = 'marcus@devlogo.io';
 *   $lp_user_role    = 'Super Administrator';
 *   $lp_client_name  = 'Apex Global Logistics';
 *   $lp_active_tab   = 'overview';   // overview | deliverables | billing | approval
 */

/* ── Defaults ── */
$lp_user_name   = $lp_user_name   ?? 'Alex Lawson';
$lp_user_email  = $lp_user_email  ?? 'alex.lawson@devlogo.io';
$lp_user_role   = $lp_user_role   ?? 'Super Administrator';
$lp_client_name = $lp_client_name ?? 'Apex Global Logistics';
$lp_active_tab  = $lp_active_tab  ?? 'overview';

/* ── Initials ── */
$parts       = explode(' ', trim($lp_user_name));
$lp_initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

/* ── Sanitise ── */
$lp_user_name   = htmlspecialchars($lp_user_name,   ENT_QUOTES, 'UTF-8');
$lp_user_email  = htmlspecialchars($lp_user_email,  ENT_QUOTES, 'UTF-8');
$lp_user_role   = htmlspecialchars($lp_user_role,   ENT_QUOTES, 'UTF-8');
$lp_client_name = htmlspecialchars($lp_client_name, ENT_QUOTES, 'UTF-8');
$lp_initials    = htmlspecialchars($lp_initials,    ENT_QUOTES, 'UTF-8');

/* ── Helper: active tab class ── */
function lp_tab_active(string $tab, string $current): string {
    return $tab === $current ? ' lp-nav-tab--active' : '';
}
?>

<!-- ═══════════════════════════════════════════════════════════
     LogixPulse · Client Navbar
     ═══════════════════════════════════════════════════════════ -->
<header id="lp-header" role="banner">

    <!-- ── LEFT: Logo / Brand ── -->
    <a href="index.php" id="lp-brand" aria-label="LogixPulse home">
        <!-- Pulse icon -->
        <svg id="lp-brand-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <rect width="32" height="32" rx="8" fill="#4f46e5"/>
            <polyline points="5,18 10,12 15,20 20,8 25,15 27,15"
                      stroke="#fff" stroke-width="2.2"
                      stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span id="lp-brand-text">Logix<span>Pulse</span></span>
    </a>

    <!-- ── CENTRE: Navigation tabs ── -->
    <nav id="lp-nav-tabs" aria-label="Client navigation">
        <a href="#"
           class="lp-nav-tab<?= lp_tab_active('overview',     $lp_active_tab) ?>"
           id="lp-tab-overview"
           data-tab="overview"
           aria-current="<?= $lp_active_tab === 'overview' ? 'page' : 'false' ?>">
            Overview
        </a>
        <a href="#"
           class="lp-nav-tab<?= lp_tab_active('deliverables', $lp_active_tab) ?>"
           id="lp-tab-deliverables"
           data-tab="deliverables"
           aria-current="<?= $lp_active_tab === 'deliverables' ? 'page' : 'false' ?>">
            Deliverables
        </a>
        <a href="#"
           class="lp-nav-tab<?= lp_tab_active('billing',      $lp_active_tab) ?>"
           id="lp-tab-billing"
           data-tab="billing"
           aria-current="<?= $lp_active_tab === 'billing' ? 'page' : 'false' ?>">
            Billing &amp; Invoices
        </a>
        <a href="#"
           class="lp-nav-tab<?= lp_tab_active('approval',     $lp_active_tab) ?>"
           id="lp-tab-approval"
           data-tab="approval"
           aria-current="<?= $lp_active_tab === 'approval' ? 'page' : 'false' ?>">
            Approval Hub
        </a>
    </nav>

    <!-- ── RIGHT: Client selector + utility icons + profile ── -->
    <div id="lp-nav-right">

        <!-- Client / project selector pill -->
        <button class="lp-client-pill" id="lp-client-pill" type="button" aria-label="Switch client">
            <span class="lp-client-dot" aria-hidden="true"></span>
            <span class="lp-client-name"><?= $lp_client_name ?></span>
            <svg class="lp-client-chevron" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>

        <!-- Messages icon -->
        <button class="lp-icon-btn" id="lp-messages-btn" type="button" aria-label="Messages">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </button>

        <!-- ── Profile trigger + account dropdown ── -->
        <div id="lp-profile-wrap">

            <button id="lp-profile-btn" type="button"
                    aria-haspopup="true" aria-expanded="false"
                    aria-controls="lp-account-dropdown"
                    aria-label="Account menu">
                <div id="lp-avatar" aria-hidden="true"><?= $lp_initials ?></div>
                <span id="lp-profile-name"><?= $lp_user_name ?></span>
                <svg id="lp-profile-chevron" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <!-- ACCOUNT DROPDOWN -->
            <div id="lp-account-dropdown" role="menu" aria-label="Account menu">

                <!-- Top profile section -->
                <div id="lp-drop-header">
                    <div id="lp-drop-avatar" aria-hidden="true"><?= $lp_initials ?></div>
                    <div id="lp-drop-info">
                        <div id="lp-drop-name"><?= $lp_user_name ?></div>
                        <div id="lp-drop-email"><?= $lp_user_email ?></div>
                        <span id="lp-drop-role-badge"><?= $lp_user_role ?></span>
                    </div>
                </div>

                <div class="lp-drop-divider" role="separator"></div>

                <!-- Menu items -->
                <div id="lp-drop-body">

                    <a href="#" class="lp-drop-item" role="menuitem" id="lp-drop-profile">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        My Profile
                    </a>

                    <a href="#" class="lp-drop-item" role="menuitem" id="lp-drop-security">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Password &amp; 2FA
                    </a>

                    <a href="#" class="lp-drop-item" role="menuitem" id="lp-drop-notifications">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        Notification Settings
                    </a>

                    <a href="#" class="lp-drop-item" role="menuitem" id="lp-drop-help">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        Help &amp; Support
                    </a>

                </div>

                <div class="lp-drop-divider" role="separator"></div>

                <!-- Sign out — destructive -->
                <div id="lp-drop-footer">
                    <button class="lp-drop-item lp-drop-danger" type="button"
                            role="menuitem" id="lp-drop-signout">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Sign Out of LogixPulse
                    </button>
                </div>

            </div><!-- /#lp-account-dropdown -->
        </div><!-- /#lp-profile-wrap -->

    </div><!-- /#lp-nav-right -->

</header>
<!-- ═══════════════════════════════════════════════ end #lp-header ═ -->
