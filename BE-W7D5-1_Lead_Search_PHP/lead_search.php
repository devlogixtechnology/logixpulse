<?php
// BE-W7D5-1: Build Lead Search (Core PHP)

// Sample Database / Array of Leads
$leads = [
    ["id" => 1, "name" => "Ahmed Khan", "email" => "ahmed@example.com", "phone" => "03001234567"],
    ["id" => 2, "name" => "Ali Raza", "email" => "ali@example.com", "phone" => "03219876543"],
    ["id" => 3, "name" => "Sara Fatima", "email" => "sara@example.com", "phone" => "03335554433"],
    ["id" => 4, "name" => "Usman Ghani", "email" => "usman@example.com", "phone" => "03121112233"],
    ["id" => 5, "name" => "Ayesha Malik", "email" => "ayesha@example.com", "phone" => "03456667788"]
];

// Get search query from URL or GET parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filter logic: Empty search query returns everyone
if ($search === '') {
    $filteredLeads = $leads;
} else {
    $filteredLeads = array_filter($leads, function($lead) use ($search) {
        $searchLower = strtolower($search);
        return (strpos(strtolower($lead['name']), $searchLower) !== false) ||
               (strpos(strtolower($lead['email']), $searchLower) !== false) ||
               (strpos($lead['phone'], $search) !== false);
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Search - Core PHP</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; max-width: 600px; margin: 0 auto; background: #f4f6f9; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input[type="text"] { width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; margin-bottom: 20px; }
        ul { list-style: none; padding: 0; margin: 0; }
        li { padding: 12px; border-bottom: 1px solid #eee; }
        li:last-child { border-bottom: none; }
        .name { font-weight: bold; font-size: 16px; color: #333; }
        .details { font-size: 14px; color: #666; margin-top: 4px; }
        .empty { color: #888; font-style: italic; text-align: center; }
    </style>
</head>
<body>

<div class="card">
    <h2>BE-W7D5-1: Lead Search</h2>
    
    <form method="GET" action="lead_search.php">
        <input type="text" name="search" placeholder="Search name, email, or phone (e.g. 'Ah')..." 
               value="<?php echo htmlspecialchars($search); ?>" oninput="this.form.submit()">
    </form>

    <ul>
        <?php if (empty($filteredLeads)): ?>
            <li class="empty">No matching leads found.</li>
        <?php else: ?>
            <?php foreach ($filteredLeads as $lead): ?>
                <li>
                    <div class="name"><?php echo htmlspecialchars($lead['name']); ?></div>
                    <div class="details">Email: <?php echo htmlspecialchars($lead['email']); ?> | Phone: <?php echo htmlspecialchars($lead['phone']); ?></div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
