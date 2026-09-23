<?php
/**
 * index.php
 * Main entry point for the Client Portal Empty Dashboard.
 * Task: FEB-W7D1-3 - Build the Empty Client Dashboard (Navbar, Profile Dropdown)
 */
$pageTitle = "Dashboard - LogixPulse Client Portal";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="dashboard">
    <div class="container py-4 py-md-5">

        <section id="dashboard" class="app-section active" data-section="dashboard">

            <div class="dashboard-heading">
                <h1>Dashboard</h1>
                <p>Welcome to your Client Portal. This is where your project activity will appear.</p>
            </div>

            <div class="row g-4 mb-4">
                <article class="col-12">
                    <div class="dashboard-card timeline-card">
                        <div class="card-header">
                            <h2>Project Timeline</h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </span>
                        </div>

                        <div class="timeline-scroll">
                            <ol class="timeline-track" style="--timeline-progress: 37.5%;">

                                <li class="timeline-step is-completed">
                                    <span class="timeline-dot" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </span>
                                    <span class="timeline-title">Project Started</span>
                                    <span class="timeline-status">Completed</span>
                                    <span class="timeline-date">Jan 6</span>
                                </li>

                                <li class="timeline-step is-completed">
                                    <span class="timeline-dot" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </span>
                                    <span class="timeline-title">Requirements &amp; Planning</span>
                                    <span class="timeline-status">Completed</span>
                                    <span class="timeline-date">Jan 20</span>
                                </li>

                                <li class="timeline-step is-current">
                                    <span class="timeline-dot" aria-hidden="true">
                                        <span class="timeline-dot-pulse"></span>
                                    </span>
                                    <span class="timeline-title">Development</span>
                                    <span class="timeline-status">In Progress</span>
                                    <span class="timeline-date">Est. Feb 28</span>
                                </li>

                                <li class="timeline-step is-upcoming">
                                    <span class="timeline-dot" aria-hidden="true"></span>
                                    <span class="timeline-title">Review &amp; Approval</span>
                                    <span class="timeline-status">Upcoming</span>
                                    <span class="timeline-date">Est. Mar 10</span>
                                </li>

                                <li class="timeline-step is-upcoming">
                                    <span class="timeline-dot" aria-hidden="true"></span>
                                    <span class="timeline-title">Delivery</span>
                                    <span class="timeline-status">Upcoming</span>
                                    <span class="timeline-date">Est. Mar 20</span>
                                </li>

                            </ol>
                        </div>
                    </div>
                </article>
            </div>

            <?php
            // Static sample data for the invoice table look (FEB-W7D2-3).
            // Replace with live data from the billing/invoices service once available.
            $invoices = [
                ["id" => "INV-2026-014", "project" => "LogixPulse Client Portal", "issued" => "Aug 02, 2026", "due" => "Aug 16, 2026", "amount" => "$1,250.00", "status" => "paid"],
                ["id" => "INV-2026-018", "project" => "LogixPulse Client Portal", "issued" => "Aug 20, 2026", "due" => "Sep 03, 2026", "amount" => "$980.00",   "status" => "paid"],
                ["id" => "INV-2026-021", "project" => "Mobile App - Phase 1",      "issued" => "Sep 01, 2026", "due" => "Sep 15, 2026", "amount" => "$2,400.00", "status" => "pending"],
                ["id" => "INV-2026-023", "project" => "LogixPulse Client Portal", "issued" => "Sep 05, 2026", "due" => "Sep 19, 2026", "amount" => "$640.00",   "status" => "pending"],
                ["id" => "INV-2026-019", "project" => "Mobile App - Phase 1",      "issued" => "Aug 10, 2026", "due" => "Aug 24, 2026", "amount" => "$1,800.00", "status" => "overdue"],
                ["id" => "INV-2026-025", "project" => "LogixPulse Client Portal", "issued" => "Sep 12, 2026", "due" => "Sep 26, 2026", "amount" => "$1,120.00", "status" => "draft"],
            ];

            $statusMeta = [
                "paid"    => ["label" => "Paid",    "class" => "status-paid"],
                "pending" => ["label" => "Pending",  "class" => "status-pending"],
                "overdue" => ["label" => "Overdue",  "class" => "status-overdue"],
                "draft"   => ["label" => "Draft",    "class" => "status-draft"],
            ];
            ?>

            <div class="row g-4">
                <article class="col-12">
                    <div class="dashboard-card invoice-card">
                        <div class="card-header">
                            <h2>Your Invoices</h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="7" x2="16" y2="7"></line><line x1="8" y1="11" x2="16" y2="11"></line><line x1="8" y1="15" x2="12" y2="15"></line></svg>
                            </span>
                        </div>

                        <div class="invoice-toolbar">
                            <div class="invoice-search">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="invoiceSearch" placeholder="Search invoice number or project...">
                            </div>

                            <div class="invoice-filters" role="group" aria-label="Filter invoices by status">
                                <button type="button" class="filter-chip is-active" data-filter="all">All</button>
                                <button type="button" class="filter-chip" data-filter="paid">Paid</button>
                                <button type="button" class="filter-chip" data-filter="pending">Pending</button>
                                <button type="button" class="filter-chip" data-filter="overdue">Overdue</button>
                                <button type="button" class="filter-chip" data-filter="draft">Draft</button>
                            </div>
                        </div>

                        <div class="invoice-table-scroll">
                            <table class="invoice-table" id="invoiceTable">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Project</th>
                                        <th>Issued On</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $invoice): $meta = $statusMeta[$invoice["status"]]; ?>
                                    <tr data-status="<?php echo htmlspecialchars($invoice["status"]); ?>">
                                        <td class="invoice-id"><?php echo htmlspecialchars($invoice["id"]); ?></td>
                                        <td class="invoice-project"><?php echo htmlspecialchars($invoice["project"]); ?></td>
                                        <td><?php echo htmlspecialchars($invoice["issued"]); ?></td>
                                        <td><?php echo htmlspecialchars($invoice["due"]); ?></td>
                                        <td class="invoice-amount"><?php echo htmlspecialchars($invoice["amount"]); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $meta["class"]; ?>">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <?php echo $meta["label"]; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="invoice-actions">
                                                <button type="button" class="invoice-action-btn" title="View invoice" aria-label="View invoice">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </button>
                                                <button type="button" class="invoice-action-btn" title="Download PDF" aria-label="Download invoice PDF">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="invoice-empty" id="invoiceEmptyState" hidden>
                                <p>No invoices match this filter.</p>
                                <span class="card-empty-hint">Try a different status or clear your search.</span>
                            </div>
                        </div>

                        <div class="invoice-table-footer">
                            <span class="invoice-count" id="invoiceCount">Showing <?php echo count($invoices); ?> of <?php echo count($invoices); ?> invoices</span>
                        </div>
                    </div>
                </article>
            </div>
        

            <div class="row g-4">

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Active Projects</h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No active projects yet.</p>
                            <span class="card-empty-hint">Your projects will be listed here once assigned.</span>
                        </div>
                    </div>
                </article>

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Recent Documents</h2>
                            <span class="card-icon-chip chip-blue" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No documents uploaded yet.</p>
                            <span class="card-empty-hint">Shared files and reports will show up here.</span>
                        </div>
                    </div>
                </article>

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Notifications</h2>
                            <span class="card-icon-chip chip-amber" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No new notifications.</p>
                            <span class="card-empty-hint">Updates about your account will appear here.</span>
                        </div>
                    </div>
                </article>

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Support Tickets</h2>
                            <span class="card-icon-chip chip-green" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No open tickets.</p>
                            <span class="card-empty-hint">Any support requests will be tracked here.</span>
                        </div>
                    </div>
                </article>

                <article class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Account Overview</h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>Your account summary is not available yet.</p>
                            <span class="card-empty-hint">Billing and plan details will be displayed in this section.</span>
                        </div>
                    </div>
                </article>

            </div>
        </section>

        <section id="projects" class="app-section" data-section="projects">

            <div class="dashboard-heading">
                <h1>Projects</h1>
                <p>Track the status of your ongoing and completed projects here.</p>
            </div>

            <div class="row g-4">
                <article class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>All Projects</h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No projects yet.</p>
                            <span class="card-empty-hint">Once a project is assigned to you, it will appear in this section.</span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section id="documents" class="app-section" data-section="documents">

            <div class="dashboard-heading">
                <h1>Documents</h1>
                <p>Access files, reports, and shared documents from your team.</p>
            </div>

            <div class="row g-4">
                <article class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>All Documents</h2>
                            <span class="card-icon-chip chip-blue" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No documents shared yet.</p>
                            <span class="card-empty-hint">Files shared with you will be listed here.</span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section id="support" class="app-section" data-section="support">

            <div class="dashboard-heading">
                <h1>Support</h1>
                <p>Reach out to the team or track your existing support requests.</p>
            </div>

            <div class="row g-4">
                <article class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Support Tickets</h2>
                            <span class="card-icon-chip chip-green" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                            </span>
                        </div>
                        <div class="card-empty">
                            <p>No support tickets yet.</p>
                            <span class="card-empty-hint">Any request you raise with the team will show up here.</span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

    </div>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
