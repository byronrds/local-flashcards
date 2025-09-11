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
$error = '';
$success = false;

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        // First, drop all user's flashcard sets
        $stmt = $db->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            if (strpos($row[0], 'set_') === 0) {
                $db->exec("DROP TABLE `{$row[0]}`");
            }
        }
        
        // Then delete the user from the user table
        $delete_stmt = $db->prepare("DELETE FROM user WHERE user_id = ?");
        $delete_stmt->execute([$user_id]);
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login with deletion confirmation
        header("Location: login.php?account_deleted=1");
        exit();
    } catch (PDOException $e) {
        $error = 'Error deleting account: ' . htmlspecialchars($e->getMessage());
    }
}

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
    <title>Profile | FlashMaster</title>
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
    <style>
        body { background: #2c2e31; color: #e2e2e2; }
        .container { width: 95%; max-width: 1200px; margin: 2.5rem auto; background: #323437; border: 1px solid #4a4d52; border-radius: 8px; padding: 2.5rem; }
        .title { font-size: 1.5rem; font-weight: 700; color: #e2e2e2; margin-bottom: 1.5rem; }
        .section-title { font-size: 1.2rem; font-weight: 600; color: #e2e2e2; margin: 2rem 0 1rem 0; }
        .profile-info { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1.5rem; margin-bottom: 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1.5rem; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: 700; color: #FFD700; }
        .stat-label { color: #b5b5b5; font-size: 0.9rem; }
        .profile-item { margin-bottom: 1rem; }
        .profile-label { font-weight: 600; color: #e2e2e2; }
        .profile-value { color: #b5b5b5; }
        .sets-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .set-card { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1.2rem; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3); }
        .set-card:hover { border-color: #FFD700; background: #424549; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4); }
        .set-name { font-weight: 600; color: #e2e2e2; margin-bottom: 0.5rem; }
        .set-info { color: #b5b5b5; font-size: 0.9rem; margin-bottom: 1rem; }
        .set-actions { display: flex; gap: 0.5rem; }
        .btn-small { background: #FFD700; color: #000000; border: none; border-radius: 4px; padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .btn-small:hover { background: #FFC107; }
        .btn-outline { background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; }
        .btn-outline:hover { background: #424549; }
        .back-btn { display: inline-block; margin-bottom: 1.5rem; background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; border-radius: 4px; padding: 10px 16px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .back-btn:hover { background: #424549; }
        .logout-btn { background: #FFD700; color: #000000; border: none; border-radius: 4px; padding: 10px 16px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; margin-right: 1rem; }
        .delete-btn { 
            background: #d32f2f; 
            color: #fff; 
            border: none; 
            border-radius: 4px; 
            padding: 10px 16px; 
            font-weight: 600; 
            text-decoration: none; 
            display: inline-block; 
            transition: background 0.2s; 
        }
        .delete-btn:hover { background:rgb(204, 22, 16); }
        .logout-btn:hover { background: #FFC107; }
        .create-btn { background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; border-radius: 4px; padding: 10px 16px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .create-btn:hover { background: #424549; }
        .empty-state { text-align: center; padding: 3rem 2rem; color: #b5b5b5; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
        .modal-content { background-color: #323437; margin: 15% auto; padding: 2rem; border: 1px solid #4a4d52; border-radius: 8px; width: 90%; max-width: 480px; }
        .modal-header { margin-bottom: 1rem; }
        .modal-title { color: #dc2626; font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
        .modal-body { margin-bottom: 1.5rem; color: #e2e2e2; line-height: 1.5; }
        .modal-actions { display: flex; gap: 1rem; justify-content: flex-end; }
        .btn-danger { background: #dc2626; color: #ffffff; border: none; border-radius: 4px; padding: 10px 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; border-radius: 4px; padding: 10px 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-secondary:hover { background: #424549; }
        .error { color: #e2e2e2; margin-bottom: 1em; background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1rem; }
    </style>
</head>
<body>
    <?php require("../includes/header.php"); ?>
    
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <div class="title">Profile</div>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
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

      

        <div class="section-title">Actions</div>
        <a href="create_flashcards.php" class="create-btn">Create New Set</a>
        <a href="logout.php" class="logout-btn">Sign Out</a>
        
        <div class="section-title" style="color: #dc2626; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #4a4d52;">⚠️ Danger Zone</div>
        <p style="color: #b5b5b5; font-size: 0.9rem; margin-bottom: 1rem;">Permanently delete your account and all associated data. This action cannot be undone.</p>
        <button type="button" onclick="showDeleteModal()" class="delete-btn">Delete Account</button>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">⚠️ Delete Account</h2>
            </div>
            <div class="modal-body">
                <p><strong>Are you sure you want to permanently delete your account?</strong></p>
                <p>This will delete:</p>
                <ul style="margin: 1rem 0; padding-left: 1.5rem; color: #b5b5b5;">
                    <li>Your user account (<?= htmlspecialchars($user['user_id']) ?>)</li>
                    <li>All <?= count($sets) ?> flashcard sets</li>
                    <li>All <?= $total_cards ?> flashcards</li>
                    <li>All account data and statistics</li>
                </ul>
                <p style="color: #dc2626; font-weight: 600;">This action cannot be undone!</p>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="hideDeleteModal()" class="btn-secondary">Cancel</button>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="delete_account" value="1">
                    <button type="submit" class="btn-danger">Yes, Delete My Account</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                hideDeleteModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideDeleteModal();
            }
        });
    </script>
</body>
</html>
