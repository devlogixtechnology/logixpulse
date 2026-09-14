<?php
/**
 * LogixPulse — index.php  (Page Frame Shell)
 * -------------------------------------------------------
 * Master layout template. Every page reuses this structure
 * and replaces ONLY the <!-- PAGE CONTENT --> section.
 *
 * Set the active sidebar link BEFORE including sidebar.php:
 *   <?php $lp_active_page = 'leadboard'; ?>
 *
 * Accepted values: dashboard | leadboard | clients | team |
 *                  reports | rbac | auditlogs | admin
 * -------------------------------------------------------
 */

$lp_active_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <title>Dashboard — LogixPulse</title>
    <meta name="description"
          content="LogixPulse — modern client & project management: deliverables, billing, approvals, and more in one place.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <!-- LogixPulse Global Design System -->
    <link rel="stylesheet" href="assets/css/app.css?v=3">

    <!-- LogixPulse Navbar -->
    <link rel="stylesheet" href="assets/css/navbar.css?v=5">

    <!-- LogixPulse Sidebar -->
    <link rel="stylesheet" href="assets/css/sidebar.css?v=1">
</head>
<body>

<div class="lp-app-wrapper">

    <!-- ═══════════════════════════════════════════════════
         NAVBAR  ·  reusable on every page
         Include this single line on every new page.
    ════════════════════════════════════════════════════ -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Mobile sidebar overlay backdrop -->
    <div class="lp-sidebar-overlay" id="lp-sidebar-overlay" aria-hidden="true"></div>

    <!-- ═══════════════════════════════════════════════════
         APP BODY  ·  sidebar + main content
    ════════════════════════════════════════════════════ -->
    <div class="lp-app-body">

        <!-- ── LEFT SIDEBAR ── -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- ══════════════════════════════════════════════
             PAGE CONTENT AREA
             Replace everything inside .lp-content on each
             new page. Do NOT modify the wrapper elements.
        ═══════════════════════════════════════════════ -->
        <main class="lp-content" id="lp-main-content" role="main">

            <!-- ─── Page content goes here ─── -->
            <!-- This area is intentionally empty.          -->
            <!-- Other team members will add content below. -->

        </main><!-- /#lp-main-content -->

    </div><!-- /.lp-app-body -->

</div><!-- /.lp-app-wrapper -->


<!-- Bootstrap 5 JS Bundle (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmWyf9kSq9cxLlNMG5B6e9bWqBD"
        crossorigin="anonymous"></script>

<!-- LogixPulse: Profile dropdown -->
<script src="assets/js/navbar.js?v=5"></script>

<!-- LogixPulse: Sidebar collapse + mobile toggle -->
<script src="assets/js/sidebar.js?v=1"></script>

</body>
</html>
