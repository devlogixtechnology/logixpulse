<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
requireLogin('internal');

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Main Dashboard - LogixPulse</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 40px 16px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .badge { display: inline-block; background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 12px; }
        h1 { margin-top: 0; font-size: 22px; }
        .user-info { background: #f1f5f9; padding: 16px; border-radius: 8px; margin: 20px 0; font-size: 14px; }
        .user-info p { margin: 6px 0; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 8px; background: #4f46e5; color: #fff; text-decoration: none; font-size: 14px; font-weight: 500; }
        .btn.secondary { background: #0284c7; }
        .btn.danger { background: #ef4444; }
        .links-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .links-grid a { display: block; padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #334155; text-decoration: none; font-size: 13px; font-weight: 500; }
        .links-grid a:hover { background: #e0e7ff; border-color: #c7d2fe; color: #4338ca; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <span class="badge">Internal Staff</span>
        <h1>Main Dashboard</h1>
        <p style="color: #64748b; font-size: 14px;">Welcome back. You are authenticated with internal privileges.</p>

        <div class="user-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['role'] ?? '') ?></p>
            <p><strong>Logged In At:</strong> <?= htmlspecialchars($user['logged_at'] ?? '') ?></p>
        </div>

        <h3 style="font-size: 15px; margin-top: 24px; margin-bottom: 8px;">Quick Management Tools</h3>
        <div class="links-grid">
            <a href="lead_search.php">Lead Search & CRM</a>
            <a href="save_lead.php">Add New Lead</a>
            <a href="upload.php">Upload Invoices</a>
            <a href="dashboard_kpi.php">KPI Analytics API</a>
            <a href="who_is_logged_in.php">Session Check</a>
            <a href="../kanban/index.php">Kanban Board</a>
        </div>

        <div class="actions">
            <a class="btn danger" href="../auth/logout.php">Logout</a>
        </div>
    </div>
</div>
</body>
</html>
