<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Activity History</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        input, button { padding: 10px; font-size: 16px; }
        button { cursor: pointer; }
        #result { margin-top: 20px; }
        .item { padding: 12px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Lead Activity History</h1>
    <p>Enter a lead ID to fetch its complete activity history.</p>

    <form id="activityForm">
        <input type="number" id="lead_id" min="1" placeholder="Lead ID" required>
        <button type="submit">Load History</button>
    </form>

    <div id="result"></div>

    <script>
        document.getElementById('activityForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            const leadId = document.getElementById('lead_id').value;
            const result = document.getElementById('result');

            result.textContent = 'Loading...';

            try {
                const response = await fetch('api/lead_activity.php?lead_id=' + encodeURIComponent(leadId));
                const data = await response.json();

                if (!data.success) {
                    result.textContent = data.message || 'Unable to load activity history.';
                    return;
                }

                if (data.activities.length === 0) {
                    result.textContent = 'No activity history found for this lead.';
                    return;
                }

                result.innerHTML = data.activities.map(function (activity) {
                    return `
                        <div class="item">
                            <strong>${escapeHtml(activity.activity_type)}</strong><br>
                            ${escapeHtml(activity.description)}<br>
                            <small>${escapeHtml(activity.created_at)} · ${escapeHtml(activity.created_by || '')}</small>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                result.textContent = 'Unable to load activity history.';
            }
        });

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    </script>
</body>
</html>
