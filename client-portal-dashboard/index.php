<?php
/**
 * index.php
 * Main entry point for the Client Portal Dashboard.
 */

// Backend Authentication
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/timeline.php';

$client_id = $_SESSION['client_id'] ?? $_SESSION['user_id'] ?? 1;
$clientId = (int)$client_id;
$user_role = $_SESSION['user_role'] ?? 'client';

// ---- Fetch Timeline from Database ----
 $timeline = getClientTimeline($client_id);

// --- Sample/dummy data for cards not yet wired to a real backend ---
// Same pattern as the invoices data above: clearly labeled "Sample" in
// each card header, to be replaced once each service is connected.
$projects = [
    ["name" => "LogixPulse Client Portal", "status" => "On Track", "status_class" => "status-paid",    "progress" => 72],
    ["name" => "Mobile App - Phase 1",      "status" => "At Risk",  "status_class" => "status-overdue", "progress" => 38],
];

$documents = [
    ["name" => "Project Kickoff Deck.pdf", "date" => "Sep 05, 2026", "size" => "2.4 MB"],
    ["name" => "Signed Contract.pdf",      "date" => "Aug 18, 2026", "size" => "860 KB"],
    ["name" => "Brand Guidelines.pdf",     "date" => "Aug 02, 2026", "size" => "5.1 MB"],
];

$notifications = [
    ["message" => "Your invoice is due in 4 days.",                                "time" => "2 hours ago", "unread" => true],
    ["message" => "New document \"Brand Guidelines.pdf\" was shared with you.",     "time" => "1 day ago",   "unread" => true],
    ["message" => "Development phase started for your project.",                    "time" => "3 days ago",  "unread" => false],
];

$supportTickets = [
    ["id" => "TCK-1042", "subject" => "Question about a recent invoice",     "status" => "Open",     "status_class" => "status-pending", "updated" => "Sep 10, 2026"],
    ["id" => "TCK-1036", "subject" => "Request to update billing email",     "status" => "Resolved", "status_class" => "status-paid",    "updated" => "Aug 22, 2026"],
];

$accountOverview = [
    "plan"            => "Business Plan",
    "status"          => "Active",
    "next_billing"    => "Oct 01, 2026",
    "account_manager" => "Sana Malik",
];

// ---- Fetch Invoices from Database ----
 $invoices      = [];
 $error_message = null;

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('
        SELECT 
            i.id, 
            i.invoice_number, 
            i.project_id, 
            p.name AS project_name, 
            i.amount, 
            i.status, 
            i.issue_date, 
            i.due_date, 
            i.paid_date
        FROM invoices i
        LEFT JOIN projects p ON i.project_id = p.id
        WHERE i.client_id = :client_id
        ORDER BY i.issue_date DESC, i.id DESC
    ');
    $stmt->execute([':client_id' => $client_id]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Unable to load invoices. Please try again later.';
    $invoices      = [];
}

// Helper function to format dates
function formatDate($date_string) {
    if (empty($date_string)) return '—';
    try {
        $date = new DateTime($date_string);
        return $date->format('M j, Y');
    } catch (Exception $e) {
        return '—';
    }
}

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
                            <?php if ($timeline): ?>
                            <ol class="timeline-track" style="--timeline-progress: <?php echo $timeline['progress']; ?>%; --timeline-steps: <?php echo count($timeline['steps']); ?>;">
                                <?php foreach ($timeline['steps'] as $step): ?>
                                <li class="timeline-step is-<?php echo $step['status']; ?>">
                                    <span class="timeline-dot" aria-hidden="true">
                                        <?php if ($step['status'] === 'completed'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <?php elseif ($step['status'] === 'current'): ?>
                                            <span class="timeline-dot-pulse"></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="timeline-title"><?php echo htmlspecialchars($step['title']); ?></span>
                                    <span class="timeline-status"><?php echo htmlspecialchars($step['status_label']); ?></span>
                                    <span class="timeline-date"><?php echo htmlspecialchars($step['date_label']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ol>
                            <?php elseif (!$clientId): ?>
                                <p class="card-empty-hint">Log in to view your project timeline.</p>
                            <?php else: ?>
                                <p class="card-empty-hint">No timeline data found for this client.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </div>

            <div class="row g-4">
                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Active Projects <span class="dummy-data-flag">Sample</span></h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            </span>
                        </div>
                        <div class="simple-list">
                            <?php foreach ($projects as $project): ?>
                            <div class="simple-list-item">
                                <div class="simple-list-body">
                                    <p class="simple-list-title"><?php echo htmlspecialchars($project['name']); ?></p>
                                    <span class="simple-list-meta"><?php echo (int) $project['progress']; ?>% complete</span>
                                    <div class="project-progress-track">
                                        <div class="project-progress-fill" style="width: <?php echo (int) $project['progress']; ?>%;"></div>
                                    </div>
                                </div>
                                <div class="simple-list-side">
                                    <span class="status-badge <?php echo $project['status_class']; ?>">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        <?php echo htmlspecialchars($project['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Recent Documents <span class="dummy-data-flag">Sample</span></h2>
                            <span class="card-icon-chip chip-blue" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                            </span>
                        </div>
                        <div class="simple-list">
                            <?php foreach ($documents as $doc): ?>
                            <div class="simple-list-item">
                                <span class="simple-list-icon chip-blue" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </span>
                                <div class="simple-list-body">
                                    <p class="simple-list-title"><?php echo htmlspecialchars($doc['name']); ?></p>
                                    <span class="simple-list-meta"><?php echo htmlspecialchars($doc['date']); ?> &middot; <?php echo htmlspecialchars($doc['size']); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Notifications <span class="dummy-data-flag">Sample</span></h2>
                            <span class="card-icon-chip chip-amber" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            </span>
                        </div>
                        <div class="simple-list">
                            <?php foreach ($notifications as $note): ?>
                            <div class="simple-list-item">
                                <div class="simple-list-body">
                                    <p class="simple-list-title">
                                        <?php echo htmlspecialchars($note['message']); ?>
                                        <?php if ($note['unread']): ?><span class="unread-dot" aria-label="Unread"></span><?php endif; ?>
                                    </p>
                                    <span class="simple-list-meta"><?php echo htmlspecialchars($note['time']); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>

                <article class="col-12 col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header">
                            <h2>Support Tickets <span class="dummy-data-flag">Sample</span></h2>
                            <span class="card-icon-chip chip-green" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                            </span>
                        </div>
                        <div class="simple-list">
                            <?php foreach ($supportTickets as $ticket): ?>
                            <div class="simple-list-item">
                                <div class="simple-list-body">
                                    <p class="simple-list-title"><?php echo htmlspecialchars($ticket['id']); ?> &mdash; <?php echo htmlspecialchars($ticket['subject']); ?></p>
                                    <span class="simple-list-meta">Updated <?php echo htmlspecialchars($ticket['updated']); ?></span>
                                </div>
                                <div class="simple-list-side">
                                    <span class="status-badge <?php echo $ticket['status_class']; ?>">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        <?php echo htmlspecialchars($ticket['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>

                <article class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Account Overview <span class="dummy-data-flag">Sample</span></h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </span>
                        </div>
                        <div class="account-overview-grid">
                            <div class="account-overview-item">
                                <div class="account-overview-label">Plan</div>
                                <div class="account-overview-value"><?php echo htmlspecialchars($accountOverview['plan']); ?></div>
                            </div>
                            <div class="account-overview-item">
                                <div class="account-overview-label">Status</div>
                                <div class="account-overview-value">
                                    <span class="status-badge status-paid">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        <?php echo htmlspecialchars($accountOverview['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="account-overview-item">
                                <div class="account-overview-label">Next Billing Date</div>
                                <div class="account-overview-value"><?php echo htmlspecialchars($accountOverview['next_billing']); ?></div>
                            </div>
                            <div class="account-overview-item">
                                <div class="account-overview-label">Account Manager</div>
                                <div class="account-overview-value"><?php echo htmlspecialchars($accountOverview['account_manager']); ?></div>
                            </div>
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

        <section id="invoices" class="app-section" data-section="invoices">
            <div class="dashboard-heading">
                <h1>Your Invoices</h1>
                <p>View, filter, and download all invoices issued for your projects.</p>
            </div>

            <div class="row g-4">
                <article class="col-12">
                    <div class="dashboard-card invoice-card">
                        <div class="card-header">
                            <h2>Your Invoices</h2>
                            <span class="card-icon-chip chip-indigo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="7" x2="16" y2="7"></line><line x1="8" y1="11" x2="16" y2="11"></line><line x1="8" y1="15" x2="12" y2="15"></line></svg>
                            </span>
                        </div>

                        <?php if ($error_message): ?>
                            <!-- Error State -->
                            <div class="card-empty" style="border-color: #FBD2D2; background-color: #FDECEC;">
                                <p style="color: #B42318;"><?php echo htmlspecialchars($error_message); ?></p>
                            </div>
                        <?php elseif (empty($invoices)): ?>
                            <!-- Empty State -->
                            <div class="card-empty">
                                <p>No invoices yet</p>
                                <span class="card-empty-hint">Your invoices will appear here once they are generated for your projects.</span>
                            </div>
                        <?php else: ?>
                            <!-- Invoice Table (Shows only if invoices exist) -->
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
                                        <?php foreach ($invoices as $invoice): 
                                            $status = strtolower($invoice['status']);
                                            $status_class = 'status-' . $status;
                                            $status_label = ucfirst($status);
                                        ?>
                                        <tr data-status="<?php echo htmlspecialchars($status); ?>">
                                            <td class="invoice-id" data-label="Invoice"><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                            <td class="invoice-project" data-label="Project"><?php echo htmlspecialchars($invoice['project_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo formatDate($invoice['issue_date']); ?></td>
                                            <td><?php echo formatDate($invoice['due_date']); ?></td>
                                            <td class="invoice-amount" data-label="Amount">$<?php echo number_format((float) $invoice['amount'], 2); ?></td>
                                            <td data-label="Status">
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <span class="status-dot" aria-hidden="true"></span>
                                                    <?php echo $status_label; ?>
                                                </span>
                                            </td>
                                            <td class="text-end" data-label="Action">
                                                <div class="invoice-actions">
                                                    <a href="../api/download-invoice.php?id=<?php echo urlencode($invoice['id']); ?>" class="invoice-action-btn" title="Download Invoice" aria-label="Download invoice" style="text-decoration: none;">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                    </a>
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
                        <?php endif; ?>
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