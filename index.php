<?php
/**
 * index.php
 * Main entry point for the Client Portal Empty Dashboard.
 * Task: FEB-W7D1-3 - Build the Empty Client Dashboard (Navbar, Profile Dropdown)
 */
$pageTitle = "Dashboard - DevLogix Client Portal";
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
