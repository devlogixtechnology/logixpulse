<?php
declare(strict_types=1);

/**
 * LogixPulse — Internal Lead Search View (FR-CRM-05)
 * Multi-column debounced search across name, email, phone, company, and stage.
 */

require_once __DIR__ . '/auth.php';
requireLogin('internal');

require_once __DIR__ . '/../config/database.php';

$search = trim((string)($_GET['search'] ?? $_GET['q'] ?? ''));
$leads = [];
$error = '';

try {
    $pdo = getDatabaseConnection();

    if ($search === '') {
        $stmt = $pdo->query(
            "SELECT id, 
                    COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
                    email, phone, company, status, stage, created_at
             FROM leads 
             ORDER BY id ASC"
        );
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, 
                    COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
                    email, phone, company, status, stage, created_at
             FROM leads 
             WHERE name LIKE :q1 
                OR first_name LIKE :q2 
                OR last_name LIKE :q3 
                OR email LIKE :q4 
                OR phone LIKE :q5 
                OR company LIKE :q6
             ORDER BY id ASC"
        );
        $term = "%{$search}%";
        $stmt->execute([
            ':q1' => $term,
            ':q2' => $term,
            ':q3' => $term,
            ':q4' => $term,
            ':q5' => $term,
            ':q6' => $term,
        ]);
    }

    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    $error = 'Failed to load leads: ' . $e->getMessage();
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Search — LogixPulse CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --lp-indigo: #4F46E5;
            --lp-indigo-hover: #4338CA;
            --lp-emerald: #10B981;
            --lp-amber: #F59E0B;
            --lp-red: #EF4444;
            --lp-canvas: #F8FAFC;
            --lp-surface: #FFFFFF;
            --lp-text: #1E293B;
            --lp-muted: #64748B;
            --lp-border: #E2E8F0;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--lp-canvas);
            color: var(--lp-text);
            margin: 0;
            padding: 32px 16px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; color: var(--lp-indigo); font-size: 14px; text-decoration: none; font-weight: 500; }
        .btn-back:hover { text-decoration: underline; }
        .card {
            background: var(--lp-surface);
            border-radius: 12px;
            border: 1px solid var(--lp-border);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        h1 {
            font-family: 'Manrope', sans-serif;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px;
        }
        .subtitle { color: var(--lp-muted); font-size: 14px; margin: 0 0 20px; }
        
        .search-box-wrap { margin-bottom: 20px; }
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--lp-surface);
            border: 1.5px solid var(--lp-border);
            border-radius: 8px;
            padding: 10px 14px;
            transition: border-color .15s, box-shadow .15s;
        }
        .search-box:focus-within {
            border-color: var(--lp-indigo);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        .search-box svg { flex: none; color: var(--lp-muted); }
        .search-box input {
            flex: 1;
            border: 0;
            background: transparent;
            outline: 0;
            color: var(--lp-text);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
        }
        .search-box input::placeholder { color: var(--lp-muted); }
        .search-meta { font-size: 13px; color: var(--lp-muted); margin-top: 8px; font-weight: 500; }
        
        .table-responsive { overflow-x: auto; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid var(--lp-border);
            color: var(--lp-muted);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--lp-border);
            vertical-align: middle;
        }
        tr:hover td { background: #F8FAFC; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-new { background: #DBEAFE; color: #1E40AF; }
        .badge-contacted { background: #FEF3C7; color: #92400E; }
        .badge-qualified { background: #D1FAE5; color: #065F46; }
        .badge-lost { background: #FEE2E2; color: #991B1B; }
        .actions-cell { display: flex; gap: 8px; }
        .btn-sm {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: background .15s;
        }
        .btn-view { background: #EEF2FF; color: var(--lp-indigo); }
        .btn-view:hover { background: #E0E7FF; }
        .btn-note { background: #F1F5F9; color: var(--lp-text); }
        .btn-note:hover { background: #E2E8F0; }
        .empty-state { text-align: center; padding: 40px 16px; color: var(--lp-muted); font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-nav">
        <a href="main_dashboard.php" class="btn-back">← Back to Dashboard</a>
        <a href="save_lead.php" class="btn-sm btn-view" style="font-size: 13px; padding: 8px 14px; background: var(--lp-indigo); color: white;">+ Add New Lead</a>
    </div>

    <div class="card">
        <h1>Lead Search</h1>
        <p class="subtitle">Search CRM leads across name, email, phone, company, and sales stage.</p>

        <div class="search-box-wrap">
            <div class="search-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input id="leadSearchInput" type="text" placeholder="Type a name, email, phone, or company to search..." value="<?= e($search) ?>" autocomplete="off">
            </div>
            <div class="search-meta" id="searchMeta">
                Showing <?= count($leads) ?> <?= count($leads) === 1 ? 'lead' : 'leads' ?><?= $search !== '' ? ' matching "' . e($search) . '"' : '' ?>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div style="background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 16px;">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="leadsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="leadsBody">
                    <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">No matching leads found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leads as $l): ?>
                            <?php
                            $stage = strtolower((string)($l['stage'] ?? $l['status'] ?? 'new'));
                            $badgeClass = match($stage) {
                                'contacted' => 'badge-contacted',
                                'qualified', 'won' => 'badge-qualified',
                                'lost' => 'badge-lost',
                                default => 'badge-new'
                            };
                            ?>
                            <tr class="lead-row" data-search="<?= e(strtolower($l['name'] . ' ' . $l['email'] . ' ' . $l['phone'] . ' ' . ($l['company'] ?? ''))) ?>">
                                <td>#<?= (int)$l['id'] ?></td>
                                <td><strong><?= e($l['name']) ?></strong></td>
                                <td><?= e($l['email']) ?></td>
                                <td><?= e($l['phone']) ?></td>
                                <td><?= e($l['company'] ?? '—') ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= e($stage) ?></span></td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="lead_details.php?lead_id=<?= (int)$l['id'] ?>" class="btn-sm btn-view">Details</a>
                                        <a href="add_note.php?lead_id=<?= (int)$l['id'] ?>" class="btn-sm btn-note">+ Note</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Debounced multi-column search logic
(function() {
    const input = document.getElementById('leadSearchInput');
    const meta = document.getElementById('searchMeta');
    const rows = Array.from(document.querySelectorAll('#leadsBody .lead-row'));
    let debounceTimer = null;

    if (!input) return;

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = input.value.trim().toLowerCase();
            let matches = 0;

            rows.forEach(row => {
                const text = row.getAttribute('data-search') || '';
                if (query === '' || text.includes(query)) {
                    row.style.display = '';
                    matches++;
                } else {
                    row.style.display = 'none';
                }
            });

            meta.textContent = query === ''
                ? `Showing ${matches} leads`
                : `Showing ${matches} of ${rows.length} leads matching "${input.value.trim()}"`;

            // Update URL without reloading page
            const newUrl = new URL(window.location.href);
            if (query) {
                newUrl.searchParams.set('search', input.value.trim());
            } else {
                newUrl.searchParams.delete('search');
            }
            window.history.replaceState({}, '', newUrl.toString());
        }, 300);
    });
})();
</script>
</body>
</html>
