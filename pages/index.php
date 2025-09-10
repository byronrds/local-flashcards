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
    <title>Welcome | FlashMaster</title>
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
    <style>
        body { background: #2c2e31; color: #e2e2e2; }
        .card {
            background: #323437;
            border: 1px solid #4a4d52;
            border-radius: 8px;
            padding: 2.5rem 2rem;
            width: 95%;
            max-width: 1200px;
            margin: 2rem auto;
        }
        .welcome {
            font-size: 2rem;
            font-weight: 700;
            color: #e2e2e2;
        }
        .subtitle {
            color: #b5b5b5;
            margin-bottom: 1.5rem;
        }
        .cta-btn {
            background: #FFD700;
            color: #000000;
            font-weight: 600;
            padding: 0.9rem 2.2rem;
            border-radius: 4px;
            font-size: 1.1rem;
            border: none;
            transition: background 0.2s;
            cursor: pointer;
        }
        .cta-btn:hover {
            background: #FFC107;
        }
        .features {
            margin: 1.5rem 0 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e5e5e5;
        }
        .feature {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
        }
        .feature-icon {
            color: #FFD700;
            font-size: 1.1rem;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #e2e2e2;
            margin: 2rem 0 1rem 0;
        }
        .sets-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .set-card {
            background: #3a3d42;
            border: 1px solid #4a4d52;
            border-radius: 6px;
            padding: 1.5rem;
            text-align: center;
            font-weight: 600;
            color: #e2e2e2;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        .set-card:hover {
            background: #424549;
            border-color: #FFD700;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
        }
        .set-card-name {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .set-card-info {
            color: #b5b5b5;
            font-size: 0.9rem;
            font-weight: 400;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #b5b5b5;
            border: 1px dashed #4a4d52;
            border-radius: 6px;
        }
        
        /* Robot Mascot Animation */
        .robot-container {
            position: relative;
            height: 60px;
            margin: 1rem 0;
            overflow: hidden;
        }
        .robot {
            position: absolute;
            width: 40px;
            height: 40px;
            background: #FFD700;
            border: 2px solid #000000;
            border-radius: 6px;
            animation: robotRun 4s linear infinite;
        }
        .robot::before {
            content: "⚡";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
        }
        .robot::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 8px;
            width: 6px;
            height: 6px;
            background: #FFD700;
            border: 1px solid #000000;
            border-radius: 50%;
            animation: robotLegs 0.3s ease-in-out infinite alternate;
        }
        @keyframes robotRun {
            0% { left: -50px; }
            100% { left: calc(100% + 50px); }
        }
        @keyframes robotLegs {
            0% { left: 8px; }
            100% { left: 16px; }
        }
        @media (max-width: 768px) {
            .card { 
                width: 95%;
                padding: 1.5rem 1rem; 
                margin: 1rem auto;
            }
            .welcome { font-size: 1.5rem; }
            .sets-list { 
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
        @media (max-width: 480px) {
            .card { 
                width: 98%;
                padding: 1.2rem 0.8rem; 
            }
            .welcome { font-size: 1.3rem; }
            .sets-list { 
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php require("../includes/header.php"); ?>

    <div class="card">
        <div class="welcome mb-2">Welcome, <?php echo htmlspecialchars($user['user_id']); ?></div>
        <div class="subtitle">Ready to boost your memory and master new topics? Let's get started with your flashcards journey!</div>
        
    

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

        <button onClick="window.location='create_flashcards.php';" class="cta-btn">Create Flashcard Set</button>

        <?php if (!empty($sets)): ?>
            <div class="section-title">Your Flashcard Sets</div>
            <div class="sets-list">
                <?php foreach ($sets as $set): ?>
                    <a class="set-card" href="flashcard_list.php?set=<?= urlencode($set) ?>">
                        <div class="set-card-name"><?= htmlspecialchars(str_replace('set_', '', $set)) ?></div>
                        <div class="set-card-info">Click to study</div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="section-title">Get Started</div>
            <div class="empty-state">
                <p>No flashcard sets yet. Create your first one to start studying!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
