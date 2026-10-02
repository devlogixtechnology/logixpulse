<?php
require_once __DIR__ . '/../config/database.php';


$columns = $pdo->query("SELECT * FROM columns_table ORDER BY position")->fetchAll(PDO::FETCH_ASSOC);
$tasks = $pdo->query("SELECT * FROM tasks ORDER BY position")->fetchAll(PDO::FETCH_ASSOC);

$tasksByColumn = [];
foreach ($tasks as $t) {
    $tasksByColumn[$t['column_id']][] = $t;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>LogixPulse — Kanban (FEA-W7D4-2)</title>
<style>
  :root {
    --primary: #4F46E5; --primary-hover: #4338CA;
    --success: #10B981; --warning: #F59E0B; --danger: #EF4444;
    --canvas: #F8FAFB; --surface: #FFFFFF;
    --text-primary: #111827; --text-muted: #6B7280;
  }
  * { box-sizing: border-box; }
  body { font-family: Inter, -apple-system, sans-serif; background: var(--canvas); color: var(--text-primary); margin: 0; padding: 24px; }
  h1 { font-family: Manrope, sans-serif; font-size: 22px; font-weight: 600; margin-bottom: 4px; }
  .subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 20px; }
  .board { display: flex; gap: 16px; overflow-x: auto; align-items: flex-start; }
  .column { background: var(--surface); border-radius: 12px; padding: 12px; min-width: 260px; flex: 1; box-shadow: 0 1px 3px rgba(0,0,0,.08); min-height: 300px; }
  .column h2 { font-size: 14px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .03em; margin: 4px 0 12px 4px; }
  .card { background: var(--canvas); border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; font-size: 14px; cursor: grab; }
  .card.dragging { opacity: .4; }
  .board-search-wrap { max-width: 480px; margin-bottom: 20px; }
  .board-search-box {
    display: flex; align-items: center; gap: 8px; background: var(--surface);
    border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 12px;
    transition: border-color .15s;
  }
  .board-search-box:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
  .board-search-box svg { flex: none; color: var(--text-muted); }
  .board-search-box input {
    flex: 1; border: 0; background: transparent; outline: 0; color: var(--text-primary);
    font-family: inherit; font-size: 14px;
  }
  .board-search-box input::placeholder { color: var(--text-muted); }
  .board-search-meta { margin-top: 6px; font-size: 12px; font-weight: 500; color: var(--text-muted); min-height: 1em; }
  #status { margin-top: 16px; font-size: 13px; color: var(--text-muted); }
  #status.success { color: var(--success); }
  #status.error { color: var(--danger); }
</style>
</head>
<body>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
  <a href="../backend/main_dashboard.php" style="color: var(--primary); text-decoration: none; font-size: 14px; font-weight: 500;">← Back to Dashboard</a>
  <a href="../backend/lead_search.php" style="color: var(--primary); text-decoration: none; font-size: 14px; font-weight: 500;">Lead Search & CRM →</a>
</div>

<h1>LogixPulse Board</h1>
<p class="subtitle">Tasks loaded from database — drag and drop to update, search in real-time, and track workflow.</p>

<div class="board-search-wrap">
  <div class="board-search-box">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input id="boardSearchInput" type="text" placeholder="Search tasks by title..." autocomplete="off">
  </div>
  <div class="board-search-meta" id="boardSearchMeta"></div>
</div>

<div class="board" id="board">
  <?php foreach ($columns as $col): ?>
    <div class="column" data-column-id="<?= $col['id'] ?>">
      <h2><?= htmlspecialchars($col['name']) ?></h2>
      <?php foreach (($tasksByColumn[$col['id']] ?? []) as $task): ?>
        <div class="card" draggable="true" data-task-id="<?= $task['id'] ?>">
          <?= htmlspecialchars($task['title']) ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>

<div id="status"></div>

<script>
let draggedCard = null;

document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('dragstart', () => { draggedCard = card; card.classList.add('dragging'); });
  card.addEventListener('dragend', () => { card.classList.remove('dragging'); draggedCard = null; });
});

document.querySelectorAll('.column').forEach(column => {
  column.addEventListener('dragover', e => { e.preventDefault(); column.classList.add('drag-over'); });
  column.addEventListener('dragleave', () => column.classList.remove('drag-over'));
  column.addEventListener('drop', e => {
    e.preventDefault();
    column.classList.remove('drag-over');
    if (!draggedCard) return;

    column.appendChild(draggedCard);

    const taskId = draggedCard.dataset.taskId;
    const newColumnId = column.dataset.columnId;
    const newPosition = [...column.querySelectorAll('.card')].indexOf(draggedCard) + 1;

    saveDragToDatabase(taskId, newColumnId, newPosition);
  });
});

function saveDragToDatabase(taskId, newColumnId, newPosition) {
  const statusEl = document.getElementById('status');
  statusEl.className = '';
  statusEl.textContent = 'Saving...';

  fetch('update-stage.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ task_id: taskId, new_column_id: newColumnId, new_position: newPosition })
  })
  .then(res => res.json())
  .then(data => {
    statusEl.textContent = data.success
      ? '✓ Saved to database — refresh (F5) to verify changes.'
      : '✗ Save failed: ' + data.message;
    statusEl.className = data.success ? 'success' : 'error';
  })
  .catch(err => {
    statusEl.textContent = '✗ Network error: ' + err.message;
    statusEl.className = 'error';
  });
}

// Real-Time Search & Filtering (FEA-W7D5-1)
(function() {
  const searchInput = document.getElementById('boardSearchInput');
  const searchMeta = document.getElementById('boardSearchMeta');
  const allCards = Array.from(document.querySelectorAll('.card'));

  if (!searchInput) return;

  searchInput.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    let visibleCount = 0;

    allCards.forEach(card => {
      const text = card.textContent.trim().toLowerCase();
      if (q === '' || text.includes(q)) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (q === '') {
      searchMeta.textContent = '';
    } else {
      searchMeta.textContent = `Showing ${visibleCount} of ${allCards.length} tasks matching "${this.value.trim()}"`;
    }
  });
})();
</script>

</body>
</html>