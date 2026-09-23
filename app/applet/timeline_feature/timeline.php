<?php
// 1. Database Connection (Replace with your actual DB details)
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'logixpulse_db';

// Uncomment this in your actual project
// $conn = new mysqli($host, $db_user, $db_pass, $db_name);

// 2. Get Logged-in Client ID 
// session_start();
// $client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 1; 

// Simulated Database Fetching based on Client ID (For Testing)
// Client 1 = Phase 3, Client 2 = Phase 1
$client_id = 1; // Change this to 2 to test Client 2

if ($client_id === 1) {
    $current_phase = 3; 
} else {
    $current_phase = 1;
}

/* REAL DATABASE QUERY (Use this when connecting to actual DB)
$query = "SELECT current_phase FROM client_projects WHERE client_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$current_phase = $project ? (int)$project['current_phase'] : 1;
*/

// 3. Define Timeline Steps
$timeline_steps = [
    1 => ["title" => "Planning & Strategy", "date" => "Phase 1"],
    2 => ["title" => "UI/UX Design", "date" => "Phase 2"],
    3 => ["title" => "Development", "date" => "Phase 3"],
    4 => ["title" => "Testing & QA", "date" => "Phase 4"],
    5 => ["title" => "Final Launch", "date" => "Phase 5"]
];
?>

<!-- HTML TIMELINE STRUCTURE -->
<div class="custom-timeline-wrapper">
    <h3 class="timeline-heading">Project Progress</h3>
    <div class="timeline-container">
        
        <?php foreach ($timeline_steps as $step_num => $step_data): ?>
            <?php
                // Check status based on real data
                if ($step_num < $current_phase) {
                    $status_class = "completed";
                    $icon = "&#10003;"; // HTML Checkmark
                } elseif ($step_num === $current_phase) {
                    $status_class = "active";
                    $icon = "&#9679;"; // HTML Circle Dot
                } else {
                    $status_class = "pending";
                    $icon = ""; // Empty for pending
                }
            ?>
            
            <div class="timeline-step <?php echo $status_class; ?>">
                <div class="timeline-line"></div>
                <div class="timeline-icon"><?php echo $icon; ?></div>
                <div class="timeline-content">
                    <span class="timeline-date"><?php echo $step_data['date']; ?></span>
                    <h4 class="timeline-title"><?php echo $step_data['title']; ?></h4>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>
