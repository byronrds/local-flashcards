<?php 
require("../config/database_connection.php");
require("../includes/user_functions.php");
session_start();

// redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserByUserId($user_id);

// Fetch all flashcard sets (tables starting with set_)
$sets = [];
try {
    $stmt = $db->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        if (strpos($row[0], 'set_') === 0) {
            $sets[] = $row[0];
        }
    }
} catch (Exception $e) {
    // ignore for now
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | Local Flashcards</title>
    <link rel="stylesheet" href="../assets/css/output.css">
    <style>
        body { background: #ffffff; }
        .card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 2.5rem 2rem;
            width: 95%;
            max-width: 1200px;
            margin: 2rem auto;
        }
        .welcome {
            font-size: 2rem;
            font-weight: 700;
            color: #000000;
        }
        .subtitle {
            color: #666666;
            margin-bottom: 1.5rem;
        }
        .cta-btn {
            background: #000000;
            color: #ffffff;
            font-weight: 600;
            padding: 0.9rem 2.2rem;
            border-radius: 4px;
            font-size: 1.1rem;
            border: none;
            transition: background 0.2s;
            cursor: pointer;
        }
        .cta-btn:hover {
            background: #333333;
        }
        .features {
            margin-top: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        .feature {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
        }
        .feature-icon {
            color: #000000;
            font-size: 1.2rem;
        }
        .sets-list {
            margin-top: 2.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
            justify-content: center;
        }
        .set-card {
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 1.2rem 1.5rem;
            min-width: 180px;
            text-align: center;
            font-weight: 600;
            color: #000000;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .set-card:hover {
            background: #f9f9f9;
            border-color: #9ca3af;
        }
        @media (max-width: 768px) {
            .card { 
                width: 95%;
                padding: 1.5rem 1rem; 
                margin: 1rem auto;
            }
            .welcome { font-size: 1.5rem; }
            .sets-list { flex-direction: column; align-items: center; }
        }
        @media (max-width: 480px) {
            .card { 
                width: 98%;
                padding: 1.2rem 0.8rem; 
            }
            .welcome { font-size: 1.3rem; }
        }
    </style>
</head>

<body>
    <?php require("../includes/header.php"); ?>

    <div class="card">
        <div class="welcome mb-2">Welcome, <?php echo htmlspecialchars($user['user_id']); ?></div>
        <div class="subtitle">Ready to boost your memory and master new topics? Let's get started with your flashcards journey!</div>

        <button onClick="window.location='create_flashcards.php';" class="cta-btn mt-4">Create Flashcard Set</button>

        <div class="features">
            <div class="feature">
                <span class="feature-icon">•</span>
                <span>Organize your knowledge with unlimited flashcard sets.</span>
            </div>
            <div class="feature">
                <span class="feature-icon">•</span>
                <span>Quickly review and test yourself anytime, anywhere.</span>
            </div>
            <div class="feature">
                <span class="feature-icon">•</span>
                <span>Track your progress and focus on what matters most.</span>
            </div>
        </div>

        <?php if (!empty($sets)): ?>
        <div class="sets-list">
            <?php foreach ($sets as $set): ?>
                <a class="set-card" href="flashcard_list.php?set=<?= urlencode($set) ?>">
                    <?= htmlspecialchars(str_replace('set_', '', $set)) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="sets-list" style="color:#666666;">No flashcard sets yet. Create your first one!</div>
        <?php endif; ?>
    </div>
</body>
</html>
