<?php 
require("connect_db.php");
require("user_db.php");
session_start();

// redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserByUserId($user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | Local Flashcards</title>
    <link rel="stylesheet" href="./output.css">
    <style>
        .hero-bg {
            background: linear-gradient(120deg, #e0e7ff 0%, #f0fdfa 100%);
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 2.5rem 2rem;
            max-width: 480px;
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
        @media (max-width: 600px) {
            .card { padding: 1.2rem 0.5rem; }
            .welcome { font-size: 1.3rem; }
        }
    </style>
</head>

<body class="hero-bg min-h-screen">
    <?php require("header.php"); ?>

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
    </div>
</body>
</html>
