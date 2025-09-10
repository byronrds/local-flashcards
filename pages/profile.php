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

// Count user's flashcard sets
$sets_count = 0;
try {
    $stmt = $db->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        if (strpos($row[0], 'set_') === 0) {
            $sets_count++;
        }
    }
} catch (Exception $e) {
    // ignore
}
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
        .profile-info { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 1.5rem; margin-bottom: 2rem; }
        .profile-item { margin-bottom: 1rem; }
        .profile-label { font-weight: 600; color: #000000; }
        .profile-value { color: #666666; }
        .back-btn { display: inline-block; margin-bottom: 1.5rem; background: #ffffff; color: #000000; border: 1px solid #d1d5db; border-radius: 4px; padding: 10px 16px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .back-btn:hover { background: #f9f9f9; }
        .logout-btn { background: #000000; color: #ffffff; border: none; border-radius: 4px; padding: 10px 16px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .logout-btn:hover { background: #333333; }
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
                <span class="profile-value"><?= htmlspecialchars($user['created_at']) ?></span>
            </div>
            <div class="profile-item">
                <span class="profile-label">Flashcard sets:</span> 
                <span class="profile-value"><?= $sets_count ?> sets</span>
            </div>
        </div>
        
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </div>
</body>
</html>
