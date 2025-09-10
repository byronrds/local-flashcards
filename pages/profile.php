<?php
require "../config/database_connection.php";
require "../includes/user_functions.php";
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserByUserId($user_id);

// Get user's flashcard sets with details
$sets = [];
$total_cards = 0;
try {
    $stmt = $db->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        if (strpos($row[0], 'set_') === 0) {
            $set_name = $row[0];
            // Count cards in this set
            $count_stmt = $db->query("SELECT COUNT(*) as count FROM `$set_name`");
            $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
            $card_count = $count_result['count'];
            
            $sets[] = [
                'name' => str_replace('set_', '', $set_name),
                'full_name' => $set_name,
                'card_count' => $card_count
            ];
            $total_cards += $card_count;
        }
    }
} catch (Exception $e) {
    // ignore
}

// Sort sets by name
usort($sets, function($a, $b) {
    return strcasecmp($a['name'], $b['name']);
});
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Local Flashcards</title>
    <link rel="stylesheet" href="../assets/css/output.css">
    <style>
        body { background: #ffffff; }
        .container { width: 95%; max-width: 1200px; margin: 2.5rem auto; background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 2.5rem; }
        .title { font-size: 1.5rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem; }
        .section-title { font-size: 1.2rem; font-weight: 600; color: #000000; margin: 2rem 0 1rem 0; }
        .profile-info { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 1.5rem; margin-bottom: 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 1.5rem; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: 700; color: #000000; }
        .stat-label { color: #666666; font-size: 0.9rem; }
        .profile-item { margin-bottom: 1rem; }
        .profile-label { font-weight: 600; color: #000000; }
        .profile-value { color: #666666; }
        .sets-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .set-card { background: #ffffff; border: 1px solid #d1d5db; border-radius: 4px; padding: 1.2rem; transition: border-color 0.2s; }
        .set-card:hover { border-color: #9ca3af; }
        .set-name { font-weight: 600; color: #000000; margin-bottom: 0.5rem; }
        .set-info { color: #666666; font-size: 0.9rem; margin-bottom: 1rem; }
        .set-actions { display: flex; gap: 0.5rem; }
        .btn-small { background: #000000; color: #ffffff; border: none; border-radius: 4px; padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .btn-small:hover { background: #333333; }
        .btn-outline { background: #ffffff; color: #000000; border: 1px solid #d1d5db; }
        .btn-outline:hover { background: #f9f9f9; }
        .back-btn { display: inline-block; margin-bottom: 1.5rem; background: #ffffff; color: #000000; border: 1px solid #d1d5db; border-radius: 4px; padding: 10px 16px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .back-btn:hover { background: #f9f9f9; }
        .logout-btn { background: #000000; color: #ffffff; border: none; border-radius: 4px; padding: 10px 16px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; margin-right: 1rem; }
        .logout-btn:hover { background: #333333; }
        .create-btn { background: #ffffff; color: #000000; border: 1px solid #d1d5db; border-radius: 4px; padding: 10px 16px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .create-btn:hover { background: #f9f9f9; }
        .empty-state { text-align: center; padding: 3rem 2rem; color: #666666; }
    </style>
</head>
<body>
    <?php require("../includes/header.php"); ?>
    
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <div class="title">Profile</div>
        
        <div class="profile-info">
            <div class="profile-item">
                <span class="profile-label">Username:</span> 
                <span class="profile-value"><?= htmlspecialchars($user['user_id']) ?></span>
            </div>
            <div class="profile-item">
                <span class="profile-label">Account created:</span> 
                <span class="profile-value"><?= date('F j, Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= count($sets) ?></div>
                <div class="stat-label">Flashcard Sets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_cards ?></div>
                <div class="stat-label">Total Cards</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_cards > 0 ? round($total_cards / max(count($sets), 1)) : 0 ?></div>
                <div class="stat-label">Avg Cards per Set</div>
            </div>
        </div>

        <div class="section-title">Your Flashcard Sets</div>
        
        <?php if (empty($sets)): ?>
            <div class="empty-state">
                <p>You haven't created any flashcard sets yet.</p>
                <a href="create_flashcards.php" class="btn-small" style="margin-top: 1rem;">Create Your First Set</a>
            </div>
        <?php else: ?>
            <div class="sets-list">
                <?php foreach ($sets as $set): ?>
                    <div class="set-card">
                        <div class="set-name"><?= htmlspecialchars($set['name']) ?></div>
                        <div class="set-info"><?= $set['card_count'] ?> cards</div>
                        <div class="set-actions">
                            <a href="flashcard_list.php?set=<?= urlencode($set['full_name']) ?>" class="btn-small">View</a>
                            <a href="edit_flashcards.php?set=<?= urlencode($set['full_name']) ?>" class="btn-small btn-outline">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="section-title">Actions</div>
        <a href="create_flashcards.php" class="create-btn">Create New Set</a>
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </div>
</body>
</html>
