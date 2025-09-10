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
        .hero-bg {
            background: linear-gradient(120deg, #e0e7ff 0%, #f0fdfa 100%);
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 2.5rem 2rem;
            width: 95%;
            max-width: 1200px;
            min-width: 320px;
            margin: 2rem auto;
        }
        .welcome {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }
        .subtitle {
            color: #475569;
            margin-bottom: 1.5rem;
        }
        .cta-btn {
            background: linear-gradient(90deg, #6366f1 0%, #0ea5e9 100%);
            color: #fff;
            font-weight: 600;
            padding: 0.9rem 2.2rem;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px #6366f133;
            transition: background 0.2s;
        }
        .cta-btn:hover {
            background: linear-gradient(90deg, #4f46e5 0%, #0284c7 100%);
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
            color: #0ea5e9;
            font-size: 1.5rem;
        }
        .sets-list {
            margin-top: 2.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
            justify-content: center;
        }
        .set-card {
            background: #f1f5f9;
            border-radius: 0.7rem;
            box-shadow: 0 2px 8px #6366f133;
            padding: 1.2rem 1.5rem;
            min-width: 180px;
            text-align: center;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            transition: box-shadow 0.2s, background 0.2s;
            border: 1px solid #e0e7ef;
        }
        .set-card:hover {
            background: #e0e7ff;
            box-shadow: 0 4px 16px #6366f133;
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

<body class="hero-bg min-h-screen">
    <?php require("../includes/header.php"); ?>

    <div class="card">
        <div class="welcome mb-2">Welcome, <?php echo htmlspecialchars($user['user_id']); ?>! 👋</div>
        <div class="subtitle">Ready to boost your memory and master new topics? Let's get started with your flashcards journey!</div>

        <button onClick="window.location='create_flashcards.php';" class="cta-btn mt-4">Create Flashcard Set</button>

        <div class="features">
            <div class="feature">
                <span class="feature-icon">📚</span>
                <span>Organize your knowledge with unlimited flashcard sets.</span>
            </div>
            <div class="feature">
                <span class="feature-icon">⚡</span>
                <span>Quickly review and test yourself anytime, anywhere.</span>
            </div>
            <div class="feature">
                <span class="feature-icon">🎯</span>
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
        <div class="sets-list" style="color:#64748b;">No flashcard sets yet. Create your first one!</div>
        <?php endif; ?>
    </div>
</body>
</html>
